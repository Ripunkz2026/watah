<?php
session_start();

const APP_NAME = 'Pure Life Wholesale';
const APP_BASE_URL = '/watah';

$allowedStates = ['AZ', 'CA', 'CO', 'FL', 'NV', 'NM', 'TX', 'UT'];
$stateRules = [
    'AZ' => ['minOrder' => 2, 'zone' => 'Southwest'],
    'CA' => ['minOrder' => 3, 'zone' => 'West'],
    'CO' => ['minOrder' => 2, 'zone' => 'Mountain'],
    'FL' => ['minOrder' => 2, 'zone' => 'Southeast'],
    'NV' => ['minOrder' => 2, 'zone' => 'West'],
    'NM' => ['minOrder' => 2, 'zone' => 'Southwest'],
    'TX' => ['minOrder' => 2, 'zone' => 'South'],
    'UT' => ['minOrder' => 2, 'zone' => 'Mountain'],
];

$zipRestrictions = [
    'AZ' => ['85001', '85301', '86001'],
    'CA' => ['90001', '90210', '94105'],
    'CO' => ['80001', '80111', '80202'],
    'FL' => ['33004', '33131', '33602'],
    'NV' => ['89101', '89431', '89501'],
    'NM' => ['87101', '87001', '88201'],
    'TX' => ['75001', '75201', '77002'],
    'UT' => ['84101', '84001', '84601'],
];

function siteUrl(string $path = ''): string
{
    return APP_BASE_URL . '/' . ltrim($path, '/');
}

function assetUrl(string $path): string
{
    return APP_BASE_URL . '/assets/' . ltrim($path, '/');
}

function currentPage(string $name): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $route = parse_url($uri, PHP_URL_PATH);
    return $route === $name ? 'text-blue-700 border-blue-700' : 'text-slate-600 hover:text-blue-700';
}

function formatPrice(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function productImage(string $name): string
{
    return 'https://images.unsplash.com/' . $name . '?auto=format&fit=crop&w=900&q=80';
}

function bootstrapCart(): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function addToCart(int $productId, int $quantity = 1): void
{
    bootstrapCart();
    if ($quantity < 1) {
        return;
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function cartCount(): int
{
    bootstrapCart();
    return array_sum($_SESSION['cart']);
}

function cartTotal(): float
{
    $products = require __DIR__ . '/data.php';
    $total = 0.0;
    foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
        foreach ($products as $product) {
            if ((int)$product['id'] === (int)$id) {
                $total += (float)$product['price_per_pallet'] * $qty;
            }
        }
    }
    return $total;
}

function isShippingAllowed(string $stateCode, string $zipCode): array
{
    global $allowedStates, $stateRules, $zipRestrictions;

    $state = strtoupper(trim($stateCode));
    $zip = trim($zipCode);

    if (!in_array($state, $allowedStates, true)) {
        return ['allowed' => false, 'message' => 'This state is not currently approved for pallet sales.'];
    }

    if (isset($zipRestrictions[$state]) && !empty($zipRestrictions[$state])) {
        if ($zip === '' || !in_array($zip, $zipRestrictions[$state], true)) {
            return ['allowed' => false, 'message' => 'The ZIP code is not currently eligible for delivery in this state.'];
        }
    }

    return ['allowed' => true, 'message' => 'Delivery is available for this destination.', 'rule' => $stateRules[$state] ?? null];
}
