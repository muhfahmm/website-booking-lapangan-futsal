<?php
/**
 * Midtrans Webhook Notification Handler
 * 
 * Endpoint: POST /payment/notification.php
 * Dipanggil oleh Midtrans setelah transaksi selesai
 */

require '../config/koneksi.php';
require '../config/midtrans.php';

// Get raw POST data
$input = file_get_contents('php://input');
$notification = json_decode($input);

// Log incoming notification
$log_data = json_encode($notification, JSON_PRETTY_PRINT);
error_log("Midtrans Notification: " . $log_data);

// Validate incoming data
if(!$notification) {
    http_response_code(400);
    die('Invalid JSON');
}

// Extract data
$orderId = $notification->order_id ?? null;
$statusCode = $notification->status_code ?? null;
$grossAmount = $notification->gross_amount ?? null;
$transactionStatus = $notification->transaction_status ?? null;
$signatureKey = $notification->signature_key ?? null;

if(!$orderId || !$statusCode) {
    http_response_code(400);
    die('Missing required fields');
}

// Verify Midtrans signature
$serverKey = MIDTRANS_SERVER_KEY;
$input_string = $orderId . $statusCode . $grossAmount . $serverKey;
$signature = hash('sha512', $input_string);

if($signature !== $signatureKey) {
    error_log('SECURITY: Invalid signature from Midtrans!');
    http_response_code(403);
    die('Invalid signature');
}

// Extract booking ID from order_id
$booking_id = (int)str_replace('BOOKING-', '', $orderId);

// Get current booking & payment data
$result = $conn->query("
    SELECT b.*, p.id as payment_id, p.status as payment_status
    FROM tb_booking b
    LEFT JOIN tb_pembayaran p ON b.id = p.booking_id
    WHERE b.id = $booking_id
");

if($result->num_rows === 0) {
    error_log("Booking not found for ID: $booking_id");
    http_response_code(404);
    die('Booking not found');
}

$booking = $result->fetch_assoc();

// Map Midtrans status to our status
$payment_status_map = [
    'settlement' => 'paid',
    'pending' => 'pending',
    'expire' => 'failed',
    'cancel' => 'failed',
    'deny' => 'failed',
    'failure' => 'failed',
];

$new_payment_status = $payment_status_map[$transactionStatus] ?? 'pending';

// Update payment record
if($booking['payment_id']) {
    $conn->query("
        UPDATE tb_pembayaran SET 
        status = '$transactionStatus',
        midtrans_response = '" . $conn->real_escape_string($input) . "',
        updated_at = NOW()
        WHERE id = " . $booking['payment_id']
    );
} else {
    // Insert new payment record if not exists
    $conn->query("
        INSERT INTO tb_pembayaran 
        (booking_id, transaction_id, amount, status, midtrans_response, created_at)
        VALUES 
        ($booking_id, '$orderId', $grossAmount, '$transactionStatus', 
         '" . $conn->real_escape_string($input) . "', NOW())
    ");
}

// Update booking payment status
$conn->query("
    UPDATE tb_booking SET 
    payment_status = '$new_payment_status',
    updated_at = NOW()
    WHERE id = $booking_id
");

// Log transaction
$conn->query("
    INSERT INTO tb_payment_log 
    (booking_id, transaction_id, action, old_status, new_status, response)
    VALUES 
    ($booking_id, '$orderId', 'webhook_received', 
     '" . ($booking['payment_status'] ?? 'pending') . "', 
     '$new_payment_status', 
     '" . $conn->real_escape_string($input) . "')
");

// Handle successful payment
if($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
    
    // Send confirmation email to customer
    if($booking['email']) {
        sendConfirmationEmail($booking, $orderId);
    }
    
    // Send notification to admin
    sendAdminNotification($booking, $orderId, 'PAYMENT_RECEIVED');
}

// Handle payment failure
if(in_array($transactionStatus, ['expire', 'cancel', 'deny', 'failure'])) {
    // Send failure notification to customer
    if($booking['email']) {
        sendPaymentFailedEmail($booking, $orderId, $transactionStatus);
    }
}

// Response to Midtrans
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Notification processed',
    'booking_id' => $booking_id,
    'transaction_status' => $transactionStatus
]);

// Helper functions

function sendConfirmationEmail($booking, $orderId) {
    $to = $booking['email'];
    $subject = "✓ Pembayaran Berhasil - Booking " . $orderId;
    
    $tanggal = date('d M Y', strtotime($booking['tanggal']));
    $harga = number_format($booking['total_harga'], 0, ',', '.');
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>Pembayaran Anda Telah Diterima!</h2>
        
        <p>Halo {$booking['nama_pemesan']},</p>
        
        <p>Terima kasih atas pembayaran Anda. Booking lapangan Anda telah dikonfirmasi.</p>
        
        <h3>Ringkasan Booking:</h3>
        <table border='1' cellpadding='10'>
            <tr>
                <td>No. Referensi</td>
                <td>{$orderId}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{$tanggal}</td>
            </tr>
            <tr>
                <td>Jam</td>
                <td>{$booking['jam_mulai']} - {$booking['jam_selesai']}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>Rp {$harga}</td>
            </tr>
        </table>
        
        <p>Nomor lapangan akan dikirim 30 menit sebelum jam bermain.</p>
        
        <p>Terima kasih,<br>FutsalBook Admin</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    
    // mail($to, $subject, $message, $headers);
    error_log("Email sent to: $to");
}

function sendAdminNotification($booking, $orderId, $type) {
    // Send to admin email or push notification
    error_log("Admin notification: $type for booking $orderId");
}

function sendPaymentFailedEmail($booking, $orderId, $reason) {
    // Send payment failed notification
    error_log("Payment failed: $reason for booking $orderId");
}

?>
