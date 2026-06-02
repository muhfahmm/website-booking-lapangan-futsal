<?php
/**
 * Create Midtrans Transaction
 * 
 * Endpoint: POST /payment/create_transaction.php
 * 
 * Request JSON:
 * {
 *     "lapangan_id": 1,
 *     "tanggal": "2026-06-15",
 *     "jam_mulai": "18:00",
 *     "jam_selesai": "19:00",
 *     "nama_pemesan": "Budi Santoso",
 *     "no_hp": "08123456789",
 *     "email": "budi@example.com"
 * }
 * 
 * Response JSON:
 * {
 *     "status": "success",
 *     "snap_token": "xxxxx",
 *     "booking_id": 123,
 *     "order_id": "BOOKING-123",
 *     "total_harga": 100000
 * }
 */

// Clean any output buffer
if (ob_get_level()) ob_end_clean();

// Start fresh output buffer
ob_start();

// Set JSON header first
header('Content-Type: application/json');

// Suppress PHP errors from being output
ini_set('display_errors', 0);
error_reporting(0);

require '../config/koneksi.php';
require '../config/midtrans.php';

// Check if request is POST
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if(!$data) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Invalid JSON input']));
}

// Validate required fields
$lapangan_id = isset($data['lapangan_id']) ? (int)$data['lapangan_id'] : null;
$tanggal = isset($data['tanggal']) ? $data['tanggal'] : null;
$jam_mulai = isset($data['jam_mulai']) ? $data['jam_mulai'] : null;
$jam_selesai = isset($data['jam_selesai']) ? $data['jam_selesai'] : null;
$nama_pemesan = isset($data['nama_pemesan']) ? $data['nama_pemesan'] : null;
$no_hp = isset($data['no_hp']) ? $data['no_hp'] : null;
$email = isset($data['email']) ? $data['email'] : null;

if(!$lapangan_id || !$tanggal || !$jam_mulai || !$jam_selesai || !$nama_pemesan || !$no_hp || !$email) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Missing required fields']));
}

// Validate date format
if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Invalid date format (YYYY-MM-DD)']));
}

// Validate email format
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Invalid email format']));
}

// Get lapangan data
$result = $conn->query("SELECT id, nama, harga, harga_weekend FROM tb_lapangan WHERE id = $lapangan_id");
if(!$result || $result->num_rows === 0) {
    http_response_code(404);
    die(json_encode(['status' => 'error', 'message' => 'Lapangan not found']));
}

$lapangan = $result->fetch_assoc();

// Calculate price based on weekday/weekend
try {
    $date_obj = new DateTime($tanggal);
    $day_of_week = (int)$date_obj->format('N'); // 1=Monday, 7=Sunday
    $is_weekend = ($day_of_week == 6 || $day_of_week == 7); // Saturday=6, Sunday=7
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Invalid date']));
}

$hourly_price = $is_weekend ? $lapangan['harga_weekend'] : $lapangan['harga'];

// Calculate duration
try {
    $start = new DateTime("2000-01-01 $jam_mulai");
    $end = new DateTime("2000-01-01 $jam_selesai");
    
    if($end <= $start) {
        http_response_code(400);
        die(json_encode(['status' => 'error', 'message' => 'End time must be after start time']));
    }
    
    $interval = $start->diff($end);
    $hours = $interval->h + ($interval->i / 60);
} catch (Exception $e) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Invalid time format']));
}

$total_harga = (int)round($hourly_price * $hours);

// Check for booking conflicts
$result = $conn->query("
    SELECT id FROM tb_booking 
    WHERE lapangan_id = $lapangan_id 
    AND tanggal = '$tanggal'
    AND (
        (jam_mulai < '$jam_selesai' AND jam_selesai > '$jam_mulai')
    )
    AND status NOT IN ('cancelled', 'failed')
    AND payment_status != 'failed'
    LIMIT 1
");

if($result && $result->num_rows > 0) {
    http_response_code(409);
    die(json_encode(['status' => 'error', 'message' => 'Time slot already booked. Please choose another time.']));
}

// Sanitize inputs
$nama_pemesan = $conn->real_escape_string($nama_pemesan);
$no_hp = $conn->real_escape_string($no_hp);
$email = $conn->real_escape_string($email);

// Create booking record
$conn->query("
    INSERT INTO tb_booking 
    (lapangan_id, nama_pemesan, tanggal, jam_mulai, jam_selesai, total_harga, payment_status, no_hp, email, status)
    VALUES 
    ($lapangan_id, '$nama_pemesan', '$tanggal', '$jam_mulai', '$jam_selesai', $total_harga, 'pending', '$no_hp', '$email', 'pending')
");

if($conn->errno) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Failed to create booking']));
}

$booking_id = $conn->insert_id;
$order_id = 'BOOKING-' . $booking_id;

// Create payment record
$conn->query("
    INSERT INTO tb_pembayaran 
    (booking_id, transaction_id, amount, status)
    VALUES 
    ($booking_id, '$order_id', $total_harga, 'pending')
");

if($conn->errno) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Failed to create payment record']));
}

// Now create Midtrans transaction using real Snap integration
// Disable demo mode so Midtrans can show e-wallet and other payment methods

// Set DEMO_MODE to false for real Midtrans flow
if (!defined('DEMO_MODE')) {
    define('DEMO_MODE', false);
}

if(DEMO_MODE) {
    // For testing only: skip Midtrans and simulate payment approval
    $snap_token = 'DEMO-MODE-APPROVED';
    $conn->query("UPDATE tb_booking SET payment_status = 'paid', status = 'confirmed' WHERE id = $booking_id");
    $conn->query("UPDATE tb_pembayaran SET status = 'settlement', payment_method = 'demo_mode' WHERE booking_id = $booking_id");
    $conn->query("
        INSERT INTO tb_payment_log 
        (booking_id, transaction_id, action, old_status, new_status, response)
        VALUES 
        ($booking_id, '$order_id', 'demo_approve', 'pending', 'settlement', 'Demo mode auto-approval')
    ");
    ob_clean();
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'snap_token' => $snap_token,
        'booking_id' => $booking_id,
        'order_id' => $order_id,
        'total_harga' => $total_harga,
        'lapangan_nama' => $lapangan['nama'],
        'demo_mode' => true,
        'message' => 'Booking confirmed (Demo Mode - Midtrans bypassed)'
    ]);
    ob_end_flush();
    exit;
}

