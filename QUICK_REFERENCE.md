# ⚡ Quick Reference Guide

**Version:** 2.0 | **Last Updated:** 2 Juni 2026

---

## 🚀 Quick Links

| Purpose | URL |
|---------|-----|
| **Homepage** | `http://localhost/project-client-php/website_booking_lapangan_futsal/` |
| **Admin Login** | `http://localhost/project-client-php/website_booking_lapangan_futsal/admin/auth/login.php` |
| **phpMyAdmin** | `http://localhost/phpmyadmin/` |
| **Tailwind Docs** | `https://tailwindcss.com` |
| **Font Awesome Icons** | `https://fontawesome.com` |

---

## 👤 Admin Credentials

```
Username: admin
Password: admin123 (change after install)
```

---

## 📁 File Locations

```
Root Project: C:\xampp\htdocs\project-client-php\website_booking_lapangan_futsal\

Homepage:           index.php
Admin Login:        admin/auth/login.php
Database Config:    config/koneksi.php
Database:           database.sql
Migration:          database_update.sql
Images Upload:      assets/images/
```

---

## 🔧 Database Setup Commands

### Create Fresh Database:
```bash
mysql -u root -p < database.sql
```

### Migrate Existing Database:
```bash
mysql -u root -p < database_update.sql
```

### Test Connection:
```bash
mysql -u root -p -e "USE db_booking_lapangan_futsal; SELECT * FROM tb_lapangan;"
```

### Generate Bcrypt Hash:
```php
echo password_hash('admin123', PASSWORD_BCRYPT);
```

---

## 🔐 SQL Queries (Quick Copy-Paste)

### Insert Admin User:
```sql
INSERT INTO tb_admin (username, password) VALUES 
('admin', '$2y$10$YourHashHere');
```

### Insert Sample Lapangan:
```sql
INSERT INTO tb_lapangan (nama, harga, status, deskripsi, rating, lokasi) VALUES 
('Lapangan A', 125000, 'tersedia', 'Lapangan indoor berkualitas', 4.86, 'Jakarta Barat'),
('Lapangan B', 100000, 'tersedia', 'Lapangan outdoor premium', 4.65, 'Jakarta Timur'),
('Lapangan C', 75000, 'tersedia', 'Lapangan indoor AC', 4.75, 'Jakarta Pusat');
```

### View All Lapangan:
```sql
SELECT id, nama, harga, status, rating, lokasi FROM tb_lapangan ORDER BY id;
```

### View With Images:
```sql
SELECT nama, harga, gambar, rating FROM tb_lapangan WHERE gambar IS NOT NULL;
```

### Check Database Size:
```sql
SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) 
FROM information_schema.tables WHERE table_schema = 'db_booking_lapangan_futsal';
```

---

## 📝 Admin Panel Features

### Manage Lapangan (`manage_lapangan.php`):
- **Tambah**: Click "Tambah Lapangan" → Modal form → Upload gambar → Save
- **Edit**: Click "Edit" on card → Update fields → Save
- **Hapus**: Click "Hapus" on card → Confirm → Delete
- **Filter**: Grid view, no filter yet (coming soon)

### Manage Booking (`manage_booking.php`):
- **View**: List all bookings
- **Edit**: Update booking status
- **Delete**: Remove booking

### Manage Konten (`manage_konten.php`):
- **View**: List all content
- **Create**: Add artikel/berita/panduan
- **Edit**: Update content
- **Delete**: Remove content

---

## 🖼️ Image Upload Guide

### Supported Formats:
- JPG / JPEG
- PNG
- Max size: 2MB

### Upload Process:
1. Admin Panel → Manage Lapangan
2. Click "Tambah Lapangan" or "Edit"
3. Select image file
4. Click "Simpan"
5. Image stored to: `assets/images/[timestamp]_[filename]`

### Troubleshooting:
```
❌ Upload not working?
✅ Check assets/images/ folder exists & writable
✅ Check file size < 2MB
✅ Check format JPG/PNG
✅ Check browser console (F12) for errors
```

---

## 🎨 Color Codes (Quick Reference)

