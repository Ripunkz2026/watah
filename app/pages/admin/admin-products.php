<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/admin-guard.php';
$isExpressPage = true;
$isAdminPage = true;
$adminSection = 'products';
$pageTitle = 'Products & inventory';
$isCustomerPage = true;
$saveError = '';
$isCreating = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? requestText($_POST, 'action') === 'create'
    : requestText($_GET, 'action') === 'create';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
            throw new InvalidArgumentException('The upload exceeds the server limit. Choose an image smaller than 2 MB and try again.');
        }
        verifyCsrf();
        if ($isCreating) {
            $editId = createCatalogProduct($_POST, $_FILES['product_image'] ?? null);
        } else {
            $editId = (int)requestText($_POST,'product_id');
            updateCatalogProduct($editId, $_POST);
        }
        header('Location: ' . siteUrl('admin-products.php?edit=' . $editId . ($isCreating ? '&created=1' : '&saved=1')));
        exit;
    } catch (InvalidArgumentException $error) { $saveError = $error->getMessage(); }
    catch (Throwable $error) { error_log('Product update: '.$error->getMessage()); $saveError = 'The product could not be saved. Please try again.'; }
}
$catalog = require APP_ROOT . '/app/data/products.php';
$query = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$size = is_string($_GET['size'] ?? null) && in_array($_GET['size'], ['full', 'half'], true) ? $_GET['size'] : '';
$filteredProducts = array_filter($catalog, function ($item) use ($query, $size) {
    $itemSize = ($item['pallet_type'] ?? '') === 'Half pallet' ? 'half' : 'full';
    return ($size === '' || $size === $itemSize)
        && ($query === '' || stripos($item['name'] . ' ' . $item['sku'] . ' ' . $item['category'], $query) !== false);
});
$halfCount = count(array_filter($catalog, fn($item) => ($item['pallet_type'] ?? '') === 'Half pallet'));
$editId = (int)($_GET['edit'] ?? $_POST['product_id'] ?? 0);
$editingProduct = current(array_filter($catalog, fn($item) => $item['id'] === $editId)) ?: null;
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ad-page ad-list-page lp-wrap">
    <section class="ad-welcome" aria-labelledby="page-title">
        <div><p class="lp-eyebrow">THE PRODUCT COLLECTION</p><h1 id="page-title">A well-stocked<br><span>storefront.</span></h1><p class="ad-intro">Your familiar brands, pallet sizes, and pricing. Everything in one place.</p></div>
        <div class="ad-welcome-actions"><a class="lp-button lp-button-primary" href="<?php echo siteUrl('admin-products.php?action=create#product-editor'); ?>">Add product <span aria-hidden="true">+</span></a><a class="lp-text-link" href="<?php echo siteUrl('shop.php'); ?>">View collection <span aria-hidden="true">&nearr;</span></a></div>
    </section>
    <?php require APP_ROOT . '/app/views/layouts/admin-product-editor.php'; ?>
    <div class="ad-summary-grid" aria-label="Catalog summary">
        <div><span class="ad-summary-icon" aria-hidden="true">01</span><div><strong><?php echo count($catalog); ?></strong><span>Catalog products</span></div></div>
        <div><span class="ad-summary-icon" aria-hidden="true">02</span><div><strong><?php echo count($catalog) - $halfCount; ?></strong><span>Full pallet options</span></div></div>
        <div><span class="ad-summary-icon" aria-hidden="true">03</span><div><strong><?php echo $halfCount; ?></strong><span>Half pallet options</span></div></div>
    </div>
    <section class="ad-catalog ad-list-panel" aria-labelledby="list-title">
        <div class="ad-panel-heading"><div><p class="lp-eyebrow">YOUR CATALOG, AT A GLANCE</p><h2 id="list-title">Products &amp; inventory</h2></div><span class="ad-count"><?php echo count($filteredProducts); ?> of <?php echo count($catalog); ?> products</span></div>
        <form class="ad-filter-form" method="get" action="<?php echo siteUrl('admin-products.php'); ?>">
            <div class="ad-search-field"><label for="product-search">Search products</label><input id="product-search" type="search" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Product name, brand, or SKU"></div>
            <div><label for="pallet-size">Pallet size</label><select id="pallet-size" name="size"><option value="">All pallet sizes</option><option value="full"<?php echo $size === 'full' ? ' selected' : ''; ?>>Full pallet</option><option value="half"<?php echo $size === 'half' ? ' selected' : ''; ?>>Half pallet</option></select></div>
            <button class="ad-filter-button" type="submit">Apply filters</button><a class="ad-reset" href="<?php echo siteUrl('admin-products.php'); ?>">Reset</a>
        </form>
        <div class="ad-table-scroll" role="region" aria-labelledby="list-title" tabindex="0">
            <table class="ad-table ad-products-table">
                <caption class="ad-visually-hidden">Products matching your search and pallet size</caption>
                <thead><tr><th scope="col">Product</th><th scope="col">Pallet price</th><th scope="col">Inventory</th><th scope="col">Warehouse</th><th scope="col">Status</th><th scope="col">Manage</th></tr></thead>
                <tbody><?php foreach ($filteredProducts as $item): ?>
                    <tr><td><a class="ad-product" href="<?php echo siteUrl('product.php?id=' . $item['id']); ?>"><span class="ad-product-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" width="58" height="58" loading="lazy"></span><span><strong><?php echo htmlspecialchars(str_replace(' - Half Pallet', '', $item['name'])); ?></strong><small><?php echo htmlspecialchars($item['pallet_type'] ?? 'Full pallet'); ?> &middot; <?php echo htmlspecialchars($item['sku']); ?></small></span></a></td>
                    <td class="ad-product-price"><?php echo formatPrice($item['price_per_pallet']); ?></td><td><span class="ad-stock<?php echo $item['inventory'] === null ? ' ad-stock-pending' : ''; ?>"><?php echo $item['inventory'] === null ? 'Count pending' : number_format($item['inventory']) . ' pallets'; ?></span></td>
                    <td class="ad-muted"><?php echo htmlspecialchars($item['warehouse']); ?></td><td><span class="ad-stock ad-stock-neutral"><?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $item['status']))); ?></span></td>
                    <td><a class="ad-row-link" href="<?php echo siteUrl('admin-products.php?edit=' . $item['id']); ?>" aria-label="Edit <?php echo htmlspecialchars($item['name']); ?>">Edit <span aria-hidden="true">&nearr;</span></a></td></tr>
                <?php endforeach; ?></tbody>
            </table>
        </div>
        <?php if (!$filteredProducts): ?><div class="ad-empty"><h3>No products found.</h3><p>Try another name or choose a different pallet size.</p><a class="lp-text-link" href="<?php echo siteUrl('admin-products.php'); ?>">Show all products <span aria-hidden="true">&rarr;</span></a></div><?php endif; ?>
        <p class="ad-catalog-note">Prices are per selected pallet. Inventory counts and warehouse locations marked pending still need confirmation.</p>
    </section>
    <div class="ad-context-note"><span aria-hidden="true">&nearr;</span><div><strong>Delivery requirements vary by destination.</strong><p>Check your service areas for ZIP eligibility and minimum pallet quantities.</p></div><a class="lp-text-link" href="<?php echo siteUrl('admin-states.php'); ?>">Delivery areas <span aria-hidden="true">&rarr;</span></a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
