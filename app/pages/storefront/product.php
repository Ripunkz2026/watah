<?php
require_once APP_ROOT . '/app/bootstrap.php';
$products = require APP_ROOT . '/app/data/products.php';
$productId = (int)($_GET['id'] ?? 0);
$product = null;
foreach ($products as $item) {
    if ((int)$item['id'] === $productId) {
        $product = $item;
        break;
    }
}

if (!$product) {
    header('Location: ' . siteUrl('shop.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    addToCart($product['id'], $quantity);
    header('Location: ' . siteUrl('cart.php'));
    exit;
}

$isExpressPage = true;
$isProductPage = true;
$pageTitle = $product['name'];
$pageDescription = $product['description'];
$isHalf = ($product['pallet_type'] ?? '') === 'Half pallet';
$palletLabel = $isHalf ? 'Half pallet' : 'Full pallet';
$displayName = str_replace(' - Half Pallet', '', $product['name']);
$brandCode = explode('-', $product['sku'])[0];
$variants = array_filter($products, fn($item) => explode('-', $item['sku'])[0] === $brandCode);
require APP_ROOT . '/app/views/layouts/header.php';
?>

<main id="main-content" class="pd-page lp-wrap">
    <nav class="pd-breadcrumb" aria-label="Breadcrumb">
        <a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span>
        <a href="<?php echo siteUrl('index.php#pallets'); ?>">The pallet collection</a><span aria-hidden="true">/</span>
        <span aria-current="page"><?php echo htmlspecialchars($product['name']); ?></span>
    </nav>

    <section class="pd-hero" aria-labelledby="product-title">
        <div class="pd-gallery">
            <div class="pd-gallery-heading"><span class="lp-eyebrow">YOUR NEXT RESTOCK</span><span class="pd-pill"><?php echo $palletLabel; ?></span></div>
            <div class="pd-image-stage"><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="660" height="660" fetchpriority="high"></div>
            <div class="pd-gallery-caption"><span>BUSINESS-SIZED HYDRATION</span><strong><?php echo number_format($product['total_bottles_per_pallet']); ?> bottles. One <?php echo strtolower($palletLabel); ?>.</strong></div>
        </div>

        <div class="pd-summary">
            <p class="lp-eyebrow"><?php echo htmlspecialchars($product['category']); ?> <span aria-hidden="true">/</span> <?php echo htmlspecialchars($product['bottle_size']); ?></p>
            <h1 id="product-title"><?php echo htmlspecialchars($displayName); ?><span><?php echo $palletLabel; ?>.</span></h1>
            <p class="pd-description"><?php echo htmlspecialchars($product['description']); ?></p>
            <div class="pd-price"><strong><?php echo formatPrice($product['price_per_pallet']); ?></strong><span>per <?php echo strtolower($palletLabel); ?></span></div>
            <p class="pd-freight-note">Product price only. Freight quoted separately.</p>

            <nav class="pd-variants" aria-label="Choose pallet size">
                <?php foreach ($variants as $variant): $variantLabel = $variant['pallet_type'] ?? 'Full pallet'; ?>
                    <a href="<?php echo siteUrl('product.php?id=' . $variant['id']); ?>"<?php if ($variant['id'] === $product['id']): ?> aria-current="page"<?php endif; ?>>
                        <strong><?php echo htmlspecialchars($variantLabel); ?></strong>
                        <span><?php echo $variant['cases_per_pallet']; ?> cases &middot; <?php echo formatPrice($variant['price_per_pallet']); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <form method="post" class="pd-order-form" action="<?php echo siteUrl('product.php?id=' . $product['id']); ?>">
                <input type="hidden" name="add_to_cart" value="1">
                <label for="quantity">Quantity <span>(<?php echo strtolower($palletLabel); ?>s)</span></label>
                <div class="pd-order-actions">
                    <select id="quantity" name="quantity" aria-describedby="order-note">
                        <?php for ($i = $product['min_order']; $i <= min(999, $product['min_order'] + 5); $i++): ?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php endfor; ?>
                    </select>
                    <button type="submit" class="lp-button lp-button-primary" <?php echo productCanOrder($product) ? '' : 'disabled'; ?>><?php echo productCanOrder($product) ? 'Add to cart' : 'Currently unavailable'; ?> <span aria-hidden="true">&nearr;</span></button>
                </div>
                <p id="order-note" class="pd-order-note">Product minimum: <?php echo $product['min_order']; ?> pallet(s). Delivery areas may require a larger order.</p>
            </form>
            <p class="pd-availability"><?php echo !productCanOrder($product) ? 'Currently unavailable' : ($product['inventory'] === null ? 'Available &mdash; pallet count to be confirmed' : number_format($product['inventory']) . ' pallets available'); ?></p>
            <a class="pd-delivery-link" href="<?php echo siteUrl('service-area.php'); ?>">Check delivery to your ZIP code <span aria-hidden="true">&rarr;</span></a>
        </div>
    </section>

    <ul class="pd-highlights" aria-label="Product highlights">
        <?php foreach ($product['features'] as $feature): ?><li><span aria-hidden="true">&#10003;</span><?php echo htmlspecialchars($feature); ?></li><?php endforeach; ?>
    </ul>

    <div class="pd-details-grid">
        <section class="pd-specs" aria-labelledby="specs-title">
            <p class="lp-eyebrow">EVERY DETAIL, UP FRONT</p><h2 id="specs-title">Know your pallet.</h2>
            <dl>
                <?php foreach (['SKU' => $product['sku'], 'Bottle size' => $product['bottle_size'], 'Bottles per case' => $product['bottles_per_case'], 'Cases per ' . strtolower($palletLabel) => $product['cases_per_pallet'], 'Total bottles' => number_format($product['total_bottles_per_pallet']), 'Pallet dimensions' => $product['pallet_dimensions'], 'Pallet weight' => $product['pallet_weight'], 'Warehouse' => $product['warehouse']] as $label => $value): ?>
                    <div><dt><?php echo htmlspecialchars($label); ?></dt><dd><?php echo htmlspecialchars((string)$value); ?></dd></div>
                <?php endforeach; ?>
            </dl>
        </section>
        <aside class="pd-help" aria-labelledby="help-title">
            <span class="pd-help-icon" aria-hidden="true">&nearr;</span>
            <p class="lp-eyebrow">LET&rsquo;S GET YOU STOCKED</p><h2 id="help-title">Big order?<br>We&rsquo;re here to help.</h2>
            <p>Check your delivery area and order requirements, or talk to our team about your next restock.</p>
            <a class="lp-button lp-button-light" href="<?php echo siteUrl('contact.php'); ?>">Talk to our team <span aria-hidden="true">&rarr;</span></a>
            <a class="pd-shipping-link" href="<?php echo siteUrl('shipping-policy.php'); ?>">Shipping &amp; delivery details <span aria-hidden="true">&nearr;</span></a>
        </aside>
    </div>
</main>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
