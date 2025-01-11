<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil nama user dari session
$nama_user = $_SESSION['nama'];

// Tentukan salam berdasarkan waktu
date_default_timezone_set('Asia/Jakarta');
$hour = date('H');
if ($hour >= 5 && $hour < 12) {
    $salam = "Selamat Pagi";
} elseif ($hour >= 12 && $hour < 18) {
    $salam = "Selamat Siang";
} else {
    $salam = "Selamat Malam";
}

// Simulasi data (ganti dengan query database yang sebenarnya)
$total_mahasiswa = 20;
$total_sks = 24;
$total_kehadiran = 85;
$total_izin = 1;
$total_sakit = 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #4b0082, #00bfff);
            color: #e5e5e5;
            min-height: 100vh;
        }

        header {
            background: rgba(32, 32, 32, 0.9);
            color: #e5e5e5;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #00ffcc;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            margin-bottom: 60px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .stat-box i {
            font-size: 2.5em;
            margin-bottom: 10px;
            color: #00ffcc;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #ffffff;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 1.1em;
            color: #00ffcc;
        }

        .tools-container {
            padding: 20px;
        }

        .tool-button {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            border-radius: 10px;
            color: #00ffcc;
            text-decoration: none;
            margin: 10px;
            display: inline-block;
            transition: all 0.3s;
        }

        .tool-button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffcc00;
            transform: scale(1.05);
        }

        footer {
            text-align: center;
            padding: 15px;
            background: rgba(32, 32, 32, 0.9);
            color: #e5e5e5;
            position: fixed;
            bottom: 0;
            width: 100%;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<header>
    <div>
        <h1>Superadmin Dashboard</h1>
    </div>
    <div class="greeting">
        <p>Hi, <?php echo $nama_user; ?>!</p>
        <p><?php echo $salam; ?></p>
        <img src="https://i.pinimg.com/236x/b3/49/f5/b349f5157d5f3b43186805a07394bfa4.jpg" alt="Profile Icon">
    </div>
</header>

<main>
    <div class="stats-container">
        <div class="stat-box">
            <i class="fas fa-users"></i>
            <div class="stat-number"><?php echo $total_mahasiswa; ?></div>
            <div class="stat-label">Total Mahasiswa</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-book"></i>
            <div class="stat-number"><?php echo $total_sks; ?></div>
            <div class="stat-label">Total SKS</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-check-circle"></i>
            <div class="stat-number"><?php echo $total_kehadiran; ?>%</div>
            <div class="stat-label">Kehadiran</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-calendar-check"></i>
            <div class="stat-number"><?php echo $total_izin; ?></div>
            <div class="stat-label">Total Izin</div>
        </div>
        <div class="stat-box">
            <i class="fas fa-procedures"></i>
            <div class="stat-number"><?php echo $total_sakit; ?></div>
            <div class="stat-label">Total Sakit</div>
        </div>
    </div>

    <div class="tools-container">
        <h2>Manage Tools</h2>
        <a href="manage-matkul.php" class="tool-button"><i class="fas fa-book-open"></i> Manage Mata Kuliah</a>
        <a href="manage-absensi.php" class="tool-button"><i class="fas fa-clipboard-list"></i> Manage Absensi</a>
        <a href="validasi.php" class="tool-button"><i class="fas fa-user-check"></i> Validasi Mahasiswa</a>
    </div>
</main>

<footer>
    Dibuat dengan <i class="fas fa-heart" style="color: #ff0066;"></i> oleh 555 Team
</footer>

</body>
</html>