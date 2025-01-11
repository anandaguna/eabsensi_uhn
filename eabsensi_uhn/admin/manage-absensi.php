<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'superadmin') {
    header('Location: ../auth/login.php');
    exit;
}

include('../config/database.php');

// Flash message handling
if (!isset($_SESSION['flash'])) {
    $_SESSION['flash'] = [];
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Proses tambah absensi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['tambah_absensi'])) {
            $user_id = $_POST['user_id'];
            $matkul_id = $_POST['matkul_id'];
            $status = $_POST['status'];
            $tanggal = $_POST['tanggal'];

            $stmt = $conn->prepare("INSERT INTO absensi (user_id, matkul_id, status, tanggal) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $matkul_id, $status, $tanggal]);
            setFlashMessage('success', 'Absensi berhasil ditambahkan');
        } elseif (isset($_POST['edit_absensi'])) {
            $id = $_POST['id'];
            $user_id = $_POST['user_id'];
            $matkul_id = $_POST['matkul_id'];
            $status = $_POST['status'];
            $tanggal = $_POST['tanggal'];

            $stmt = $conn->prepare("UPDATE absensi SET user_id = ?, matkul_id = ?, status = ?, tanggal = ? WHERE id = ?");
            $stmt->execute([$user_id, $matkul_id, $status, $tanggal, $id]);
            setFlashMessage('success', 'Absensi berhasil diperbarui');
        }
    } catch (PDOException $e) {
        setFlashMessage('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
    header('Location: manage-absensi.php');
    exit;
}

// Ambil daftar mahasiswa
$stmt_mahasiswa = $conn->prepare("SELECT id, nama FROM users WHERE role = 'mahasiswa'");
$stmt_mahasiswa->execute();
$mahasiswa = $stmt_mahasiswa->fetchAll(PDO::FETCH_ASSOC);

// Ambil daftar mata kuliah
$stmt_matkul = $conn->prepare("SELECT id, nama_matkul FROM mata_kuliah");
$stmt_matkul->execute();
$mata_kuliah = $stmt_matkul->fetchAll(PDO::FETCH_ASSOC);

// Ambil data absensi untuk edit jika ada parameter id
$edit_absensi = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM absensi WHERE id = ?");
    $stmt->execute([$id]);
    $edit_absensi = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Ambil daftar absensi
$stmt = $conn->prepare("
    SELECT a.*, u.nama as nama_mahasiswa, m.nama_matkul 
    FROM absensi a
    JOIN users u ON a.user_id = u.id
    JOIN mata_kuliah m ON a.matkul_id = m.id
");
$stmt->execute();
$absensi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f3f4f7, #e2e8f0);
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2, h3 {
            color: #1a202c;
            text-align: center;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background: #4a90e2;
            color: white;
        }
        table tr:hover {
            background: #f1f5f9;
        }
        .form-container {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
        }
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }
        .btn {
            padding: 12px 20px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #357ab7;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #55595c;
        }
        .action-buttons a {
            margin-right: 10px;
        }
        .alert {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Absensi</h2>

        <!-- Flash Messages -->
        <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert <?php echo $_SESSION['flash']['type']; ?>" style="display: block;">
                <?php 
                echo $_SESSION['flash']['message'];
                unset($_SESSION['flash']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah/Edit Absensi -->
        <div class="form-container">
            <h3><?php echo $edit_absensi ? 'Edit Absensi' : 'Tambah Absensi'; ?></h3>
            <form method="POST" action="manage-absensi.php">
                <?php if ($edit_absensi): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_absensi['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Mahasiswa</label>
                    <select name="user_id" required>
                        <?php foreach ($mahasiswa as $mhs): ?>
                            <option value="<?php echo $mhs['id']; ?>" 
                                <?php echo ($edit_absensi && $edit_absensi['user_id'] == $mhs['id']) ? 'selected' : ''; ?>>
                                <?php echo $mhs['nama']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mata Kuliah</label>
                    <select name="matkul_id" required>
                        <?php foreach ($mata_kuliah as $matkul): ?>
                            <option value="<?php echo $matkul ['id']; ?>" 
                                <?php echo ($edit_absensi && $edit_absensi['matkul_id'] == $matkul['id']) ? 'selected' : ''; ?>>
                                <?php echo $matkul['nama_matkul']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" required>
                        <option value="hadir" <?php echo ($edit_absensi && $edit_absensi['status'] == 'hadir') ? 'selected' : ''; ?>>Hadir</option>
                        <option value="izin" <?php echo ($edit_absensi && $edit_absensi['status'] == 'izin') ? 'selected' : ''; ?>>Izin</option>
                        <option value="sakit" <?php echo ($edit_absensi && $edit_absensi['status'] == 'sakit') ? 'selected' : ''; ?>>Sakit</option>
                        <option value="alpa" <?php echo ($edit_absensi && $edit_absensi['status'] == 'alpa') ? 'selected' : ''; ?>>Alpa</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" 
                        value="<?php echo $edit_absensi ? $edit_absensi['tanggal'] : ''; ?>" 
                        required>
                </div>

                <div class="form-group">
                    <?php if ($edit_absensi): ?>
                        <button type="submit" name="edit_absensi" class="btn">Update Absensi</button>
                        <a href="manage-absensi.php" class="btn btn-secondary">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="tambah_absensi" class="btn">Tambah Absensi</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Absensi -->
        <table>
            <thead>
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>Nama Mata Kuliah</th>
                    <th>Status Absensi</th>
                    <th>Tanggal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($absensi as $absen): ?>
                <tr>
                    <td><?php echo $absen['nama_mahasiswa']; ?></td>
                    <td><?php echo $absen['nama_matkul']; ?></td>
                    <td><?php echo ucfirst($absen['status']); ?></td>
                    <td><?php echo $absen['tanggal']; ?></td>
                    <td class="action-buttons">
                        <a href="?edit=1&id=<?php echo $absen['id']; ?>" class="btn" style="background-color: #28a745;">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>