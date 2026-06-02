# Admin Guide: Image Upload System
## Panduan Lengkap Upload Gambar Lapangan

**Bahasa:** Indonesia + English  
**Version:** 2.2.0  
**Last Updated:** June 2, 2026

---

## 📖 Daftar Isi
1. [Pengenalan Sistem](#pengenalan-sistem)
2. [Akses Admin Panel](#akses-admin-panel)
3. [Menambah Lapangan Baru](#menambah-lapangan-baru)
4. [Mengedit Lapangan](#mengedit-lapangan)
5. [Mengelola Gallery](#mengelola-gallery)
6. [Troubleshooting](#troubleshooting)

---

## Pengenalan Sistem

### Dua Jenis Gambar

#### 1. **Gambar Lapangan (Main Image)**
- **Fungsi:** Thumbnail di halaman utama (homepage)
- **Lokasi Simpan:** `uploads/lapangan/`
- **Jumlah:** 1 per lapangan
- **Ukuran:** Max 2MB
- **Format:** JPG, PNG
- **Tampilan:** Card di homepage dengan 16:9 aspect ratio

#### 2. **Multiple Gambar (Gallery)**
- **Fungsi:** Foto-foto detail lapangan di halaman detail
- **Lokasi Simpan:** `uploads/gallery/`
- **Jumlah:** 1-20+ per lapangan
- **Ukuran:** Max 2MB per file
- **Format:** JPG, PNG
- **Tampilan:** Carousel dengan thumbnail di detail page

---

## Akses Admin Panel

### Cara Login:

1. Buka browser
2. Masuk ke: `http://localhost/project-client-php/website_booking_lapangan_futsal/admin/auth/login.php`
3. Masukkan username dan password admin
4. Klik "Login"

### Jika Belum Punya Akun:

1. Hubungi administrator
2. Minta credentials (username & password)
3. Password dienkripsi dengan bcrypt

---

## Menambah Lapangan Baru

### Step-by-Step Guide:

#### Step 1: Buka Form Tambah Lapangan
```
1. Login ke Admin Panel
2. Di sidebar, klik "Dashboard" atau refresh
3. Di main area, klik tab "Kelola Lapangan"
4. Klik button "+ Tambah Lapangan" (warna hijau)
```

#### Step 2: Isi Form Dasar
```
Nama Lapangan:
  → Contoh: "Lapangan Indoor Premium Jakarta Barat"
  → Required (harus diisi)

Harga per Jam (Weekday - Sen-Jum):
  → Contoh: 100000 (Rp 100.000)
  → Required, numeric only

Harga per Jam (Weekend - Sab-Min):
  → Contoh: 150000 (Rp 150.000)
  → Required, numeric only
  → Biasanya lebih tinggi dari weekday

Status:
  → Pilih: "Tersedia" atau "Maintenance"
  → Default: "Tersedia"
```

#### Step 3: Upload Gambar Lapangan (Main Image)
```
1. Scroll ke field "Gambar Lapangan"
2. Klik input atau drag-drop gambar
3. Pilih 1 foto terbaik sebagai thumbnail
4. Ukuran: max 2MB
5. Format: JPG atau PNG
6. Tips: Gunakan foto yang menarik dengan resolusi tinggi
```

**Preview:**
- Gambar akan ditampilkan di card homepage
- Aspect ratio: 16:9

#### Step 4: Upload Multiple Gambar (Gallery)
```
1. Scroll ke field "Multiple Gambar Lapangan (Gallery)"
2. Klik input atau drag-drop gambar
3. Bisa memilih 1-20+ foto sekaligus
4. Setiap foto: max 2MB
5. Format: JPG atau PNG
```

**Preview:**
- Semua gambar akan ditampilkan dalam grid 4 kolom
- Setiap thumbnail menunjukkan nomor (1/5, 2/5, dst)
- Bisa hover untuk lihat nama file

#### Step 5: Isi Deskripsi & Detail
```
Deskripsi Singkat:
  → Max 100 karakter
  → Tampil di card di homepage
  → Contoh: "Lapangan indoor dengan AC penuh"

Deskripsi Lengkap:
  → Unlimited
  → Tampil di halaman detail
  → Jelaskan secara detail fasilitas, lokasi, dll

Fasilitas (pisahkan dengan koma):
  → Contoh: "AC, Toilet, WiFi, Parkir, Kantin"
  → Akan ditampilkan sebagai checklist

Rating (0-5):
  → Default: 4.5
  → Tampil dengan bintang di card

Lokasi:
  → Contoh: "Jakarta Barat"
  → Default: "Jakarta"

Ukuran Lapangan:
  → Contoh: "40m x 20m"
  → Default: "40m x 20m"

Pencahayaan:
  → Contoh: "LED Modern", "Standar Plus"
  → Default: "Standar"

Parkir:
  → Contoh: "Tersedia (100+ spot)"
  → Default: "Tersedia"

Tipe Lantai:
  → Contoh: "Rumput Sintetis Premium"
  → Default: "Rumput Sintetis"
```

#### Step 6: Simpan
```
1. Scroll ke bawah
2. Klik button "Simpan" (warna hijau)
3. System akan:
   - Upload gambar utama ke uploads/lapangan/
   - Create lapangan entry di database
   - Get lapangan ID
   - Upload semua gallery images ke uploads/gallery/
   - Create gallery entries di database
4. Redirect ke halaman lapangan list
```

---

## Mengedit Lapangan

### Cara Edit:

```
1. Di halaman "Kelola Lapangan"
2. Cari lapangan yang ingin diedit
3. Klik button "Edit" (warna biru)
4. Form akan terisi dengan data existing
5. Update field yang perlu diubah
6. Optional: Upload gambar baru
   - Jika upload gambar baru → replace gambar lama
   - Jika tidak upload → tetap gambar lama
7. Optional: Add gallery images baru
   - Gallery images akan ditambahkan (tidak replace)
8. Klik "Simpan"
```

**Catatan:**
- Data existing akan tetap ada jika tidak diubah
- Gambar lama tidak otomatis dihapus dari disk
- Untuk hapus gallery images, gunakan halaman Gallery terpisah

---

## Mengelola Gallery

### Akses Gallery Manager:

```
1. Di halaman "Kelola Lapangan"
2. Cari lapangan yang ingin dikelola gallery-nya
3. Klik button "Gallery" (warna ungu)
4. Terbuka halaman gallery management
```

### Di Halaman Gallery:

#### Upload Foto Tambahan:
```
1. Di bagian "Upload Foto"
2. Klik file input
3. Pilih 1-10+ foto
4. Klik "Upload Foto"
5. Foto akan ditambahkan ke gallery
```

#### Lihat Gallery Saat Ini:
```
1. Di bagian "Foto Lapangan (X foto)"
2. Lihat thumbnail semua foto yang sudah diupload
3. Nama file dan urutan ditampilkan
```

#### Hapus Foto:
```
1. Cari foto yang ingin dihapus
2. Klik button "Hapus" di bawah foto
3. Konfirmasi "Hapus foto ini?"
4. Foto akan dihapus dari disk dan database
```

---

## Gambar di Homepage

### Tampilan:

```
┌─────────────────────────┐
│   Gambar Lapangan       │  ← Main image (uploads/lapangan/)
│   (Aspect 16:9)         │
├─────────────────────────┤
│ Lapangan A              │
│ ⭐ 4.86 - Jakarta Barat │
│ Rp 125,000/jam          │
│ Indoor, AC, WiFi...     │  ← Deskripsi singkat
│                         │
│ [Detail] [Booking]      │
└─────────────────────────┘
```

**Sumber Data:**
- Image: `tb_lapangan.gambar` (uploads/lapangan/)
- Nama: `tb_lapangan.nama`
- Rating: `tb_lapangan.rating`
- Lokasi: `tb_lapangan.lokasi`
- Harga: `tb_lapangan.harga`
- Deskripsi: `tb_lapangan.deskripsi`

---

## Gambar di Detail Page

### Tampilan:

```
┌───────────────────────────────────────┐
│   Main Image Gallery                  │
│  [◀]  [GAMBAR]            [▶]  2/5    │  ← Carousel
├───────────────────────────────────────┤
│ [🖼️] [🖼️] [🖼️] [🖼️] [🖼️]      │  ← Thumbnail gallery
│   Thumbnail Strip (uploads/gallery/)  │
└───────────────────────────────────────┘

Fungsi:
- User bisa lihat multiple foto lapangan
- Navigate dengan arrow atau klik thumbnail
- Counter menunjukkan position (2/5)
```

**Sumber Data:**
- Main image: `tb_lapangan.gambar` (uploads/lapangan/) - optional
- Gallery: `tb_lapangan_gallery.foto` (uploads/gallery/) - multiple

---

## File Paths & Storage

### Struktur Folder:

```
project-client-php/website_booking_lapangan_futsal/
├── uploads/
│   ├── lapangan/
│   │   ├── 1780399979_lapangan_a.jpg
│   │   ├── 1780399980_lapangan_b.png
│   │   └── ...
│   ├── gallery/
│   │   ├── 1780399982_gallery_0_image1.jpg
│   │   ├── 1780399982_gallery_1_image2.jpg
│   │   ├── 1780399983_gallery_0_image1.jpg
│   │   └── ...
│   └── .gitkeep
├── assets/
│   └── images/         ← OLD (masih support untuk backward compat)
└── ...
```

### Naming Convention:

**Main Image:**
```
{timestamp}_{original_filename}
Contoh: 1780399979_lapangan_a.jpg
```

**Gallery Image:**
```
{timestamp}_gallery_{index}_{original_filename}
Contoh: 1780399982_gallery_0_image1.jpg
        1780399982_gallery_1_image2.jpg
```

**Keuntungan:**
- Timestamp mencegah filename conflict
- Original filename tetap terlihat
- Gallery index menunjukkan urutan upload

---

## Tips & Best Practices

### Upload Gambar:

✅ **DO:**
- Gunakan gambar berkualitas tinggi (min 800x600px)
- Potret lapangan dari angle yang bagus
- Upload foto dari berbagai sudut untuk gallery
- Gunakan format JPG untuk photo, PNG untuk graphics
- Compress gambar untuk performa lebih baik
- Upload 3-5 foto untuk gallery yang menarik

❌ **DON'T:**
- Jangan upload gambar dengan text watermark
- Jangan upload screenshot blur atau low quality
- Jangan upload lebih dari 20 gambar (performance)
- Jangan gunakan format WEBP (belum support)
- Jangan upload gambar dengan aspect ratio ekstrem
- Jangan lupa caption/deskripsi lengkap

### Deskripsi:

✅ **Tips:**
- Buat deskripsi yang detail dan menarik
- Sebutkan keunggulan unik lapangan
- Jelaskan fasilitas lengkap
- Kasih info harga dan jam operasional
- Update info jika ada perubahan

### Pricing:

✅ **Tips:**
- Atur harga weekday lebih rendah dari weekend
- Contoh: Weekday 100k, Weekend 150k
- Update harga jika ada perubahan
- Transparansi harga penting untuk trust

---

## Troubleshooting

### Gambar Tidak Upload

**Masalah:** Upload button tidak bekerja
```
Solusi:
1. Refresh halaman
2. Clear browser cache (Ctrl+Shift+Delete)
3. Coba browser lain
4. Pastikan file < 2MB
5. Cek format (harus JPG/PNG)
```

**Masalah:** Error "File terlalu besar"
```
Solusi:
1. Compress gambar (gunakan online tool)
2. Reduce resolution jika terlalu tinggi
3. Gunakan tool seperti TinyPNG atau ImageOptim
4. Max 2MB per file
```

### Preview Tidak Muncul

**Masalah:** Thumbnail preview tidak tampil
```
Solusi:
1. Buka Developer Tools (F12)
2. Cek Console untuk error message
3. Cek Network tab untuk failed requests
4. Try upload ulang
5. Clear cache dan refresh
```

### Gambar Tidak Tampil di Frontend

**Masalah:** Gambar upload tapi tidak tampil di homepage/detail
```
Solusi:
1. Check path di database: SELECT * FROM tb_lapangan
2. Pastikan file ada di uploads/lapangan/ atau uploads/gallery/
3. Pastikan folder permission sudah benar (777)
4. Cek browser console untuk 404 errors
5. Clear browser cache
```

**Masalah:** File size error saat upload multiple
```
Solusi:
1. Upload lebih sedikit file sekaligus (max 5-10 per upload)
2. Compress semua gambar terlebih dahulu
3. Cek PHP upload limits di server
4. Contact administrator jika masalah persist
```

### Gallery Tidak Muncul di Detail Page

**Masalah:** Gallery carousel tidak tampil
```
Solusi:
1. Pastikan sudah upload gallery images
2. Klik "Gallery" button dan upload foto
3. Refresh detail page
4. Cek browser console untuk JavaScript errors
5. Try upload minimal 1 gambar untuk gallery
```

---

## Frequently Asked Questions (FAQ)

### Q: Berapa jumlah gambar optimal untuk gallery?
A: 3-5 gambar sudah cukup. Max 10 untuk performa optimal.

### Q: Bisa ganti gambar utama setelah upload?
A: Ya, edit lapangan dan upload gambar baru sebagai "Gambar Lapangan".

### Q: Gambar lama otomatis dihapus?
A: Tidak. File lama tetap tersimpan. Admin perlu manual delete jika perlu.

### Q: Format gambar apa saja yang support?
A: JPG dan PNG saja. WebP belum support.

### Q: Bisa resize gambar otomatis?
A: Tidak (saat ini). Resize manual sebelum upload recommended.

### Q: Gambar bisa di-reorder?
A: Main image: tidak. Gallery: ya, lewat halaman Gallery terpisah.

### Q: Bisa upload video?
A: Tidak. Sistem ini untuk gambar saja.

### Q: Dimana file gambar disimpan?
A: Main: `uploads/lapangan/`. Gallery: `uploads/gallery/`.

### Q: Gambar aman? Ada enkripsi?
A: File tidak dienkripsi tapi disimpan di folder private. Database relatif aman.

### Q: Bisa backup gambar?
A: Ya, backup folder `uploads/` secara berkala.

---

## Checklist Sebelum Publish Lapangan

- [ ] Nama lapangan sudah benar
- [ ] Harga weekday & weekend sudah tepat
- [ ] Gambar utama sudah di-upload (resolution OK)
- [ ] Gallery minimal 1 gambar sudah di-upload
- [ ] Deskripsi singkat sudah filled
- [ ] Deskripsi lengkap sudah filled
- [ ] Fasilitas sudah listed lengkap
- [ ] Rating sudah set (minimal 4.0)
- [ ] Lokasi sudah benar
- [ ] Ukuran, pencahayaan, parkir sudah tepat
- [ ] Status set ke "Tersedia"
- [ ] Preview di homepage OK
- [ ] Preview di detail page OK
- [ ] Gallery carousel berfungsi

---

## Perlu Bantuan?

**Contact Support:**
- Chat WhatsApp: [Admin Phone]
- Email: admin@futsalbook.com
- Internal: Hubungi IT Administrator

**Dokumentasi Lainnya:**
- README.md - Project overview
- SETUP_GUIDE.md - Setup instructions
- MULTIPLE_IMAGE_UPLOAD_FEATURE.md - Technical details
- QUICK_REFERENCE.md - Quick help

---

**Selamat menggunakan sistem admin panel! Happy uploading!** 📸

---

*Version 2.2.0 - Last Updated June 2, 2026*
