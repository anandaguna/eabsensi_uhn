<?php
session_start();
include('../config/database.php');

$login_status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = $_POST['email_or_username'];
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan email atau username
    $stmt = $conn->prepare("SELECT * FROM users WHERE (email = :email_or_username OR nama = :email_or_username) AND status_validasi = 'valid'");
    $stmt->bindParam(':email_or_username', $email_or_username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validasi user dan password menggunakan password_verify
    if ($user && password_verify($password, $user['password'])) {
        // Simpan data user ke session
        $_SESSION['id'] = $user['id'];
        $_SESSION['nama'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // Redirect berdasarkan role
        if ($user['role'] == 'superadmin') {
            header('Location: ../admin/dashboard.php');
            exit;
        } elseif ($user['role'] == 'mahasiswa') {
            header('Location: ../mahasiswa/dashboard.php');
            exit;
        }
    } else {
        $login_status = 'Login gagal! Username/Email atau Password salah.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.5s ease forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: #4a5568;
            font-size: 2rem;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        button:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        .status-message {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #feb2b2;
            text-align: center;
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #4a5568;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: #5a67d8;
            text-decoration: underline;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php if (!empty($login_status)) { ?>
            <div class="status-message shake"><?php echo $login_status; ?></div>
        <?php } ?>

        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="form-group">
                <label for="email_or_username">Username/Email</label>
                <i class="fas fa-user"></i>
                <input 
                    type="text" 
                    id="email_or_username" 
                    name="email_or_username" 
                    required 
                    autocomplete="username"
                    value="<?php echo isset($_POST['email_or_username']) ? htmlspecialchars($_POST['email_or_username']) : ''; ?>"
                >
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <i class="fas fa-lock"></i>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                >
            </div>
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <div class="form-footer">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>