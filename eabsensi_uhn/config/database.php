<?php
$host = "localhost";
$username = "root"; // Sesuaikan dengan username DB Anda
$password = ""; // Sesuaikan dengan password DB Anda
$dbname = "eabsensi_uhn";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
