<?php
header('Content-Type: application/json');
require '../config/koneksi.php';

$lapangan_id = isset($_GET['lapangan_id']) ? (int)$_GET['lapangan_id'] : 0;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

if ($lapangan_id === 0 || empty($tanggal)) {
    echo json_encode([]);
    exit;
}

$tanggal = $conn->real_escape_string($tanggal);

$query = "SELECT DATE_FORMAT(jam_mulai, '%H:%i') as jam_mulai, DATE_FORMAT(jam_selesai, '%H:%i') as jam_selesai 
          FROM tb_booking 
          WHERE lapangan_id = $lapangan_id 
          AND tanggal = '$tanggal' 
          AND status NOT IN ('cancelled', 'failed')
          AND payment_status != 'failed'";

$result = $conn->query($query);
$booked_slots = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $booked_slots[] = [
            'start' => $row['jam_mulai'],
            'end' => $row['jam_selesai']
        ];
    }
}

echo json_encode($booked_slots);
exit;
