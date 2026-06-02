-- Migration Script untuk Database v3
-- Menambahkan harga weekend dan gallery table

-- 1. Tambah kolom harga_weekend ke tb_lapangan (jika belum ada)
ALTER TABLE tb_lapangan ADD COLUMN harga_weekend INT DEFAULT 0 AFTER harga;

-- 2. Buat tabel gallery jika belum ada
CREATE TABLE IF NOT EXISTS tb_lapangan_gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lapangan_id INT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0,
    dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lapangan_id) REFERENCES tb_lapangan(id) ON DELETE CASCADE,
    INDEX idx_lapangan_id (lapangan_id)
);

-- 3. Update sample data dengan harga weekend
UPDATE tb_lapangan SET harga_weekend = 150000 WHERE id = 1;
UPDATE tb_lapangan SET harga_weekend = 120000 WHERE id = 2;
UPDATE tb_lapangan SET harga_weekend = 110000 WHERE id = 3;

-- Selesai! Database sekarang support:
-- - Harga weekday (harga column)
-- - Harga weekend (harga_weekend column)
-- - Multiple photos per lapangan (tb_lapangan_gallery table)
