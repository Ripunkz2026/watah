<?php
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = $isDemoAccount ? 'Customer dashboard preview' : 'My account';
$firstName = explode(' ', trim($customer['name']))[0];
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span><span>My account</span></nav>
    <div class="cx-account-layout">
        <aside class="cx-sidebar" aria-label="Account navigation">
            <div class="cx-identity"><div class="cx-avatar" aria-hidden="true"><?php echo authEscape(function_exists('mb_substr') ? mb_substr($customer['name'], 0, 1) : substr($customer['name'], 0, 1)); ?></div><strong><?php echo authEscape($customer['name']); ?></strong><p><?php echo authEscape($customer['company']); ?></p></div>
            <nav class="cx-account-nav">
                <a href="<?php echo siteUrl($isDemoAccount ? 'dashboard.php' : 'account.php'); ?>" aria-current="page"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Overview</a>
                <a href="#recent-orders"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 3h14v18l-3-2-4 2-4-2-3 2V3Z M8 8h8M8 12h8"/></svg>Recent orders</a>
                <a href="#business-profile"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="7" r="4"/><path d="M4 21v-3a8 8 0 0 1 16 0v3"/></svg>Business profile</a>
                <a href="<?php echo siteUrl('service-area.php'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>Delivery areas</a>
                <a href="<?php echo siteUrl('contact.php'); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M21 11a9 9 0 0 1-9 9H3l2-5a9 9 0 1 1 16-4Z"/></svg>Get in touch</a>
            </nav>
            <div class="cx-sidebar-bottom"><?php if (!$isDemoAccount): ?><form action="<?php echo siteUrl('logout.php'); ?>" method="post"><input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>"><button type="submit">Sign out <span aria-hidden="true">&rarr;</span></button></form><?php else: ?><a class="cx-link" href="<?php echo siteUrl('login.php'); ?>">Sign in to your account &rarr;</a><?php endif; ?></div>
        </aside>
        <div>
            <header class="cx-heading"><div><p class="lp-eyebrow">YOUR BUSINESS. WELL SUPPLIED.</p><h1>Welcome back,<br><span><?php echo authEscape($firstName); ?>.</span></h1><p class="cx-intro">A little less admin. A little more ready for your next restock.</p></div><a class="lp-button lp-button-primary" href="<?php echo siteUrl('shop.php'); ?>">Shop pallets <span aria-hidden="true">&nearr;</span></a></header>
            <?php if ($isDemoAccount): ?><div class="cx-alert">Dashboard preview &mdash; the orders and business details below are sample data. <a class="cx-link" href="<?php echo siteUrl('login.php'); ?>">Sign in to see your account.</a></div><?php endif; ?>
            <?php if (!empty($_SESSION['checkout_notice'])): ?><div class="cx-alert" role="status"><?php echo authEscape($_SESSION['checkout_notice']); unset($_SESSION['checkout_notice']); ?></div><?php endif; ?>
            <?php if (!$isDemoAccount) require APP_ROOT . '/app/views/layouts/customer-delivery-notice.php'; ?>
            <div class="cx-stat-grid">
                <div class="cx-stat"><p><?php echo $isDemoAccount ? 'Sample orders' : 'Recent orders'; ?></p><strong><?php echo count($orders); ?></strong><small><?php echo $orders ? 'In this overview' : 'Ready for your first order'; ?></small></div>
                <div class="cx-stat"><p>In your cart</p><strong><?php echo cartCount(); ?></strong><small>Pallets selected</small></div>
                <div class="cx-stat"><p>Cart subtotal</p><strong><?php echo formatPrice(cartTotal()); ?></strong><small>Before freight &amp; tax</small></div>
            </div>
            <section class="cx-panel" id="recent-orders" aria-labelledby="recent-orders-title">
                <div class="cx-panel-head"><div><h2 id="recent-orders-title">Recent orders</h2><p>Your next delivery starts here.</p></div><span class="cx-badge"><?php echo count($orders); ?> orders</span></div>
                <?php if (!$orders): ?>
                <div class="cx-empty"><span class="cx-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><path d="m3 7 9-4 9 4v11l-9 4-9-4V7Z M3 7l9 4 9-4M12 11v11M7 5l9 4"/></svg></span><h3>Your first restock awaits.</h3><p class="cx-note">No orders to display yet. Find your favorite water and sports drinks, by the full or half pallet.</p><a class="lp-button lp-button-primary" href="<?php echo siteUrl('shop.php'); ?>">Explore the pallets <span aria-hidden="true">&nearr;</span></a></div>
                <?php else: ?>
                <div class="cx-table-wrap"><table class="cx-table"><thead><tr><th scope="col">Order</th><th scope="col">Placed on</th><th scope="col">Status</th><th scope="col">Total</th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr><td><a class="cx-link" href="<?php echo siteUrl('order-status.php?order=' . rawurlencode($order['id'])); ?>"><?php echo authEscape($order['id']); ?> &rarr;</a></td><td><?php echo date('M j, Y', strtotime($order['date'])); ?></td><td><span class="cx-badge <?php echo $order['status'] === 'Delivered' ? 'cx-badge-success' : 'cx-badge-wait'; ?>"><?php echo authEscape($order['status']); ?></span></td><td><?php echo formatPrice($order['total']); ?></td></tr><?php endforeach; ?></tbody></table></div>
                <?php endif; ?>
            </section>
            <div class="cx-account-bottom">
                <section id="business-profile" class="cx-panel" aria-labelledby="profile-title"><div class="cx-panel-head"><h2 id="profile-title">Business profile</h2><span class="cx-badge cx-badge-success"><?php echo $isDemoAccount ? 'Sample' : 'Active'; ?></span></div><dl class="cx-facts"><div><dt>Contact</dt><dd><?php echo authEscape($customer['name']); ?></dd></div><div><dt>Company</dt><dd><?php echo authEscape($customer['company']); ?></dd></div><div><dt>Email</dt><dd><?php echo $customer['email'] !== '' ? authEscape($customer['email']) : 'Not provided in preview'; ?></dd></div></dl><p class="cx-note">Need a hand with your account? <a href="<?php echo siteUrl('contact.php'); ?>">Talk to our team &rarr;</a></p></section>
                <section class="cx-restock"><div><p class="lp-eyebrow">READY WHEN YOU ARE</p><h2>Good hydration.<br>In great supply.</h2><p class="cx-note">Familiar brands. Full and half pallets. One less thing to run low on.</p><a class="lp-text-link" href="<?php echo siteUrl('shop.php'); ?>">Find your next restock &nearr;</a></div><img src="<?php echo assetUrl('images/products/Pure Life Half-Pallet Water Display.png'); ?>" alt="Pure Life water half pallet" width="160" height="180"></section>
            </div>
            <div class="cx-help-strip"><p class="cx-note">Planning a delivery? Check your destination before you order.</p><a class="lp-text-link" href="<?php echo siteUrl('service-area.php'); ?>">Check delivery <span aria-hidden="true">&rarr;</span></a></div>
        </div>
    </div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
