<?php
session_start();
if ($_SESSION['role'] != 'mahasiswa') {
    header('Location: ../auth/login.php');
    exit;
}

// Include database connection
include('../config/database.php');

// Fetch user profile data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $prodi = $_POST['prodi'];
    
    $stmt = $conn->prepare("UPDATE users SET nama = :nama, email = :email, prodi = :prodi WHERE id = :id");
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':prodi', $prodi);
    $stmt->bindParam(':id', $_SESSION['id']);
    
    if ($stmt->execute()) {
        $_SESSION['nama'] = $nama;
        $update_status = "Profile berhasil diperbarui!";
    } else {
        $update_status = "Gagal memperbarui profile!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
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
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem 2rem;
            color: white;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .nav-menu {
            display: flex;
            gap: 1.5rem;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }

        .nav-link i {
            margin-right: 0.5rem;
        }

        .main-content {
            max-width: 1200px;
            margin: 6rem auto 2rem;
            padding: 0 2rem;
        }

        .welcome-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease;
        }

        .welcome-text {
            color: #4a5568;
            margin-bottom: 1rem;
        }

        .greeting {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .profile-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: none;
        }

        .section-title {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-1px);
        }

        .status-message {
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .status-success {
            background: #c6f6d5;
            color: #2f855a;
        }

        .status-error {
            background: #fed7d7;
            color: #c53030;
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

        .logout-btn {
            background: rgba(255,255,255,0.1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.2);
        }

    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">E-ABSENSI_UHN</div>
            <nav class="nav-menu">
                <a href="absensi.php" class="nav-link">
                    <i class="fas fa-clock"></i>Absensi
                </a>
                <a href="#" class="nav-link" onclick="toggleProfile()">
                    <i class="fas fa-user"></i>Profile
                </a>
                <a href="../auth/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="greeting">Om Swastyastu, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</div>
            <p class="welcome-text">Selamat datang di Sistem Informasi Akademik Mahasiswa.</p>
        </div>

        <!-- Profile Section -->
        <div id="profileSection" class="profile-section">
            <h2 class="section-title">Edit Profile</h2>
            
            <?php if (isset($update_status)): ?>
                <div class="status-message <?php echo strpos($update_status, 'berhasil') !== false ? 'status-success' : 'status-error'; ?>">
                    <?php echo $update_status; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">NIM</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['nim']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Program Studi</label>
                    <input type="text" name="prodi" class="form-control" value="<?php echo htmlspecialchars($user['prodi']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </main>

    <script>
        function toggleProfile() {
            const profileSection = document.getElementById('profileSection');
            profileSection.style.display = profileSection.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>