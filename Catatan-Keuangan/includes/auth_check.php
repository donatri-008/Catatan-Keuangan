<?php
require_once __DIR__ . '/config.php';

// Daftar halaman yang boleh diakses tanpa login
$public_pages = ['index.php', 'login.php', 'register.php'];
$current_page = basename($_SERVER['PHP_SELF']);

// Redirect logic
if (isset($_SESSION['user_id'])) {
    if (in_array($current_page, $public_pages)) {
        header("Location: dashboard.php");
        exit();
    }

    // Validasi session dengan database
    try {
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        if (!$user) {
            session_unset();
            session_destroy();
            header("Location: auth/login.php");
            exit();
        }

        $_SESSION['user_data'] = $user;
    } catch (PDOException $e) {
        error_log("Session validation failed: " . $e->getMessage());
        session_unset();
        session_destroy();
        header("Location: auth/login.php");
        exit();
    }
} else {
    if (!in_array($current_page, $public_pages)) {
        header("Location: auth/login.php");
        exit();
    }
}
?>