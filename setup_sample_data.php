<?php
/**
 * Script untuk insert/reset sample data lapangan dengan path gambar yang benar
 * Jalankan: http://localhost/project-client-php/website_booking_lapangan_futsal/setup_sample_data.php
 */

require 'config/koneksi.php';

// Clear existing data
$conn->query("DELETE FROM tb_lapangan_gallery");
$conn->query("DELETE FROM tb_lapangan");

// Insert sample data dengan path yang benar
$sample_data = [
    [
        'nama' => 'Lapangan A - Indoor Premium',
        'harga' => 100000,
        'harga_weekend' => 150000,
        'status' => 'tersedia',
        'deskripsi' => 'Lapangan indoor dengan pencahayaan standar dan fasilitas lengkap',
        'deskripsi_lengkap' => 'Lapangan A adalah lapangan futsal indoor premium yang berlokasi di Jakarta Barat. Dengan ukuran standar internasional dan dilengkapi pencahayaan LED modern, lapangan ini menawarkan kenyamanan bermain yang optimal. Cocok untuk pertandingan resmi maupun latihan rutin.',
        'fasilitas' => 'AC Central, Toilet & Kamar Mandi, Ruang Tunggu Nyaman, Penyewaan Perlengkapan, Kantin & Minuman, Tempat Parkir Luas, Keamanan 24 Jam',
        'lokasi' => 'Jakarta Barat',
        'ukuran' => '40m x 20m',
        'pencahayaan' => 'LED Modern',
        'parkir' => 'Tersedia (100+ spot)',
        'tipe_lantai' => 'Rumput Sintetis Premium',
        'gambar' => 'uploads/lapangan/sample_lapangan_a.jpg'
    ],
    [
        'nama' => 'Lapangan B - Outdoor Internasional',
        'harga' => 80000,
        'harga_weekend' => 120000,
        'status' => 'tersedia',
        'deskripsi' => 'Lapangan outdoor berkualitas internasional dengan rumput sintetis',
        'deskripsi_lengkap' => 'Lapangan B adalah lapangan futsal outdoor terbesar dan tercanggih dengan standar internasional. Dilengkapi rumput sintetis berkualitas tinggi dan sistem drainase modern, lapangan ini siap untuk berbagai jenis pertandingan. Lokasi strategis di Jakarta Timur memudahkan akses dari berbagai area.',
        'fasilitas' => 'Sistem Pencahayaan Profesional, Tribun Penonton, Kantor Pengelola, Area Istirahat Ber-AC, Toilet Bersih, Fasilitas Olahraga Lengkap, Parkir Bertingkat',
        'lokasi' => 'Jakarta Timur',
        'ukuran' => '45m x 25m',
        'pencahayaan' => 'Profesional High-Mast',
        'parkir' => 'Tersedia (150+ spot)',
        'tipe_lantai' => 'Rumput Sintetis Internasional',
        'gambar' => 'uploads/lapangan/sample_lapangan_b.jpg'
    ],
    [
        'nama' => 'Lapangan C - Casual Comfort',
        'harga' => 75000,
        'harga_weekend' => 110000,
        'status' => 'tersedia',
        'deskripsi' => 'Lapangan indoor dengan AC dan fasilitas premium',
        'deskripsi_lengkap' => 'Lapangan C menawarkan pengalaman bermain futsal yang nyaman dengan ber-AC penuh. Lapangan ini ideal untuk casual games maupun turnamen skala kecil. Harga yang kompetitif menjadikan lapangan ini pilihan utama untuk grup yang mencari value terbaik.',
        'fasilitas' => 'AC Pendingin Optimal, Kamar Ganti Bersih, Penyewaan Bola & Sepatu, Snack Bar, WiFi Gratis, Parkir Aman, Staff Profesional',
        'lokasi' => 'Jakarta Pusat',
        'ukuran' => '35m x 18m',
        'pencahayaan' => 'Standar Plus',
        'parkir' => 'Tersedia (80+ spot)',
        'tipe_lantai' => 'Rumput Sintetis Standar',
        'gambar' => 'uploads/lapangan/sample_lapangan_c.jpg'
    ]
];

