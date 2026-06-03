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
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        
        .card-shadow {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .badge-weekend {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .badge-weekday {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
        }

        .price-box {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.2);
        }

        .facility-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            transition: all 0.2s ease;
        }

        .facility-item:hover {
            padding-left: 5px;
        }
    </style>
</head>
<body class="py-8 md:py-12">
    <div class="min-h-screen">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Back Button -->
            <div class="mb-8">
                <a href="../index.php" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-semibold transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6 lg:gap-8">
                <!-- Checkout Form -->
                <div class="md:col-span-2">
                    <div class="card-shadow bg-white rounded-2xl p-8">
                        <div class="mb-8">
                            <h1 class="text-4xl font-bold text-slate-900">Checkout Booking</h1>
                            <p class="text-gray-600 mt-2">Lengkapi data Anda untuk menyelesaikan pemesanan</p>
                        </div>

                        <form id="checkout-form" class="space-y-6">
                            <input type="hidden" id="lapangan_id" value="<?php echo $lapangan_id; ?>">
                            <input type="hidden" id="tanggal" value="<?php echo $tanggal; ?>">
                            <input type="hidden" id="jam_mulai" value="<?php echo $jam_mulai; ?>">
                            <input type="hidden" id="jam_selesai" value="<?php echo $jam_selesai; ?>">

                            <!-- Nama Pemesan -->
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-900 mb-3">
                                    <span class="inline-flex items-center gap-2 text-emerald-600">
                                        <i class="fas fa-user text-lg"></i> Nama Pemesan
                                    </span>
                                </label>
                                <input 
                                    type="text" 
                                    id="nama_pemesan" 
                                    name="nama_pemesan" 
                                    required
                                    placeholder="Masukkan nama lengkap Anda"
                                    class="input-field w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition"
                                >
                                <small class="text-gray-500 mt-1 block">Nama yang akan muncul di booking Anda</small>
                            </div>

                            <!-- No. HP -->
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-900 mb-3">
                                    <span class="inline-flex items-center gap-2 text-emerald-600">
                                        <i class="fas fa-phone text-lg"></i> Nomor HP
                                    </span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="no_hp" 
                                    name="no_hp" 
                                    required
                                    placeholder="08123456789"
                                    pattern="^[0-9+\-\s]+$"
                                    class="input-field w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition"
                                >
                                <small class="text-gray-500 mt-1 block">Nomor WhatsApp/Telepon yang aktif untuk konfirmasi</small>
                            </div>

                            <!-- Email -->
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-900 mb-3">
                                    <span class="inline-flex items-center gap-2 text-emerald-600">
                                        <i class="fas fa-envelope text-lg"></i> Email
                                    </span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required
                                    placeholder="nama@example.com"
                                    class="input-field w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition"
                                >
                                <small class="text-gray-500 mt-1 block">Email untuk konfirmasi dan invoice booking</small>
                            </div>

                            <!-- Catatan Khusus -->
                            <div class="group">
                                <label class="block text-sm font-bold text-slate-900 mb-3">
                                    <span class="inline-flex items-center gap-2 text-emerald-600">
                                        <i class="fas fa-sticky-note text-lg"></i> Catatan (Opsional)
                                    </span>
                                </label>
                                <textarea 
                                    id="notes" 
                                    name="notes" 
                                    rows="4"
                                    placeholder="Contoh: Butuh kursi tambahan, peralatan khusus, kebutuhan khusus lainnya, dll"
                                    class="input-field w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition resize-none"
                                ></textarea>
                            </div>

                            <!-- T&C -->
                            <div class="bg-emerald-50 border-l-4 border-emerald-500 rounded-xl p-4 my-6">
                                <div class="flex items-start gap-3">
                                    <input 
                                        type="checkbox" 
                                        id="agree_tc" 
                                        name="agree_tc" 
                                        required
                                        class="mt-1 w-5 h-5 text-emerald-600 rounded cursor-pointer"
                                    >
                                    <label for="agree_tc" class="text-sm text-gray-700 cursor-pointer">
                                        Saya setuju dengan <a href="#" class="text-emerald-600 font-semibold hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-emerald-600 font-semibold hover:underline">Kebijakan Privasi</a> serta memahami bahwa pembayaran bersifat non-refundable
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button 
                                type="submit" 
                                id="submit-btn"
                                class="w-full price-box text-white py-4 rounded-xl font-bold hover:shadow-lg transform hover:scale-105 transition-all flex items-center justify-center gap-2 text-lg"
                            >
                                <i class="fas fa-lock"></i> Lanjut ke Pembayaran
                            </button>
                        </form>

                        <!-- Loading Spinner -->
                        <div id="loading" class="hidden text-center py-8">
                            <div class="inline-block animate-spin mb-4">
                                <i class="fas fa-spinner text-emerald-600 text-4xl"></i>
                            </div>
                            <p class="mt-3 text-gray-600 font-semibold">Memproses booking Anda...</p>
                            <p class="text-sm text-gray-500 mt-2">Jangan tutup halaman ini</p>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="md:col-span-1">
                    <div class="card-shadow bg-white rounded-2xl p-8 sticky top-8">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">Ringkasan Booking</h2>

                        <!-- Lapangan -->
                        <div class="mb-6 pb-6 border-b-2 border-gray-100">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">📍 Lapangan</p>
                            <p class="text-xl font-bold text-slate-900"><?php echo htmlspecialchars($lapangan['nama']); ?></p>
                            <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars($lapangan['lokasi']); ?></p>
                        </div>

                        <!-- Tanggal & Jam -->
                        <div class="mb-6 pb-6 border-b-2 border-gray-100">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">📅 Tanggal & Waktu</p>
                            <p class="text-lg font-bold text-slate-900"><?php echo $tanggal_display; ?></p>
                            <p class="text-emerald-600 font-semibold mt-2 flex items-center gap-2">
                                <i class="fas fa-clock"></i> <?php echo $jam_mulai . ' - ' . $jam_selesai; ?>
                            </p>
                            <div class="mt-3">
                                <span class="<?php echo $is_weekend ? 'badge-weekend' : 'badge-weekday'; ?> text-white text-xs font-bold px-3 py-1 rounded-full inline-block">
                                    <?php echo $is_weekend_text; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Durasi -->
                        <div class="mb-6 pb-6 border-b-2 border-gray-100">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">⏱️ Durasi</p>
                            <p class="text-2xl font-bold text-emerald-600"><?php echo number_format($hours, 1); ?> Jam</p>
                        </div>

                        <!-- Harga Breakdown -->
                        <div class="mb-6 pb-6 border-b-2 border-gray-100 space-y-3">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">💰 Detail Harga</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Harga per jam</span>
                                <span class="font-semibold text-slate-900">Rp <?php echo number_format($hourly_price, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Jumlah jam</span>
                                <span class="font-semibold text-slate-900">× <?php echo number_format($hours, 1); ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Biaya Admin</span>
                                <span class="font-semibold text-slate-900">Rp 0</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="price-box rounded-2xl p-6 mb-6">
                            <p class="text-white text-sm font-semibold uppercase tracking-wider mb-2">Total Pembayaran</p>
                            <p class="text-4xl font-black text-white">
                                Rp <?php echo number_format($total_harga, 0, ',', '.'); ?>
                            </p>
                        </div>

                        <!-- Fasilitas -->
                        <div class="bg-gray-50 rounded-xl p-5">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">✨ Fasilitas Tersedia</p>
                            <div class="space-y-2">
                                <?php
                                $fasilitas = explode(',', $lapangan['fasilitas']);
                                foreach(array_slice($fasilitas, 0, 5) as $f) {
                                    echo '<div class="facility-item"><i class="fas fa-check-circle text-emerald-500 mr-3 flex-shrink-0"></i><span class="text-sm text-gray-700">' . htmlspecialchars(trim($f)) . '</span></div>';
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
