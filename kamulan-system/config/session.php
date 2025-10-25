<?php
// session.php - start session and helper for role checks
if (session_status() === PHP_SESSION_NONE) session_start();

function require_login() {
    if (!isset($_SESSION['user'])) {
        header('Location: /kamulan-system/auth/login.php');
        exit;
    }
}

function require_role($roles = []) {
    require_login();
    $role = $_SESSION['user']['role'] ?? 'buyer';
    if (!in_array($role, (array)$roles)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
?>
