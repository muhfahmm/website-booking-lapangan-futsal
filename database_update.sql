-- UPDATE DATABASE SCHEMA - Tambah Kolom Detail Lapangan
-- Jalankan query ini di phpMyAdmin untuk update existing database

-- Tambah kolom detail lapangan ke tb_lapangan
ALTER TABLE tb_lapangan ADD COLUMN deskripsi_lengkap TEXT DEFAULT NULL AFTER deskripsi;
ALTER TABLE tb_lapangan ADD COLUMN fasilitas TEXT DEFAULT NULL AFTER deskripsi_lengkap;
ALTER TABLE tb_lapangan ADD COLUMN ukuran VARCHAR(50) DEFAULT '40m x 20m' AFTER lokasi;
ALTER TABLE tb_lapangan ADD COLUMN pencahayaan VARCHAR(100) DEFAULT 'Standar' AFTER ukuran;
ALTER TABLE tb_lapangan ADD COLUMN parkir VARCHAR(100) DEFAULT 'Tersedia' AFTER pencahayaan;
ALTER TABLE tb_lapangan ADD COLUMN tipe_lantai VARCHAR(100) DEFAULT 'Rumput Sintetis' AFTER parkir;

-- Struktur tb_lapangan setelah update:
-- id, nama, harga, status, gambar, deskripsi, deskripsi_lengkap, fasilitas, rating, lokasi, ukuran, pencahayaan, parkir, tipe_lantai
