<?php
// Run from the command line; never expose database installation over HTTP.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('APP_ROOT', dirname(__DIR__));
require APP_ROOT . '/config/app.php';
require APP_ROOT . '/app/support/helpers.php';
$name = getenv('DB_NAME') ?: 'pure_life_demo';
if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) throw new RuntimeException('Invalid database name.');
$pdo = new PDO('mysql:host=' . (getenv('DB_HOST') ?: '127.0.0.1') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';charset=utf8mb4', getenv('DB_USER') ?: 'root', getenv('DB_PASS') ?: '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS `' . $name . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$pdo->exec('USE `' . $name . '`');
foreach (explode(';', file_get_contents(__DIR__ . '/schema.sql')) as $sql) {
    if (trim($sql) !== '') $pdo->exec($sql);
}
// Upgrade existing accounts without inventing a delivery location for them.
$customerColumns = $pdo->query('SHOW COLUMNS FROM customers')->fetchAll(PDO::FETCH_COLUMN);
foreach (['state' => 'CHAR(2)', 'zip' => 'CHAR(5)'] as $column => $type) {
    if (!in_array($column, $customerColumns, true)) {
        $pdo->exec('ALTER TABLE customers ADD COLUMN ' . $column . ' ' . $type . ' NULL');
    }
}
$pdo->exec("INSERT IGNORE INTO schema_migrations (version) VALUES ('002_customer_delivery_location')");
$products = require __DIR__ . '/seeds/products.php';
require __DIR__ . '/seeds/shipping.php';
$pdo->beginTransaction();
try {
    $insert = $pdo->prepare('INSERT INTO products (id, sku, name, slug, category, price_per_pallet, min_order, inventory, status, warehouse, bottle_size, bottles_per_case, cases_per_pallet, total_bottles_per_pallet, pallet_weight, pallet_dimensions, description, features, image, pallet_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE id=id');
    foreach ($products as $product) {
        $insert->execute([$product['id'], $product['sku'], $product['name'], $product['slug'], $product['category'], $product['price_per_pallet'], $product['min_order'], $product['inventory'], $product['status'], $product['warehouse'], $product['bottle_size'], $product['bottles_per_case'], $product['cases_per_pallet'], $product['total_bottles_per_pallet'], $product['pallet_weight'], $product['pallet_dimensions'], $product['description'], json_encode($product['features']), substr($product['image'], strlen(APP_BASE_URL . '/assets/')), $product['pallet_type'] ?? 'Full pallet']);
    }
    // Seed delivery rules once. Re-running migrations never undoes admin edits.
    $seeded = $pdo->query("SELECT COUNT(*) FROM schema_migrations WHERE version='001_initial_seed'")->fetchColumn();
    if (!$seeded) {
        $stateNames = ['AZ'=>'Arizona','CA'=>'California','CO'=>'Colorado','FL'=>'Florida','NV'=>'Nevada','NM'=>'New Mexico','TX'=>'Texas','UT'=>'Utah'];
        $stateInsert = $pdo->prepare('INSERT INTO shipping_states (code, name, zone, min_order, enabled, restrict_zips) VALUES (?,?,?,?,1,?) ON DUPLICATE KEY UPDATE code=code');
        $zipInsert = $pdo->prepare('INSERT IGNORE INTO shipping_zips (state_code, zip) VALUES (?,?)');
        foreach ($allowedStates as $code) {
            $stateInsert->execute([$code, $stateNames[$code], $stateRules[$code]['zone'], $stateRules[$code]['minOrder'], !empty($zipRestrictions[$code]) ? 1 : 0]);
            foreach ($zipRestrictions[$code] ?? [] as $zip) $zipInsert->execute([$code, $zip]);
        }
        $pdo->exec("INSERT INTO schema_migrations (version) VALUES ('001_initial_seed')");
    }
    $pdo->commit();
} catch (Throwable $error) { $pdo->rollBack(); throw $error; }
echo "Database ready: $name. Existing accounts and records preserved.\n";
