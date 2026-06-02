CREATE DATABASE db_booking_lapangan_futsal;
USE db_booking_lapangan_futsal;

CREATE TABLE tb_admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE tb_lapangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_lapangan VARCHAR(50),
    harga_per_jam INT,
    status ENUM('tersedia', 'maintenance') DEFAULT 'tersedia'
);

CREATE TABLE tb_booking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_pemesan VARCHAR(100),
    tanggal_booking DATE,
    jam_mulai TIME,
    jam_selesai TIME,
    id_lapangan INT,
    FOREIGN KEY (id_lapangan) REFERENCES tb_lapangan(id)
);

CREATE TABLE tb_konten (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_name VARCHAR(50),
    content_text TEXT,
    image_path VARCHAR(255)
);
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);