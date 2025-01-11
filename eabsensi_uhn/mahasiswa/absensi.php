<?php
session_start();
if ($_SESSION['role'] != 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

include('../config/database.php');

// Fetch mata kuliah
$stmt = $conn->prepare("SELECT * FROM mata_kuliah");
$stmt->execute();
$matkul = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch riwayat absensi
$stmt = $conn->prepare("
    SELECT a.*, m.nama_matkul, DATE_FORMAT(a.tanggal, '%d %M %Y') as formatted_tanggal, 
           CONCAT(DATE_FORMAT(a.waktu_mulai, '%H:%i'), ' - ', DATE_FORMAT(a.waktu_selesai, '%H:%i')) as formatted_waktu
    FROM absensi a 
    JOIN mata_kuliah m ON a.matkul_id = m.id 
    WHERE a.user_id = :user_id 
    ORDER BY a.created_at DESC 
    LIMIT 5
");
$stmt->bindParam(':user_id', $_SESSION['id']);
$stmt->execute();
$riwayat_absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: #f0f2f5;
            min-height: 100vh;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card-title {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            color: #4a5568;
            background-color: white;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234A5568'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        select:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(102,126,234,0.2);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-hadir {
            background: #c6f6d5;
            color: #2f855a;
        }

        .status-sakit {
            background: #feebc8;
            color: #c05621;
        }

        .status-izin {
            background: #e9d8fd;
            color: #6b46c1;
        }

        .status-alfa {
            background: #fed7d7;
            color: #c53030;
        }

        .history-item {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-matkul {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .history-date {
            font-size: 0.875rem;
            color: #718096;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #4a5568;
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #667eea;
        }

        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form Absensi -->
        <div class="card">
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h2 class="card-title">
                <i class="fas fa-clock"></i>
                Form Absensi
            </h2>
            <form method="POST" action="submit-absensi.php">
                <div class="form-group">
                    <label for="matkul">Mata Kuliah</label>
                    <select id="matkul" name="matkul" required>
                        <option value="">Pilih Mata Kuliah</option>
                        <?php foreach ($matkul as $m): ?>
                            <option value="<?php echo htmlspecialchars($m['id']); ?>">
                                <?php echo htmlspecialchars($m['nama_matkul']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status Kehadiran</label>
                    <select id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="hadir">Hadir</option>
                        <option value="sakit">Sakit</option>
                        <option value="izin">Izin</option>
                        <option value="alfa">Alfa</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i>
                    Submit Absensi
                </button>
            </form>
        </div>

        <!-- Riwayat Absensi -->
        <div class="card">
            <h2 class="card-title">
                <i class="fas fa-history"></i>
                Riwayat Absensi
            </h2>
            <?php if ($riwayat_absensi): ?>
                <?php foreach ($riwayat_absensi as $absensi): ?>
                    <div class="history-item">
                        <div class="history-matkul">
                            <?php echo htmlspecialchars($absensi['nama_matkul']); ?>
                        </div>
                        <div class="status-badge status-<?php echo $absensi['status']; ?>">
                            <?php echo ucfirst($absensi['status']); ?>
                        </div>
                        <div class="history-date">
                            <i class="far fa-clock"></i>
                            <?php echo $absensi['formatted_tanggal']; ?> - <?php echo $absensi['formatted_waktu']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada riwayat absensi</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
