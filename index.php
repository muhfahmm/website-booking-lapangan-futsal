<?php
require 'config/koneksi.php';

// Ambil data lapangan dari database
$query_lapangan = "SELECT * FROM tb_lapangan ORDER BY id ASC";
$result_lapangan = $conn->query($query_lapangan);
$lapangan_list = [];
if ($result_lapangan->num_rows > 0) {
    while ($row = $result_lapangan->fetch_assoc()) {
        $lapangan_list[] = $row;
    }
}

// Ambil data konten untuk hero section
$query_konten = "SELECT * FROM tb_konten WHERE tipe = 'panduan' LIMIT 1";
$result_konten = $conn->query($query_konten);
$konten_hero = null;
if ($result_konten->num_rows > 0) {
    $konten_hero = $result_konten->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Lapangan Futsal - Website Booking Lapangan Futsal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            scroll-behavior: smooth;
        }
        
        /* Navbar fixed styling */
        nav {
            transition: all 0.3s ease-in-out;
        }
        
        nav.scrolled {
            @apply bg-emerald-700 shadow-lg;
        }
        
        /* Hero section styling */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23059669" width="1200" height="600"/><circle cx="100" cy="100" r="30" fill="%23facc15" opacity="0.1"/><circle cx="1100" cy="500" r="50" fill="%23facc15" opacity="0.1"/></svg>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        /* Lapangan card hover effect */
        .lapangan-card {
            transition: all 0.3s ease-in-out;
        }
        
        .lapangan-card:hover {
            @apply shadow-xl -translate-y-1;
        }
        
        /* Status badge styling */
        .badge-tersedia {
            @apply bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold;
        }
        
        .badge-maintenance {
            @apply bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold;
        }
        
        /* CTA button styling */
        .btn-primary {
            @apply bg-emerald-600 text-white rounded-lg px-6 py-3 font-semibold transition-all hover:bg-emerald-700 hover:shadow-lg;
        }
        
        .btn-cta {
            @apply bg-yellow-400 text-slate-900 rounded-lg px-8 py-4 font-bold transition-all hover:bg-yellow-500 hover:shadow-lg;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 bg-transparent">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-2">
                <i class="fas fa-futbol text-emerald-600 text-3xl"></i>
                <span class="text-2xl font-bold text-slate-900">FutsalBook</span>
            </div>
            
            <!-- Navigation Links -->
            <div class="hidden md:flex gap-8 items-center">
                <a href="#home" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Home</a>
                <a href="#lapangan" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Lapangan</a>
                <a href="#booking" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Booking</a>
                <a href="#kontak" class="text-slate-900 font-semibold hover:text-emerald-600 transition-all">Kontak</a>
            </div>
            
            <!-- Admin Link -->
            <a href="admin/auth/login.php" class="btn-primary hidden md:block">
                <i class="fas fa-lock mr-2"></i> Admin
            </a>
            
            <!-- Mobile Menu Icon -->
            <div class="md:hidden">
                <i class="fas fa-bars text-2xl text-slate-900 cursor-pointer"></i>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section id="home" class="hero-section h-screen flex items-center justify-center text-center text-white relative">
        <div class="container mx-auto px-6 z-10">
            <h1 class="text-5xl md:text-7xl font-bold mb-6">
                Booking Lapangan Futsal Tanpa Ribet
            </h1>
            <p class="text-lg md:text-2xl mb-8 text-gray-200">
                Pesan lapangan futsal favorit Anda dengan mudah, cepat, dan terpercaya
            </p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a href="#lapangan" class="btn-cta inline-block">
                    <i class="fas fa-calendar-check mr-2"></i> Booking Sekarang
                </a>
                <a href="#kontak" class="bg-white text-emerald-600 rounded-lg px-8 py-4 font-bold transition-all hover:bg-gray-100 inline-block">
                    <i class="fas fa-phone mr-2"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- SECTION LAPANGAN -->
    <section id="lapangan" class="py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">
                    Daftar Lapangan Futsal
                </h2>
                <p class="text-gray-600 text-lg">
                    Pilih lapangan terbaik untuk bermain futsal bersama teman-teman Anda
                </p>
            </div>

            <!-- Lapangan Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if (count($lapangan_list) > 0): ?>
                    <?php foreach ($lapangan_list as $lapangan): ?>
                        <div class="lapangan-card bg-white border-l-4 border-emerald-600 rounded-lg shadow-sm p-6">
                            <!-- Icon -->
                            <div class="mb-4">
                                <i class="fas fa-futbol text-4xl text-emerald-600"></i>
                            </div>
                            
                            <!-- Nama Lapangan -->
                            <h3 class="text-2xl font-bold text-slate-900 mb-2">
                                <?php echo htmlspecialchars($lapangan['nama']); ?>
                            </h3>
                            
                            <!-- Status -->
                            <div class="mb-4">
                                <?php 
                                    if ($lapangan['status'] === 'tersedia') {
                                        echo '<span class="badge-tersedia">
                                                <i class="fas fa-check-circle mr-1"></i> Tersedia
                                            </span>';
                                    } else {
                                        echo '<span class="badge-maintenance">
                                                <i class="fas fa-exclamation-circle mr-1"></i> Maintenance
                                            </span>';
                                    }
                                ?>
                            </div>
                            
                            <!-- Harga -->
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <p class="text-gray-600 text-sm mb-1">Harga per Jam</p>
                                <p class="text-3xl font-bold text-emerald-600">
                                    Rp <?php echo number_format($lapangan['harga'], 0, ',', '.'); ?>
                                </p>
                            </div>
                            
                            <!-- Details -->
                            <div class="mb-6 space-y-3">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-ruler text-emerald-600 w-5"></i>
                                    <span>Ukuran: 40m x 20m</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-lightbulb text-emerald-600 w-5"></i>
                                    <span>Pencahayaan: Standar</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    <i class="fas fa-car text-emerald-600 w-5"></i>
                                    <span>Parkir: Tersedia</span>
                                </div>
                            </div>
                            
                            <!-- CTA Button -->
                            <?php if ($lapangan['status'] === 'tersedia'): ?>
                                <button class="w-full btn-primary flex items-center justify-center gap-2">
                                    <i class="fas fa-bookmark"></i> Booking Sekarang
                                </button>
                            <?php else: ?>
                                <button class="w-full bg-gray-300 text-gray-500 rounded-lg px-6 py-3 font-semibold cursor-not-allowed" disabled>
                                    <i class="fas fa-ban mr-2"></i> Tidak Tersedia
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-12">
                        <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada lapangan yang tersedia</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- SECTION INFORMASI / KONTEN -->
    <section id="konten" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-6">
                        Mengapa Memilih Kami?
                    </h2>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-4">
                            <i class="fas fa-check text-emerald-600 text-2xl mt-1"></i>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Mudah & Cepat</h3>
                                <p class="text-gray-600">Proses booking hanya membutuhkan beberapa klik saja</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <i class="fas fa-check text-emerald-600 text-2xl mt-1"></i>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Terpercaya</h3>
                                <p class="text-gray-600">Lapangan berkualitas dengan fasilitas lengkap</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <i class="fas fa-check text-emerald-600 text-2xl mt-1"></i>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900">Harga Kompetitif</h3>
                                <p class="text-gray-600">Dapatkan harga terbaik dengan kualitas premium</p>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <!-- Right Image -->
                <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-lg p-12 text-white text-center">
                    <i class="fas fa-futbol text-9xl opacity-30 mb-6"></i>
                    <h3 class="text-3xl font-bold mb-4">Siap Bermain?</h3>
                    <p class="text-lg mb-6">Booking lapangan favorit Anda sekarang dan nikmati pengalaman bermain yang luar biasa</p>
                    <a href="#lapangan" class="inline-block bg-yellow-400 text-slate-900 rounded-lg px-8 py-3 font-bold hover:bg-yellow-500 transition-all">
                        Lihat Semua Lapangan
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION BOOKING -->
    <section id="booking" class="py-16 md:py-24">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">
                    Cara Booking
                </h2>
                <p class="text-gray-600 text-lg">
                    Ikuti langkah-langkah sederhana untuk melakukan booking lapangan
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="bg-emerald-600 text-white rounded-full w-16 h-16 flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Pilih Lapangan</h3>
                    <p class="text-gray-600">Browsing dan pilih lapangan favorit Anda dari daftar yang tersedia</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="bg-emerald-600 text-white rounded-full w-16 h-16 flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Pilih Tanggal & Jam</h3>
                    <p class="text-gray-600">Tentukan tanggal dan jam bermain yang sesuai dengan jadwal Anda</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="bg-emerald-600 text-white rounded-full w-16 h-16 flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Isi Data Pemesan</h3>
                    <p class="text-gray-600">Masukkan informasi pribadi Anda untuk konfirmasi booking</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="bg-emerald-600 text-white rounded-full w-16 h-16 flex items-center justify-center text-3xl font-bold mx-auto mb-4">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Konfirmasi</h3>
                    <p class="text-gray-600">Selesai! Booking Anda sudah dikonfirmasi, siap bermain</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION KONTAK -->
    <section id="kontak" class="py-16 md:py-24 bg-gradient-to-r from-emerald-600 to-emerald-800 text-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold mb-4">
                    Hubungi Kami
                </h2>
                <p class="text-lg text-emerald-100">
                    Punya pertanyaan? Kami siap membantu Anda
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Phone -->
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 text-center">
                    <i class="fas fa-phone text-5xl mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">Telepon</h3>
                    <p class="text-emerald-100">(+62) 812-3456-7890</p>
                </div>

                <!-- Email -->
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 text-center">
                    <i class="fas fa-envelope text-5xl mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">Email</h3>
                    <p class="text-emerald-100">info@futsalbook.com</p>
                </div>

                <!-- Location -->
                <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8 text-center">
                    <i class="fas fa-map-marker-alt text-5xl mb-4"></i>
                    <h3 class="text-2xl font-bold mb-2">Lokasi</h3>
                    <p class="text-emerald-100">Jl. Stadion No. 123, Kota</p>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="mt-12 max-w-2xl mx-auto bg-white bg-opacity-10 backdrop-blur-md rounded-lg p-8">
                <h3 class="text-2xl font-bold mb-6">Kirim Pesan Kami</h3>
                <form class="space-y-4">
                    <div>
                        <input type="text" placeholder="Nama Anda" class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-90 text-slate-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <input type="email" placeholder="Email Anda" class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-90 text-slate-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    </div>
                    <div>
                        <textarea placeholder="Pesan Anda" rows="4" class="w-full px-4 py-3 rounded-lg bg-white bg-opacity-90 text-slate-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-yellow-400 text-slate-900 rounded-lg px-6 py-3 font-bold transition-all hover:bg-yellow-500">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-gray-300 py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fas fa-futbol text-emerald-400 text-2xl"></i>
                        <span class="text-2xl font-bold text-white">FutsalBook</span>
                    </div>
                    <p class="text-gray-400">Platform booking lapangan futsal online yang terpercaya dan mudah digunakan.</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Menu</h3>
                    <ul class="space-y-2">
                        <li><a href="#home" class="text-gray-400 hover:text-emerald-400 transition-all">Home</a></li>
                        <li><a href="#lapangan" class="text-gray-400 hover:text-emerald-400 transition-all">Lapangan</a></li>
                        <li><a href="#booking" class="text-gray-400 hover:text-emerald-400 transition-all">Booking</a></li>
                        <li><a href="#kontak" class="text-gray-400 hover:text-emerald-400 transition-all">Kontak</a></li>
                    </ul>
                </div>

                <!-- Help -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Bantuan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">FAQ</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">Kebijakan Privasi</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-emerald-400 transition-all">Hubungi Support</a></li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Ikuti Kami</h3>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-700 transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-emerald-600 rounded-full flex items-center justify-center hover:bg-emerald-700 transition-all">
                            <i class="fab fa-twitter"></i>
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

            <!-- Divider -->
            <hr class="border-gray-700 mb-6">

            <!-- Copyright -->
            <div class="text-center text-gray-400">
                <p>&copy; 2026 FutsalBook. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript untuk Navbar Scroll -->
    <script>
        const navbar = document.querySelector('nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>
