-- =====================================================
-- Reset Database Script
-- Website Booking Lapangan Futsal
-- =====================================================
-- WARNING: Hati-hati! Script ini akan MENGHAPUS semua data
-- Gunakan untuk menghapus data tanpa perlu menghapus struktur tabel
-- Setelah script ini, jalankan database.sql untuk recreate struktur & data
-- =====================================================

-- Drop tables dengan urutan yang benar (mempertimbangkan foreign key)
-- Urutan: tabel yang memiliki foreign key didrop terlebih dahulu

DROP TABLE IF EXISTS tb_payment_log;
DROP TABLE IF EXISTS tb_payment_methods;
DROP TABLE IF EXISTS tb_pembayaran;
DROP TABLE IF EXISTS tb_booking;
DROP TABLE IF EXISTS tb_lapangan_gallery;
DROP TABLE IF EXISTS tb_lapangan;
DROP TABLE IF EXISTS tb_admin;

COMMIT;