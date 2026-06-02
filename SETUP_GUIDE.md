# 🚀 Setup Guide - Website Booking Lapangan Futsal v2.0

**Last Updated:** 2 Juni 2026

---

## 📋 Prerequisites

- XAMPP (Apache + MySQL + PHP 7.4+)
- MySQL running & accessible via phpMyAdmin
- Web browser (Chrome, Firefox, Safari, Edge)

---

## 🔧 Step-by-Step Setup

### Step 1: Database Setup

#### Option A: Menggunakan database.sql baru
```bash
# Ganti isi database.sql dengan file baru, lalu import:
cd c:\xampp\htdocs\project-client-php\website_booking_lapangan_futsal
mysql -u root -p < database.sql
```

#### Option B: Update existing database
Jika Anda sudah punya database, jalankan migration:

```bash
mysql -u root -p < database_update.sql
```

**Atau manual di phpMyAdmin:**
1. Buka phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `db_booking_lapangan_futsal`
3. Buka tab "SQL"
4. Copy paste query di `database_update.sql`
5. Klik Execute

---

### Step 2: Konfigurasi Koneksi Database

Edit file: `config/koneksi.php`

```php
<?php
$host = 'localhost';
$user = 'root';
$password = '';              // Sesuaikan dengan password XAMPP Anda
$db = 'db_booking_lapangan_futsal';

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
```

**Test Connection:**
- Akses: `http://localhost/project-client-php/website_booking_lapangan_futsal/`
- Jika error "Connection failed", periksa konfigurasi di atas

---

### Step 3: Setup Admin User

Di phpMyAdmin, jalankan query:

```sql
-- Generate password hash: password_hash('admin123', PASSWORD_BCRYPT)
-- Atau gunakan tools: https://www.bcryptcalculator.com/

INSERT INTO tb_admin (username, password) VALUES 
('admin', '$2y$10$YOu.Should.Replace.This.With.Real.Hash');
```

**Alternatif: Generate hash dengan PHP**
```php
echo password_hash('admin123', PASSWORD_BCRYPT);
// Output: $2y$10$...hash...
```

---

### Step 4: Insert Sample Data (Optional)

```sql
INSERT INTO tb_lapangan (nama, harga, status, deskripsi, rating, lokasi) VALUES 
('Lapangan A', 125000, 'tersedia', 'Lapangan indoor dengan pencahayaan standar', 4.86, 'Jakarta Barat'),
('Lapangan B', 100000, 'tersedia', 'Lapangan outdoor berkualitas internasional', 4.65, 'Jakarta Timur'),
('Lapangan C', 75000, 'tersedia', 'Lapangan indoor dengan fasilitas premium', 4.75, 'Jakarta Pusat');
```

---

### Step 5: Verify File Structure

```
website_booking_lapangan_futsal/
├── admin/
│   ├── auth/
│   │   ├── login.php
│   │   ├── logout.php
│   │   └── register.php
│   ├── dashboard.php
│   ├── manage_lapangan.php    ← BARU
│   ├── manage_booking.php
│   ├── manage_konten.php
│   ├── sidebar.php
│   └── get_lapangan.php       ← BARU
├── assets/
│   ├── css/
│   │   └── tailwind.css
│   └── images/                ← FOLDER BARU (auto-created)
├── config/
│   └── koneksi.php
├── index.php
├── database.sql               ← UPDATED
├── database_update.sql        ← BARU
├── DATABASE_SCHEMA.md         ← UPDATED
├── PANDUAN_AKSES.md          ← UPDATED
├── CHANGELOG_CARD_DESIGN.md  ← BARU
└── SETUP_GUIDE.md            ← File ini
```

---

## 🌐 Accessing the Website

### User Side:
```
http://localhost/project-client-php/website_booking_lapangan_futsal/
```

**Features:**
- View semua lapangan dengan gambar
- Lihat detail: harga, rating, lokasi
- Lihat info kontak
- Chat via WhatsApp

### Admin Side:
```
http://localhost/project-client-php/website_booking_lapangan_futsal/admin/
```

**Login Credentials:**
- Username: `admin`
- Password: `admin123` (atau sesuai yang Anda set)

---

## 📸 Admin Panel - How to Upload Gambar

### Steps:
1. Login ke admin panel
2. Klik "Kelola Lapangan"
3. Klik tombol "Tambah Lapangan" (atau Edit existing)
4. Isi form:
   - Nama Lapangan
   - Harga per Jam
   - Status (Tersedia/Maintenance)
   - **Gambar Lapangan** ← Upload di sini (JPG/PNG max 2MB)
   - Deskripsi (optional)
   - Rating (0-5)
   - Lokasi
5. Klik "Simpan"

### Supported Formats:
- JPG / JPEG
- PNG
- Max file size: 2MB

### Image Storage:
Gambar disimpan di: `assets/images/[timestamp]_[filename]`

