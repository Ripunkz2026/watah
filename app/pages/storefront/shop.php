<?php
require_once APP_ROOT . '/app/bootstrap.php';
$products = require APP_ROOT . '/app/data/products.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    addToCart($productId, $quantity);
    header('Location: ' . siteUrl('cart.php'));
    exit;
}
$category = is_string($_GET['category'] ?? null) ? $_GET['category'] : '';
$palletSize = is_string($_GET['pallet'] ?? null) ? $_GET['pallet'] : '';
$sort = is_string($_GET['sort'] ?? null) ? $_GET['sort'] : '';
$categories = array_values(array_unique(array_column($products, 'category')));
if (!in_array($category, $categories, true)) $category = '';
if (!in_array($palletSize, ['Full pallet', 'Half pallet'], true)) $palletSize = '';
if (!in_array($sort, ['price-asc', 'price-desc'], true)) $sort = '';
$filteredProducts = array_filter($products, fn($product) =>
    ($category === '' || $product['category'] === $category) &&
    ($palletSize === '' || ($product['pallet_type'] ?? 'Full pallet') === $palletSize)
);
if ($sort !== '') usort($filteredProducts, fn($a, $b) => $sort === 'price-asc' ? $a['price_per_pallet'] <=> $b['price_per_pallet'] : $b['price_per_pallet'] <=> $a['price_per_pallet']);
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Shop pallets';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span><span>Shop pallets</span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">THE PALLET COLLECTION</p><h1>A little restock.<br><span>Or a whole lot.</span></h1><p class="cx-intro">Your favorite water and sports drinks, in quantities that work for your business. Pick a brand. Choose your pallet.</p></div><a class="lp-text-link" href="<?php echo siteUrl('service-area.php'); ?>">Check delivery to your door <span aria-hidden="true">&rarr;</span></a></header>
    <form method="get" class="cx-filter-bar" aria-label="Filter products">
        <div class="cx-field"><label for="category">Product type</label><select id="category" name="category"><option value="">All products</option><?php foreach ($categories as $option): ?><option value="<?php echo htmlspecialchars($option); ?>" <?php echo $category === $option ? 'selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option><?php endforeach; ?></select></div>
        <div class="cx-field"><label for="pallet">Pallet size</label><select id="pallet" name="pallet"><option value="">Full &amp; half pallets</option><?php foreach (['Full pallet', 'Half pallet'] as $option): ?><option <?php echo $palletSize === $option ? 'selected' : ''; ?>><?php echo $option; ?></option><?php endforeach; ?></select></div>
        <div class="cx-field"><label for="sort">Sort by</label><select id="sort" name="sort"><option value="">Featured</option><option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Price: low to high</option><option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Price: high to low</option></select></div>
        <button class="lp-button lp-button-primary" type="submit">Apply filters <span aria-hidden="true">&rarr;</span></button>
        <?php if ($category || $palletSize || $sort): ?><a class="cx-link" href="<?php echo siteUrl('shop.php'); ?>">Reset</a><?php endif; ?>
    </form>
    <p class="cx-catalog-count"><?php echo count($filteredProducts); ?> products <span aria-hidden="true">&middot;</span> Full cases. Familiar brands.</p>
    <div class="lp-product-grid">
        <?php foreach ($filteredProducts as $product): $isHalf = ($product['pallet_type'] ?? '') === 'Half pallet'; ?>
        <article class="lp-product">
            <a class="lp-product-image" href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>" tabindex="-1" aria-hidden="true"><span class="lp-product-badge"><?php echo $isHalf ? 'Half pallet' : 'Full pallet'; ?></span><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="" width="360" height="300" loading="lazy"></a>
            <div class="lp-product-content"><p class="lp-product-category"><?php echo htmlspecialchars($product['category']); ?> <span aria-hidden="true">/</span> <?php echo htmlspecialchars($product['bottle_size']); ?></p><h3><a href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars(str_replace(' - Half Pallet', '', $product['name'])); ?></a></h3><p class="lp-product-spec"><?php echo $product['cases_per_pallet']; ?> cases <span aria-hidden="true">&middot;</span> <?php echo number_format($product['total_bottles_per_pallet']); ?> bottles</p>
                <div class="lp-product-buy"><div><strong><?php echo formatPrice($product['price_per_pallet']); ?></strong><span>per <?php echo $isHalf ? 'half' : 'full'; ?> pallet</span></div><a class="lp-product-link" href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>">Details <span aria-hidden="true">&nearr;</span></a></div>
                <form method="post" class="cx-product-form"><input type="hidden" name="product_id" value="<?php echo $product['id']; ?>"><input type="hidden" name="add_to_cart" value="1"><label class="cx-sr-only" for="qty-<?php echo $product['id']; ?>">Quantity for <?php echo htmlspecialchars($product['name']); ?></label><select id="qty-<?php echo $product['id']; ?>" name="quantity"><?php for ($i = $product['min_order']; $i <= min(999, $product['min_order'] + 4); $i++): ?><option value="<?php echo $i; ?>"><?php echo $i; ?></option><?php endfor; ?></select><button type="submit" class="lp-button lp-button-primary" <?php echo productCanOrder($product) ? '' : 'disabled'; ?>><?php echo productCanOrder($product) ? 'Add to cart' : 'Unavailable'; ?> <span aria-hidden="true">+</span></button></form>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php if (!$filteredProducts): ?><div class="cx-empty cx-panel"><h2>No pallets match those filters.</h2><p class="cx-note">Try a different product type or pallet size.</p><a class="lp-button lp-button-primary" href="<?php echo siteUrl('shop.php'); ?>">See all pallets &rarr;</a></div><?php endif; ?>
    <div class="cx-help-strip"><p class="cx-note">Product prices shown. Freight and order minimums depend on your destination.</p><a class="lp-text-link" href="<?php echo siteUrl('service-area.php'); ?>">Explore delivery areas &rarr;</a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>