<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config/koneksi.php';

// Get lapangan ID from URL
$lapangan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($lapangan_id === 0) {
    header('Location: index.php');
    exit;
}

// Get lapangan details from database
$query = "SELECT * FROM tb_lapangan WHERE id = $lapangan_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$lapangan = $result->fetch_assoc();

// Get gallery images
$query_gallery = "SELECT * FROM tb_lapangan_gallery WHERE lapangan_id = $lapangan_id ORDER BY urutan ASC";
$result_gallery = $conn->query($query_gallery);
$gallery = [];
while ($row = $result_gallery->fetch_assoc()) {
    $gallery[] = $row;
}

// Get related lapangan (other lapangan)
$query_related = "SELECT * FROM tb_lapangan WHERE id != $lapangan_id ORDER BY RAND() LIMIT 3";
$result_related = $conn->query($query_related);
$related_lapangan = [];
while ($row = $result_related->fetch_assoc()) {
    $related_lapangan[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lapangan['nama']); ?> - Detail Lapangan Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            scroll-behavior: smooth;
        }
        
        nav {
            transition: all 0.3s ease-in-out;
        }
        
        nav.scrolled {
            @apply bg-emerald-700 shadow-lg;
        }

        /* Mobile Menu Sidebar Styling */
        #mobile-menu {
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
        }

        #mobile-menu.open {
            transform: translateX(0);
        }

        #mobile-menu-overlay.open {
            opacity: 0.5;
            visibility: visible;
        }
        
        .gallery-image {
            @apply w-full h-96 object-cover rounded-lg;
        }
        
        .detail-section {
            @apply bg-white rounded-lg p-6 shadow-md mb-6;
        }
        
        .facility-item {
            @apply flex items-start gap-3 mb-4;
        }
        
        .facility-icon {
            @apply text-emerald-600 text-2xl w-8 flex-shrink-0;
        }
        
        .badge-status {
            @apply inline-block px-4 py-2 rounded-full font-semibold;
        }
        
        .badge-tersedia {
            @apply bg-green-100 text-green-800;
        }
        
        .badge-maintenance {
            @apply bg-red-100 text-red-800;
        }
        
        .rating-star {
            @apply text-yellow-400;
        }
        
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: white;
            text-decoration: none;
            z-index: 100;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease-in-out;
            animation: float 3s ease-in-out infinite;
        }
        
        .whatsapp-float:hover {
            background-color: #1f9d56;
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(37, 211, 102, 0.4);
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        @media (max-width: 640px) {
            .whatsapp-float {
                bottom: 20px;
                right: 20px;
                width: 55px;
                height: 55px;
                font-size: 28px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 bg-transparent">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="index.php" class="flex items-center gap-2">
                <i class="fas fa-futbol text-emerald-600 text-3xl"></i>
                <span class="text-2xl font-bold text-slate-900">FutsalBook</span>
            </a>
            
            <div class="hidden md:flex gap-8 items-center">
                <a href="index.php#home" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Home</a>
                <a href="index.php#lapangan" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Lapangan</a>
                <a href="index.php#booking" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Booking</a>
                <a href="index.php#kontak" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Kontak</a>
            </div>
            
            <!-- Space filler for alignment in place of admin button -->
            <div class="hidden md:block w-[100px]"></div>

            <!-- Mobile Menu Toggle Button -->
            <button id="mobile-menu-btn" class="md:hidden text-2xl text-slate-900 cursor-pointer focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <!-- Mobile Menu Sidebar -->
        <div id="mobile-menu" class="fixed left-0 top-0 h-full w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out z-40 md:hidden">
            <!-- Close Button -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <span class="text-xl font-bold text-slate-900">Menu</span>
                <button id="close-menu-btn" class="text-2xl text-slate-900 focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Mobile Navigation Links -->
            <div class="flex flex-col gap-0 p-0">
                <a href="index.php#home" class="px-6 py-4 text-slate-900 font-semibold hover:bg-emerald-50 hover:text-emerald-600 border-b border-gray-100 transition-all" onclick="closeMobileMenu()">
                    <i class="fas fa-home mr-3 text-emerald-600"></i>Home
                </a>
                <a href="index.php#lapangan" class="px-6 py-4 text-slate-900 font-semibold hover:bg-emerald-50 hover:text-emerald-600 border-b border-gray-100 transition-all" onclick="closeMobileMenu()">
                    <i class="fas fa-futbol mr-3 text-emerald-600"></i>Lapangan
                </a>
                <a href="index.php#booking" class="px-6 py-4 text-slate-900 font-semibold hover:bg-emerald-50 hover:text-emerald-600 border-b border-gray-100 transition-all" onclick="closeMobileMenu()">
                    <i class="fas fa-calendar-check mr-3 text-emerald-600"></i>Booking
                </a>
                <a href="index.php#kontak" class="px-6 py-4 text-slate-900 font-semibold hover:bg-emerald-50 hover:text-emerald-600 border-b border-gray-100 transition-all" onclick="closeMobileMenu()">
                    <i class="fas fa-phone mr-3 text-emerald-600"></i>Kontak
                </a>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black opacity-0 invisible md:hidden transition-all duration-300 ease-in-out z-30"></div>
    </nav>

    <!-- BREADCRUMB -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center gap-2 text-sm">
                <a href="index.php" class="text-emerald-600 hover:text-emerald-700 font-semibold">Home</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <a href="index.php#lapangan" class="text-emerald-600 hover:text-emerald-700 font-semibold">Lapangan</a>
                <i class="fas fa-chevron-right text-gray-400"></i>
                <span class="text-gray-600"><?php echo htmlspecialchars($lapangan['nama']); ?></span>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="container mx-auto px-6 py-12">
        <!-- Header Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Left - Image & Details -->
            <div class="md:col-span-2">
                <!-- Main Image with Gallery Carousel -->
                <div class="mb-6">
                    <!-- Main Image Container -->
                    <div class="relative bg-gray-200 rounded-lg overflow-hidden mb-4" style="aspect-ratio: 16/9;">
                        <?php 
                            $main_image = $lapangan['gambar'];
                            if (!$main_image && count($gallery) > 0) {
                                $main_image = $gallery[0]['foto'];
                            }
                        ?>
                        <img id="mainImage" src="<?php echo htmlspecialchars($main_image ?? 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22100%22 height=%22100%22/%3E%3C/svg%3E'); ?>" 
                             alt="<?php echo htmlspecialchars($lapangan['nama']); ?>" 
                             class="w-full h-full object-cover">
                        
                        <!-- Gallery Controls (only if there are multiple images) -->
                        <?php if (count($gallery) > 1): ?>
                            <button onclick="prevImage()" class="absolute left-3 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button onclick="nextImage()" class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 hover:bg-opacity-70 text-white w-10 h-10 rounded-full flex items-center justify-center transition-all">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <div class="absolute bottom-3 right-3 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                <span id="imageCounter">1</span> / <?php echo count($gallery) + 1; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Gallery Thumbnails -->
                    <?php if (count($gallery) > 0 || $lapangan['gambar']): ?>
                        <div class="flex gap-2 overflow-x-auto pb-2">
                            <!-- Main image thumbnail -->
                            <?php if ($lapangan['gambar']): ?>
                                <button onclick="selectImage(0)" class="thumbnail-btn flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-emerald-600 hover:border-emerald-700 transition-all">
                                    <img src="<?php echo htmlspecialchars($lapangan['gambar']); ?>" 
                                         alt="Main" 
                                         class="w-full h-full object-cover">
                                </button>
                            <?php endif; ?>

                            <!-- Gallery images thumbnails -->
                            <?php foreach ($gallery as $index => $item): ?>
                                <?php $thumb_index = $lapangan['gambar'] ? $index + 1 : $index; ?>
                                <button onclick="selectImage(<?php echo $thumb_index; ?>)" class="thumbnail-btn flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 border-gray-300 hover:border-emerald-600 transition-all">
                                    <img src="<?php echo htmlspecialchars($item['foto']); ?>" 
                                         alt="Gallery <?php echo $index + 1; ?>" 
                                         class="w-full h-full object-cover">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Title & Status -->
                <div class="mb-6">
                    <h1 class="text-4xl font-bold text-slate-900 mb-3"><?php echo htmlspecialchars($lapangan['nama']); ?></h1>
                    <div class="flex flex-wrap gap-3 items-center">
                        <?php 
                            if ($lapangan['status'] === 'tersedia') {
                                echo '<span class="badge-status badge-tersedia">
                                        <i class="fas fa-check-circle mr-2"></i>Tersedia
                                    </span>';
                            } else {
                                echo '<span class="badge-status badge-maintenance">
                                        <i class="fas fa-exclamation-circle mr-2"></i>Maintenance
                                    </span>';
                            }
                        ?>
                        <div class="flex items-center gap-1">
                            <i class="fas fa-star rating-star"></i>
                            <span class="font-semibold text-slate-900"><?php echo $lapangan['rating']; ?></span>
                        </div>
                        <span class="text-gray-600">
                            <i class="fas fa-map-marker-alt text-emerald-600 mr-2"></i><?php echo htmlspecialchars($lapangan['lokasi']); ?>
                        </span>
                    </div>
                </div>

                <!-- Deskripsi Singkat -->
                <div class="detail-section">
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Tentang Lapangan</h2>
                    <p class="text-gray-700 leading-relaxed text-lg"><?php echo htmlspecialchars($lapangan['deskripsi_lengkap']); ?></p>
                </div>

                <!-- Spesifikasi -->
                <div class="detail-section">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">Spesifikasi Lapangan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-ruler text-emerald-600 text-3xl"></i>
                            <div>
                                <p class="text-gray-600 text-sm">Ukuran</p>
                                <p class="font-bold text-slate-900"><?php echo htmlspecialchars($lapangan['ukuran']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-lightbulb text-emerald-600 text-3xl"></i>
                            <div>
                                <p class="text-gray-600 text-sm">Pencahayaan</p>
                                <p class="font-bold text-slate-900"><?php echo htmlspecialchars($lapangan['pencahayaan']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-car text-emerald-600 text-3xl"></i>
                            <div>
                                <p class="text-gray-600 text-sm">Parkir</p>
                                <p class="font-bold text-slate-900"><?php echo htmlspecialchars($lapangan['parkir']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-layer-group text-emerald-600 text-3xl"></i>
                            <div>
                                <p class="text-gray-600 text-sm">Tipe Lantai</p>
                                <p class="font-bold text-slate-900"><?php echo htmlspecialchars($lapangan['tipe_lantai']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas -->
                <div class="detail-section">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6">Fasilitas Lengkap</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php 
                            $fasilitas_list = explode(',', $lapangan['fasilitas']);
                            foreach ($fasilitas_list as $fasilitas): 
                                $fasilitas = trim($fasilitas);
                        ?>
                            <div class="facility-item">
                                <div class="facility-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <p class="text-slate-900 font-semibold"><?php echo htmlspecialchars($fasilitas); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right - Pricing & Booking -->
            <div>
                <!-- Harga Card -->
                <div class="detail-section sticky top-24">
                    <!-- Date Selection for Dynamic Pricing -->
                    <div class="mb-6">
                        <label class="block text-gray-600 text-sm font-semibold mb-2">Pilih Tanggal (untuk melihat harga):</label>
                        <input type="date" id="selectedDate" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-600" onchange="updatePricing()">
                        <p id="dayType" class="text-sm text-gray-500 mt-2">Pilih tanggal terlebih dahulu</p>
                    </div>

                    <hr class="border-gray-200 mb-6">

                    <!-- Pricing Display -->
                    <div>
                        <p class="text-gray-600 text-sm mb-2">Harga per Jam</p>
                        <p id="currentPrice" class="text-5xl font-bold text-emerald-600 mb-1">
                            Rp <?php echo number_format($lapangan['harga'], 0, ',', '.'); ?>
                        </p>
                        <p class="text-gray-500 text-sm mb-4">Harga berlaku untuk 1 jam</p>

                        <!-- Price Comparison -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <p class="text-xs text-gray-600 font-semibold mb-3">PERBANDINGAN HARGA</p>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">
                                        <i class="fas fa-sun text-yellow-500 mr-2"></i>Weekday (Sen-Jum):
                                    </span>
                                    <span class="font-bold text-slate-900">Rp <?php echo number_format($lapangan['harga'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">
                                        <i class="fas fa-moon text-blue-500 mr-2"></i>Weekend (Sab-Min):
                                    </span>
                                    <span class="font-bold text-slate-900">Rp <?php echo number_format($lapangan['harga_weekend'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200 my-6">

                    <!-- Booking Buttons -->
                    <div class="space-y-3">
                        <button onclick="whatsappBooking()" class="w-full bg-yellow-400 text-slate-900 px-6 py-4 rounded-lg font-bold transition-all hover:bg-yellow-500 flex items-center justify-center gap-2">
                            <i class="fab fa-whatsapp"></i> Booking via WhatsApp
                        </button>
                        
                        <button onclick="openBookingForm()" class="w-full bg-emerald-600 text-white px-6 py-4 rounded-lg font-bold transition-all hover:bg-emerald-700 flex items-center justify-center gap-2">
                            <i class="fas fa-calendar-check"></i> Booking Sekarang
                        </button>
                    </div>

                    <hr class="border-gray-200 my-6">

                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-blue-900 text-sm">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Hubungi kami untuk mendapatkan penawaran khusus grup atau member bulanan.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Lapangan -->
        <?php if (count($related_lapangan) > 0): ?>
            <div class="border-t border-gray-200 pt-12">
                <h2 class="text-3xl font-bold text-slate-900 mb-8">Lapangan Lainnya</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($related_lapangan as $related): ?>
                        <a href="detail-lapangan.php?id=<?php echo $related['id']; ?>" class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all">
                            <div class="h-48 bg-gray-300 flex items-center justify-center">
                                <?php if ($related['gambar']): ?>
                                    <img src="<?php echo htmlspecialchars($related['gambar']); ?>" 
                                         alt="<?php echo htmlspecialchars($related['nama']); ?>" 
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-image text-gray-400 text-6xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="text-xl font-bold text-slate-900 mb-2"><?php echo htmlspecialchars($related['nama']); ?></h3>
                                <p class="text-emerald-600 font-bold text-lg mb-3">Rp <?php echo number_format($related['harga'], 0, ',', '.'); ?>/jam</p>
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <span><i class="fas fa-star text-yellow-400 mr-1"></i><?php echo $related['rating']; ?></span>
                                    <span><i class="fas fa-map-marker-alt text-emerald-600 mr-1"></i><?php echo htmlspecialchars($related['lokasi']); ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- FLOATING WHATSAPP BUTTON -->
    <a href="https://wa.me/6288983514206?text=Halo%20Admin%2C%20saya%20ingin%20menanyakan%20ketersediaan%20jadwal%20lapangan%20futsal%20untuk%20%5BTanggal%5D.%20Mohon%20informasinya%2C%20terima%20kasih." 
       class="whatsapp-float" 
       target="_blank" 
       rel="noopener noreferrer"
       title="Chat dengan kami di WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-gray-300 py-12 mt-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-futbol text-emerald-400 text-2xl"></i>
                        <span class="text-2xl font-bold text-white">FutsalBook</span>
                    </div>
                    <p class="text-gray-400">Platform booking lapangan futsal online yang terpercaya dan mudah digunakan.</p>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Menu</h3>
                    <ul class="space-y-2">
                        <li><a href="index.php#home" class="text-gray-400 hover:text-emerald-400 transition-all">Home</a></li>
                        <li><a href="index.php#lapangan" class="text-gray-400 hover:text-emerald-400 transition-all">Lapangan</a></li>
                        <li><a href="index.php#booking" class="text-gray-400 hover:text-emerald-400 transition-all">Booking</a></li>
                        <li><a href="index.php#kontak" class="text-gray-400 hover:text-emerald-400 transition-all">Kontak</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Ikuti Kami</h3>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-700 transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-700 transition-all">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-700 transition-all">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <hr class="border-gray-700 mb-6">

            <div class="text-center text-gray-400">
                <p>&copy; 2026 FutsalBook. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Gallery images data
        const galleryImages = [
            <?php if ($lapangan['gambar']): ?>
                { src: '<?php echo htmlspecialchars($lapangan['gambar']); ?>' },
            <?php endif; ?>
            <?php foreach ($gallery as $item): ?>
                { src: '<?php echo htmlspecialchars($item['foto']); ?>' },
            <?php endforeach; ?>
        ];

        let currentImageIndex = 0;

        function selectImage(index) {
            currentImageIndex = index;
            updateMainImage();
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
            updateMainImage();
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
            updateMainImage();
        }

        function updateMainImage() {
            if (galleryImages.length > 0) {
                document.getElementById('mainImage').src = galleryImages[currentImageIndex].src;
                const counter = document.getElementById('imageCounter');
                if (counter) {
                    counter.textContent = currentImageIndex + 1;
                }
                
                // Update active thumbnail border
                const thumbnails = document.querySelectorAll('.thumbnail-btn');
                thumbnails.forEach((thumb, idx) => {
                    if (idx === currentImageIndex) {
                        thumb.classList.remove('border-gray-300');
                        thumb.classList.add('border-emerald-600');
                    } else {
                        thumb.classList.remove('border-emerald-600');
                        thumb.classList.add('border-gray-300');
                    }
                });
            }
        }

        // Dynamic Pricing Logic
        function updatePricing() {
            const dateInput = document.getElementById('selectedDate').value;
            if (!dateInput) {
                document.getElementById('dayType').textContent = 'Pilih tanggal terlebih dahulu';
                document.getElementById('currentPrice').innerHTML = 'Rp <?php echo number_format($lapangan['harga'], 0, ',', '.'); ?>';
                return;
            }

            const date = new Date(dateInput);
            const dayOfWeek = date.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            
            const weekdayPrice = <?php echo $lapangan['harga']; ?>;
            const weekendPrice = <?php echo $lapangan['harga_weekend']; ?>;
            
            // Determine if it's weekend (Saturday = 6, Sunday = 0)
            const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
            const selectedPrice = isWeekend ? weekendPrice : weekdayPrice;
            
            // Format date for display
            const dateFormatter = new Intl.DateTimeFormat('id-ID', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            const formattedDate = dateFormatter.format(date);
            
            const dayType = isWeekend ? '<i class="fas fa-calendar-days text-blue-500 mr-2"></i>Weekend' : '<i class="fas fa-calendar-days text-yellow-500 mr-2"></i>Weekday';
            
            document.getElementById('dayType').innerHTML = `${dayType} - ${formattedDate}`;
            document.getElementById('currentPrice').innerHTML = `Rp ${selectedPrice.toLocaleString('id-ID')}`;
        }

        // Navbar scroll effect
        const navbar = document.querySelector('nav');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        function whatsappBooking() {
            const lapanganName = '<?php echo htmlspecialchars($lapangan['nama']); ?>';
            const dateInput = document.getElementById('selectedDate').value;
            
            let message;
            if (dateInput) {
                const date = new Date(dateInput);
                const dateFormatter = new Intl.DateTimeFormat('id-ID', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                const formattedDate = dateFormatter.format(date);
                message = `Halo Admin, saya tertarik booking ${lapanganName} untuk tanggal ${formattedDate}. Mohon informasi ketersediaan jam bermainnya. Terima kasih.`;
            } else {
                message = `Halo Admin, saya tertarik booking ${lapanganName}. Mohon informasi ketersediaan dan harganya. Terima kasih.`;
            }
            
            const encodedMessage = encodeURIComponent(message);
            window.open(`https://wa.me/6288983514206?text=${encodedMessage}`, '_blank');
        }

        // Function untuk membuka form booking
        function openBookingForm() {
            const selectedDate = document.getElementById('selectedDate').value;
            
            if (!selectedDate) {
                alert('Silakan pilih tanggal terlebih dahulu');
                return;
            }

            // Redirect ke booking checkout dengan parameter
            window.location.href = `booking/checkout.php?lapangan_id=<?php echo $lapangan_id; ?>&tanggal=${selectedDate}&jam_mulai=09:00&jam_selesai=10:00`;
        }

        // Mobile Menu Functions
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeMenuBtn = document.getElementById('close-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

        if (mobileMenuBtn && closeMenuBtn && mobileMenu && mobileMenuOverlay) {
            // Open menu
            mobileMenuBtn.addEventListener('click', function() {
                mobileMenu.classList.add('open');
                mobileMenuOverlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            });

            // Close menu
            closeMenuBtn.addEventListener('click', closeMobileMenu);
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        }

        // Close menu function
        function closeMobileMenu() {
            if (mobileMenu && mobileMenuOverlay) {
                mobileMenu.classList.remove('open');
                mobileMenuOverlay.classList.remove('open');
                document.body.style.overflow = 'auto';
            }
        }

        // Close menu when clicking on a link
        document.querySelectorAll('#mobile-menu a').forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });

        // Close menu when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('open')) {
                closeMobileMenu();
            }
        });
    </script>
</body>
</html>
