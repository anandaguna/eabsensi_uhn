<?php
session_start();
include('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['id'];
    $matkul_id = $_POST['matkul'];
    $status = $_POST['status'];
    $tanggal = date('Y-m-d');
    $waktu_mulai = date('H:i:s');
    $waktu_selesai = date('H:i:s', strtotime('+2 hours')); // Contoh waktu selesai 2 jam setelah mulai
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO absensi (user_id, matkul_id, status, tanggal, waktu_mulai, waktu_selesai, created_at) 
        VALUES (:user_id, :matkul_id, :status, :tanggal, :waktu_mulai, :waktu_selesai, :created_at)
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':matkul_id', $matkul_id);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':tanggal', $tanggal);
    $stmt->bindParam(':waktu_mulai', $waktu_mulai);
    $stmt->bindParam(':waktu_selesai', $waktu_selesai);
    $stmt->bindParam(':created_at', $created_at);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Absensi berhasil dicatat.";
    } else {
        $_SESSION['error'] = "Gagal mencatat absensi.";
    }
    header('Location: absensi.php');
    exit;
}
?>
