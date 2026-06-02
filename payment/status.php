<?php
/**
 * Payment Status Checker
 * 
 * GET /payment/status.php?order_id=BOOKING-123
 * 
 * Displays current payment status and booking information
 */

require '../config/koneksi.php';

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if(!$order_id) {
    header('Location: ../index.php');
    exit;
}

// Extract booking ID from order_id
$booking_id = (int)str_replace('BOOKING-', '', $order_id);

// Get booking and payment data
$result = $conn->query("
    SELECT 
        b.*, 
        p.status as payment_status,
        p.transaction_id,
        p.payment_method,
        p.updated_at as payment_updated,
        l.nama as lapangan_nama,
        l.lokasi
    FROM tb_booking b
    LEFT JOIN tb_pembayaran p ON b.id = p.booking_id
    LEFT JOIN tb_lapangan l ON b.lapangan_id = l.id
    WHERE b.id = $booking_id
");

if(!$result || $result->num_rows === 0) {
    header('Location: ../index.php');
    exit;
}

$booking = $result->fetch_assoc();

// Map payment status to display
$status_map = [
    'pending' => ['bg' => 'yellow', 'icon' => 'hourglass', 'text' => 'Menunggu Pembayaran', 'color' => 'yellow-800'],
    'settlement' => ['bg' => 'green', 'icon' => 'check-circle', 'text' => 'Sudah Dibayar', 'color' => 'green-800'],
    'capture' => ['bg' => 'green', 'icon' => 'check-circle', 'text' => 'Pembayaran Dikonfirmasi', 'color' => 'green-800'],
    'deny' => ['bg' => 'red', 'icon' => 'times-circle', 'text' => 'Pembayaran Ditolak', 'color' => 'red-800'],
    'cancel' => ['bg' => 'red', 'icon' => 'times-circle', 'text' => 'Pembayaran Dibatalkan', 'color' => 'red-800'],
    'expire' => ['bg' => 'orange', 'icon' => 'clock', 'text' => 'Pembayaran Kadaluarsa', 'color' => 'orange-800'],
    'fail' => ['bg' => 'red', 'icon' => 'times-circle', 'text' => 'Pembayaran Gagal', 'color' => 'red-800'],
];

$payment_status = $booking['payment_status'] ?? 'pending';
$status_info = $status_map[$payment_status] ?? $status_map['pending'];

// Format display
$tanggal = date('d M Y', strtotime($booking['tanggal']));
$jam = $booking['jam_mulai'] . ' - ' . $booking['jam_selesai'];
$harga = number_format($booking['total_harga'], 0, ',', '.');

// Check if payment is paid
$is_paid = in_array($payment_status, ['settlement', 'capture']);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="refresh" content="5">
</head>
<body class="bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Status Header -->
            <div class="bg-gradient-to-r from-<?php echo $status_info['bg']; ?>-600 to-<?php echo $status_info['bg']; ?>-700 p-8 text-center text-white">
                <div class="mb-4">
                    <i class="fas fa-<?php echo $status_info['icon']; ?> text-6xl"></i>
                </div>
                <h1 class="text-2xl font-bold"><?php echo $status_info['text']; ?></h1>
                <p class="text-<?php echo $status_info['bg']; ?>-100 mt-2">Pesanan #<?php echo htmlspecialchars($order_id); ?></p>
                
                <?php if($payment_status === 'pending'): ?>
                <p class="text-<?php echo $status_info['bg']; ?>-100 text-sm mt-3">
                    <i class="fas fa-sync-alt mr-1 animate-spin"></i> Menyegarkan setiap 5 detik...
                </p>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="p-8">
                
                <!-- Status Badge -->
                <div class="mb-6 p-4 rounded-lg bg-<?php echo $status_info['bg']; ?>-50 border border-<?php echo $status_info['bg']; ?>-200">
                    <p class="text-sm text-<?php echo $status_info['color']; ?>">
                        <strong>Status Pembayaran:</strong><br>
                        <?php echo $status_info['text']; ?>
                    </p>
                </div>

                <!-- Transaction Details -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-xs uppercase">Order ID</p>
                            <p class="font-bold text-slate-900"><?php echo htmlspecialchars($order_id); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-xs uppercase">Transaction ID</p>
                            <p class="font-mono text-xs text-slate-900"><?php echo htmlspecialchars(substr($booking['transaction_id'] ?? 'PENDING', 0, 12)); ?>...</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="mb-6">
                    <h2 class="font-bold text-slate-900 mb-3">Detail Booking</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lapangan</span>
                            <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($booking['lapangan_nama']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tanggal</span>
                            <span class="font-semibold text-slate-900"><?php echo $tanggal; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Jam</span>
                            <span class="font-semibold text-slate-900"><?php echo $jam; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lokasi</span>
                            <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($booking['lokasi']); ?></span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between text-base">
                            <span class="font-bold text-slate-900">Total</span>
                            <span class="font-bold text-blue-600">Rp <?php echo $harga; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="mb-6 bg-blue-50 rounded-lg p-4">
                    <h3 class="font-bold text-slate-900 mb-2">Data Pemesan</h3>
                    <div class="text-sm space-y-1">
                        <p><strong>Nama:</strong> <?php echo htmlspecialchars($booking['nama_pemesan']); ?></p>
                        <p><strong>No. HP:</strong> <?php echo htmlspecialchars($booking['no_hp'] ?? '-'); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email'] ?? '-'); ?></p>
                    </div>
                </div>

                <!-- Status-specific info -->
                <?php if($payment_status === 'pending'): ?>
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded text-sm text-yellow-800">
                    <p><strong>Pembayaran masih menunggu...</strong></p>
                    <p class="mt-2">Silakan selesaikan pembayaran Anda. Halaman akan otomatis diperbarui setiap 5 detik.</p>
                </div>
                <?php elseif($is_paid): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded text-sm text-green-800">
                    <p><strong>✓ Pembayaran Berhasil!</strong></p>
                    <p class="mt-2">Email konfirmasi telah dikirim ke <?php echo htmlspecialchars($booking['email']); ?>. Nomor lapangan akan dikirim 30 menit sebelum jam bermain.</p>
                </div>
                <?php else: ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded text-sm text-red-800">
                    <p><strong>✗ Pembayaran <?php echo htmlspecialchars($status_info['text']); ?></strong></p>
                    <p class="mt-2">Silakan coba lagi atau hubungi support kami.</p>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <?php if($payment_status === 'pending'): ?>
                    <a href="../booking/checkout.php?lapangan_id=<?php echo $booking['lapangan_id']; ?>&tanggal=<?php echo $booking['tanggal']; ?>&jam_mulai=<?php echo $booking['jam_mulai']; ?>&jam_selesai=<?php echo $booking['jam_selesai']; ?>" class="block w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-emerald-700 transition-all">
                        <i class="fas fa-credit-card mr-2"></i> Bayar Sekarang
                    </a>
                    <?php elseif($is_paid): ?>
                    <a href="../index.php" class="block w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-emerald-700 transition-all">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                    <a href="javascript:window.print()" class="block w-full bg-slate-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-slate-700 transition-all">
                        <i class="fas fa-print mr-2"></i> Cetak Bukti
                    </a>
                    <?php else: ?>
                    <a href="../payment/failed.php?order_id=<?php echo htmlspecialchars($order_id); ?>" class="block w-full bg-red-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-red-700 transition-all">
                        <i class="fas fa-times mr-2"></i> Lihat Detail Kegagalan
                    </a>
                    <a href="../booking/checkout.php?lapangan_id=<?php echo $booking['lapangan_id']; ?>&tanggal=<?php echo $booking['tanggal']; ?>&jam_mulai=<?php echo $booking['jam_mulai']; ?>&jam_selesai=<?php echo $booking['jam_selesai']; ?>" class="block w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-emerald-700 transition-all">
                        <i class="fas fa-redo mr-2"></i> Coba Lagi
                    </a>
                    <?php endif; ?>
                    
                    <a href="https://wa.me/6288983514206" target="_blank" class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition-all">
                        <i class="fab fa-whatsapp mr-2"></i> Hubungi Support
                    </a>
                </div>

            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-8 py-4 text-center text-sm text-gray-600">
                <p>Butuh bantuan? Hubungi support kami</p>
                <p class="mt-1">admin@futsalbook.com | 0812-3456-7890</p>
            </div>

        </div>
    </div>

    <script>
        // Redirect to success/failed page after payment completes
        const paymentStatus = '<?php echo $payment_status; ?>';
        
        if (paymentStatus === 'settlement' || paymentStatus === 'capture') {
            // Auto redirect to success after 2 seconds
            setTimeout(() => {
                window.location.href = '../payment/success.php?order_id=<?php echo htmlspecialchars($order_id); ?>';
            }, 2000);
        } else if (paymentStatus !== 'pending' && paymentStatus !== 'null') {
            // Auto redirect to failed after 2 seconds
            setTimeout(() => {
                window.location.href = '../payment/failed.php?order_id=<?php echo htmlspecialchars($order_id); ?>';
            }, 2000);
        }
    </script>
</body>
</html>

