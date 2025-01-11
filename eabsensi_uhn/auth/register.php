<?php
session_start();
include('../config/database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $prodi = $_POST['prodi'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM users WHERE nim = :nim");
    $stmt_check->bindParam(':nim', $nim);
    $stmt_check->execute();
    $nim_exists = $stmt_check->fetchColumn();
    
    $response = array();
    
    if ($nim_exists > 0) {
        $response['status'] = 'error';
        $response['message'] = 'NIM sudah terdaftar!';
    } else {
        $stmt = $conn->prepare("INSERT INTO users (nama, nim, prodi, email, password, role, status_validasi) VALUES (:nama, :nim, :prodi, :email, :password, 'mahasiswa', 'pending')");
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':prodi', $prodi);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        
        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Registration successful! Wait for admin approval.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Terjadi kesalahan saat mendaftar.';
        }
    }
    
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.5s ease forwards;
        }

        h2 {
            color: #4a5568;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
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

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: none;
        }

        .alert-success {
            background: #c6f6d5;
            color: #2f855a;
            border: 1px solid #9ae6b4;
        }

        .alert-error {
            background: #fed7d7;
            color: #c53030;
            border: 1px solid #feb2b2;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .form-group.shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">
        <h2>Registrasi Mahasiswa</h2>
        
        <div class="alert alert-success" id="successAlert"></div>
        <div class="alert alert-error" id="errorAlert"></div>
        
        <form id="registrationForm" method="POST">
            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap</label>
                <i class="fas fa-user"></i>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="nim">NIM</label>
                <i class="fas fa-id-card"></i>
                <input type="text" id="nim" name="nim" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="prodi">Program Studi</label>
                <i class="fas fa-graduation-cap"></i>
                <input type="text" id="prodi" name="prodi" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-user-plus"></i> Daftar
            </button>
        </form>
    </div>

    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const successAlert = document.getElementById('successAlert');
            const errorAlert = document.getElementById('errorAlert');
            const loading = document.querySelector('.loading');
            
            // Hide previous alerts
            successAlert.style.display = 'none';
            errorAlert.style.display = 'none';
            
            // Show loading spinner
            loading.style.display = 'flex';
            
            const formData = new FormData(this);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (data.status === 'success') {
                    successAlert.textContent = data.message;
                    successAlert.style.display = 'block';
                    document.getElementById('registrationForm').reset();
                } else {
                    errorAlert.textContent = data.message;
                    errorAlert.style.display = 'block';
                    // Add shake animation to the form group containing NIM
                    document.querySelector('input[name="nim"]').closest('.form-group').classList.add('shake');
                    setTimeout(() => {
                        document.querySelector('input[name="nim"]').closest('.form-group').classList.remove('shake');
                    }, 500);
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                errorAlert.textContent = 'Terjadi kesalahan pada sistem. Silakan coba lagi.';
                errorAlert.style.display = 'block';
            });
        });
    </script>
</body>
</html>