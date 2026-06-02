# 📘 Panduan Akses - Website Booking Lapangan Futsal

## Daftar Isi
1. [Akses User](#akses-user)
2. [Akses Admin Panel](#akses-admin-panel)
3. [Fitur & Fungsi](#fitur--fungsi)
4. [Troubleshooting](#troubleshooting)

---

## 🌐 Akses User

### Halaman Utama
**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/`

Halaman utama menampilkan:
- **Header/Navbar** - Menu navigasi ke berbagai section
- **Hero Section** - Pengenalan platform dengan CTA button
- **Section Lapangan** - Daftar semua lapangan futsal tersedia
- **Section Informasi** - Keunggulan platform
- **Section Cara Booking** - Panduan 4 langkah booking
- **Section Kontak** - Informasi kontak dan form pesan
- **Footer** - Link penting dan social media

### Menu Navigasi
| Menu | Fungsi |
|------|--------|
| **Home** | Ke bagian hero section |
| **Lapangan** | Menampilkan daftar semua lapangan |
| **Booking** | Menampilkan panduan cara booking (4 langkah) |
| **Kontak** | Ke form kontak & informasi kontak |

### Fitur User

#### 1. **Melihat Daftar Lapangan**
- Scroll ke section "Daftar Lapangan Futsal"
- Setiap kartu menampilkan:
  - Nama lapangan
  - Status (Tersedia / Maintenance)
  - Harga per jam
  - Fasilitas (ukuran, pencahayaan, parkir)
  - Tombol "Booking Sekarang" (jika tersedia)

#### 2. **Status Lapangan**
```
🟢 TERSEDIA    → Lapangan bisa di-booking
🔴 MAINTENANCE → Lapangan sedang tidak tersedia
```

#### 3. **Harga & Tarif**
Harga ditampilkan dalam format Rupiah (Rp) per jam, contoh:
- Lapangan A: Rp 50.000/jam
- Lapangan B: Rp 60.000/jam

#### 4. **Hubungi Kami**
Di section kontak, user bisa:
- Mengirim pesan melalui form
- Melihat nomor telepon: **(+62) 812-3456-7890**
- Melihat email: **info@futsalbook.com**
- Melihat lokasi: **Jl. Stadion No. 123, Kota**

---

## 🔐 Akses Admin Panel

### Login Admin
**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/admin/auth/login.php`

Atau klik tombol **"Admin"** di navbar halaman utama.

### Kredensial Login

| Field | Value |
|-------|-------|
| **Username** | `admin` |
| **Password** | *(sesuai password yang diset saat setup)* |

> ⚠️ **Catatan:** Password default harus di-setup melalui database dengan bcrypt hash. Lihat [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md) untuk instruksi setup.

### Menu Admin Dashboard
Setelah login, admin akan masuk ke dashboard utama dengan sidebar berikut:

#### 1. **Dashboard**
- Menampilkan overview statistik
- Ringkasan booking terbaru
- Status lapangan
- Quick stats

#### 2. **Manage Lapangan** (`manage_lapangan.php`)
Kelola data lapangan dengan fitur lengkap:
- **Lihat Lapangan** - Grid view semua lapangan dengan preview gambar
- **Tambah Lapangan Baru** - Form input lengkap dengan upload gambar
- **Edit Lapangan** - Ubah data lapangan termasuk gambar
- **Hapus Lapangan** - Hapus lapangan dari sistem

**Field Data yang dapat diinput:**
- Nama Lapangan (required)
- Harga per Jam (required, numeric)
- Status (tersedia / maintenance)
- **Gambar Lapangan** (JPG/PNG, max 2MB) - NEW
- **Deskripsi** (penjelasan singkat lapangan) - NEW
- **Rating** (0-5, format decimal) - NEW
- **Lokasi** (kota/area) - NEW

**Display di Frontend:**
- Preview gambar lapangan
- Nama, status, harga prominently
- Rating dan lokasi
- Deskripsi singkat
- Detail fasilitas (ukuran, pencahayaan, parkir)

#### 3. **Manage Booking** (`manage_booking.php`)
Kelola data booking pelanggan:
- **Lihat Booking** - Daftar semua booking dengan detail
- **Filter Status** - Tampilkan booking: pending / confirmed / cancelled
- **Edit Status Booking** - Ubah status pemesanan
- **Hapus Booking** - Hapus record booking

**Kolom Data:**
- ID Booking
- Nama Pemesan
- Lapangan
- Tanggal & Jam
- Status (pending / confirmed / cancelled)
- Aksi (Edit / Hapus)

#### 4. **Manage Konten** (`manage_konten.php`)
Kelola konten informasi:
- **Lihat Konten** - Daftar semua artikel/berita/panduan
- **Tambah Konten** - Buat konten baru
- **Edit Konten** - Ubah konten existing
- **Hapus Konten** - Hapus konten

**Tipe Konten:**
- **Artikel** - Konten edukatif
- **Berita** - Berita terkini
- **Panduan** - Panduan penggunaan

**Kolom Data:**
- Judul
- Tipe Konten
- Tanggal Dibuat
- Aksi (Edit / Hapus)

#### 5. **Logout** 
Keluar dari admin panel dan kembali ke halaman utama.

---

## 🎯 Fitur & Fungsi

### Fitur Admin

#### Manage Lapangan
```
[Tambah] → Form Input → Database (tb_lapangan)
[Edit]   → Update Data → Database
[Hapus]  → Konfirmasi → Database Deleted
```

**Field Input:**
- Nama Lapangan (required)
- Harga per Jam (required, numeric)
- Status (dropdown: tersedia / maintenance)

#### Manage Booking
```
[Lihat] → List Semua Booking
[Edit]  → Update Status (pending → confirmed → cancelled)
[Hapus] → Delete Booking
```

**Workflow Booking:**
1. User booking → Status: **pending**
2. Admin approve → Status: **confirmed**
3. Atau cancel → Status: **cancelled**

#### Manage Konten
```
[Tambah] → Form Input (judul, isi, tipe) → Database (tb_konten)
[Edit]   → Update Konten
[Hapus]  → Delete Konten
```

**Tipe Konten untuk ditampilkan:**
- Tipe `panduan` = Ditampilkan di hero section
- Tipe `berita` = Bisa ditampilkan di section berita (future)
- Tipe `artikel` = Bisa ditampilkan di blog (future)

### Fitur User (Frontend)

#### Melihat Lapangan
- ✅ Melihat daftar lapangan
- ✅ Filter berdasarkan status
- ✅ Melihat harga & detail fasilitas
- ✅ Melihat info kontak

#### Booking Lapangan
*(Feature in development)*
- 🔄 Form booking (nama, tanggal, jam mulai, jam selesai)
- 🔄 Validasi ketersediaan jam
- 🔄 Konfirmasi booking

#### Hubungi Admin
- ✅ Form pesan kontak
- ✅ Info telepon & email
- ✅ Peta lokasi (future)

---

## 📊 Alur Data

### Alur Booking
```
User (Frontend)
    ↓
    Lihat Lapangan (dari tb_lapangan)
    ↓
    Klik "Booking Sekarang"
    ↓
    Isi Form Booking
    ↓
    Submit → Database (tb_booking, status: pending)
    ↓
Admin (Dashboard)
    ↓
    Review Booking di "Manage Booking"
    ↓
    Update Status → confirmed / cancelled
    ↓
User → Notifikasi Status
```

### Alur Manage Lapangan
```
Admin Input/Edit Lapangan
    ↓
Disimpan ke Database (tb_lapangan)
    ↓
Frontend otomatis menampilkan lapangan terbaru
    ↓
User bisa lihat & booking
```

---

## 🔧 Sistem File

```
website_booking_lapangan_futsal/
├── index.php                    # Halaman utama user
├── admin/
│   ├── auth/
│   │   ├── login.php           # Login admin
│   │   ├── logout.php          # Logout admin
│   │   └── register.php        # Register admin (optional)
│   ├── dashboard.php           # Dashboard utama
│   ├── manage_lapangan.php     # CRUD lapangan
│   ├── manage_booking.php      # CRUD booking
│   ├── manage_konten.php       # CRUD konten
│   └── sidebar.php             # Sidebar admin
├── config/
│   └── koneksi.php             # Database connection
├── assets/
│   └── css/
│       └── tailwind.css        # Tailwind CSS
└── database.sql                # Database schema
```

---

## 🚀 Setup & Instalasi

### 1. Setup Database
```bash
# Import database
mysql -u root -p < database.sql

# Atau manual:
# 1. Buka phpMyAdmin
# 2. Create database: db_booking_lapangan_futsal
# 3. Import file: database.sql
```

### 2. Konfigurasi Koneksi
Edit file `config/koneksi.php`:
```php
$host = 'localhost';
$user = 'root';
$password = '';          // Sesuaikan password XAMPP Anda
$db = 'db_booking_lapangan_futsal';
```

### 4. Konfigurasi Koneksi
Edit file `config/koneksi.php`:
```php
$host = 'localhost';
$user = 'root';
$password = '';          // Sesuaikan password XAMPP Anda
$db = 'db_booking_lapangan_futsal';
```

### 5. Setup Folder Assets
Buat folder untuk menyimpan gambar lapangan:
```bash
mkdir -p assets/images
chmod 777 assets/images  # (Linux/Mac) berikan permission write
```

Di Windows, folder akan otomatis dibuat saat upload pertama.

### 6. Setup Admin User
```sql
-- Hash password 'admin' dengan bcrypt
-- Gunakan: password_hash('admin123', PASSWORD_BCRYPT)
-- Atau tools online: https://www.bcryptcalculator.com

INSERT INTO tb_admin (username, password) VALUES 
('admin', '$2y$10$abcdefghijklmnopqrstuvwxyz');
```

### 7. Insert Sample Data Lapangan
```sql
INSERT INTO tb_lapangan (nama, harga, status, deskripsi, rating, lokasi, gambar) VALUES 
('Lapangan A', 125000, 'tersedia', 'Lapangan indoor dengan pencahayaan standar', 4.86, 'Jakarta Barat', 'assets/images/lapangan-a.jpg'),
('Lapangan B', 100000, 'tersedia', 'Lapangan outdoor berkualitas internasional', 4.65, 'Jakarta Timur', 'assets/images/lapangan-b.jpg'),
('Lapangan C', 75000, 'tersedia', 'Lapangan indoor dengan fasilitas premium', 4.75, 'Jakarta Pusat', 'assets/images/lapangan-c.jpg');
```

### 8. Akses Website
- **User:** http://localhost/project-client-php/website_booking_lapangan_futsal/
- **Admin Login:** http://localhost/project-client-php/website_booking_lapangan_futsal/admin/auth/login.php

---

## ❓ Troubleshooting

### Error: "Connection failed"
**Solusi:**
- Pastikan XAMPP MySQL sudah running
- Cek konfigurasi di `config/koneksi.php`
- Cek nama database di phpMyAdmin

### Error: "Table not found"
**Solusi:**
- Import ulang file `database.sql`
- Pastikan database sudah di-create

### Error: "Login failed"
**Solusi:**
- Pastikan username: `admin` sudah ada di `tb_admin`
- Pastikan password sudah di-hash dengan bcrypt
- Cek kembali username/password yang diinput

### Error: "Lapangan tidak muncul"
**Solusi:**
- Pastikan ada data di `tb_lapangan`
- Insert sample data melalui phpmyadmin
- Refresh halaman browser (Ctrl+F5)

### Stylesheet/CSS tidak loading
**Solusi:**
- Pastikan CDN Tailwind CSS bisa diakses (internet aktif)
- Atau setup Tailwind CSS lokal jika offline

---

## 📱 Responsivitas

Website sudah dioptimalkan untuk:
- ✅ Mobile (320px - 640px)
- ✅ Tablet (641px - 1024px)
- ✅ Desktop (1025px+)

### Breakpoint Tailwind
- `grid-cols-1` - Mobile
- `md:grid-cols-2` - Tablet
- `lg:grid-cols-3` - Desktop

---

## 📞 Support & Bantuan

Jika ada kendala atau pertanyaan, silakan:
1. Cek file `README.md` untuk informasi tambahan
2. Lihat `DATABASE_SCHEMA.md` untuk struktur database
3. Review file `design.md` untuk design system
4. Hubungi developer / admin

---

**Versi:** 1.0  
**Last Updated:** 2 Juni 2026  
**Status:** Active