---

## ✅ Testing Checklist

### Frontend (User Page):
- [ ] Halaman load tanpa error
- [ ] Navbar sticky saat scroll
- [ ] Hero section menampilkan dengan benar
- [ ] Card lapangan menampilkan gambar
- [ ] Status badge (Tersedia/Maintenance) terlihat
- [ ] Harga, rating, lokasi terlihat jelas
- [ ] Button "Booking Sekarang" responsive
- [ ] WhatsApp floating button muncul
- [ ] Footer links berfungsi
- [ ] Responsive di mobile/tablet/desktop

### Admin Panel:
- [ ] Login berhasil dengan credentials yang benar
- [ ] Sidebar navigation berfungsi
- [ ] Manage Lapangan page load
- [ ] Grid view menampilkan lapangan dengan gambar
- [ ] Button "Tambah Lapangan" membuka modal
- [ ] Upload gambar bekerja
- [ ] Edit lapangan bekerja
- [ ] Delete lapangan bekerja (dengan konfirmasi)
- [ ] Form validation berfungsi
- [ ] Logout button berfungsi

### Database:
- [ ] Connection test successful
- [ ] tb_lapangan memiliki 8 columns (termasuk 4 baru)
- [ ] Sample data terinsert dengan benar
- [ ] Query SELECT * FROM tb_lapangan menampilkan data
- [ ] Images folder writable (dapat menerima file upload)

---

## 🐛 Troubleshooting

### Error: "Connection failed"
**Solusi:**
1. Pastikan MySQL service running (XAMPP Control Panel)
2. Cek database name di `config/koneksi.php`
3. Cek username/password sesuai XAMPP setup
4. Test manual di phpMyAdmin

### Error: "Table not found"
**Solusi:**
1. Import ulang `database.sql`
2. Atau jalankan migration `database_update.sql`
3. Verify database structure di phpMyAdmin

### Error: "Login failed"
**Solusi:**
1. Verify admin user sudah di insert ke tb_admin
2. Pastikan password hash sesuai format bcrypt
3. Test ulang login dengan username: admin

### Error: "Gambar tidak upload"
**Solusi:**
1. Cek folder `assets/images/` ada dan writable
2. Cek file size < 2MB
3. Cek format JPG/PNG
4. Cek permission folder (Windows biasanya auto OK)

### Gambar tidak tampil di frontend:
**Solusi:**
1. Verify path di database: `SELECT gambar FROM tb_lapangan`
2. Cek file fisik ada di `assets/images/`
3. Refresh browser cache (Ctrl+Shift+Del)
4. Check browser console untuk error

### CSS/Tailwind tidak loading:
**Solusi:**
1. Pastikan internet connection aktif (CDN Tailwind)
2. Atau setup Tailwind CSS lokal
3. Clear browser cache
4. Try different browser

---

## 📱 Responsive Design

### Mobile (< 640px):
- 1 column card layout
- Button full width
- Font sizes adjusted
- Touch-friendly spacing

### Tablet (640px - 1024px):
- 2 column card layout
- Balanced spacing
- Readable text sizes

### Desktop (> 1024px):
- 3 column card layout
- Optimal spacing
- Premium feel

---

## 🔒 Security Notes

### Important:
1. **Never commit sensitive files:**
   - `.env` dengan credentials (if future)
   - Password hashes (keep bcrypt)
   - API keys (if future)

2. **File Upload Security:**
   - Only allow image formats (JPG/PNG)
   - Max file size validation (2MB)
   - Rename files dengan timestamp
   - Store outside web root (if future enhancement)

3. **SQL Injection Prevention:**
   - Use prepared statements (future)
   - Sanitize user inputs (future)

4. **Admin Panel:**
   - Use session-based auth
   - Implement CSRF tokens (future)
   - Rate limiting on login (future)

---

## 📚 Additional Resources

- [Tailwind CSS Docs](https://tailwindcss.com)
- [Font Awesome Icons](https://fontawesome.com)
- [PHP MySQLi Docs](https://www.php.net/manual/en/book.mysqli.php)
- [bcrypt Hash Generator](https://www.bcryptcalculator.com)

---

## 🆘 Getting Help

1. Check `PANDUAN_AKSES.md` untuk panduan penggunaan
2. Check `CHANGELOG_CARD_DESIGN.md` untuk detail perubahan
3. Check `DATABASE_SCHEMA.md` untuk struktur database
4. Check browser console (F12) untuk error messages

---

## 📞 Contact & Support

**Issues atau Pertanyaan:**
1. Verify semua steps di panduan ini sudah diikuti
2. Check troubleshooting section
3. Verify file structure benar
4. Contact development team jika masalah persist

---

**Version:** 2.0  
**Status:** Ready for Production  
**Last Updated:** 2 Juni 2026

---

**Happy Booking! 🎉**
