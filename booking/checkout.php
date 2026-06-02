<?php
/**
 * Booking Checkout Page
 * 
 * GET /booking/checkout.php?lapangan_id=1&tanggal=2026-06-15&jam_mulai=18:00&jam_selesai=19:00
 */

require '../config/koneksi.php';
require '../config/midtrans.php';

// Get parameters
$lapangan_id = isset($_GET['lapangan_id']) ? (int)$_GET['lapangan_id'] : null;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : null;
$jam_mulai = isset($_GET['jam_mulai']) ? $_GET['jam_mulai'] : null;
$jam_selesai = isset($_GET['jam_selesai']) ? $_GET['jam_selesai'] : null;

// Validate parameters - redirect back if missing
if(!$lapangan_id || !$tanggal || !$jam_mulai || !$jam_selesai) {
    echo "<script>alert('Parameter tidak lengkap. Silakan pilih lapangan dan waktu booking terlebih dahulu.'); window.location.href='../index.php';</script>";
    exit;
}

// Get lapangan details
$result = $conn->query("
    SELECT * FROM tb_lapangan WHERE id = $lapangan_id
");

if(!$result || $result->num_rows === 0) {
    header('Location: ../index.php');
    exit;
}

$lapangan = $result->fetch_assoc();

// Calculate price
try {
    $date_obj = new DateTime($tanggal);
    $day_of_week = (int)$date_obj->format('N');
    $is_weekend = ($day_of_week == 6 || $day_of_week == 7);
    
    $start = new DateTime("2000-01-01 $jam_mulai");
    $end = new DateTime("2000-01-01 $jam_selesai");
    $interval = $start->diff($end);
    $hours = $interval->h + ($interval->i / 60);
} catch (Exception $e) {
    header('Location: ../index.php');
    exit;
}

$hourly_price = $is_weekend ? $lapangan['harga_weekend'] : $lapangan['harga'];
$total_harga = (int)round($hourly_price * $hours);
$tanggal_display = date('d M Y', strtotime($tanggal));
$is_weekend_text = $is_weekend ? 'Weekend' : 'Weekday';

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Booking Lapangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo MIDTRANS_ENVIRONMENT === 'production'
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js'; ?>"
        data-client-key="<?php echo MIDTRANS_CLIENT_KEY; ?>"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4">
            <div class="mb-6">
                <a href="../index.php" class="text-emerald-600 hover:text-emerald-700 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <!-- Checkout Form -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h1 class="text-2xl font-bold text-slate-900 mb-6">Checkout Booking</h1>

                        <form id="checkout-form" class="space-y-4">
                            <input type="hidden" id="lapangan_id" value="<?php echo $lapangan_id; ?>">
                            <input type="hidden" id="tanggal" value="<?php echo $tanggal; ?>">
                            <input type="hidden" id="jam_mulai" value="<?php echo $jam_mulai; ?>">
                            <input type="hidden" id="jam_selesai" value="<?php echo $jam_selesai; ?>">

                            <!-- Nama Pemesan -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    <i class="fas fa-user mr-2 text-emerald-600"></i> Nama Pemesan
                                </label>
                                <input 
                                    type="text" 
                                    id="nama_pemesan" 
                                    name="nama_pemesan" 
                                    required
                                    placeholder="Masukkan nama lengkap Anda"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                >
                                <small class="text-gray-500">Nama yang akan muncul di booking</small>
                            </div>

                            <!-- No. HP -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    <i class="fas fa-phone mr-2 text-emerald-600"></i> Nomor HP
                                </label>
                                <input 
                                    type="tel" 
                                    id="no_hp" 
                                    name="no_hp" 
                                    required
                                    placeholder="08123456789"
                                    pattern="^[0-9+\-\s]+$"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                >
                                <small class="text-gray-500">Nomor WhatsApp/Telepon yang aktif</small>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    <i class="fas fa-envelope mr-2 text-emerald-600"></i> Email
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required
                                    placeholder="nama@example.com"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                >
                                <small class="text-gray-500">Email untuk konfirmasi booking</small>
                            </div>

                            <!-- Catatan Khusus -->
                            <div>
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    <i class="fas fa-sticky-note mr-2 text-emerald-600"></i> Catatan (Opsional)
                                </label>
                                <textarea 
                                    id="notes" 
                                    name="notes" 
                                    rows="3"
                                    placeholder="Contoh: Butuh kursi tambahan, peralatan khusus, dll"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                ></textarea>
                            </div>

                            <!-- T&C -->
                            <div class="flex items-start">
                                <input 
                                    type="checkbox" 
                                    id="agree_tc" 
                                    name="agree_tc" 
                                    required
                                    class="mt-1 w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500"
                                >
                                <label for="agree_tc" class="ml-2 text-sm text-gray-600">
                                    Saya setuju dengan <a href="#" class="text-emerald-600 hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-emerald-600 hover:underline">Kebijakan Privasi</a>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                id="submit-btn"
                                class="w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold hover:bg-emerald-700 transition-all flex items-center justify-center"
                            >
                                <i class="fas fa-lock mr-2"></i> Lanjut ke Pembayaran
                            </button>
                        </form>

                        <!-- Loading Spinner -->
                        <div id="loading" class="hidden text-center py-4">
                            <div class="inline-block animate-spin">
                                <i class="fas fa-spinner text-emerald-600 text-2xl"></i>
                            </div>
                            <p class="mt-2 text-gray-600">Memproses booking Anda...</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="font-bold text-slate-900 mb-4">Ringkasan Booking</h2>

                        <!-- Lapangan -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Lapangan</p>
                            <p class="font-bold text-slate-900"><?php echo htmlspecialchars($lapangan['nama']); ?></p>
                            <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($lapangan['lokasi']); ?></p>
                        </div>

                        <!-- Tanggal & Jam -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Tanggal</p>
                            <p class="font-bold text-slate-900"><?php echo $tanggal_display; ?></p>
                            <p class="text-sm text-gray-600 mt-1"><?php echo $jam_mulai . ' - ' . $jam_selesai; ?></p>
                            <p class="text-xs text-emerald-600 mt-1"><strong><?php echo $is_weekend_text; ?></strong></p>
                        </div>

                        <!-- Durasi -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Durasi</p>
                            <p class="font-bold text-slate-900"><?php echo number_format($hours, 1); ?> jam</p>
                        </div>

                        <!-- Harga Breakdown -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Harga per jam</span>
                                <span class="font-semibold">Rp <?php echo number_format($hourly_price, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Jumlah jam</span>
                                <span class="font-semibold"><?php echo number_format($hours, 1); ?> x</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Biaya Admin</span>
                                <span class="font-semibold">Rp 0</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-emerald-50 rounded-lg p-4">
                            <div class="flex justify-between items-baseline">
                                <span class="text-gray-700">Total Pembayaran</span>
                                <span class="text-3xl font-bold text-emerald-600">Rp</span>
                            </div>
                            <p class="text-2xl font-bold text-emerald-600 text-right">
                                <?php echo number_format($total_harga, 0, ',', '.'); ?>
                            </p>
                        </div>

                        <!-- Fasilitas -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-xs text-gray-600 uppercase tracking-wide mb-3">Fasilitas</p>
                            <div class="space-y-2 text-sm">
                                <?php
                                $fasilitas = explode(',', $lapangan['fasilitas']);
                                foreach(array_slice($fasilitas, 0, 4) as $f) {
                                    echo '<div class="flex items-start"><i class="fas fa-check text-emerald-600 mr-2 mt-0.5 flex-shrink-0"></i><span class="text-gray-600">' . htmlspecialchars(trim($f)) . '</span></div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('checkout-form');
        const loadingDiv = document.getElementById('loading');
        const submitBtn = document.getElementById('submit-btn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Show loading
            loadingDiv.classList.remove('hidden');
            submitBtn.disabled = true;

            // Collect form data
            const formData = {
                lapangan_id: parseInt(document.getElementById('lapangan_id').value),
                tanggal: document.getElementById('tanggal').value,
                jam_mulai: document.getElementById('jam_mulai').value,
                jam_selesai: document.getElementById('jam_selesai').value,
                nama_pemesan: document.getElementById('nama_pemesan').value,
                no_hp: document.getElementById('no_hp').value,
                email: document.getElementById('email').value
            };

            try {
                // Call backend to create transaction
                const response = await fetch('../payment/create_transaction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // Check if demo mode
                    if (result.demo_mode === true) {
                        // Demo mode: redirect directly to success
                        alert('✅ Booking berhasil! (Demo Mode - Payment Gateway dilewati untuk testing)');
                        window.location.href = '../payment/success.php?order_id=' + result.order_id;
                        return;
                    }
                    
                    // Production mode: Show Midtrans Snap popup
                    snap.pay(result.snap_token, {
                        onSuccess: function(resultMidtrans) {
                            // Redirect to success page
                            window.location.href = '../payment/success.php?order_id=' + result.order_id;
                        },
                        onPending: function(resultMidtrans) {
                            alert('Pembayaran sedang diproses');
                            window.location.href = '../payment/status.php?order_id=' + result.order_id;
                        },
                        onError: function(resultMidtrans) {
                            alert('Pembayaran gagal: ' + (resultMidtrans.status_message || 'Unknown error'));
                            window.location.href = '../payment/failed.php?order_id=' + result.order_id;
                        },
                        onClose: function() {
                            alert('Anda menutup pembayaran. Silakan coba lagi.');
                            loadingDiv.classList.add('hidden');
                            submitBtn.disabled = false;
                        }
                    });
                } else {
                    alert('Error: ' + (result.message || 'Failed to create transaction'));
                    loadingDiv.classList.add('hidden');
                    submitBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                loadingDiv.classList.add('hidden');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>

