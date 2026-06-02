# Setup Sample Images & Database Data
## Panduan Lengkap untuk Testing Multiple Image Upload

**Version:** 2.2.0  
**Created:** June 2, 2026  
**Status:** ✅ Ready to Use

---

## 🎯 Tujuan

Dokumen ini menjelaskan cara setup sample images dan data untuk testing fitur multiple image upload yang baru.

---

## 📋 Step-by-Step Setup

### Step 1: Generate Sample Images

**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/generate_sample_images.php`

**Apa yang dilakukan:**
- Generate 3 main lapangan images (800x600px)
- Generate 9 gallery images (3 per lapangan)
- Simpan ke folder `uploads/lapangan/` dan `uploads/gallery/`

**Hasil:**
```
uploads/
├── lapangan/
│   ├── sample_lapangan_a.jpg
│   ├── sample_lapangan_b.jpg
│   └── sample_lapangan_c.jpg
├── gallery/
│   ├── sample_gallery_1_1.jpg
│   ├── sample_gallery_1_2.jpg
│   ├── sample_gallery_1_3.jpg
│   ├── sample_gallery_2_1.jpg
│   ├── sample_gallery_2_2.jpg
│   ├── sample_gallery_2_3.jpg
│   ├── sample_gallery_3_1.jpg
│   ├── sample_gallery_3_2.jpg
│   └── sample_gallery_3_3.jpg
└── .gitkeep
```

### Step 2: Insert Sample Data ke Database

**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/setup_sample_data.php`

**Apa yang dilakukan:**
- Clear data existing (tb_lapangan dan tb_lapangan_gallery)
- Insert 3 sample lapangan:
  - **Lapangan A**: Rp 100k (weekday) / 150k (weekend)
  - **Lapangan B**: Rp 80k (weekday) / 120k (weekend)
  - **Lapangan C**: Rp 75k (weekday) / 110k (weekend)
- Insert 3 gallery images per lapangan
- Link images dengan path yang benar

**Data yang diinsert:**
```
Lapangan A - Indoor Premium
├── Main image: uploads/lapangan/sample_lapangan_a.jpg
├── Gallery 1: uploads/gallery/sample_gallery_1_1.jpg
├── Gallery 2: uploads/gallery/sample_gallery_1_2.jpg
└── Gallery 3: uploads/gallery/sample_gallery_1_3.jpg

Lapangan B - Outdoor Internasional
├── Main image: uploads/lapangan/sample_lapangan_b.jpg
├── Gallery 1: uploads/gallery/sample_gallery_2_1.jpg
├── Gallery 2: uploads/gallery/sample_gallery_2_2.jpg
└── Gallery 3: uploads/gallery/sample_gallery_2_3.jpg

Lapangan C - Casual Comfort
├── Main image: uploads/lapangan/sample_lapangan_c.jpg
├── Gallery 1: uploads/gallery/sample_gallery_3_1.jpg
├── Gallery 2: uploads/gallery/sample_gallery_3_2.jpg
└── Gallery 3: uploads/gallery/sample_gallery_3_3.jpg
```

### Step 3: Verify di Admin Panel

**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/admin/dashboard.php`

**Yang harus dilihat:**
1. Klik tab **"Kelola Lapangan"**
2. Harus ada 3 lapangan card dengan gambar preview
3. Setiap card menunjukkan:
   - ✓ Gambar thumbnail dari `uploads/lapangan/`
   - ✓ Nama lapangan
   - ✓ Status (Tersedia)
   - ✓ Rating & lokasi
   - ✓ Harga
   - ✓ 3 button: Edit, Gallery, Hapus

### Step 4: Verify di Homepage

**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/`

**Yang harus dilihat:**
1. Section "Daftar Lapangan Futsal"
2. Harus ada 3 lapangan card dalam grid
3. Setiap card menunjukkan:
   - ✓ Gambar dengan aspect ratio 16:9
   - ✓ Nama lapangan
   - ✓ Status badge (Tersedia/Maintenance)
   - ✓ Rating dengan bintang
   - ✓ Harga per jam
   - ✓ Detail singkat (ukuran, pencahayaan, parkir)
   - ✓ Button "Detail" dan "Booking"

### Step 5: Test Detail Page

**URL:** `http://localhost/project-client-php/website_booking_lapangan_futsal/detail-lapangan.php?id=1`

