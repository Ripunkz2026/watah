<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/auth.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($_POST['csrf_token'] ?? null) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    exit('Invalid sign-out request.');
}
unset($_SESSION['customer']);
unset($_SESSION['admin']);
session_regenerate_id(true);
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
header('Location: ' . siteUrl('login.php'));
exit;
