<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php'; // adjust path

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../dashboard.php');
    exit();
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if ($username && $password && $confirm) {
        if ($password !== $confirm) {
            $error = 'Password dan konfirmasi tidak cocok.';
        } else {
            // Check if username already exists
            $stmt = $conn->prepare('SELECT id FROM tb_admin WHERE username = ?');
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = 'Username sudah terdaftar.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $insert = $conn->prepare('INSERT INTO tb_admin (username, password) VALUES (?, ?)');
                $insert->bind_param('ss', $username, $hash);
                if ($insert->execute()) {
                    $success = 'Registrasi berhasil. Silakan login.';
                } else {
                    $error = 'Gagal mendaftar. Coba lagi.';
                }
                $insert->close();
            }
            $stmt->close();
        }
    } else {
        $error = 'Harap isi semua kolom.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-emerald-600">Registrasi Admin</h2>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($success): ?>
            <p class="text-green-600 mb-4"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" required class="w-full mt-1 p-2 border rounded" placeholder="adminuser">
            </div>
            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required class="w-full mt-1 p-2 border rounded" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-gray-700">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required class="w-full mt-1 p-2 border rounded" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 transition">Daftar</button>
        </form>
        <p class="mt-4 text-center text-sm">Sudah punya akun? <a href="login.php" class="text-emerald-600 hover:underline">Login</a></p>
    </div>
</body>
</html>
