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
| gambar | VARCHAR(255) | DEFAULT NULL | Path/URL gambar lapangan |
| deskripsi | TEXT | DEFAULT NULL | Deskripsi singkat lapangan (untuk card) |
| deskripsi_lengkap | TEXT | DEFAULT NULL | Deskripsi lengkap (untuk halaman detail) |
| fasilitas | TEXT | DEFAULT NULL | Daftar fasilitas (dipisahkan koma) |
| rating | DECIMAL(3,2) | DEFAULT 4.5 | Rating lapangan (0-5) |
| lokasi | VARCHAR(150) | DEFAULT 'Jakarta' | Lokasi lapangan |
| ukuran | VARCHAR(50) | DEFAULT '40m x 20m' | Ukuran lapangan |
| pencahayaan | VARCHAR(100) | DEFAULT 'Standar' | Tipe pencahayaan |
| parkir | VARCHAR(100) | DEFAULT 'Tersedia' | Informasi parkir |
| tipe_lantai | VARCHAR(100) | DEFAULT 'Rumput Sintetis' | Tipe lantai lapangan |

**Sample Data:**
```sql
INSERT INTO tb_lapangan (nama, harga, status, deskripsi, deskripsi_lengkap, fasilitas, rating, lokasi, ukuran, pencahayaan, parkir, tipe_lantai) VALUES 
('Lapangan A', 125000, 'tersedia', 'Lapangan indoor dengan pencahayaan standar', 'Lapangan A adalah lapangan futsal indoor premium...', 'AC Central, Toilet & Kamar Mandi, Ruang Tunggu, WiFi', 4.86, 'Jakarta Barat', '40m x 20m', 'LED Modern', 'Tersedia (100+ spot)', 'Rumput Sintetis Premium'),
('Lapangan B', 100000, 'tersedia', 'Lapangan outdoor berkualitas internasional', 'Lapangan B adalah lapangan futsal outdoor terbesar...', 'Pencahayaan Profesional, Tribun, Kantor, Area Istirahat', 4.65, 'Jakarta Timur', '45m x 25m', 'Profesional High-Mast', 'Tersedia (150+ spot)', 'Rumput Sintetis Internasional'),
('Lapangan C', 75000, 'tersedia', 'Lapangan indoor dengan AC dan fasilitas premium', 'Lapangan C menawarkan pengalaman bermain yang nyaman...', 'AC Pendingin, Kamar Ganti, Penyewaan Bola, WiFi Gratis', 4.75, 'Jakarta Pusat', '35m x 18m', 'Standar Plus', 'Tersedia (80+ spot)', 'Rumput Sintetis Standar');
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
