<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
// Protect page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit();
}

$admin_name = 'Admin';
if (isset($_SESSION['admin_id']) && isset($conn)) {
    $admin_id = (int)$_SESSION['admin_id'];
    $stmt_admin = $conn->prepare('SELECT username FROM tb_admin WHERE id = ?');
    if ($stmt_admin) {
        $stmt_admin->bind_param('i', $admin_id);
        $stmt_admin->execute();
        $stmt_admin->bind_result($username_db);
        if ($stmt_admin->fetch()) {
            $admin_name = $username_db;
        }
        $stmt_admin->close();
    }
}

// Get data counts
$lapangan_count = 0;
$booking_count = 0;

// Query lapangan count
if (isset($conn)) {
    $result = $conn->query('SELECT COUNT(*) as total FROM tb_lapangan');
    if ($result) {
        $row = $result->fetch_assoc();
        $lapangan_count = $row['total'];
    }
    
    // Query booking count
    $result = $conn->query('SELECT COUNT(*) as total FROM tb_booking');
    if ($result) {
        $row = $result->fetch_assoc();
        $booking_count = $row['total'];
    }
    

}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <main class="min-h-screen">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-4 md:p-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-4xl font-bold text-slate-900">Dashboard</h1>
                    <p class="text-gray-600 mt-1">Selamat datang di Admin Panel</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-emerald-100 text-emerald-800 text-sm font-semibold px-4 py-2 rounded-full">
                        halo <span class="font-bold"><?php echo htmlspecialchars($admin_name); ?></span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <!-- Kelola Lapangan -->
                    <a href="manage_lapangan.php" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-emerald-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Kelola Lapangan</p>
                                <p class="text-4xl font-bold text-emerald-600"><?php echo $lapangan_count; ?></p>
                                <p class="text-gray-500 text-xs mt-2">Total lapangan tersedia</p>
                            </div>
                            <div class="text-5xl text-emerald-100">
                                <i class="fas fa-soccer-ball"></i>
                            </div>
                        </div>
                    </a>

                    <!-- Kelola Booking -->
                    <a href="manage_booking.php" class="bg-white rounded-lg shadow hover:shadow-lg transition p-6 border-l-4 border-blue-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-600 text-sm font-semibold mb-1">Kelola Booking</p>
                                <p class="text-4xl font-bold text-blue-600"><?php echo $booking_count; ?></p>
                                <p class="text-gray-500 text-xs mt-2">Total booking dalam sistem</p>
                            </div>
                            <div class="text-5xl text-blue-100">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Quick Actions -->
                <div class="mt-8 bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Aksi Cepat</h2>
                    <div class="grid grid-cols-2 md:grid-cols-2 gap-3">
                        <a href="manage_lapangan.php" class="p-4 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition text-center">
                            <i class="fas fa-plus text-emerald-600 text-2xl mb-2"></i>
                            <p class="text-sm font-semibold text-emerald-900">Tambah Lapangan</p>
                        </a>
                        <a href="manage_booking.php" class="p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition text-center">
                            <i class="fas fa-plus text-blue-600 text-2xl mb-2"></i>
                            <p class="text-sm font-semibold text-blue-900">Tambah Booking</p>
                        </a>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-8 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-lg shadow p-6">
                    <div class="flex items-start gap-4">
                        <i class="fas fa-info-circle text-2xl flex-shrink-0 mt-1"></i>
                        <div>
                            <h3 class="font-bold text-lg mb-2">Selamat datang di Admin Panel</h3>
                            <p class="text-emerald-100 text-sm">Panel admin untuk mengelola lapangan futsal, booking, konten, dan gallery. Gunakan menu di sebelah kiri untuk navigasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
