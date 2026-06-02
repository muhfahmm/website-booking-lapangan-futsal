<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';
// Protect page
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
    <title>Kelola Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-2xl w-full p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-emerald-600 mb-6">Kelola Booking</h1>
        <p class="text-gray-700">Halaman ini akan menampilkan daftar booking dan aksi CRUD. Saat ini masih placeholder.</p>
        <a href="dashboard.php" class="inline-block mt-4 text-emerald-600 hover:underline">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
