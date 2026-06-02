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
    $conn->query("DELETE FROM tb_booking WHERE id = $id");
    header('Location: manage_booking.php');
    exit();
}

// Handle INSERT
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $lapangan_id = intval($_POST['lapangan_id'] ?? 0);
        $nama_pemesan = $conn->real_escape_string($_POST['nama_pemesan'] ?? '');
        $tanggal = $_POST['tanggal'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        
        if ($lapangan_id && $nama_pemesan && $tanggal && $jam_mulai && $jam_selesai) {
            $conn->query("INSERT INTO tb_booking (lapangan_id, nama_pemesan, tanggal, jam_mulai, jam_selesai, status) 
                         VALUES ($lapangan_id, '$nama_pemesan', '$tanggal', '$jam_mulai', '$jam_selesai', '$status')");
            $message = '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Booking berhasil ditambahkan!</div>';
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = intval($_POST['id']);
        $lapangan_id = intval($_POST['lapangan_id'] ?? 0);
        $nama_pemesan = $conn->real_escape_string($_POST['nama_pemesan'] ?? '');
        $tanggal = $_POST['tanggal'] ?? '';
        $jam_mulai = $_POST['jam_mulai'] ?? '';
        $jam_selesai = $_POST['jam_selesai'] ?? '';
        $status = $_POST['status'] ?? 'pending';
        
        if ($lapangan_id && $nama_pemesan && $tanggal && $jam_mulai && $jam_selesai) {
            $conn->query("UPDATE tb_booking SET lapangan_id=$lapangan_id, nama_pemesan='$nama_pemesan', tanggal='$tanggal', 
                         jam_mulai='$jam_mulai', jam_selesai='$jam_selesai', status='$status' WHERE id=$id");
            $message = '<div class="bg-green-100 text-green-700 p-3 rounded mb-4">Booking berhasil diperbarui!</div>';
        }
    }
}

// Get all booking
$result = $conn->query('SELECT b.id, b.lapangan_id, b.nama_pemesan, b.tanggal, b.jam_mulai, b.jam_selesai, b.status, l.nama as lapangan_nama FROM tb_booking b LEFT JOIN tb_lapangan l ON b.lapangan_id = l.id');
$booking_list = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $booking_list[] = $row;
    }
}

// Get lapangan list for dropdown
$lapangan_result = $conn->query('SELECT * FROM tb_lapangan');
$lapangan_list = [];
if ($lapangan_result) {
    while ($row = $lapangan_result->fetch_assoc()) {
        $lapangan_list[] = $row;
    }
}

$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT b.id, b.lapangan_id, b.nama_pemesan, b.tanggal, b.jam_mulai, b.jam_selesai, b.status FROM tb_booking b WHERE b.id = $id");
    $edit_data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Booking</title>
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
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-4xl font-bold text-slate-900">Kelola Booking</h1>
                        <p class="text-gray-600 text-sm">Manage pesanan booking lapangan futsal</p>
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
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                        <?php echo $edit_data ? 'Edit Booking' : 'Tambah Booking Baru'; ?>
                    </h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Lapangan</label>
                                <select name="lapangan_id" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Lapangan</option>
                                    <?php foreach ($lapangan_list as $lap): ?>
                                        <option value="<?php echo $lap['id']; ?>" <?php echo ($edit_data['lapangan_id'] ?? '') == $lap['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($lap['nama']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Nama Pemesan</label>
                                <input type="text" name="nama_pemesan" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nama" value="<?php echo htmlspecialchars($edit_data['nama_pemesan'] ?? ''); ?>">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Tanggal</label>
                                <input type="date" name="tanggal" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $edit_data['tanggal'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Status</label>
                                <select name="status" class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" <?php echo ($edit_data['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo ($edit_data['status'] ?? '') === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="cancelled" <?php echo ($edit_data['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Jam Mulai</label>
                                <input type="time" name="jam_mulai" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $edit_data['jam_mulai'] ?? ''; ?>">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2 text-sm md:text-base">Jam Selesai</label>
                                <input type="time" name="jam_selesai" required class="w-full px-4 py-3 md:py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo $edit_data['jam_selesai'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 md:py-2 rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2">
                                <i class="fas fa-save"></i>
                                <?php echo $edit_data ? 'Update' : 'Tambah'; ?>
                            </button>
                            <?php if ($edit_data): ?>
                                <a href="manage_booking.php" class="inline-block bg-gray-400 text-white px-6 py-3 md:py-2 rounded-lg hover:bg-gray-500 transition font-semibold flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="w-full text-sm md:text-base">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-4 md:px-6 py-3 text-left">ID</th>
                                <th class="px-4 md:px-6 py-3 text-left">Lapangan</th>
                                <th class="px-4 md:px-6 py-3 text-left">Pemesan</th>
                                <th class="px-4 md:px-6 py-3 text-left">Tanggal</th>
                                <th class="px-4 md:px-6 py-3 text-left">Jam</th>
                                <th class="px-4 md:px-6 py-3 text-left">Status</th>
                                <th class="px-4 md:px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php if (count($booking_list) > 0): ?>
                                <?php foreach ($booking_list as $item): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 md:px-6 py-3 font-semibold text-gray-900"><?php echo $item['id']; ?></td>
                                        <td class="px-4 md:px-6 py-3"><?php echo htmlspecialchars($item['lapangan_nama'] ?? '-'); ?></td>
                                        <td class="px-4 md:px-6 py-3"><?php echo htmlspecialchars($item['nama_pemesan']); ?></td>
                                        <td class="px-4 md:px-6 py-3"><?php echo $item['tanggal']; ?></td>
                                        <td class="px-4 md:px-6 py-3 text-xs md:text-sm"><?php echo $item['jam_mulai']; ?> - <?php echo $item['jam_selesai']; ?></td>
                                        <td class="px-4 md:px-6 py-3">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold
                                                <?php 
                                                if ($item['status'] === 'confirmed') echo 'bg-green-100 text-green-700';
                                                elseif ($item['status'] === 'pending') echo 'bg-yellow-100 text-yellow-700';
                                                else echo 'bg-red-100 text-red-700';
                                                ?>">
                                                <i class="fas fa-circle text-xs"></i>
                                                <?php echo ucfirst($item['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 md:px-6 py-3 text-center">
                                            <a href="manage_booking.php?edit=<?php echo $item['id']; ?>" class="text-blue-600 hover:text-blue-800 hover:underline mr-3 inline-block py-2">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="manage_booking.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:text-red-800 hover:underline inline-block py-2">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-2xl mb-2"></i>
                                        <p>Belum ada data booking</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
