<?php
/**
 * Script untuk generate placeholder images untuk testing
 * Jalankan: http://localhost/project-client-php/website_booking_lapangan_futsal/generate_sample_images.php
 */

// Buat folder jika belum ada
$lapangan_dir = 'uploads/lapangan/';
$gallery_dir = 'uploads/gallery/';

if (!is_dir($lapangan_dir)) {
    mkdir($lapangan_dir, 0777, true);
}
if (!is_dir($gallery_dir)) {
    mkdir($gallery_dir, 0777, true);
}

// Function untuk generate placeholder image
function generatePlaceholderImage($filename, $title, $color_r = 0, $color_g = 150, $color_b = 255) {
    $width = 800;
    $height = 600;
    
    $image = imagecreatetruecolor($width, $height);
    
    // Set background color
    $bgColor = imagecolorallocate($image, $color_r, $color_g, $color_b);
    imagefill($image, 0, 0, $bgColor);
    
    // Add darker gradient effect
    $darkColor = imagecolorallocate($image, max(0, $color_r - 50), max(0, $color_g - 50), max(0, $color_b - 50));
    for ($i = 0; $i < 100; $i++) {
        $line_color = imagecolorallocate($image, 
            $color_r - ($i * 0.5), 
            $color_g - ($i * 0.5), 
            $color_b - ($i * 0.5)
        );
        imageline($image, 0, $i * ($height / 100), $width, $i * ($height / 100), $line_color);
    }
    
    // Add text
    $textColor = imagecolorallocate($image, 255, 255, 255);
    $font = 5; // Built-in font
    
    $text = $title;
    $textBox = imagettfbbox(40, 0, __DIR__ . '/arial.ttf', $text);
    $textWidth = isset($textBox[2]) ? $textBox[2] - $textBox[0] : strlen($text) * 10;
    $textHeight = 20;
    
    $x = ($width - $textWidth) / 2;
    $y = ($height - $textHeight) / 2;
    
    imagestring($image, 5, $x, $y - 40, 'LAPANGAN FUTSAL', $textColor);
    imagestring($image, 5, $x, $y, $text, $textColor);
    imagestring($image, 3, $x, $y + 40, '800x600 | Sample Image', $textColor);
    
    // Save image
    imagejpeg($image, $filename, 85);
    imagedestroy($image);
    
    return file_exists($filename);
}

// Generate main lapangan images
$lapangan_data = [
    ['filename' => 'sample_lapangan_a.jpg', 'title' => 'LAPANGAN A', 'r' => 30, 'g' => 150, 'b' => 100],
    ['filename' => 'sample_lapangan_b.jpg', 'title' => 'LAPANGAN B', 'r' => 100, 'g' => 120, 'b' => 200],
    ['filename' => 'sample_lapangan_c.jpg', 'title' => 'LAPANGAN C', 'r' => 200, 'g' => 100, 'b' => 100],
];

$generated = 0;
$failed = 0;

echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Generate Sample Images</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        .success { color: #27ae60; background: #d5f4e6; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error { color: #c0392b; background: #fadbd8; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .info { color: #2c3e50; font-size: 14px; line-height: 1.6; margin: 20px 0; }
        .code { background: #ecf0f1; padding: 10px; border-radius: 4px; font-family: monospace; margin: 10px 0; }
        h1 { color: #2c3e50; }
        .image-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0; }
        .image-item { text-align: center; }
        .image-item img { max-width: 100%; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .image-item p { font-size: 12px; color: #7f8c8d; margin-top: 10px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📸 Generate Sample Images</h1>";

// Generate lapangan images
echo "<h2>1. Main Lapangan Images</h2>";
foreach ($lapangan_data as $data) {
    $filepath = $lapangan_dir . $data['filename'];
    // Menggunakan GD library untuk generate simple placeholder
    $image = imagecreatetruecolor(800, 600);
    $bg = imagecolorallocate($image, $data['r'], $data['g'], $data['b']);
    imagefill($image, 0, 0, $bg);
    
    $textColor = imagecolorallocate($image, 255, 255, 255);
    imagestring($image, 5, 350, 250, $data['title'], $textColor);
    imagestring($image, 3, 300, 300, 'Sample Image - 800x600', $textColor);
    
    if (imagejpeg($image, $filepath, 85)) {
        echo "<div class='success'>✓ Generated: {$data['filename']}</div>";
        $generated++;
    } else {
        echo "<div class='error'>✗ Failed: {$data['filename']}</div>";
        $failed++;
    }
    imagedestroy($image);
}

// Generate gallery images
echo "<h2>2. Gallery Images (3 per lapangan = 9 total)</h2>";
for ($lapangan_id = 1; $lapangan_id <= 3; $lapangan_id++) {
    for ($i = 1; $i <= 3; $i++) {
        $gallery_filename = 'sample_gallery_' . $lapangan_id . '_' . $i . '.jpg';
        $filepath = $gallery_dir . $gallery_filename;
        
        $image = imagecreatetruecolor(800, 600);
        $colors = [
            1 => [30, 150, 100],
            2 => [100, 120, 200],
            3 => [200, 100, 100],
        ];
        $color = $colors[$lapangan_id];
        $bg = imagecolorallocate($image, $color[0], $color[1], $color[2]);
        imagefill($image, 0, 0, $bg);
        
        $textColor = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 5, 320, 240, 'Lapangan ' . chr(64 + $lapangan_id), $textColor);
        imagestring($image, 3, 310, 280, 'Gallery Image ' . $i . ' of 3', $textColor);
        
        if (imagejpeg($image, $filepath, 85)) {
            echo "<div class='success'>✓ Generated: $gallery_filename</div>";
            $generated++;
        } else {
            echo "<div class='error'>✗ Failed: $gallery_filename</div>";
            $failed++;
        }
        imagedestroy($image);
    }
}

echo "<div class='info'>
    <h2>📊 Summary</h2>
    <p>✓ Generated: $generated images</p>
    <p>✗ Failed: $failed images</p>
    
    <h2>📁 Images Created In:</h2>
    <div class='code'>
$lapangan_dir<br>
$gallery_dir
    </div>
    
    <h2>🚀 Langkah Berikutnya:</h2>
    <ol>
        <li>Buka: <strong>setup_sample_data.php</strong> untuk insert data ke database</li>
        <li>Lalu refresh halaman admin panel</li>
        <li>Gambar akan tampil di homepage dan detail page</li>
    </ol>
    
    <h2>✨ Test URLs:</h2>
    <ul>
        <li>Homepage: <a href='/project-client-php/website_booking_lapangan_futsal/' target='_blank'>http://localhost/project-client-php/website_booking_lapangan_futsal/</a></li>
        <li>Admin: <a href='/project-client-php/website_booking_lapangan_futsal/admin/dashboard.php' target='_blank'>http://localhost/project-client-php/website_booking_lapangan_futsal/admin/dashboard.php</a></li>
    </ul>
</div>
    </div>
</body>
</html>";
?>