/*
\Midtrans\Config::$serverKey = MIDTRANS_SERVER_KEY;
\Midtrans\Config::$clientKey = MIDTRANS_CLIENT_KEY;
\Midtrans\Config::$isProduction = (MIDTRANS_ENVIRONMENT === 'production');
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

try {
    $transaction = \Midtrans\Snap::createTransaction([
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $total_harga,
        ],
        'customer_details' => [
            'first_name' => $nama_pemesan,
            'phone' => $no_hp,
            'email' => $email,
        ],
        'item_details' => [
            [
                'id' => $lapangan_id,
                'price' => $hourly_price,
                'quantity' => (int)ceil($hours),
                'name' => $lapangan['nama'] . ' (' . (int)ceil($hours) . ' jam)',
            ]
        ]
    ]);
    
    $snap_token = $transaction->token;
    
    // Update payment with actual transaction ID
    $conn->query("
        UPDATE tb_pembayaran 
        SET transaction_id = '$order_id'
        WHERE booking_id = $booking_id
    ");
    
} catch (Exception $e) {
    // Rollback: delete booking and payment record
    $conn->query("DELETE FROM tb_pembayaran WHERE booking_id = $booking_id");
    $conn->query("DELETE FROM tb_booking WHERE id = $booking_id");
    
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Failed to create Midtrans transaction: ' . $e->getMessage()]));
}
*/

// Now create Midtrans transaction using direct Snap API request
$isProduction = (MIDTRANS_ENVIRONMENT === 'production');
$midtransHost = $isProduction ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
$transactionPayload = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $total_harga,
    ],
    'customer_details' => [
        'first_name' => $nama_pemesan,
        'phone' => $no_hp,
        'email' => $email,
    ],
    'item_details' => [
        [
            'id' => $lapangan_id,
            'price' => $hourly_price,
            'quantity' => (int)ceil($hours),
            'name' => $lapangan['nama'] . ' (' . (int)ceil($hours) . ' jam)',
        ]
    ],
    'enabled_payments' => [
        'credit_card',
        'gopay',
        'shopeepay',
        'qris',
        'bank_transfer',
        'echannel',
        'cstore'
    ],
    'finish_redirect_url' => MIDTRANS_SUCCESS_URL,
];

$payloadJson = json_encode($transactionPayload);
$response = false;
$httpStatus = null;
$curlError = null;

if(function_exists('curl_init')) {
    $ch = curl_init($midtransHost . '/snap/v1/transactions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode(MIDTRANS_SERVER_KEY . ':'),
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payloadJson);

    $response = curl_exec($ch);
    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
} else {
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n" .
                        "Accept: application/json\r\n" .
                        "Authorization: Basic " . base64_encode(MIDTRANS_SERVER_KEY . ':') . "\r\n",
            'content' => $payloadJson,
            'ignore_errors' => true,
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($midtransHost . '/snap/v1/transactions', false, $context);
    if(isset($http_response_header) && is_array($http_response_header)) {
        foreach($http_response_header as $header) {
            if(preg_match('#^HTTP/\d+\.\d+\s+(\d+)#', $header, $matches)) {
                $httpStatus = (int)$matches[1];
                break;
            }
        }
    }
}

if($response === false || ($httpStatus !== null && $httpStatus >= 400)) {
    $errorMessage = 'Failed to request Midtrans Snap token';
    if($curlError) {
        $errorMessage .= ': ' . $curlError;
    } elseif($response) {
        $errorMessage .= ': ' . $response;
    }

    $conn->query("DELETE FROM tb_pembayaran WHERE booking_id = $booking_id");
    $conn->query("DELETE FROM tb_booking WHERE id = $booking_id");

    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => $errorMessage]));
}

$transactionResponse = json_decode($response, true);
if(!$transactionResponse || !isset($transactionResponse['token'])) {
    $conn->query("DELETE FROM tb_pembayaran WHERE booking_id = $booking_id");
    $conn->query("DELETE FROM tb_booking WHERE id = $booking_id");

    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'Invalid Midtrans response: ' . $response]));
}

$snap_token = $transactionResponse['token'];

// Clean output buffer and send only JSON
ob_clean();

// Return success response
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'snap_token' => $snap_token,
    'booking_id' => $booking_id,
    'order_id' => $order_id,
    'total_harga' => $total_harga,
    'lapangan_nama' => $lapangan['nama'],
    'message' => 'Transaction created successfully'
]);

// Flush and end
ob_end_flush();
exit;
