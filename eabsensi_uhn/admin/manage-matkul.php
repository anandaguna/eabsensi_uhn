<?php
session_start();
if ($_SESSION['role'] != 'superadmin') {
    header('Location: ../auth/login.php');
    exit;
}

include('../config/database.php');

// Process course addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $stmt = $conn->prepare("INSERT INTO mata_kuliah (nama_matkul, jumlah_sks, dosen, jenis_pertemuan, hari, jam, tanggal) 
                               VALUES (:nama_matkul, :jumlah_sks, :dosen, :jenis_pertemuan, :hari, :jam, :tanggal)");
        
        $result = $stmt->execute([
            ':nama_matkul' => $_POST['nama_matkul'],
            ':jumlah_sks' => $_POST['jumlah_sks'],
            ':dosen' => $_POST['dosen'],
            ':jenis_pertemuan' => $_POST['jenis_pertemuan'],
            ':hari' => $_POST['hari'],
            ':jam' => $_POST['jam'],
            ':tanggal' => $_POST['tanggal']
        ]);

        if ($result) {
            $success_message = "Mata kuliah berhasil ditambahkan!";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Fetch courses data
$stmt = $conn->prepare("SELECT * FROM mata_kuliah ORDER BY tanggal, jam");
$stmt->execute();
$mata_kuliah = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #f5f6fa;
            --accent-color: #2c3e50;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --border-radius: 8px;
            --box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f0f2f5;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .header h1 {
            color: var(--accent-color);
            font-size: 2em;
            margin-bottom: 10px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--accent-color);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 2px solid #e1e1e1;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .btn {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            width: 100%;
        }

        .btn:hover {
            background-color: #357abd;
        }

        .alert {
            padding: 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .table-container {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--secondary-color);
            color: var(--accent-color);
            font-weight: 600;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .badge-online {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-offline {
            background-color: #cce5ff;
            color: #004085;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 1.5em;
            }

            .table-container {
                padding: 10px;
            }

            th, td {
                padding: 10px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Course Management System</h1>
        </div>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama_matkul">Course Name</label>
                        <input type="text" class="form-control" id="nama_matkul" name="nama_matkul" required>
                    </div>

                    <div class="form-group">
                        <label for="jumlah_sks">Credit Hours</label>
                        <input type="number" class="form-control" id="jumlah_sks" name="jumlah_sks" required>
                    </div>

                    <div class="form-group">
                        <label for="dosen">Lecturer Name</label>
                        <input type="text" class="form-control" id="dosen" name="dosen" required>
                    </div>

                    <div class="form-group">
                        <label for="jenis_pertemuan">Meeting Type</label>
                        <select class="form-control" id="jenis_pertemuan" name="jenis_pertemuan" required>
                            <option value="online">Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="hari">Day</label>
                        <select class="form-control" id="hari" name="hari" required>
                            <option value="Senin">Monday</option>
                            <option value="Selasa">Tuesday</option>
                            <option value="Rabu">Wednesday</option>
                            <option value="Kamis">Thursday</option>
                            <option value="Jumat">Friday</option>
                            <option value="Sabtu">Saturday</option>
                            <option value="Minggu">Sunday</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jam">Time</label>
                        <input type="time" class="form-control" id="jam" name="jam" required>
                    </div>

                    <div class="form-group">
                        <label for="tanggal">Date</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn">
                            <i class="fas fa-plus"></i> Add Course
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                        <th>Lecturer</th>
                        <th>Meeting Type</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mata_kuliah as $mk): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mk['nama_matkul']); ?></td>
                            <td><?php echo htmlspecialchars($mk['jumlah_sks']); ?></td>
                            <td><?php echo htmlspecialchars($mk['dosen']); ?></td>
                            <td>
                                <span class="badge <?php echo $mk['jenis_pertemuan'] === 'online' ? 'badge-online' : 'badge-offline'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($mk['jenis_pertemuan'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($mk['hari']); ?></td>
                            <td><?php echo htmlspecialchars($mk['jam']); ?></td>
                            <td><?php echo htmlspecialchars($mk['tanggal']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>