**Yang harus dilihat:**
1. ✓ Main image carousel dengan:
   - Arrow navigation (◀ ▶)
   - Image counter (1/4, 2/4, dst)
2. ✓ Thumbnail gallery di bawah main image
3. ✓ Klik thumbnail untuk navigate
4. ✓ Semua 4 gambar tampil (1 main + 3 gallery)
5. ✓ Dynamic pricing saat select tanggal

---

## 🧪 Testing Checklist

### Homepage Testing
- [ ] 3 lapangan card tampil
- [ ] Gambar preview loading dengan benar
- [ ] Layout responsive (mobile, tablet, desktop)
- [ ] Button "Detail" dan "Booking" berfungsi
- [ ] Status badge tampil dengan benar
- [ ] Harga tampil dengan format Rp X,XXX

### Detail Page Testing
- [ ] Main image carousel berfungsi
- [ ] Arrow navigation works
- [ ] Image counter updates
- [ ] Thumbnail strip clickable
- [ ] All 4 images appear (1 main + 3 gallery)
- [ ] Gallery muncul di carousel
- [ ] Dynamic pricing works
- [ ] WhatsApp button functional

### Admin Panel Testing
- [ ] Lapangan list dengan gambar
- [ ] Edit button opens modal
- [ ] Gallery button opens gallery page
- [ ] Delete confirmation works
- [ ] Modal form loads data correctly

### Image Upload Testing
- [ ] Click "+ Tambah Lapangan"
- [ ] Can select main image
- [ ] Can select multiple gallery images
- [ ] Preview grid shows on selection
- [ ] Counter shows correct number (1/N)
- [ ] Can submit form
- [ ] Images save to correct folder
- [ ] Database records created

---

## 📁 File Locations

### Main Lapangan Images
```
uploads/lapangan/
├── sample_lapangan_a.jpg (800x600)
├── sample_lapangan_b.jpg (800x600)
└── sample_lapangan_c.jpg (800x600)
```

### Gallery Images
```
uploads/gallery/
├── sample_gallery_1_1.jpg (Lapangan A, image 1)
├── sample_gallery_1_2.jpg (Lapangan A, image 2)
├── sample_gallery_1_3.jpg (Lapangan A, image 3)
├── sample_gallery_2_1.jpg (Lapangan B, image 1)
├── sample_gallery_2_2.jpg (Lapangan B, image 2)
├── sample_gallery_2_3.jpg (Lapangan B, image 3)
├── sample_gallery_3_1.jpg (Lapangan C, image 1)
├── sample_gallery_3_2.jpg (Lapangan C, image 2)
└── sample_gallery_3_3.jpg (Lapangan C, image 3)
```

---

## 🔄 Reset Data

Jika ingin reset dan start dari awal:

### Option 1: Via Script
1. Buka `generate_sample_images.php` lagi
2. Buka `setup_sample_data.php` lagi
3. Data dan images akan direset

### Option 2: Manual
1. Delete folder contents:
   - `uploads/lapangan/*`
   - `uploads/gallery/*`
2. Run SQL:
   ```sql
   DELETE FROM tb_lapangan_gallery;
   DELETE FROM tb_lapangan;
   ```
3. Re-run setup scripts

### Option 3: Via Database Reset
```sql
-- Backup data (optional)
SELECT * FROM tb_lapangan;

-- Clear all
DELETE FROM tb_lapangan_gallery;
DELETE FROM tb_lapangan;

-- Then run setup_sample_data.php
```

---

## 📊 Database Verification

### Check tb_lapangan
```sql
SELECT id, nama, harga, harga_weekend, gambar FROM tb_lapangan;
```

**Expected Result:**
```
| id | nama                    | harga  | harga_weekend | gambar                           |
|----|-------------------------|--------|---------------|---------------------------------|
| 1  | Lapangan A - ...        | 100000 | 150000        | uploads/lapangan/sample_lapangan_a.jpg |
| 2  | Lapangan B - ...        | 80000  | 120000        | uploads/lapangan/sample_lapangan_b.jpg |
| 3  | Lapangan C - ...        | 75000  | 110000        | uploads/lapangan/sample_lapangan_c.jpg |
```

### Check tb_lapangan_gallery
```sql
SELECT lapangan_id, COUNT(*) as total FROM tb_lapangan_gallery GROUP BY lapangan_id;
```