```css
/* Primary Colors */
--emerald-600: #059669;    /* Brand, primary buttons */
--slate-900:  #0f172a;     /* Dark, sidebar, footer */
--yellow-400: #facc15;     /* Accent, CTA buttons */

/* Utility Colors */
--gray-50:    #f9fafb;     /* Background */
--gray-200:   #e5e7eb;     /* Border */
--gray-600:   #4b5563;     /* Text secondary */
--slate-900:  #0f172a;     /* Text primary */

/* Status Colors */
--green-600:  #16a34a;     /* Success, tersedia */
--red-600:    #dc2626;     /* Danger, maintenance */
```

---

## 📱 Responsive Breakpoints

```css
/* Mobile First Approach */
sm: 640px   /* Small devices */
md: 768px   /* Medium (tablet) */
lg: 1024px  /* Large (desktop) */
xl: 1280px  /* Extra large */
```

### Grid Layout:
```
Mobile:  1 column   (grid-cols-1)
Tablet:  2 columns  (md:grid-cols-2)
Desktop: 3 columns  (lg:grid-cols-3)
```

---

## 🔗 Database Relationships

```
tb_lapangan (Master)
    ↓ (1:M)
tb_booking (Detail)

tb_lapangan.id ← → tb_booking.lapangan_id
(ON DELETE CASCADE)

Example:
- Delete lapangan ID 1
- All bookings with lapangan_id = 1 also deleted
```

---

## 🧪 Testing URLs

| Test | URL |
|------|-----|
| **Homepage Load** | `http://localhost/.../` |
| **Hero Section** | Scroll to top (should see) |
| **Lapangan Cards** | Scroll down to see card grid |
| **WhatsApp Button** | Bottom right corner |
| **Admin Login** | `http://localhost/.../admin/auth/login.php` |
| **Manage Lapangan** | `http://localhost/.../admin/manage_lapangan.php` |

---

## 🐛 Common Issues & Fixes

### Issue: "Connection failed"
```
Cause: Database not running or wrong credentials
Fix: 
1. Start MySQL in XAMPP
2. Check config/koneksi.php
3. Verify database name
```

### Issue: "Image not uploading"
```
Cause: Folder permissions or file size
Fix:
1. Create assets/images/ folder
2. Set permissions to 777 (Linux/Mac)
3. Check file < 2MB
```

### Issue: "Login failed"
```
Cause: Wrong credentials or admin user missing
Fix:
1. Verify admin user in tb_admin
2. Check password is bcrypt hash
3. Clear browser cookies
```

### Issue: "Card not showing image"
```
Cause: Image path wrong or file missing
Fix:
1. Check assets/images/ folder
2. Verify image file exists
3. Check database path is correct
4. Clear browser cache (Ctrl+Shift+Del)
```

---

## 🔍 Database Query Cheat Sheet

```sql
-- Count lapangan
SELECT COUNT(*) as total FROM tb_lapangan;

-- Count booking by status
SELECT status, COUNT(*) FROM tb_booking GROUP BY status;

-- Find lapangan by name
SELECT * FROM tb_lapangan WHERE nama LIKE '%A%';

-- Get highest price lapangan
SELECT * FROM tb_lapangan ORDER BY harga DESC LIMIT 1;

-- Get lapangan with highest rating
SELECT * FROM tb_lapangan ORDER BY rating DESC LIMIT 3;

-- Get all bookings for specific lapangan
SELECT * FROM tb_booking WHERE lapangan_id = 1 ORDER BY tanggal DESC;

-- Get today's bookings
SELECT * FROM tb_booking WHERE tanggal = CURDATE();

-- Count pending bookings
SELECT COUNT(*) FROM tb_booking WHERE status = 'pending';
```

---

## 🎯 Workflow Quick Steps

### First Time Setup:
```
1. Import database.sql
2. Edit config/koneksi.php
3. Insert admin user (bcrypt)
4. Insert sample lapangan
5. Access homepage
6. Login admin panel
7. Upload lapangan images
8. Done! 🎉
```