// Insert data
$inserted = 0;
foreach ($sample_data as $data) {
    $nama = $conn->real_escape_string($data['nama']);
    $harga = (int)$data['harga'];
    $harga_weekend = (int)$data['harga_weekend'];
    $status = $conn->real_escape_string($data['status']);
    $deskripsi = $conn->real_escape_string($data['deskripsi']);
    $deskripsi_lengkap = $conn->real_escape_string($data['deskripsi_lengkap']);
    $fasilitas = $conn->real_escape_string($data['fasilitas']);
    $lokasi = $conn->real_escape_string($data['lokasi']);
    $ukuran = $conn->real_escape_string($data['ukuran']);
    $pencahayaan = $conn->real_escape_string($data['pencahayaan']);
    $parkir = $conn->real_escape_string($data['parkir']);
    $tipe_lantai = $conn->real_escape_string($data['tipe_lantai']);
    $gambar = $conn->real_escape_string($data['gambar']);
    
    $sql = "INSERT INTO tb_lapangan 
            (nama, harga, harga_weekend, status, deskripsi, deskripsi_lengkap, fasilitas, lokasi, ukuran, pencahayaan, parkir, tipe_lantai, gambar) 
            VALUES 
            ('$nama', $harga, $harga_weekend, '$status', '$deskripsi', '$deskripsi_lengkap', '$fasilitas', '$lokasi', '$ukuran', '$pencahayaan', '$parkir', '$tipe_lantai', '$gambar')";
    
    if ($conn->query($sql)) {
        $inserted++;
        $lapangan_id = $conn->insert_id;
        
        // Insert sample gallery images (3 per lapangan)
        for ($i = 1; $i <= 3; $i++) {
            $foto = $conn->real_escape_string("uploads/gallery/sample_gallery_{$lapangan_id}_{$i}.jpg");
            $conn->query("INSERT INTO tb_lapangan_gallery (lapangan_id, foto, urutan) VALUES ($lapangan_id, '$foto', $i)");
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Setup Sample Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .success { color: #27ae60; font-size: 18px; padding: 20px; background: #d5f4e6; border-radius: 4px; margin: 20px 0; }
        .info { color: #2c3e50; font-size: 14px; line-height: 1.6; }
        .code { background: #ecf0f1; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        h1 { color: #2c3e50; }
        ul { margin: 15px 0; }
        li { margin: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Setup Sample Data</h1>
        
        <div class="success">
            ✓ Berhasil insert <?php echo $inserted; ?> lapangan dengan sample data!
        </div>

        <div class="info">
            <h2>Data yang diinsert:</h2>
            <ul>
                <li><strong>Lapangan A</strong> - Harga: Rp 100.000 (weekday) / 150.000 (weekend)</li>
                <li><strong>Lapangan B</strong> - Harga: Rp 80.000 (weekday) / 120.000 (weekend)</li>
                <li><strong>Lapangan C</strong> - Harga: Rp 75.000 (weekday) / 110.000 (weekend)</li>
            </ul>

            <p><strong>Setiap lapangan memiliki:</strong></p>
            <ul>
                <li>1 gambar utama</li>
                <li>3 gambar gallery</li>
                <li>Deskripsi lengkap</li>
                <li>Fasilitas yang detail</li>
                <li>Lokasi</li>
            </ul>

            <p><strong>Langkah berikutnya:</strong></p>
            <ul>
                <li>1. Buka admin panel: <code>http://localhost/project-client-php/website_booking_lapangan_futsal/admin/dashboard.php</code></li>
                <li>2. Masuk ke "Kelola Lapangan" untuk lihat data</li>
                <li>3. Klik "Edit" untuk upload gambar yang sebenarnya</li>
                <li>4. Gambar akan disimpan di folder <code>uploads/lapangan/</code> dan <code>uploads/gallery/</code></li>
            </ul>

            <p><strong>Catatan Penting:</strong></p>
            <p>Data ini menggunakan path placeholder. Untuk menampilkan gambar yang sebenarnya:</p>
            <div class="code">
uploads/lapangan/sample_lapangan_a.jpg<br>
uploads/lapangan/sample_lapangan_b.jpg<br>
uploads/lapangan/sample_lapangan_c.jpg
            </div>

            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ecf0f1;">
                <strong>Sekarang Anda bisa:</strong><br>
                • Lihat data di admin panel<br>
                • Edit lapangan dan upload gambar asli<br>
                • Test multiple image upload di form "Tambah Lapangan"<br>
                • Lihat gambar muncul di homepage dan detail page
            </p>
        </div>
    </div>
</body>
</html>
