<?php
require_once __DIR__ . '\includes\config.php';
require_once __DIR__ . '\includes\auth_check.php';

header('Content-Type: text/plain'); // Pastikan response dalam format teks

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $user_id = $_SESSION['user_id'];

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            throw new Exception("Data yang dikirim tidak valid.");
        }

        if (empty($data['name']) || empty($data['amount'])) {
            throw new Exception("Nama dan jumlah harus diisi!");
        }

        $stmt = $conn->prepare("INSERT INTO transactions (user_id, name, amount, date, category, type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $data['name'],
            $data['amount'],
            $data['date'],
            $data['category'],
            $data['type']
        ]);

        echo "Transaksi berhasil disimpan";
    } else {
        http_response_code(405);
        echo "Metode tidak diizinkan";
    }

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>