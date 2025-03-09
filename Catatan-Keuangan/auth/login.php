<?php
require_once __DIR__ . '/../includes/config.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_data'] = $user;
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error = "Email atau password salah!";
        }
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan sistem";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .auth-container {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .auth-container h1 {
            text-align: center;
            color: var(--primary);
            margin: 0 0 2rem 0;
        }
        
        .auth-container p {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .auth-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 1.5rem;
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
            margin-bottom: 1rem;
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
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 10px 0;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
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
        <form method="POST">
            <div class="auth-header">
                <h1>🔐 Login</h1>
                <p>Buat akun untuk mulai mengelola keuangan</p>
            </div>
            <?php if ($error): ?>
                <div class="alert error"><?= $error ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>📧 Email</label>
                <input type="email" name="email" placeholder="Masukkan email kamu" required>
            </div>

            <div class="form-group">
                <label>🔒 Password</label>
                <input type="password" name="password" placeholder="Masukkan password kamu" required>
            </div>

            <button type="submit" class="btn-primary">Masuk</button>
            
            <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
        </form>
    </div>
</body>
</html>