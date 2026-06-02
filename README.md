# Website Booking Lapangan Futsal

Aplikasi sederhana untuk booking lapangan futsal (PHP + MySQL)

## Deskripsi

Proyek ini adalah aplikasi pemesanan lapangan futsal berbasis PHP dan MySQL yang dijalankan di lingkungan XAMPP. Berisi fitur dasar seperti registrasi, login, manajemen lapangan, dan manajemen booking.

## Struktur singkat

- `admin/` - Halaman panel admin (manage booking, konten, lapangan)
- `auth/` - Login, logout, register
- `assets/` - CSS dan aset frontend
- `config/koneksi.php` - Konfigurasi koneksi database
- `database.sql` - Dump database untuk import

## Persiapan lokal

1. Pastikan XAMPP terpasang dan Apache + MySQL berjalan.
2. Salin folder proyek ke `htdocs` (misal: `C:\xampp\htdocs\website_booking_lapangan_futsal`).
3. Import `database.sql` ke database MySQL melalui phpMyAdmin.
4. Edit `config/koneksi.php` untuk sesuaikan `username`, `password`, dan `database`.
5. Buka `http://localhost/website_booking_lapangan_futsal` di browser.

## Cara kontribusi

- Fork repository, buat branch fitur, lalu buat pull request ke repository utama.

## Lisensi

Proyek ini terbuka untuk digunakan dan dimodifikasi. Beri kredit jika digunakan ulang.
