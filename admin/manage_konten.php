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
    $conn->query("DELETE FROM tb_konten WHERE id = $id");
    header('Location: manage_konten.php');
    exit();
}

// Handle INSERT
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $judul = $conn->real_escape_string($_POST['judul'] ?? '');
        $isi = $conn->real_escape_string($_POST['isi'] ?? '');
        $tipe = $_POST['tipe'] ?? 'artikel';
        
        if ($judul && $isi) {
            $conn->query("INSERT INTO tb_konten (judul, isi, tipe) VALUES ('$judul', '$isi', '$tipe')");
            $message = '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Konten berhasil ditambahkan!</div>';
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = intval($_POST['id']);
        $judul = $conn->real_escape_string($_POST['judul'] ?? '');
        $isi = $conn->real_escape_string($_POST['isi'] ?? '');
        $tipe = $_POST['tipe'] ?? 'artikel';
        
        if ($judul && $isi) {
            $conn->query("UPDATE tb_konten SET judul='$judul', isi='$isi', tipe='$tipe' WHERE id=$id");
            $message = '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Konten berhasil diperbarui!</div>';
        }
    }
}

// Get all konten
$result = $conn->query('SELECT * FROM tb_konten ORDER BY id DESC');
$konten_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $konten_list[] = $row;
    }
}

$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM tb_konten WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konten</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 flex-1 p-8">
        <header class="mb-6">
            <h1 class="text-4xl font-bold text-emerald-600">Kelola Konten</h1>
            <div class="mt-2 h-1 w-24 bg-emerald-600"></div>
        </header>

        <?php echo $message; ?>

        <!-- Form Add/Edit -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4"><?php echo $edit_data ? 'Edit Konten' : 'Tambah Konten Baru'; ?></h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Judul</label>
                    <input type="text" name="judul" required class="w-full px-3 py-2 border rounded" placeholder="Judul Konten" value="<?php echo htmlspecialchars($edit_data['judul'] ?? ''); ?>">
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Isi/Deskripsi</label>
                    <textarea name="isi" required class="w-full px-3 py-2 border rounded" rows="6" placeholder="Isi konten..."><?php echo htmlspecialchars($edit_data['isi'] ?? ''); ?></textarea>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Tipe</label>
                    <select name="tipe" class="w-full px-3 py-2 border rounded">
                        <option value="artikel" <?php echo ($edit_data['tipe'] ?? 'artikel') === 'artikel' ? 'selected' : ''; ?>>Artikel</option>
                        <option value="berita" <?php echo ($edit_data['tipe'] ?? '') === 'berita' ? 'selected' : ''; ?>>Berita</option>
                        <option value="panduan" <?php echo ($edit_data['tipe'] ?? '') === 'panduan' ? 'selected' : ''; ?>>Panduan</option>
                    </select>
                </div>
                
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700"><?php echo $edit_data ? 'Update' : 'Tambah'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="manage_konten.php" class="inline-block ml-2 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-emerald-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Judul</th>
                        <th class="px-6 py-3 text-left">Isi (Preview)</th>
                        <th class="px-6 py-3 text-left">Tipe</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($konten_list) > 0): ?>
                        <?php foreach ($konten_list as $item): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3"><?php echo $item['id']; ?></td>
                                <td class="px-6 py-3"><?php echo htmlspecialchars($item['judul']); ?></td>
                                <td class="px-6 py-3">
                                    <div class="max-w-xs truncate text-gray-600">
                                        <?php echo htmlspecialchars(substr($item['isi'], 0, 100)); ?>...
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="px-3 py-1 rounded text-sm bg-blue-100 text-blue-700">
                                        <?php echo ucfirst($item['tipe']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="manage_konten.php?edit=<?php echo $item['id']; ?>" class="text-blue-600 hover:underline mr-3">Edit</a>
                                    <a href="manage_konten.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-3 text-center text-gray-500">Belum ada data konten</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
