<?php
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
    if ($quantity < 1 || $quantity > 999) {
        return;
    }

    $query = appDatabase()->prepare('SELECT status,inventory,min_order FROM products WHERE id=?');
    $query->execute([$productId]);
    $product = $query->fetch();
    if (!$product || !productCanOrder($product)) return;

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = min(999, $_SESSION['cart'][$productId] + $quantity);
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
    $products = require APP_ROOT . '/app/data/products.php';
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

    if (isset($zipRestrictions[$state])) {
        if ($zip === '' || !in_array($zip, $zipRestrictions[$state], true)) {
            return ['allowed' => false, 'message' => 'The ZIP code is not currently eligible for delivery in this state.'];
        }
    }

    return ['allowed' => true, 'message' => 'Delivery is available for this destination.', 'rule' => $stateRules[$state] ?? null];
}
