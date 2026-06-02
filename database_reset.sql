-- =====================================================
-- Reset Database Script
-- Website Booking Lapangan Futsal
-- =====================================================
-- WARNING: Hati-hati! Script ini akan MENGHAPUS semua data
-- Gunakan hanya saat ingin reset database sepenuhnya
-- =====================================================

-- Drop tables dengan urutan yang benar (mempertimbangkan foreign key)
-- Urutan: tabel yang memiliki foreign key didrop terlebih dahulu

DROP TABLE IF EXISTS tb_payment_log;
DROP TABLE IF EXISTS tb_payment_methods;
DROP TABLE IF EXISTS tb_pembayaran;
DROP TABLE IF EXISTS tb_booking;
DROP TABLE IF EXISTS tb_lapangan_gallery;
DROP TABLE IF EXISTS tb_lapangan;
DROP TABLE IF EXISTS tb_konten;
DROP TABLE IF EXISTS tb_admin;

-- Setelah menjalankan script ini, jalankan database.sql untuk re-create structure

COMMIT;