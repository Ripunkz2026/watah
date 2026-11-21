<?php
// Include after app/bootstrap.php and before any HTML output.
function authDatabase(): PDO
{
    return appDatabase();
}

function requireAdmin(): void
{
    if (empty($_SESSION['admin'])) {
        header('Location: ' . siteUrl('login.php'));
        exit;
    }
    $query = authDatabase()->prepare('SELECT id,name,company,email FROM admins WHERE id=?');
    $query->execute([$_SESSION['admin']['id']]);
    $admin = $query->fetch();
    if (!$admin) { unset($_SESSION['admin']); header('Location: ' . siteUrl('login.php')); exit; }
    $_SESSION['admin'] = $admin;
}

function requireCustomer(): void
{
    if (empty($_SESSION['customer'])) { header('Location: ' . siteUrl('login.php')); exit; }
    $query = authDatabase()->prepare('SELECT id,name,company,email,state,zip FROM customers WHERE id=?');
    $query->execute([$_SESSION['customer']['id']]);
    $customer = $query->fetch();
    if (!$customer) { unset($_SESSION['customer']); header('Location: ' . siteUrl('login.php')); exit; }
    $_SESSION['customer'] = $customer;
}

function initializeAccountTables(PDO $pdo): void
{
    foreach (['customers', 'admins'] as $table) {
    $deliveryColumns = $table === 'customers' ? 'state CHAR(2) NULL, zip CHAR(5) NULL,' : '';
    $pdo->exec('CREATE TABLE IF NOT EXISTS ' . $table . ' (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        company VARCHAR(160) NOT NULL,
        email VARCHAR(254) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        ' . $deliveryColumns . '
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }
}

function authEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
