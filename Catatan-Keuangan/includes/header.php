<!-- <?php
// require_once 'auth_check.php';

// // Ambil data user untuk ditampilkan di header
// $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
// $stmt->execute([$_SESSION['user_id']]);
// $user = $stmt->fetch();
// ?>
<!DOCTYPE html>
<html lang="id" data-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Keuangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header class="main-header">
        <div class="header-brand">
            <h1>🐷 Catatan Keuangan</h1>
            <small>Hai, <?= htmlspecialchars($_SESSION['username']) ?></small>
        </div>

        <nav class="header-nav">
            <button class="theme-toggle" onclick="toggleTheme()">
                <?= ($_COOKIE['theme'] ?? 'light') === 'dark' ? '🌙' : '🌞' ?>
            </button>
            <a href="auth/logout.php" class="logout-btn">
                🚪 Logout
            </a>
        </nav>
    </header> -->