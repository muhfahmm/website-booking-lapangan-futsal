# Website Booking Lapangan Futsal

Aplikasi web booking lapangan futsal dengan desain modern, fitur admin lengkap, dan gambar lapangan.

**Version:** 2.0 | **Status:** Active | **Last Updated:** 2 Juni 2026

---

## 🎯 Deskripsi

Platform booking lapangan futsal online yang memudahkan user untuk:
- Melihat daftar lapangan dengan gambar preview
- Melihat harga, rating, dan lokasi lapangan
- Booking lapangan futsal dengan mudah
- Chat langsung via WhatsApp

Admin dapat:
- Manage lapangan (tambah, edit, hapus)
- Upload gambar lapangan
- Manage booking requests
- Manage konten

## ✨ Features

### Frontend (User)
✅ Halaman utama responsif dengan hero section  
✅ Daftar lapangan dengan gambar preview  
✅ Display harga, rating, lokasi, fasilitas  
✅ Navbar sticky dengan smooth scroll  
✅ Section "Cara Booking" dengan 4 langkah  
✅ Form kontak lengkap  
✅ Floating WhatsApp button untuk chat langsung  
✅ Footer dengan link sosial media  
✅ Responsive di mobile/tablet/desktop  

### Backend (Admin)
✅ Admin dashboard dengan sidebar navigation  
✅ Kelola Lapangan: add/edit/delete dengan upload gambar  
✅ Kelola Booking: view/edit status/delete  
✅ Kelola Konten: add/edit/delete (artikel/berita/panduan)  
✅ Session-based authentication  
✅ Modal forms yang user-friendly  

### Database
✅ MySQL database dengan 4 tables  
✅ Foreign key relationships  
✅ Default sample data terinclude  
✅ Support untuk gambar, rating, lokasi  

---

## 📁 Struktur Project

```
website_booking_lapangan_futsal/
├── admin/
│   ├── auth/
│   │   ├── login.php          # Admin login page
│   │   ├── logout.php         # Logout action
│   │   └── register.php       # Register (optional)
│   ├── dashboard.php          # Admin dashboard
│   ├── manage_lapangan.php    # CRUD lapangan + gambar
│   ├── manage_booking.php     # CRUD booking
│   ├── manage_konten.php      # CRUD konten
│   ├── sidebar.php            # Sidebar navigation
│   └── get_lapangan.php       # API untuk fetch data
├── assets/
│   ├── css/
│   │   └── tailwind.css       # Tailwind CDN
│   └── images/                # Folder upload gambar lapangan
├── config/
│   └── koneksi.php            # Database connection config
├── index.php                  # Homepage
├── database.sql               # Database schema + sample data
├── database_update.sql        # Migration (4 kolom baru)
├── DATABASE_SCHEMA.md         # Database dokumentasi
├── PANDUAN_AKSES.md          # User & admin guide
├── CHANGELOG_CARD_DESIGN.md  # Perubahan design v2.0
├── SETUP_GUIDE.md            # Panduan setup lengkap
└── README.md                 # File ini
```

---

## 🚀 Quick Start

### Prerequisites
- XAMPP (Apache + MySQL + PHP 7.4+)
- MySQL running
- Modern web browser

### Installation

**1. Setup Database**
```bash
# Import database baru
mysql -u root -p < database.sql

# Atau update existing:
mysql -u root -p < database_update.sql
```

**2. Configure Connection**
Edit `config/koneksi.php`:
```php
$host = 'localhost';
$user = 'root';
$password = '';              // Sesuaikan XAMPP Anda
$db = 'db_booking_lapangan_futsal';
```

**3. Setup Admin User**
Di phpMyAdmin atau CLI:
```sql
INSERT INTO tb_admin (username, password) VALUES 
('admin', '$2y$10$YourBcryptHashHere');
```

**4. Create Image Folder**
Folder `assets/images/` akan auto-created saat upload pertama.

**5. Access Website**
- User: `http://localhost/project-client-php/website_booking_lapangan_futsal/`
- Admin: `http://localhost/project-client-php/website_booking_lapangan_futsal/admin/auth/login.php`

---

## 🎨 Design System

### Color Palette
- **Primary (Emerald):** `#059669` - Brand color, buttons
- **Secondary (Slate):** `#0f172a` - Sidebar, footer, dark
- **Accent (Yellow):** `#facc15` - CTA buttons
- **Surface:** `#f9fafb` - Main background
- **Neutral (Gray):** `#e5e7eb` - Borders, subtle

### Typography
- **Headings:** Inter / Montserrat (Bold)
- **Body:** Inter / Roboto (Regular, high readability)

### Icons
- Font Awesome 6.4.0 (Outline style)

---

## 📊 Database Schema

