<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . 'includes/config.php';
require_once __DIR__ . 'includes/auth_check.php';

header('Content-Type: application/json');

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $user_id = $_SESSION['user_id'];

    if ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validasi data
        if (empty($data['name']) || empty($data['amount'])) {
            throw new Exception("Nama dan jumlah harus diisi!");
        }

        $stmt = $conn->prepare("INSERT INTO transactions 
            (user_id, name, amount, date, category, type)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $data['name'],
            $data['amount'],
            $data['date'],
            $data['category'],
            $data['type']
        ]);

        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method tidak diizinkan']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}