<?php
require_once __DIR__ . '/includes/auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>💸 Catatan Keuangan</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js/dist/chart.umd.js"></script>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>💸 Catatan Keuangan</h1>
            <div class="theme-switch">
                <button onclick="toggleTheme()">🌞</button>
            </div>
        </header>
        <section class="greeting-section">
            <div class="welcome-message">
                <h2>Selamat datang, <?= sanitize($_SESSION['user_data']['username']) ?>! 👋</h2>
                <p>Mulai kelola keuangan Anda hari ini</p>
            </div>
        </section>

        <!-- Dashboard -->
        <div class="dashboard">
            <div class="card balance-card">
                <h2>💰 Saldo Sekarang</h2>
                <div class="balance-amount" id="balance">Rp0</div>
                <div class="summary">
                    <div class="summary-item income">
                        <span>Pemasukan</span>
                        <span id="income">Rp0</span>
                    </div>
                    <div class="summary-item expense">
                        <span>Pengeluaran</span>
                        <span id="expense">Rp0</span>
                    </div>
                </div>
            </div>

            <div class="card chart-card">
                <h2>📊 Grafik Keuangan</h2>
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <!-- Form Transaksi -->
        <div class="card form-card">
            <h2>✏️ Tambah/Edit Transaksi</h2>
            <form id="transactionForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>📝 Nama Transaksi</label>
                        <input type="text" id="transactionName" required>
                    </div>
                    <div class="form-group">
                        <label>💵 Jumlah (Rp)</label>
                        <input type="number" id="transactionAmount" required>
                    </div>
                    <div class="form-group">
                        <label>🗓️ Tanggal</label>
                        <input type="date" id="transactionDate" required>
                    </div>
                    <div class="form-group">
                        <label>📦 Kategori</label>
                        <select id="transactionCategory">
                            <option value="makanan">🍔 Makanan</option>
                            <option value="transportasi">🚗 Transportasi</option>
                            <option value="belanja">🛍️ Belanja</option>
                            <option value="hiburan">🎮 Hiburan</option>
                            <option value="gaji">💼 Gaji</option>
                            <option value="lainnya">❔ Lainnya</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>🔖 Tipe</label>
                        <select id="transactionType">
                            <option value="income">⬆️ Pemasukan</option>
                            <option value="expense">⬇️ Pengeluaran</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" id="submitButton">➕ Tambah Transaksi</button>
                    <button type="button" onclick="cancelEdit()">❌ Batal Edit</button>
                </div>
            </form>
        </div>

        <!-- Filter -->
        <div class="card tools-card">
            <div class="filter-group">
                <label>📅 Filter Tanggal:</label>
                <input type="date" id="startDate">
                <span>sampai</span>
                <input type="date" id="endDate">
                <button onclick="filterTransactions()">🔍 Filter</button>
                <button onclick="clearFilter()">♻️ Reset</button>
            </div>
        </div>

        <!-- Daftar Transaksi -->
        <div class="card transaction-card">
            <h2>📜 Riwayat Transaksi</h2>
            <div id="transactions"></div>
        </div>
    </div>

    <button a href="auth/logout.php" class="logout-btn">🚪 Logout</a></button>

    <script src="./assets/js/script.js"></script>
</body>
</html>