<?php
/**
 * Payment Success Page
 * Tampil setelah pembayaran berhasil di Midtrans
 */

require '../config/koneksi.php';

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

if(!$order_id) {
    header('Location: ../index.php');
    exit;
}

// Get booking and payment data
$result = $conn->query("
    SELECT 
        b.*, 
        p.*,
        l.nama as lapangan_nama,
        l.lokasi
    FROM tb_booking b
    LEFT JOIN tb_pembayaran p ON b.id = p.booking_id
    LEFT JOIN tb_lapangan l ON b.lapangan_id = l.id
    WHERE b.id = " . (int)str_replace('BOOKING-', '', $order_id)
);

$booking = null;
if($result && $result->num_rows > 0) {
    $booking = $result->fetch_assoc();
}

if(!$booking) {
    header('Location: ../index.php');
    exit;
}

// Format display
$tanggal = date('d M Y', strtotime($booking['tanggal']));
$jam = $booking['jam_mulai'] . ' - ' . $booking['jam_selesai'];
$harga = number_format($booking['amount'] ?? $booking['total_harga'], 0, ',', '.');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✓ Pembayaran Berhasil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-emerald-50 to-blue-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Success Header -->
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 p-8 text-center text-white">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold">PEMBAYARAN BERHASIL</h1>
                <p class="text-emerald-100 mt-2">Booking Anda telah dikonfirmasi</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                
                <!-- Transaction Details -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">No. Referensi</p>
                            <p class="font-bold text-lg text-slate-900"><?php echo htmlspecialchars($order_id); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Transaction ID</p>
                            <p class="font-mono text-sm text-slate-900"><?php echo substr($booking['transaction_id'] ?? 'PENDING', 0, 12); ?>...</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="mb-6">
                    <h2 class="font-bold text-slate-900 mb-3">Ringkasan Booking</h2>
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
                            <span class="text-gray-600">Jam Bermain</span>
                            <span class="font-semibold text-slate-900"><?php echo $jam; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Lokasi</span>
                            <span class="font-semibold text-slate-900"><?php echo htmlspecialchars($booking['lokasi']); ?></span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between text-base">
                            <span class="font-bold text-slate-900">Total Pembayaran</span>
                            <span class="font-bold text-emerald-600">Rp <?php echo $harga; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="mb-6 bg-blue-50 rounded-lg p-4">
                    <h3 class="font-bold text-slate-900 mb-2">Pemesan</h3>
                    <div class="text-sm space-y-1">
                        <p><strong>Nama:</strong> <?php echo htmlspecialchars($booking['nama_pemesan']); ?></p>
                        <p><strong>No. HP:</strong> <?php echo htmlspecialchars($booking['no_hp'] ?? '-'); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email'] ?? '-'); ?></p>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-6 flex items-center gap-2 p-3 bg-green-50 rounded-lg border border-green-200">
                    <i class="fas fa-check-circle text-green-600"></i>
                    <span class="text-sm text-green-800">
                        <strong>Terkonfirmasi!</strong> Email konfirmasi telah dikirim
                    </span>
                </div>

                <!-- Important Info -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong><i class="fas fa-info-circle mr-2"></i>Informasi Penting:</strong><br>
                        Nomor lapangan dan panduan akses akan dikirim 30 menit sebelum jam bermain. Pastikan Anda telah tiba 15 menit lebih awal.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="../index.php" class="block w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-emerald-700 transition-all">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                    <a href="javascript:window.print()" class="block w-full bg-slate-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-slate-700 transition-all">
                        <i class="fas fa-print mr-2"></i> Cetak Bukti Pembayaran
                    </a>
                </div>

            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-8 py-4 text-center text-sm text-gray-600">
                <p>Terima kasih telah menggunakan <strong>FutsalBook</strong></p>
                <p class="mt-1">Hubungi: admin@futsalbook.com | 0812-3456-7890</p>
            </div>

        </div>
    </div>

    <script>
        // Auto-print (optional)
        // window.print();
    </script>
</body>
</html>
