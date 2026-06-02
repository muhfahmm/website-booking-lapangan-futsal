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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <main class="min-h-screen">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-4xl font-bold text-slate-900">Kelola Konten</h1>
                        <p class="text-gray-600 text-sm">Manage artikel, berita, dan panduan</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <?php echo $message; ?>

                <!-- Form Add/Edit -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-xl md:text-2xl font-bold text-slate-900 mb-6">
                        <i class="fas fa-plus-circle text-purple-600 mr-2"></i>
                        <?php echo $edit_data ? 'Edit Konten' : 'Tambah Konten Baru'; ?>
                    </h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Judul</label>
                            <input type="text" name="judul" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Judul Konten" value="<?php echo htmlspecialchars($edit_data['judul'] ?? ''); ?>">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Isi/Deskripsi</label>
                            <textarea name="isi" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500" rows="8" placeholder="Isi konten..."><?php echo htmlspecialchars($edit_data['isi'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Tipe</label>
                            <select name="tipe" class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="artikel" <?php echo ($edit_data['tipe'] ?? 'artikel') === 'artikel' ? 'selected' : ''; ?>>Artikel</option>
                                <option value="berita" <?php echo ($edit_data['tipe'] ?? '') === 'berita' ? 'selected' : ''; ?>>Berita</option>
                                <option value="panduan" <?php echo ($edit_data['tipe'] ?? '') === 'panduan' ? 'selected' : ''; ?>>Panduan</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="bg-purple-600 text-white px-6 py-3 md:py-2 rounded-lg hover:bg-purple-700 transition font-semibold flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <?php echo $edit_data ? 'Update' : 'Tambah'; ?>
                            </button>
                            <?php if ($edit_data): ?>
                                <a href="manage_konten.php" class="inline-block bg-gray-400 text-white px-6 py-3 md:py-2 rounded-lg hover:bg-gray-500 transition font-semibold flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-purple-600 text-white">
                                <tr>
                                    <th class="px-4 py-3 text-left">ID</th>
                                    <th class="px-4 py-3 text-left">Judul</th>
                                    <th class="px-4 py-3 text-left">Isi (Preview)</th>
                                    <th class="px-4 py-3 text-left">Tipe</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                <?php if (count($konten_list) > 0): ?>
                                    <?php foreach ($konten_list as $item): ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-4 py-3 font-semibold text-gray-900"><?php echo $item['id']; ?></td>
                                            <td class="px-4 py-3 font-semibold"><?php echo htmlspecialchars($item['judul']); ?></td>
                                            <td class="px-4 py-3">
                                                <div class="max-w-xs truncate text-gray-600 text-sm">
                                                    <?php echo htmlspecialchars(substr($item['isi'], 0, 100)); ?>...
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                                    <i class="fas fa-tag"></i>
                                                    <?php echo ucfirst($item['tipe']); ?>
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="manage_konten.php?edit=<?php echo $item['id']; ?>" class="text-blue-600 hover:text-blue-800 mr-3 inline-block">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="manage_konten.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-800 inline-block">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-2xl mb-2"></i>
                                            <p>Belum ada data konten</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="md:hidden divide-y">
                        <?php if (count($konten_list) > 0): ?>
                            <?php foreach ($konten_list as $item): ?>
                                <div class="p-4 space-y-3 border-b last:border-b-0 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start gap-2">
                                        <div class="flex-1">
                                            <p class="text-xs text-gray-500 font-semibold">ID #{<?php echo $item['id']; ?>}</p>
                                            <p class="font-bold text-gray-900"><?php echo htmlspecialchars($item['judul']); ?></p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 flex-shrink-0">
                                            <i class="fas fa-tag"></i>
                                            <?php echo ucfirst($item['tipe']); ?>
                                        </span>
                                    </div>

                                    <div class="bg-gray-50 p-3 rounded text-sm text-gray-700">
                                        <p class="text-xs text-gray-500 font-semibold mb-1">Isi Preview:</p>
                                        <p class="line-clamp-2"><?php echo htmlspecialchars(substr($item['isi'], 0, 150)); ?>...</p>
                                    </div>

                                    <div class="flex gap-2 pt-2 border-t">
                                        <a href="manage_konten.php?edit=<?php echo $item['id']; ?>" class="flex-1 bg-blue-600 text-white py-2 rounded text-center text-sm font-semibold hover:bg-blue-700 transition flex items-center justify-center gap-2">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <a href="manage_konten.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="flex-1 bg-red-600 text-white py-2 rounded text-center text-sm font-semibold hover:bg-red-700 transition flex items-center justify-center gap-2">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-3 block"></i>
                                <p>Belum ada data konten</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
