<?php
session_start();
include('../config/database.php');

$user_id = $_SESSION['id'];

$stmt = $conn->prepare("
    SELECT a.*, m.nama_matkul, DATE_FORMAT(a.tanggal, '%d %M %Y') as formatted_date 
    FROM absensi a 
    JOIN mata_kuliah m ON a.matkul_id = m.id 
    WHERE a.user_id = :user_id 
    ORDER BY a.created_at DESC 
    LIMIT 5
");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$riwayat_absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($riwayat_absensi);
exit;
?>
