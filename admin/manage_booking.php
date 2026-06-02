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
</head>
<body class="bg-gray-50 flex">
    <?php include 'sidebar.php'; ?>

    <main class="ml-64 flex-1 p-8">
        <header class="mb-6">
            <h1 class="text-4xl font-bold text-emerald-600">Kelola Booking</h1>
            <div class="mt-2 h-1 w-24 bg-emerald-600"></div>
        </header>

        <?php echo $message; ?>

        <!-- Form Add/Edit -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-semibold mb-4"><?php echo $edit_data ? 'Edit Booking' : 'Tambah Booking Baru'; ?></h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Lapangan</label>
                        <select name="lapangan_id" required class="w-full px-3 py-2 border rounded">
                            <option value="">Pilih Lapangan</option>
                            <?php foreach ($lapangan_list as $lap): ?>
                                <option value="<?php echo $lap['id']; ?>" <?php echo ($edit_data['lapangan_id'] ?? '') == $lap['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($lap['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nama Pemesan</label>
                        <input type="text" name="nama_pemesan" required class="w-full px-3 py-2 border rounded" placeholder="Nama" value="<?php echo $edit_data['nama_pemesan'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Tanggal</label>
                        <input type="date" name="tanggal" required class="w-full px-3 py-2 border rounded" value="<?php echo $edit_data['tanggal'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border rounded">
                            <option value="pending" <?php echo ($edit_data['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo ($edit_data['status'] ?? '') === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="cancelled" <?php echo ($edit_data['status'] ?? '') === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Jam Mulai</label>
                        <input type="time" name="jam_mulai" required class="w-full px-3 py-2 border rounded" value="<?php echo $edit_data['jam_mulai'] ?? ''; ?>">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Jam Selesai</label>
                        <input type="time" name="jam_selesai" required class="w-full px-3 py-2 border rounded" value="<?php echo $edit_data['jam_selesai'] ?? ''; ?>">
                    </div>
                </div>
                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700"><?php echo $edit_data ? 'Update' : 'Tambah'; ?></button>
                <?php if ($edit_data): ?>
                    <a href="manage_booking.php" class="inline-block ml-2 bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-emerald-600 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Lapangan</th>
                        <th class="px-6 py-3 text-left">Pemesan</th>
                        <th class="px-6 py-3 text-left">Tanggal</th>
                        <th class="px-6 py-3 text-left">Jam</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($booking_list) > 0): ?>
                        <?php foreach ($booking_list as $item): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-3"><?php echo $item['id']; ?></td>
                                <td class="px-6 py-3"><?php echo htmlspecialchars($item['lapangan_nama'] ?? '-'); ?></td>
                                <td class="px-6 py-3"><?php echo htmlspecialchars($item['nama_pemesan']); ?></td>
                                <td class="px-6 py-3"><?php echo $item['tanggal']; ?></td>
                                <td class="px-6 py-3"><?php echo $item['jam_mulai']; ?> - <?php echo $item['jam_selesai']; ?></td>
                                <td class="px-6 py-3">
                                    <span class="px-3 py-1 rounded text-sm 
                                        <?php 
                                        if ($item['status'] === 'confirmed') echo 'bg-green-100 text-green-700';
                                        elseif ($item['status'] === 'pending') echo 'bg-yellow-100 text-yellow-700';
                                        else echo 'bg-red-100 text-red-700';
                                        ?>">
                                        <?php echo ucfirst($item['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="manage_booking.php?edit=<?php echo $item['id']; ?>" class="text-blue-600 hover:underline mr-3">Edit</a>
                                    <a href="manage_booking.php?action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:underline">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-3 text-center text-gray-500">Belum ada data booking</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
