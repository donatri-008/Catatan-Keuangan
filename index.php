<?php
require_once __DIR__.'/includes/config.php';

// Redirect jika sudah login
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catatan Keuangan</title>
    <link rel="stylesheet" href="./assets/css/index.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🐷 Catatan Keuangan</h1>
            <p>Kelola keuangan Anda dengan mudah dan aman</p>
        </header>
        
        <main>
            <div class="cta-section">
                <a href="auth/login.php" class="auth-button">🔑 Mulai Sekarang</a>
            </div>
            
            <div class="features">
                <div class="feature-card">
                    <h3>📈 Grafik Keuangan</h3>
                    <p>Visualisasi data keuangan interaktif</p>
                </div>
                <div class="feature-card">
                    <h3>📅 Filter Tanggal</h3>
                    <p>Analisis berdasarkan periode waktu</p>
                </div>
                <div class="feature-card">
                    <h3>🔒 Keamanan Data</h3>
                    <p>Enkripsi AES-256 untuk data sensitif</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>