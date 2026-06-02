<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php'; // Database connection

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex">
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-emerald-600">Dashboard</h1>
            <div class="mt-2 h-1 w-24 bg-emerald-600"></div>
        </header>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Total Lapangan</h2>
                <p class="text-2xl font-bold text-emerald-600">0</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Total Booking</h2>
                <p class="text-2xl font-bold text-emerald-600">0</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Pengguna Aktif</h2>
                <p class="text-2xl font-bold text-emerald-600">0</p>
            </div>
        </div>
    </main>
</body>
</html>
