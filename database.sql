CREATE DATABASE db_booking_lapangan_futsal;
USE db_booking_lapangan_futsal;

CREATE TABLE tb_admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE tb_lapangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    harga INT DEFAULT 0,
    harga_weekend INT DEFAULT 0,
    status ENUM('tersedia', 'maintenance') DEFAULT 'tersedia',
    gambar VARCHAR(255) DEFAULT NULL,
    deskripsi TEXT DEFAULT NULL,
    deskripsi_lengkap TEXT DEFAULT NULL,
    fasilitas TEXT DEFAULT NULL,
    lokasi VARCHAR(150) DEFAULT 'Jakarta',
    ukuran VARCHAR(50) DEFAULT '40m x 20m',
    pencahayaan VARCHAR(100) DEFAULT 'Standar',
    parkir VARCHAR(100) DEFAULT 'Tersedia',
    tipe_lantai VARCHAR(100) DEFAULT 'Rumput Sintetis'
);

CREATE TABLE tb_lapangan_gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lapangan_id INT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE,
    INDEX idx_lapangan_id (lapangan_id)
);

CREATE TABLE tb_booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lapangan_id INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    total_harga INT DEFAULT 0,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    no_hp VARCHAR(20),
    email VARCHAR(100),
    notes TEXT,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE,
    INDEX idx_payment_status (payment_status),
    INDEX idx_tanggal (tanggal),
    INDEX idx_status (status)
);

-- Payment Tables
CREATE TABLE tb_pembayaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL UNIQUE,
    transaction_id VARCHAR(100) UNIQUE,
    amount INT NOT NULL,
    payment_method VARCHAR(50),
    status ENUM('pending', 'settlement', 'expire', 'cancel', 'deny') DEFAULT 'pending',
    midtrans_response JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES tb_booking(id) ON DELETE CASCADE,
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

CREATE TABLE tb_payment_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT,
    transaction_id VARCHAR(100),
    action VARCHAR(50),
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES tb_booking(id) ON DELETE SET NULL,
    INDEX idx_booking_id (booking_id),
    INDEX idx_created_at (created_at)
);

CREATE TABLE tb_payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    code VARCHAR(20) UNIQUE,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert payment methods
INSERT INTO tb_payment_methods (name, code) VALUES
('Credit Card', 'credit_card'),
('Debit Card', 'debit_card'),
('E-Wallet', 'e_wallet'),
('Virtual Account', 'bank_transfer'),
('QRIS', 'qris'),
('BNPL', 'bnpl');

-- Sample Data
INSERT INTO tb_lapangan (nama, harga, harga_weekend, status, gambar, deskripsi, deskripsi_lengkap, fasilitas, lokasi, ukuran, pencahayaan, parkir, tipe_lantai) VALUES 
(
    'Lapangan A', 
    100000,
    150000,
    'tersedia', 
    NULL,
    'Lapangan indoor dengan pencahayaan standar dan fasilitas lengkap', 
    'Lapangan A adalah lapangan futsal indoor premium yang berlokasi di Jakarta Barat. Dengan ukuran standar internasional dan dilengkapi pencahayaan LED modern, lapangan ini menawarkan kenyamanan bermain yang optimal. Cocok untuk pertandingan resmi maupun latihan rutin.',
    'AC Central, Toilet & Kamar Mandi, Ruang Tunggu Nyaman, Penyewaan Perlengkapan, Kantin & Minuman, Tempat Parkir Luas, Keamanan 24 Jam',
    'Jakarta Barat',
    '40m x 20m',
    'LED Modern',
    'Tersedia (100+ spot)',
    'Rumput Sintetis Premium'
),
(
    'Lapangan B', 
    80000,
    120000,
    'tersedia', 
    NULL,
    'Lapangan outdoor berkualitas internasional dengan rumput sintetis', 
    'Lapangan B adalah lapangan futsal outdoor terbesar dan tercanggih dengan standar internasional. Dilengkapi rumput sintetis berkualitas tinggi dan sistem drainase modern, lapangan ini siap untuk berbagai jenis pertandingan. Lokasi strategis di Jakarta Timur memudahkan akses dari berbagai area.',
    'Sistem Pencahayaan Profesional, Tribun Penonton, Kantor Pengelola, Area Istirahat Ber-AC, Toilet Bersih, Fasilitas Olahraga Lengkap, Parkir Bertingkat',
    'Jakarta Timur',
    '45m x 25m',
    'Profesional High-Mast',
    'Tersedia (150+ spot)',
    'Rumput Sintetis Internasional'
),
(
    'Lapangan C', 
    75000,
    110000,
    'tersedia', 
    NULL,
    'Lapangan indoor dengan AC dan fasilitas premium', 
    'Lapangan C menawarkan pengalaman bermain futsal yang nyaman dengan ber-AC penuh. Lapangan ini ideal untuk casual games maupun turnamen skala kecil. Harga yang kompetitif menjadikan lapangan ini pilihan utama untuk grup yang mencari value terbaik.',
    'AC Pendingin Optimal, Kamar Ganti Bersih, Penyewaan Bola & Sepatu, Snack Bar, WiFi Gratis, Parkir Aman, Staff Profesional',
    'Jakarta Pusat',
    '35m x 18m',
    'Standar Plus',
    'Tersedia (80+ spot)',
    'Rumput Sintetis Standar'
);



