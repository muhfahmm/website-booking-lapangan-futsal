<?php
/**
 * Payment Failed Page
 * Tampil setelah pembayaran gagal atau dibatalkan
 */

$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';
$reason = isset($_GET['reason']) ? $_GET['reason'] : 'Pembayaran dibatalkan';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✗ Pembayaran Gagal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-orange-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-lg w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Error Header -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 p-8 text-center text-white">
                <div class="mb-4">
                    <i class="fas fa-times-circle text-6xl"></i>
                </div>
                <h1 class="text-3xl font-bold">PEMBAYARAN GAGAL</h1>
                <p class="text-red-100 mt-2">Silakan coba lagi</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                
                <!-- Error Details -->
                <div class="mb-6 bg-red-50 rounded-lg p-4 border-l-4 border-red-600">
                    <h3 class="font-bold text-red-900 mb-2">Penyebab Kegagalan</h3>
                    <p class="text-sm text-red-800"><?php echo htmlspecialchars($reason); ?></p>
                </div>

                <?php if($order_id): ?>
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-600 text-sm">No. Referensi</p>
                    <p class="font-bold text-lg text-slate-900"><?php echo htmlspecialchars($order_id); ?></p>
                </div>
                <?php endif; ?>

                <!-- Info -->
                <div class="mb-6 bg-blue-50 rounded-lg p-4 border-l-4 border-blue-400">
                    <h3 class="font-bold text-blue-900 mb-2"><i class="fas fa-info-circle mr-2"></i>Apa yang dapat Anda lakukan:</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Periksa kembali data pembayaran Anda</li>
                        <li>Pastikan saldo kartu/akun cukup</li>
                        <li>Coba metode pembayaran lain</li>
                        <li>Hubungi bank atau penyedia e-wallet Anda</li>
                        <li>Hubungi customer service kami</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="../booking/checkout.php" class="block w-full bg-emerald-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-emerald-700 transition-all">
                        <i class="fas fa-redo mr-2"></i> Coba Lagi
                    </a>
                    <a href="../index.php" class="block w-full bg-slate-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-slate-700 transition-all">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                    <a href="https://wa.me/6288983514206" target="_blank" class="block w-full bg-blue-600 text-white py-3 rounded-lg font-semibold text-center hover:bg-blue-700 transition-all">
                        <i class="fab fa-whatsapp mr-2"></i> Hubungi Support
                    </a>
                </div>

            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-8 py-4 text-center text-sm text-gray-600">
                <p>Butuh bantuan? Hubungi support kami</p>
                <p class="mt-1">admin@futsalbook.com | 0812-3456-7890</p>
            </div>

        </div>
    </div>
</body>
</html>
