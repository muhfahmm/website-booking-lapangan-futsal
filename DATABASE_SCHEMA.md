# Database Schema - Website Booking Lapangan Futsal

## Overview
Database: `db_booking_lapangan_futsal`

---

## 📋 Table: tb_admin
| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | ID Admin |
| username | VARCHAR(50) | NOT NULL | Username untuk login |
| password | VARCHAR(255) | NOT NULL | Password (bcrypt hash) |

**Sample Data:**
```sql
INSERT INTO tb_admin (username, password) VALUES 
('admin', '$2y$10$...hash...');
```

---

## ⚽ Table: tb_lapangan
| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | ID Lapangan |
| nama | VARCHAR(100) | NOT NULL | Nama Lapangan |
| harga | INT | DEFAULT 0 | Harga per jam (Rupiah) |
| status | ENUM | DEFAULT 'tersedia' | Status: tersedia / maintenance |

**Sample Data:**
```sql
INSERT INTO tb_lapangan (nama, harga, status) VALUES 
('Lapangan A', 50000, 'tersedia'),
('Lapangan B', 60000, 'tersedia'),
('Lapangan C', 50000, 'maintenance');
```

---

## 📅 Table: tb_booking
| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | ID Booking |
| lapangan_id | INT | NOT NULL, FOREIGN KEY | Reference ke tb_lapangan |
| nama_pemesan | VARCHAR(100) | NOT NULL | Nama Pemesan |
| tanggal | DATE | NOT NULL | Tanggal Booking |
| jam_mulai | TIME | NOT NULL | Jam Mulai |
| jam_selesai | TIME | NOT NULL | Jam Selesai |
| status | ENUM | DEFAULT 'pending' | Status: pending / confirmed / cancelled |

**Sample Data:**
```sql
INSERT INTO tb_booking (lapangan_id, nama_pemesan, tanggal, jam_mulai, jam_selesai, status) VALUES 
(1, 'Budi Santoso', '2026-06-05', '18:00', '19:00', 'confirmed'),
(2, 'Ahmad Ridho', '2026-06-05', '19:00', '21:00', 'pending');
```

---

## 📝 Table: tb_konten
| Column | Type | Constraint | Description |
|--------|------|-----------|-------------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | ID Konten |
| judul | VARCHAR(200) | NOT NULL | Judul Konten |
| isi | TEXT | NOT NULL | Isi/Deskripsi Konten |
| tipe | ENUM | DEFAULT 'artikel' | Tipe: artikel / berita / panduan |
| dibuat_pada | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Waktu dibuat |

**Sample Data:**
```sql
INSERT INTO tb_konten (judul, isi, tipe) VALUES 
('Panduan Booking', 'Silakan hubungi admin untuk booking...', 'panduan'),
('Promo Musim Panas', 'Dapatkan diskon hingga 20%...', 'berita'),
('Teknik Futsal', 'Berikut adalah teknik dasar futsal...', 'artikel');
```

---

## 🔑 Foreign Key Relationships
```
tb_booking.lapangan_id → tb_lapangan.id
(ON DELETE CASCADE - jika lapangan dihapus, booking terkait juga terhapus)
```

---

## ✅ Setup Instructions
1. Buat database dan import schema:
   ```sql
   mysql -u root -p < database.sql
   ```

2. Cek koneksi di `config/koneksi.php` dengan:
   - Host: localhost
   - User: root
   - Password: (sesuai konfigurasi XAMPP)
   - Database: db_booking_lapangan_futsal

3. Insert admin user untuk login:
   ```sql
   INSERT INTO tb_admin (username, password) VALUES ('admin', '$2y$10$...');
   ```
   (Gunakan bcrypt hash dari password yang Anda inginkan)
