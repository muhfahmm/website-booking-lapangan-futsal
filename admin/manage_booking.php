<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit();
}

// Get all booking with additional payment info
$result = $conn->query('SELECT 
    b.id, 
    b.lapangan_id, 
    b.nama_pemesan, 
    b.no_hp,
    b.email,
    b.tanggal, 
    b.jam_mulai, 
    b.jam_selesai, 
    b.status, 
    b.payment_status,
    b.total_harga,
    b.created_at,
    l.nama as lapangan_nama,
    l.harga,
    l.harga_weekend
FROM tb_booking b 
LEFT JOIN tb_lapangan l ON b.lapangan_id = l.id 
ORDER BY b.created_at DESC');
$booking_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $booking_list[] = $row;
    }
}

// Count by status
$pending_count = 0;
$confirmed_count = 0;
$cancelled_count = 0;

foreach ($booking_list as $booking) {
    if ($booking['status'] === 'pending') $pending_count++;
    elseif ($booking['status'] === 'confirmed') $confirmed_count++;
    elseif ($booking['status'] === 'cancelled') $cancelled_count++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <main class="min-h-screen">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-4xl font-bold text-slate-900">Notifikasi Booking</h1>
                        <p class="text-gray-600 text-sm">Lihat pesanan booking dari user</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Menunggu Konfirmasi</p>
                                <p class="text-4xl font-bold text-yellow-600"><?php echo $pending_count; ?></p>
                            </div>
                            <div class="text-5xl text-yellow-100">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Terkonfirmasi</p>
                                <p class="text-4xl font-bold text-green-600"><?php echo $confirmed_count; ?></p>
                            </div>
                            <div class="text-5xl text-green-100">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Dibatalkan</p>
                                <p class="text-4xl font-bold text-red-600"><?php echo $cancelled_count; ?></p>
                            </div>
                            <div class="text-5xl text-red-100">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications List -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-blue-600 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">ID</th>
                                    <th class="px-4 py-3 text-left">Lapangan</th>
                                    <th class="px-4 py-3 text-left">Nama Pemesan</th>
                                    <th class="px-4 py-3 text-left">Tanggal & Jam</th>
                                    <th class="px-4 py-3 text-left">Harga</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Pembayaran</th>
                                    <th class="px-4 py-3 text-left">Tanggal Pesan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php if (count($booking_list) > 0): ?>
                                    <?php foreach ($booking_list as $item): ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-semibold text-gray-900">#<?php echo $item['id']; ?></td>
                                            <td class="px-4 py-3"><?php echo htmlspecialchars($item['lapangan_nama'] ?? '-'); ?></td>
                                            <td class="px-4 py-3">
                                                <div class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['nama_pemesan']); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($item['no_hp'] ?? ''); ?></div>
                                                <div class="text-xs text-gray-500"><?php echo htmlspecialchars($item['email'] ?? ''); ?></div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm"><?php echo $item['tanggal']; ?></div>
                                                <div class="text-xs text-gray-500"><?php echo $item['jam_mulai']; ?> - <?php echo $item['jam_selesai']; ?></div>
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-blue-600">Rp <?php echo number_format($item['total_harga'], 0, ',', '.'); ?></td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
                                                    <?php 
                                                    if ($item['status'] === 'confirmed') echo 'bg-green-100 text-green-700';
                                                    elseif ($item['status'] === 'pending') echo 'bg-yellow-100 text-yellow-700';
                                                    else echo 'bg-red-100 text-red-700';
                                                    ?>">
                                                    <i class="fas fa-circle text-xs"></i>
                                                    <?php echo ucfirst($item['status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
                                                    <?php 
                                                    if ($item['payment_status'] === 'paid') echo 'bg-green-100 text-green-700';
                                                    elseif ($item['payment_status'] === 'pending') echo 'bg-blue-100 text-blue-700';
                                                    else echo 'bg-red-100 text-red-700';
                                                    ?>">
                                                    <i class="fas fa-circle text-xs"></i>
                                                    <?php echo ucfirst($item['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                            <p class="font-semibold">Belum ada pesanan booking</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden divide-y">
                        <?php if (count($booking_list) > 0): ?>
                            <?php foreach ($booking_list as $item): ?>
                                <div class="p-4 space-y-3 border-b last:border-b-0 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start gap-2">
                                        <div>
                                            <p class="text-xs text-gray-500 font-semibold">Booking #<?php echo $item['id']; ?></p>
                                            <p class="font-bold text-lg text-gray-900"><?php echo htmlspecialchars($item['nama_pemesan']); ?></p>
                                            <p class="text-xs text-gray-600"><?php echo htmlspecialchars($item['no_hp'] ?? ''); ?></p>
                                            <p class="text-xs text-gray-600"><?php echo htmlspecialchars($item['email'] ?? ''); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold mb-1 block
                                                <?php 
                                                if ($item['status'] === 'confirmed') echo 'bg-green-100 text-green-700';
                                                elseif ($item['status'] === 'pending') echo 'bg-yellow-100 text-yellow-700';
                                                else echo 'bg-red-100 text-red-700';
                                                ?>">
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold block
                                                <?php 
                                                if ($item['payment_status'] === 'paid') echo 'bg-green-100 text-green-700';
                                                elseif ($item['payment_status'] === 'pending') echo 'bg-blue-100 text-blue-700';
                                                else echo 'bg-red-100 text-red-700';
                                                ?>">
                                                <?php echo ucfirst($item['payment_status']); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="bg-blue-50 p-3 rounded space-y-2">
                                        <div>
                                            <p class="text-xs text-gray-500 font-semibold">Lapangan</p>
                                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['lapangan_nama'] ?? '-'); ?></p>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                            <div>
                                                <p class="text-xs text-gray-500 font-semibold">Tanggal</p>
                                                <p class="text-gray-900"><?php echo $item['tanggal']; ?></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 font-semibold">Jam</p>
                                                <p class="text-gray-900"><?php echo $item['jam_mulai']; ?> - <?php echo $item['jam_selesai']; ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex justify-between items-center pt-2 border-t">
                                        <div>
                                            <p class="text-xs text-gray-500 font-semibold">Total</p>
                                            <p class="font-bold text-blue-600">Rp <?php echo number_format($item['total_harga'], 0, ',', '.'); ?></p>
                                        </div>
                                        <div class="text-right text-xs text-gray-500">
                                            <?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                                <p class="font-semibold">Belum ada pesanan booking</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
