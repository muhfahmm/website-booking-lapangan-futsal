-- Database v3 dengan Pricing & Gallery Support
-- Drop existing database jika perlu: DROP DATABASE db_booking_lapangan_futsal;

CREATE DATABASE IF NOT EXISTS db_booking_lapangan_futsal;
USE db_booking_lapangan_futsal;

-- Admin Table
CREATE TABLE IF NOT EXISTS tb_admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Lapangan Table (dengan harga weekday & weekend + kolom baru)
CREATE TABLE IF NOT EXISTS tb_lapangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    harga INT DEFAULT 0,
    harga_weekend INT DEFAULT 0,
    status ENUM('tersedia', 'maintenance') DEFAULT 'tersedia',
    gambar VARCHAR(255) DEFAULT NULL,
    deskripsi TEXT DEFAULT NULL,
    deskripsi_lengkap TEXT DEFAULT NULL,
    fasilitas TEXT DEFAULT NULL,
    rating DECIMAL(3,2) DEFAULT 4.5,
    lokasi VARCHAR(150) DEFAULT 'Jakarta',
    ukuran VARCHAR(50) DEFAULT '40m x 20m',
    pencahayaan VARCHAR(100) DEFAULT 'Standar',
    parkir VARCHAR(100) DEFAULT 'Tersedia',
    tipe_lantai VARCHAR(100) DEFAULT 'Rumput Sintetis'
);

-- Lapangan Gallery Table (untuk multiple photos)
CREATE TABLE IF NOT EXISTS tb_lapangan_gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lapangan_id INT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE,
    INDEX idx_lapangan_id (lapangan_id)
);

-- Booking Table
CREATE TABLE IF NOT EXISTS tb_booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lapangan_id INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE,
    INDEX idx_lapangan_booking (lapangan_id)
);

-- Konten Table
CREATE TABLE IF NOT EXISTS tb_konten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    tipe ENUM('artikel', 'berita', 'panduan') DEFAULT 'artikel',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data: Lapangan dengan harga weekday & weekend
INSERT IGNORE INTO tb_lapangan (id, nama, harga, harga_weekend, status, deskripsi, deskripsi_lengkap, fasilitas, rating, lokasi, ukuran, pencahayaan, parkir, tipe_lantai) VALUES 
(
    1,
    'Lapangan A', 
    100000, 
    150000,
    'tersedia', 
    'Lapangan indoor dengan pencahayaan standar dan fasilitas lengkap', 
    'Lapangan A adalah lapangan futsal indoor premium yang berlokasi di Jakarta Barat. Dengan ukuran standar internasional dan dilengkapi pencahayaan LED modern, lapangan ini menawarkan kenyamanan bermain yang optimal. Cocok untuk pertandingan resmi maupun latihan rutin.',
    'AC Central, Toilet & Kamar Mandi, Ruang Tunggu Nyaman, Penyewaan Perlengkapan, Kantin & Minuman, Tempat Parkir Luas, Keamanan 24 Jam',
    4.86, 
    'Jakarta Barat',
    '40m x 20m',
    'LED Modern',
    'Tersedia (100+ spot)',
    'Rumput Sintetis Premium'
),
(
    2,
    'Lapangan B', 
    80000, 
    120000,
    'tersedia', 
    'Lapangan outdoor berkualitas internasional dengan rumput sintetis', 
    'Lapangan B adalah lapangan futsal outdoor terbesar dan tercanggih dengan standar internasional. Dilengkapi rumput sintetis berkualitas tinggi dan sistem drainase modern, lapangan ini siap untuk berbagai jenis pertandingan. Lokasi strategis di Jakarta Timur memudahkan akses dari berbagai area.',
    'Sistem Pencahayaan Profesional, Tribun Penonton, Kantor Pengelola, Area Istirahat Ber-AC, Toilet Bersih, Fasilitas Olahraga Lengkap, Parkir Bertingkat',
    4.65, 
    'Jakarta Timur',
    '45m x 25m',
    'Profesional High-Mast',
    'Tersedia (150+ spot)',
    'Rumput Sintetis Internasional'
),
(
    3,
    'Lapangan C', 
    75000, 
    110000,
    'tersedia', 
    'Lapangan indoor dengan AC dan fasilitas premium', 
    'Lapangan C menawarkan pengalaman bermain futsal yang nyaman dengan ber-AC penuh. Lapangan ini ideal untuk casual games maupun turnamen skala kecil. Harga yang kompetitif menjadikan lapangan ini pilihan utama untuk grup yang mencari value terbaik.',
    'AC Pendingin Optimal, Kamar Ganti Bersih, Penyewaan Bola & Sepatu, Snack Bar, WiFi Gratis, Parkir Aman, Staff Profesional',
    4.75, 
    'Jakarta Pusat',
    '35m x 18m',
    'Standar Plus',
    'Tersedia (80+ spot)',
    'Rumput Sintetis Standar'
);
