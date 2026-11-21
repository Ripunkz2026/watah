<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/admin-guard.php';
$isExpressPage = true;
$isAdminPage = true;
$adminSection = 'overview';
$pageTitle = 'Operations dashboard';
$pageDescription = 'Manage your Aqua Dash catalog, orders, and delivery areas.';
$catalog = require APP_ROOT . '/app/data/products.php';
$adminName = $_SESSION['admin']['name'] ?? 'Administrator';
$stats = appDatabase()->query("SELECT COUNT(*) AS orders, COALESCE(SUM(CASE WHEN status IN ('Ready for shipment','Shipped','Delivered') THEN total ELSE 0 END),0) AS revenue, COALESCE(SUM(status='Pending freight quote'),0) AS freight FROM orders")->fetch();
$lowStock = count(array_filter($catalog, fn($item) => $item['inventory'] !== null && $item['inventory'] <= 5));
$overview = [
    ['label'=>'Total orders','value'=>(string)$stats['orders'],'detail'=>'Orders submitted by customers','symbol'=>'01'],
    ['label'=>'Confirmed sales','value'=>formatPrice((float)$stats['revenue']),'detail'=>'Orders marked paid by your team','symbol'=>'02'],
    ['label'=>'Open freight','value'=>(string)$stats['freight'],'detail'=>'Orders awaiting a delivery rate','symbol'=>'03'],
    ['label'=>'Low inventory','value'=>(string)$lowStock,'detail'=>'Products with 5 or fewer pallets','symbol'=>'04'],
];
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ad-page lp-wrap">
    <?php if (!empty($_SESSION['admin_registration_success'])): ?><div class="ep-notice ep-success" role="status">Administrator account created. They can now sign in with their own credentials.</div><?php unset($_SESSION['admin_registration_success']); endif; ?>
    <section class="ad-welcome" aria-labelledby="dashboard-title">
        <div><p class="lp-eyebrow">YOUR OPERATIONS, ALL TOGETHER</p><h1 id="dashboard-title">Your business,<br><span>at a glance.</span></h1><p class="ad-intro">Keep your catalog organized, your orders moving, and your next delivery in view.</p></div>
        <div class="ad-welcome-actions"><a class="lp-button lp-button-primary" href="<?php echo siteUrl('admin-orders.php'); ?>">Review orders <span aria-hidden="true">&nearr;</span></a><a class="lp-text-link" href="<?php echo siteUrl('admin-register.php'); ?>">Add administrator <span aria-hidden="true">+</span></a></div>
    </section>
    <section class="ad-overview" aria-labelledby="overview-title">
        <div class="ad-section-heading"><h2 id="overview-title">Operations overview</h2><span class="ad-demo-label">Live database</span></div>
        <p class="ad-demo-note">Updated from customer orders, confirmed payments, and current product inventory.</p>
        <div class="ad-stat-grid">
            <?php foreach ($overview as $stat): ?>
                <article class="ad-stat"><div class="ad-stat-heading"><h3><?php echo htmlspecialchars($stat['label']); ?></h3><span aria-hidden="true"><?php echo $stat['symbol']; ?></span></div><strong><?php echo htmlspecialchars($stat['value']); ?></strong><p><?php echo htmlspecialchars($stat['detail']); ?></p></article>
            <?php endforeach; ?>
        </div>
    </section>
    <div class="ad-workspace">
        <section class="ad-catalog" aria-labelledby="catalog-title">
            <div class="ad-panel-heading"><div><p class="lp-eyebrow">READY FOR THE NEXT RESTOCK</p><h2 id="catalog-title">Your product collection.</h2></div><a class="lp-text-link" href="<?php echo siteUrl('admin-products.php'); ?>">Manage <span aria-hidden="true">&nearr;</span></a></div>
            <div class="ad-table-scroll" role="region" aria-labelledby="catalog-title" tabindex="0">
                <table class="ad-table">
                    <caption><?php echo count($catalog); ?> products in your storefront catalog</caption>
                    <thead><tr><th scope="col">Product</th><th scope="col">Pallet price</th><th scope="col">Inventory</th></tr></thead>
                    <tbody>
                        <?php foreach ($catalog as $item): $palletType = $item['pallet_type'] ?? 'Full pallet'; ?>
                            <tr>
                                <td><a class="ad-product" href="<?php echo siteUrl('product.php?id=' . $item['id']); ?>"><span class="ad-product-image"><img src="<?php echo htmlspecialchars($item['image']); ?>" alt="" width="58" height="58" loading="lazy"></span><span><strong><?php echo htmlspecialchars(str_replace(' - Half Pallet', '', $item['name'])); ?></strong><small><?php echo htmlspecialchars($palletType); ?> <span aria-hidden="true">&middot;</span> <?php echo htmlspecialchars($item['sku']); ?></small></span></a></td>
                                <td class="ad-product-price"><?php echo formatPrice($item['price_per_pallet']); ?></td>
                                <td><span class="ad-stock<?php echo $item['inventory'] === null ? ' ad-stock-pending' : ''; ?>"><?php echo $item['inventory'] === null ? 'Count pending' : number_format($item['inventory']) . ' pallets'; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p class="ad-catalog-note">Inventory marked &ldquo;Count pending&rdquo; has not yet been confirmed.</p>
        </section>
        <aside class="ad-sidebar">
            <section class="ad-territory" aria-labelledby="territory-title">
                <div class="ad-territory-top"><p class="lp-eyebrow">YOUR DELIVERY FOOTPRINT</p><span aria-hidden="true">&nearr;</span></div>
                <h2 id="territory-title">Big deliveries.<br>Clear boundaries.</h2>
                <p>Currently serving select ZIP codes across <?php echo count($allowedStates); ?> U.S. states.</p>
                <div class="ad-state-list" aria-label="Approved state codes"><?php foreach ($allowedStates as $state): ?><span><?php echo htmlspecialchars($state); ?></span><?php endforeach; ?></div>
                <dl><div><dt>Delivery areas</dt><dd><?php echo count($allowedStates); ?> states</dd></div><div><dt>Eligibility</dt><dd>State &amp; ZIP code</dd></div></dl>
                <a class="lp-button lp-button-light" href="<?php echo siteUrl('admin-states.php'); ?>">Manage delivery areas <span aria-hidden="true">&rarr;</span></a>
            </section>
            <section class="ad-team" aria-labelledby="team-title"><span class="ad-team-icon" aria-hidden="true">+</span><div><h2 id="team-title">Keep your team connected.</h2><p>Give another administrator access to your workspace.</p><a class="lp-text-link" href="<?php echo siteUrl('admin-register.php'); ?>">Add administrator <span aria-hidden="true">&nearr;</span></a></div></section>
        </aside>
    </div>
    <div class="ad-bottom-note"><span>Aqua Dash <span aria-hidden="true">/</span> Administration</span><a href="<?php echo siteUrl('index.php'); ?>">View your storefront <span aria-hidden="true">&nearr;</span></a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