### Adding New Lapangan:
```
1. Admin Login
2. Manage Lapangan
3. Click "Tambah Lapangan"
4. Fill form (nama, harga, status)
5. Upload image (JPG/PNG, <2MB)
6. Fill optional (deskripsi, rating, lokasi)
7. Click "Simpan"
8. Refresh homepage to see new card
```

### Editing Lapangan:
```
1. Manage Lapangan
2. Click "Edit" on card
3. Update fields
4. Upload new image (optional)
5. Click "Simpan"
```

### Deleting Lapangan:
```
1. Manage Lapangan
2. Click "Hapus" on card
3. Confirm deletion
4. Deleted from database
```

---

## 📊 File Structure Tree

```
website_booking_lapangan_futsal/
├── index.php                    (Homepage)
├── admin/
│   ├── auth/
│   │   ├── login.php
│   │   ├── logout.php
│   │   └── register.php
│   ├── dashboard.php            (Dashboard)
│   ├── manage_lapangan.php      (🆕 Upload images)
│   ├── manage_booking.php       (Manage bookings)
│   ├── manage_konten.php        (Manage content)
│   ├── sidebar.php              (Sidebar)
│   └── get_lapangan.php         (🆕 API endpoint)
├── assets/
│   ├── css/
│   │   └── tailwind.css
│   └── images/                  (🆕 Image uploads)
├── config/
│   └── koneksi.php              (Database config)
├── database.sql                 (Database schema)
├── database_update.sql          (🆕 Migration)
├── DATABASE_SCHEMA.md           (Updated)
├── PANDUAN_AKSES.md            (Updated)
├── CHANGELOG_CARD_DESIGN.md    (🆕 Changelog)
├── SETUP_GUIDE.md              (🆕 Setup)
├── IMPLEMENTATION_SUMMARY.md   (🆕 Summary)
├── QUICK_REFERENCE.md          (🆕 This file)
└── README.md                    (Updated)
```

---

## 🚨 Important Notes

⚠️ **NEVER commit to git:**
- config/koneksi.php with real credentials
- Database passwords
- Admin password hashes
- API keys (if future)

✅ **DO:**
- Change admin password after first install
- Use strong passwords
- Regular database backups
- Keep software updated

---

## 📞 Need Help?

1. **Read docs:** Check README.md or SETUP_GUIDE.md
2. **Check troubleshooting:** See this file or PANDUAN_AKSES.md
3. **Review code:** Check file comments & structure
4. **Browser console:** F12 → Console tab for JS errors
5. **Server logs:** Check XAMPP/Apache error logs

---

## ✨ Key Features Summary

| Feature | Status | Location |
|---------|--------|----------|
| Homepage | ✅ Ready | `index.php` |
| Card with Image | ✅ Ready | `index.php` |
| Admin Login | ✅ Ready | `admin/auth/login.php` |
| Manage Lapangan | ✅ Ready + Image Upload | `admin/manage_lapangan.php` |
| Manage Booking | ✅ Ready | `admin/manage_booking.php` |
| Manage Konten | ✅ Ready | `admin/manage_konten.php` |
| WhatsApp Button | ✅ Ready | `index.php` |
| Responsive Design | ✅ Ready | All pages |
| Database | ✅ Updated | 8 columns in tb_lapangan |

---

## 🎓 Learning Resources

```
Web Fundamentals:
- HTML: https://www.w3schools.com/html/
- CSS: https://www.w3schools.com/css/
- JavaScript: https://www.w3schools.com/js/

Framework & Libraries:
- Tailwind CSS: https://tailwindcss.com/docs
- Font Awesome: https://fontawesome.com/docs

Backend:
- PHP Manual: https://www.php.net/manual/
- MySQLi: https://www.php.net/manual/en/book.mysqli.php

Database:
- MySQL Docs: https://dev.mysql.com/doc/
- phpMyAdmin: https://www.phpmyadmin.net/
```

---

**Pro Tip:** Bookmark this file for quick reference during development! 🔖

---

**Version:** 2.0 | **Status:** ✅ Complete | **Date:** 2 Juni 2026
