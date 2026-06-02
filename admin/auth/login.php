<?php
session_start();
require_once __DIR__ . '/../../config/koneksi.php'; // adjust path

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ../dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $password) {
    // Ensure database connection exists
    if (!isset($conn)) {
        $conn = new mysqli($host, $user, $password, $db);
        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }
    }
        $stmt = $conn->prepare('SELECT id, password FROM tb_admin WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hash);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $id;
                header('Location: ../dashboard.php');
                exit();
            } else {
                $error = 'Email atau password salah.';
            }
        } else {
            $error = 'Email tidak terdaftar.';
        }
        $stmt->close();
    } else {
        $error = 'Silakan isi semua kolom.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-emerald-600">Login Admin</h2>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
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
            <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 transition">Login</button>
        </form>
        <p class="mt-4 text-center text-sm">Belum punya akun? <a href="register.php" class="text-emerald-600 hover:underline">Registrasi</a></p>
    </div>
</body>
</html>