### tb_lapangan (Lapangan)
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| nama | VARCHAR(100) | Nama lapangan |
| harga | INT | Harga per jam (Rp) |
| status | ENUM | tersedia / maintenance |
| gambar | VARCHAR(255) | Path gambar |
| deskripsi | TEXT | Deskripsi singkat |
| rating | DECIMAL(3,2) | Rating (0-5) |
| lokasi | VARCHAR(150) | Lokasi area |

### tb_booking (Booking)
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| lapangan_id | INT | FK ke tb_lapangan |
| nama_pemesan | VARCHAR(100) | Nama pemesan |
| tanggal | DATE | Tanggal booking |
| jam_mulai | TIME | Jam mulai |
| jam_selesai | TIME | Jam selesai |
| status | ENUM | pending/confirmed/cancelled |

### tb_konten (Konten)
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| judul | VARCHAR(200) | Judul konten |
| isi | TEXT | Isi/deskripsi |
| tipe | ENUM | artikel/berita/panduan |
| dibuat_pada | TIMESTAMP | Waktu dibuat |

### tb_admin (Admin)
| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| username | VARCHAR(50) | Username login |
| password | VARCHAR(255) | Bcrypt password hash |

---

## 🔐 Admin Credentials

Default:
- **Username:** `admin`
- **Password:** `admin123` (sesuaikan setelah install)

Generate bcrypt hash:
- Online tool: https://www.bcryptcalculator.com/
- PHP: `echo password_hash('password', PASSWORD_BCRYPT);`

---

## 📱 Responsive Design

- **Mobile:** 1 column, full-width
- **Tablet:** 2 columns, balanced
- **Desktop:** 3 columns, optimal

Tested on:
- ✅ iPhone 12/13
- ✅ iPad
- ✅ Android devices
- ✅ Desktop browsers

---

## 🆕 What's New in v2.0

### Card Design Upgrade
✅ Minimalis design dengan gambar preview  
✅ Gambar lapangan di top card  
✅ Icon badge emerald di corner  
✅ Rating & lokasi display  
✅ Better visual hierarchy  

### Database Enhancements
✅ 4 kolom baru: gambar, deskripsi, rating, lokasi  
✅ Backward compatible dengan existing data  
✅ Support untuk future features  

### Admin Panel Improvements
✅ New Manage Lapangan page dengan image upload  
✅ Modal form yang lebih user-friendly  
✅ Real-time image preview  
✅ Grid view untuk semua lapangan  

### Documentation
✅ SETUP_GUIDE.md - Panduan lengkap setup  
✅ CHANGELOG_CARD_DESIGN.md - Detail perubahan  
✅ Updated PANDUAN_AKSES.md  
✅ Updated DATABASE_SCHEMA.md  

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3 (Tailwind), JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Server:** Apache (XAMPP)
- **Icons:** Font Awesome 6
- **UI Components:** Tailwind CSS

---

## 📖 Documentation

- **PANDUAN_AKSES.md** - Panduan user & admin access
- **SETUP_GUIDE.md** - Detailed setup instructions
- **DATABASE_SCHEMA.md** - Database structure documentation
- **CHANGELOG_CARD_DESIGN.md** - Design upgrade details
- **design.md** - Design system specifications
- **layout/user_interfaces.md** - UI requirements

---

## 🐛 Known Issues & Limitations

### Current:
- Booking form belum fully functional (in development)
- Konten form integration belum di admin panel
- No email notifications (future)
- No payment gateway (future)

### Future Enhancements:
- [ ] Complete booking system dengan payment
- [ ] Email notifications
- [ ] User account & booking history
- [ ] Search & filter lapangan
- [ ] Review & rating dari user
- [ ] Admin dashboard statistics
- [ ] Export data to Excel/PDF

---

## 🆘 Troubleshooting

### Database Connection Error
1. Ensure MySQL service running
2. Check credentials di `config/koneksi.php`
3. Verify database exists

### Image Upload Not Working
1. Check `assets/images/` folder exists
2. Verify folder is writable
3. Check file size < 2MB
4. Verify JPG/PNG format

### Login Failed
1. Verify admin user exists di tb_admin
2. Check password hash is valid bcrypt
3. Clear browser cookies & try again

See **SETUP_GUIDE.md** untuk troubleshooting lengkap.

---

## 📞 Support & Contribution

- Issues: Check troubleshooting section
- Questions: Review documentation files
- Contributions: Fork & submit pull requests

---

## 📄 Lisensi

Proyek ini terbuka untuk digunakan dan dimodifikasi. Beri kredit jika digunakan ulang.

---

## 👨‍💻 Author

**Created:** 2 Juni 2026  
**Version:** 2.0  
**Status:** Active Development

---

**Ready to book? Let's go! 🚀⚽**
