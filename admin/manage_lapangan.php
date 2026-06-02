<?php
session_start();
require '../config/koneksi.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: auth/login.php');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM tb_lapangan WHERE id = $id");
    header('Location: manage_lapangan.php');
    exit;
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $harga = (int)$_POST['harga'];
    $harga_weekend = (int)$_POST['harga_weekend'];
    $status = $conn->real_escape_string($_POST['status']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $deskripsi_lengkap = $conn->real_escape_string($_POST['deskripsi_lengkap']);
    $fasilitas = $conn->real_escape_string($_POST['fasilitas']);
    $rating = (float)$_POST['rating'];
    $lokasi = $conn->real_escape_string($_POST['lokasi']);
    $ukuran = $conn->real_escape_string($_POST['ukuran']);
    $pencahayaan = $conn->real_escape_string($_POST['pencahayaan']);
    $parkir = $conn->real_escape_string($_POST['parkir']);
    $tipe_lantai = $conn->real_escape_string($_POST['tipe_lantai']);
    $gambar = '';

    // Create uploads directory
    $upload_dir = '../uploads/lapangan/';
    $gallery_dir = '../uploads/gallery/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    if (!is_dir($gallery_dir)) mkdir($gallery_dir, 0777, true);

    // Handle main image upload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
        $filename = time() . '_' . basename($_FILES['gambar']['name']);
        $filepath = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $filepath)) {
            $gambar = 'uploads/lapangan/' . $filename;
        }
    }

    $lapangan_id = null;

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $lapangan_id = $id;
        if ($gambar) {
            $sql = "UPDATE tb_lapangan SET nama='$nama', harga=$harga, harga_weekend=$harga_weekend, status='$status', deskripsi='$deskripsi', deskripsi_lengkap='$deskripsi_lengkap', fasilitas='$fasilitas', rating=$rating, lokasi='$lokasi', ukuran='$ukuran', pencahayaan='$pencahayaan', parkir='$parkir', tipe_lantai='$tipe_lantai', gambar='$gambar' WHERE id=$id";
        } else {
            $sql = "UPDATE tb_lapangan SET nama='$nama', harga=$harga, harga_weekend=$harga_weekend, status='$status', deskripsi='$deskripsi', deskripsi_lengkap='$deskripsi_lengkap', fasilitas='$fasilitas', rating=$rating, lokasi='$lokasi', ukuran='$ukuran', pencahayaan='$pencahayaan', parkir='$parkir', tipe_lantai='$tipe_lantai' WHERE id=$id";
        }
        $conn->query($sql);
    } else {
        // Add new
        $sql = "INSERT INTO tb_lapangan (nama, harga, harga_weekend, status, deskripsi, deskripsi_lengkap, fasilitas, rating, lokasi, ukuran, pencahayaan, parkir, tipe_lantai, gambar) VALUES ('$nama', $harga, $harga_weekend, '$status', '$deskripsi', '$deskripsi_lengkap', '$fasilitas', $rating, '$lokasi', '$ukuran', '$pencahayaan', '$parkir', '$tipe_lantai', '$gambar')";
        $conn->query($sql);
        $lapangan_id = $conn->insert_id;
    }

    // Handle gallery images upload
    if (isset($_FILES['gallery_images']) && count($_FILES['gallery_images']['name']) > 0) {
        $max_order = 0;
        $result = $conn->query("SELECT MAX(urutan) as max_urutan FROM tb_lapangan_gallery WHERE lapangan_id = $lapangan_id");
        if ($result) {
            $row = $result->fetch_assoc();
            $max_order = $row['max_urutan'] ? (int)$row['max_urutan'] : 0;
        }

        for ($i = 0; $i < count($_FILES['gallery_images']['name']); $i++) {
            if ($_FILES['gallery_images']['size'][$i] > 0) {
                $filename = time() . '_gallery_' . $i . '_' . basename($_FILES['gallery_images']['name'][$i]);
                $filepath = $gallery_dir . $filename;
                if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $filepath)) {
                    $foto = 'uploads/gallery/' . $filename;
                    $urutan = $max_order + $i + 1;
                    $conn->query("INSERT INTO tb_lapangan_gallery (lapangan_id, foto, urutan) VALUES ($lapangan_id, '$foto', $urutan)");
                }
            }
        }
    }

    header('Location: manage_lapangan.php');
    exit;
}

// Get all lapangan
$result = $conn->query("SELECT * FROM tb_lapangan ORDER BY id DESC");
$lapangan_list = [];
while ($row = $result->fetch_assoc()) {
    $lapangan_list[] = $row;
}

