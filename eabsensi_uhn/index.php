<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage E-AbsensiUHN</title>
    <style>
        /* Reset dasar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
        }

        header {
            background-color: #6c63ff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        header nav a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            margin-left: 20px;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            padding-top: 100px; /* Offset header */
            background: linear-gradient(to bottom, #f4f7fc, #e2e8f0);
            padding: 60px 30px;
        }

        .main-content h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
        }

        .main-content p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .cta-button {
            background-color: #6c63ff;
            color: white;
            padding: 15px 30px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: #5a53e6;
        }

        .features {
            padding: 60px 30px;
            background-color: #fff;
            text-align: center;
        }

        .features h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 30px;
        }

        .features .feature {
            display: inline-block;
            width: 30%;
            margin: 0 1.5%;
            padding: 20px;
            background-color: #f4f7fc;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .features .feature h3 {
            font-size: 24px;
            color: #6c63ff;
            margin-bottom: 15px;
        }

        .features .feature p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            position: relative;
            width: 100%;
            margin-top: 30px;
        }

    </style>
</head>
<body>
    <!-- Header dengan menu Daftar dan Login -->
    <header>
        <div class="logo">E-AbsensiUHN</div>
        <nav>
            <a href="auth/register.php">Daftar</a>
            <a href="auth/login.php">Login</a>
        </nav>
    </header>

    <!-- Konten utama -->
    <div class="main-content">
        <div>
            <h1>Om Swastyastu, Selamat datang di website E-AbsensiUHN</h1>
            <p>Website E-AbsensiUHN hadir untuk mempermudah proses absensi mahasiswa di Universitas Hindu Negeri. Dengan fitur yang mudah digunakan, Anda dapat melakukan absensi secara online dan memantau kehadiran secara real-time.</p>
            <p>Segera daftar dan login untuk memulai perjalanan akademik Anda dengan lebih mudah!</p>
            <a href="auth/register.php" class="cta-button">Daftar Sekarang</a>
        </div>
    </div>

    <!-- Features Section -->
    <section class="features">
        <h2>Fitur Utama</h2>
        <div class="feature">
            <h3>Mudah Digunakan</h3>
            <p>Antarmuka yang sederhana dan mudah digunakan untuk semua pengguna.</p>
        </div>
        <div class="feature">
            <h3>Real-time Monitoring</h3>
            <p>Memantau kehadiran mahasiswa secara real-time dari mana saja.</p>
        </div>
        <div class="feature">
            <h3>Aman & Terpercaya</h3>
            <p>Keamanan data Anda adalah prioritas utama kami.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 E-AbsensiUHN. All rights reserved.</p>
    </footer>
</body>
</html>