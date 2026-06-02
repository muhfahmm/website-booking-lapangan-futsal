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
<body class="bg-gray-50 flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 flex-1 p-8">
        <header class="mb-6">
            <h1 class="text-3xl font-bold text-emerald-600">Kelola Booking</h1>
            <div class="mt-2 h-1 w-24 bg-emerald-600"></div>
        </header>

        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="text-gray-700">Halaman ini akan menampilkan daftar booking dan aksi CRUD. Saat ini masih placeholder.</p>
        </div>
    </main>
</body>
</html>
