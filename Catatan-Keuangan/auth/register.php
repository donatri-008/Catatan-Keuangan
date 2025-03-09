<?php
require_once '../includes/config.php';
require_once '../includes/auth_check.php'; // Gunakan auth_check yang baru

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validasi input
    if (empty($username)) {
        $error = "Username harus diisi!";
    } elseif (strlen($username) < 3) {
        $error = "Username minimal 3 karakter!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username hanya boleh mengandung huruf, angka, dan underscore!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 8) {
        $error = "Password minimal 8 karakter!";
    } else {
        try {
            // Cek duplikat
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                $error = "Username atau email sudah terdaftar!";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert ke database
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);

                // Tampilkan pesan sukses
                $success = "Pendaftaran berhasil! Silakan login.";

                // Kosongkan form
                $_POST = array();
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan sistem: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun</title>
    <style>
        :root {
            --primary: #ff85a2;
            --secondary: #ffb6c1;
            --background: #fff0f5;
            --text: #4a4a4a;
            --card-bg: #ffffff;
            --radius: 15px;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--background);
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 5px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-container {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.1rem 2rem;
            width: 100%;
            max-width: 400px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 1.2rem;
        }

        .auth-header h1 {
            color: var(--primary);
            margin: 0 0 0.5rem 0;
        }

        .auth-header p {
            color: var(--text);
            margin: 0;
        }

        .form-group {
            margin-bottom: 0.6rem;
        }

        .form-group label {
            display: block;
            color: var(--text);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            width: 92%;
            padding: 0.8rem;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 1rem;
            background: var(--card-bg);
            color: var(--text);
        }

        .btn-primary {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
            transition: transform 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text);
        }

        .text-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .error {
            background-color: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        
        .alert.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 1.5rem;
                margin: 1rem;
            }
            
            .auth-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <form class="auth-form" method="POST">
            <div class="auth-header">
                <h1>🐷 Daftar Baru</h1>
                <p>Buat akun untuk mulai mengelola keuangan</p>
            </div>

            <?php if ($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert success"><?= $success ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>👤 Username</label>
                <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    minlength="3" pattern="[a-zA-Z0-9_]+" title="Hanya boleh mengandung huruf, angka, dan underscore"
                    placeholder="Masukkan username kamu">
            </div>

            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                placeholder="Masukkan email kamu">
            </div>

            <div class="form-group">
                <label>🔒 Password</label>
                <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter">
            </div>

            <button type="submit" class="btn-primary">📝 Daftar Sekarang</button>

            <div class="auth-footer">
                Sudah punya akun?
                <a href="login.php" class="text-link">Masuk disini</a>
            </div>
        </form>
    </div>
</body>
</html>