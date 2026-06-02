<?php
session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit();
}

// Handle DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM tb_lapangan WHERE id = $id");
    header('Location: manage_lapangan.php');
    exit();
}

// Handle INSERT
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $nama = $conn->real_escape_string($_POST['nama'] ?? '');
        $harga = intval($_POST['harga'] ?? 0);
        $status = $_POST['status'] ?? 'tersedia';
        
        if ($nama) {
            $conn->query("INSERT INTO tb_lapangan (nama, harga, status) VALUES ('$nama', $harga, '$status')");
            $message = '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Lapangan berhasil ditambahkan!</div>';
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = intval($_POST['id']);
        $nama = $conn->real_escape_string($_POST['nama'] ?? '');
        $harga = intval($_POST['harga'] ?? 0);
        $status = $_POST['status'] ?? 'tersedia';
        
        if ($nama) {
            $conn->query("UPDATE tb_lapangan SET nama='$nama', harga=$harga, status='$status' WHERE id=$id");
            $message = '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Lapangan berhasil diperbarui!</div>';
        }
    }
}

// Get all lapangan
$result = $conn->query('SELECT * FROM tb_lapangan');
$lapangan_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $lapangan_list[] = $row;
    }
}

$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM tb_lapangan WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lapangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 flex-1 p-8">
        <header class="mb-6">
            <h1 class="text-4xl font-bold text-emerald-600">Kelola Lapangan</h1>
            <div class="mt-2 h-1 w-24 bg-emerald-600"></div>
        </header>

        <?php echo $message; ?>

        <!-- Form Add/Edit -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4"><?php echo $edit_data ? 'Edit Lapangan' : 'Tambah Lapangan Baru'; ?></h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Lapangan</label>
                        <input type="text" name="nama" required class="w-full px-3 py-2 border rounded" placeholder="Lapangan A" value="<?php echo $edit_data['nama'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Harga (per jam)</label>
                        <input type="number" name="harga" required class="w-full px-3 py-2 border rounded" placeholder="50000" value="<?php echo $edit_data['harga'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded">
                            <option value="tersedia" <?php echo ($edit_data['status'] ?? 'tersedia') === 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="maintenance" <?php echo ($edit_data['status'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700"><?php echo $edit_data ? 'Update' : 'Tambah'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="manage_lapangan.php" class="inline-block ml-2 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-emerald-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Nama</th>
                        <th class="px-6 py-3 text-left">Harga/Jam</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($lapangan_list) > 0): ?>
                        <?php foreach ($lapangan_list as $item): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3"><?php echo $item['id']; ?></td>
                                <td class="px-6 py-3"><?php echo htmlspecialchars($item['nama']); ?></td>
                                <td class="px-6 py-3">Rp <?php echo number_format($item['harga']); ?></td>
                                <td class="px-6 py-3">
                                    <span class="px-3 py-1 rounded text-sm <?php echo $item['status'] === 'tersedia' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="manage_lapangan.php?edit=<?php echo $item['id']; ?>" class="text-blue-600 hover:underline mr-3">Edit</a>
                                    <a href="manage_lapangan.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-3 text-center text-gray-500">Belum ada data lapangan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