// Get lapangan for edit
$edit_lapangan = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM tb_lapangan WHERE id = $id");
    $edit_lapangan = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lapangan - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'sidebar.php'; ?>

    <main class="min-h-screen">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-4 md:p-8">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-4xl font-bold text-slate-900">Kelola Lapangan</h1>
                    <p class="text-gray-600 mt-1">Tambah, edit, atau hapus lapangan futsal</p>
                </div>
                <button onclick="openModal()" class="w-full md:w-auto bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i> Tambah Lapangan
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                <!-- Lapangan Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
                    <?php foreach ($lapangan_list as $lapangan): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden flex flex-col">
                            <!-- Image -->
                            <div class="relative h-40 md:h-48 bg-gray-200 overflow-hidden">
                                <?php if ($lapangan['gambar']): ?>
                                    <img src="../<?php echo htmlspecialchars($lapangan['gambar']); ?>" alt="<?php echo htmlspecialchars($lapangan['nama']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3">
                                    <?php 
                                        if ($lapangan['status'] === 'tersedia') {
                                            echo '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                                                    <i class="fas fa-check-circle"></i> Tersedia
                                                </span>';
                                        } else {
                                            echo '<span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1">
                                                    <i class="fas fa-exclamation-circle"></i> Maintenance
                                                </span>';
                                        }
                                    ?>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4 md:p-5 flex flex-col flex-1">
                                <!-- Title & Rating -->
                                <h3 class="text-lg md:text-xl font-bold text-slate-900 mb-2"><?php echo htmlspecialchars($lapangan['nama']); ?></h3>
                                
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                    <i class="fas fa-star text-yellow-400"></i>
                                    <span><?php echo $lapangan['rating']; ?></span>
                                    <span class="text-gray-400">•</span>
                                    <i class="fas fa-map-marker-alt text-emerald-600"></i>
                                    <span><?php echo htmlspecialchars($lapangan['lokasi']); ?></span>
                                </div>

                                <!-- Price -->
                                <div class="mb-3 pb-3 border-b border-gray-200">
                                    <p class="text-gray-600 text-xs mb-1">Harga per Jam</p>
                                    <p class="text-2xl font-bold text-emerald-600">Rp <?php echo number_format($lapangan['harga'], 0, ',', '.'); ?></p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2 mt-auto">
                                    <button onclick="editModal(<?php echo $lapangan['id']; ?>)" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded font-semibold hover:bg-blue-700 text-sm transition">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="manage_gallery.php?lapangan_id=<?php echo $lapangan['id']; ?>" class="flex-1 bg-purple-600 text-white px-3 py-2 rounded font-semibold hover:bg-purple-700 text-sm transition text-center">
                                        <i class="fas fa-images"></i> Gallery
                                    </a>
                                    <button onclick="if(confirm('Hapus lapangan ini?')) window.location='manage_lapangan.php?delete=<?php echo $lapangan['id']; ?>'" class="flex-1 bg-red-600 text-white px-3 py-2 rounded font-semibold hover:bg-red-700 text-sm transition">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Form -->
    <div id="formModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full my-8">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 id="modalTitle" class="text-2xl font-bold">Tambah Lapangan Baru</h2>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form id="lapanganForm" method="POST" enctype="multipart/form-data" class="space-y-4 max-h-[70vh] overflow-y-auto">
                    <input type="hidden" id="id" name="id" value="">

                    <!-- Row 1 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Lapangan</label>
                            <input type="text" id="nama" name="nama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Lokasi</label>
                            <input type="text" id="lokasi" name="lokasi" value="Jakarta" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                    </div>

                    <!-- Row 2 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Harga per Jam - Weekday (Rp)</label>
                            <input type="number" id="harga" name="harga" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Harga per Jam - Weekend (Rp)</label>
                            <input type="number" id="harga_weekend" name="harga_weekend" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                    </div>

                    <!-- Row 3 -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Status</label>
                            <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                                <option value="tersedia">Tersedia</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Rating (0-5)</label>
                            <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" value="4.5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi Singkat</label>
                        <textarea id="deskripsi" name="deskripsi" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi Lengkap</label>
                        <textarea id="deskripsi_lengkap" name="deskripsi_lengkap" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"></textarea>
                    </div>

                    <!-- Specs -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Ukuran</label>
                            <input type="text" id="ukuran" name="ukuran" value="40m x 20m" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Pencahayaan</label>
                            <input type="text" id="pencahayaan" name="pencahayaan" value="Standar" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Parkir</label>
                            <input type="text" id="parkir" name="parkir" value="Tersedia" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tipe Lantai</label>
                            <input type="text" id="tipe_lantai" name="tipe_lantai" value="Rumput Sintetis" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Fasilitas (pisahkan dengan koma)</label>
                        <textarea id="fasilitas" name="fasilitas" rows="2" placeholder="AC, Toilet, WiFi, Parkir" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600"></textarea>
                    </div>

                    <!-- Files -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Gambar Utama</label>
                        <input type="file" id="gambar" name="gambar" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600">
                        <div id="mainImagePreview" class="mt-3 rounded-lg overflow-hidden border border-dashed border-gray-300 bg-gray-50 hidden">
                            <img id="mainImagePreviewImg" src="" alt="Preview" class="w-full h-48 object-cover">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Gallery Images</label>
                        <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple class="w-full px-4 py-2 border border-2 border-dashed border-emerald-300 rounded-lg">
                        <div id="imagePreview" class="mt-3 grid grid-cols-4 gap-2">
                            <p class="col-span-4 text-gray-400 text-sm text-center py-4"><i class="fas fa-images"></i> Preview gambar</p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3 pt-4 sticky bottom-0 bg-white">
                        <button type="submit" class="flex-1 bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                        <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-400 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('lapanganForm').reset();
            document.getElementById('id').value = '';
            document.getElementById('modalTitle').innerText = 'Tambah Lapangan Baru';
            document.getElementById('imagePreview').innerHTML = '<p class="col-span-4 text-gray-400 text-sm text-center py-4"><i class="fas fa-images"></i> Preview gambar</p>';
            document.getElementById('mainImagePreview').classList.add('hidden');
            document.getElementById('formModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('formModal').classList.add('hidden');
        }

        function editModal(id) {
            fetch(`get_lapangan.php?id=${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('id').value = data.id;
                    document.getElementById('nama').value = data.nama;
                    document.getElementById('harga').value = data.harga;
                    document.getElementById('harga_weekend').value = data.harga_weekend || data.harga;
                    document.getElementById('status').value = data.status;
                    document.getElementById('deskripsi').value = data.deskripsi || '';
                    document.getElementById('deskripsi_lengkap').value = data.deskripsi_lengkap || '';
                    document.getElementById('fasilitas').value = data.fasilitas || '';
                    document.getElementById('rating').value = data.rating || 4.5;
                    document.getElementById('lokasi').value = data.lokasi || 'Jakarta';
                    document.getElementById('ukuran').value = data.ukuran || '40m x 20m';
                    document.getElementById('pencahayaan').value = data.pencahayaan || 'Standar';
                    document.getElementById('parkir').value = data.parkir || 'Tersedia';
                    document.getElementById('tipe_lantai').value = data.tipe_lantai || 'Rumput Sintetis';
                    document.getElementById('modalTitle').innerText = 'Edit Lapangan';
                    document.getElementById('imagePreview').innerHTML = '<p class="col-span-4 text-gray-400 text-sm text-center py-4"><i class="fas fa-images"></i> Preview gambar</p>';

                    if (data.gambar) {
                        document.getElementById('mainImagePreviewImg').src = '../' + data.gambar;
                        document.getElementById('mainImagePreview').classList.remove('hidden');
                    } else {
                        document.getElementById('mainImagePreview').classList.add('hidden');
                    }

                    document.getElementById('formModal').classList.remove('hidden');
                })
                .catch(err => alert('Error: ' + err));
        }

        document.getElementById('gambar')?.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('mainImagePreviewImg').src = event.target.result;
                    document.getElementById('mainImagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('gallery_images')?.addEventListener('change', function() {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            const files = Array.from(this.files);
            if (files.length === 0) {
                preview.innerHTML = '<p class="col-span-4 text-gray-400 text-sm text-center py-4"><i class="fas fa-images"></i> Preview gambar</p>';
                return;
            }

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const container = document.createElement('div');
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.className = 'w-full h-24 object-cover rounded border-2 border-emerald-600';
                    const label = document.createElement('div');
                    label.className = 'absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white text-xs p-1 text-center rounded-b';
                    label.textContent = `${index + 1}/${files.length}`;
                    container.appendChild(img);
                    container.appendChild(label);
                    preview.appendChild(container);
                };
                reader.readAsDataURL(file);
            });
        });

        document.getElementById('formModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
</body>
</html>