**Expected Result:**
```
| lapangan_id | total |
|-------------|-------|
| 1           | 3     |
| 2           | 3     |
| 3           | 3     |
```

---

## 🚀 Test Upload Feature

Setelah setup data, test fitur multiple image upload:

1. **Go to Admin Panel** → Kelola Lapangan → "+ Tambah Lapangan"

2. **Fill form:**
   - Nama: "Test Lapangan"
   - Harga: 100000
   - Harga Weekend: 150000
   - Status: Tersedia
   - Etc.

3. **Upload Main Image:**
   - Select 1 file untuk Gambar Lapangan
   - Click upload field

4. **Upload Multiple Gallery:**
   - Select 3-5 files sekaligus
   - Preview grid akan muncul
   - Counter akan tampil (1/5, 2/5, dst)

5. **Submit:**
   - Click "Simpan"
   - System akan auto-save
   - Redirect ke lapangan list

6. **Verify:**
   - New lapangan muncul di grid
   - Images tampil di preview
   - Database updated
   - Files in correct folders

---

## 📚 Related Documentation

- `admin/manage_lapangan.php` - Upload form implementation
- `admin/manage_gallery.php` - Gallery management
- `MULTIPLE_IMAGE_UPLOAD_FEATURE.md` - Technical details
- `ADMIN_GUIDE_IMAGE_UPLOAD.md` - Admin user guide
- `detail-lapangan.php` - Gallery display

---

## 🎯 Quick URLs

### Setup Scripts
- Generate Images: `http://localhost/project-client-php/website_booking_lapangan_futsal/generate_sample_images.php`
- Setup Data: `http://localhost/project-client-php/website_booking_lapangan_futsal/setup_sample_data.php`

### Testing Pages
- Homepage: `http://localhost/project-client-php/website_booking_lapangan_futsal/`
- Admin Panel: `http://localhost/project-client-php/website_booking_lapangan_futsal/admin/dashboard.php`
- Detail 1: `http://localhost/project-client-php/website_booking_lapangan_futsal/detail-lapangan.php?id=1`
- Detail 2: `http://localhost/project-client-php/website_booking_lapangan_futsal/detail-lapangan.php?id=2`
- Detail 3: `http://localhost/project-client-php/website_booking_lapangan_futsal/detail-lapangan.php?id=3`

---

## ⚠️ Troubleshooting

### Images tidak generate
```
✓ Check: Is GD library installed? (check phpinfo())
✓ Check: uploads/ folder writable? (chmod 777)
✓ Check: PHP memory limit sufficient?
✓ Try: Refresh page
✓ Try: Clear browser cache
```

### Database insert failed
```
✓ Check: Database connection OK?
✓ Check: tb_lapangan table exists?
✓ Check: tb_lapangan_gallery table exists?
✓ Try: Run setup_sample_data.php again
✓ Check: MySQL error logs
```

### Images tidak tampil di frontend
```
✓ Check: Files exist in uploads/lapangan/?
✓ Check: Database paths correct?
✓ Check: File permissions OK?
✓ Check: Browser developer console for 404?
✓ Try: Hard refresh (Ctrl+F5)
✓ Try: Clear browser cache
```

### Multiple upload preview not showing
```
✓ Check: Browser supports FileReader API?
✓ Check: JavaScript console for errors
✓ Check: Browser is modern (Chrome, Firefox, Safari, Edge)
✓ Try: Refresh page
✓ Try: Different browser
```

---

## 📝 Notes

- Sample images are 800x600px (4:3 ratio)
- Main images stored in: `uploads/lapangan/`
- Gallery images stored in: `uploads/gallery/`
- All images are JPEG format, quality 85
- Placeholder images auto-generated with PHP GD library
- Real images can replace these anytime

---

## ✨ Summary

Dengan mengikuti panduan ini:
1. ✅ Sample images sudah generate di `uploads/`
2. ✅ Sample data sudah insert ke database
3. ✅ Homepage menampilkan 3 lapangan dengan gambar
4. ✅ Detail pages menampilkan gallery carousel
5. ✅ Admin panel menampilkan semua lapangan
6. ✅ Multiple upload form sudah siap untuk test
7. ✅ Semua feature siap untuk production

**Status: READY FOR FULL TESTING** 🎉

---

**Created:** June 2, 2026  
**Last Updated:** June 2, 2026  
**Version:** 2.2.0
