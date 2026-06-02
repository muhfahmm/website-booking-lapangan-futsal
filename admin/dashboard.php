<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
// Protect page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit();
}

// Get data counts
$lapangan_count = 0;
$booking_count = 0;
$konten_count = 0;

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
    
    // Query konten count
    $result = $conn->query('SELECT COUNT(*) as total FROM tb_konten');
    if ($result) {
        $row = $result->fetch_assoc();
        $konten_count = $row['total'];
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
</head>
<body class="bg-gray-50 flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 flex-1 p-8">
        <header class="mb-8">
            <h1 class="text-4xl font-bold text-emerald-600">Dashboard</h1>
            <div class="mt-2 h-1 w-24 bg-emerald-600"></div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kelola Lapangan -->
            <a href="manage_lapangan.php" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition p-6 cursor-pointer hover:border-emerald-500 border-2 border-transparent">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Kelola Lapangan</h2>
                        <p class="text-3xl font-bold text-emerald-600"><?php echo $lapangan_count; ?></p>
                    </div>
                    <div class="text-5xl text-emerald-200">⚽</div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Klik untuk kelola lapangan</p>
            </a>

            <!-- Kelola Booking -->
            <a href="manage_booking.php" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition p-6 cursor-pointer hover:border-emerald-500 border-2 border-transparent">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Kelola Booking</h2>
                        <p class="text-3xl font-bold text-emerald-600"><?php echo $booking_count; ?></p>
                    </div>
                    <div class="text-5xl text-emerald-200">📅</div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Klik untuk kelola booking</p>
            </a>

            <!-- Kelola Konten -->
            <a href="manage_konten.php" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition p-6 cursor-pointer hover:border-emerald-500 border-2 border-transparent">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-700 mb-2">Kelola Konten</h2>
                        <p class="text-3xl font-bold text-emerald-600"><?php echo $konten_count; ?></p>
                    </div>
                    <div class="text-5xl text-emerald-200">📝</div>
                </div>
                <p class="text-sm text-gray-500 mt-4">Klik untuk kelola konten</p>
            </a>
        </div>
    </main>
</body>
</html>
