<?php
session_start();
require '../config/koneksi.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM tb_lapangan WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $lapangan = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($lapangan);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Lapangan not found']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID parameter required']);
}
?>
