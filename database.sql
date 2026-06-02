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
    status ENUM('tersedia', 'maintenance') DEFAULT 'tersedia'
);

CREATE TABLE tb_booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lapangan_id INT NOT NULL,
    nama_pemesan VARCHAR(100) NOT NULL,
    tanggal DATE NOT NULL,
    jam_mulai TIME NOT NULL,
    jam_selesai TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE
);

CREATE TABLE tb_konten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    judul VARCHAR(200) NOT NULL,
    isi TEXT NOT NULL,
    tipe ENUM('artikel', 'berita', 'panduan') DEFAULT 'artikel',
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);