<?php
session_start();
require '../config/koneksi.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit;
}

$lapangan_id = isset($_GET['lapangan_id']) ? (int)$_GET['lapangan_id'] : 0;

if ($lapangan_id === 0) {
    header('Location: manage_lapangan.php');
    exit;
}

// Verify lapangan exists
$result = $conn->query("SELECT id FROM tb_lapangan WHERE id = $lapangan_id");
if ($result->num_rows === 0) {
    header('Location: manage_lapangan.php');
    exit;
}

// Handle Delete Gallery
if (isset($_GET['delete'])) {
    $gallery_id = (int)$_GET['delete'];
    $result = $conn->query("SELECT foto FROM tb_lapangan_gallery WHERE id = $gallery_id AND lapangan_id = $lapangan_id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        @unlink('../' . $row['foto']);
        $conn->query("DELETE FROM tb_lapangan_gallery WHERE id = $gallery_id");
    }
    header("Location: manage_gallery.php?lapangan_id=$lapangan_id");
    exit;
}

// Handle Upload Multiple Photos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery'])) {
    $upload_dir = '../uploads/gallery/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $max_order = 0;
    $result = $conn->query("SELECT MAX(urutan) as max_urutan FROM tb_lapangan_gallery WHERE lapangan_id = $lapangan_id");
    if ($result) {
        $row = $result->fetch_assoc();
        $max_order = $row['max_urutan'] ? (int)$row['max_urutan'] : 0;
    }

    $files = $_FILES['gallery'];
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['size'][$i] > 0) {
            $filename = time() . '_gallery_' . $i . '_' . basename($files['name'][$i]);
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                $foto = 'uploads/gallery/' . $filename;
                $urutan = $max_order + $i + 1;
                $conn->query("INSERT INTO tb_lapangan_gallery (lapangan_id, foto, urutan) VALUES ($lapangan_id, '$foto', $urutan)");
            }
        }
    }
    header("Location: manage_gallery.php?lapangan_id=$lapangan_id");
    exit;
}

// Get lapangan info
$result = $conn->query("SELECT nama FROM tb_lapangan WHERE id = $lapangan_id");
$lapangan = $result->fetch_assoc();

// Get gallery
$result = $conn->query("SELECT * FROM tb_lapangan_gallery WHERE lapangan_id = $lapangan_id ORDER BY urutan ASC");
$gallery = [];
while ($row = $result->fetch_assoc()) {
    $gallery[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - <?php echo htmlspecialchars($lapangan['nama']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex">
        <?php include 'sidebar.php'; ?>

        <div class="flex-1 ml-64 p-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <a href="manage_lapangan.php" class="text-emerald-600 hover:text-emerald-700 mb-4 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali ke Lapangan
                    </a>
                    <h1 class="text-4xl font-bold text-slate-900">Gallery - <?php echo htmlspecialchars($lapangan['nama']); ?></h1>
                </div>

                <!-- Upload Section -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Upload Foto</h2>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Pilih Foto (Multiple)</label>
                            <input type="file" name="gallery[]" multiple accept="image/*" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                            <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG (Max 2MB per file)</p>
                        </div>
                        <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-700">
                            <i class="fas fa-upload mr-2"></i>Upload Foto
                        </button>
                    </form>
                </div>

                <!-- Gallery Grid -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">Foto Lapangan (<?php echo count($gallery); ?> foto)</h2>
                    
                    <?php if (count($gallery) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php foreach ($gallery as $item): ?>
                                <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                                        <img src="<?php echo htmlspecialchars('../' . $item['foto']); ?>" alt="Gallery" class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-4">
                                        <div class="text-sm text-gray-500 mb-3">
                                            <i class="fas fa-image mr-1"></i>Urutan: <?php echo $item['urutan']; ?>
                                        </div>
                                        <button onclick="if(confirm('Hapus foto ini?')) window.location='manage_gallery.php?lapangan_id=<?php echo $lapangan_id; ?>&delete=<?php echo $item['id']; ?>'" class="w-full bg-red-600 text-white px-4 py-2 rounded font-semibold hover:bg-red-700">
                                            <i class="fas fa-trash mr-2"></i>Hapus
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <i class="fas fa-image text-5xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">Belum ada foto untuk lapangan ini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
