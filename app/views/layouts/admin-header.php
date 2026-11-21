<?php
$adminName = $_SESSION['admin']['name'] ?? 'Administrator';
$adminSection = $adminSection ?? 'overview';
$adminLinks = [
    'overview' => ['path' => 'admin-index.php', 'label' => 'Overview'],
    'products' => ['path' => 'admin-products.php', 'label' => 'Products'],
    'orders' => ['path' => 'admin-orders.php', 'label' => 'Orders'],
    'states' => ['path' => 'admin-states.php', 'label' => 'Delivery areas'],
];
?>
<a class="lp-skip-link" href="#main-content">Skip to content</a>
<div class="lp-announcement"><span>HYDRATION, IN GOOD SUPPLY.</span><a href="<?php echo siteUrl('index.php'); ?>">View storefront <span aria-hidden="true">&nearr;</span></a></div>
<header class="lp-header ad-header">
    <div class="lp-header-inner lp-wrap">
        <a class="lp-logo" href="<?php echo siteUrl('admin-index.php'); ?>" aria-label="Aqua Dash administration"><?php require APP_ROOT . '/app/views/layouts/brand-logo.php'; ?></a>
        <nav class="lp-desktop-nav ad-nav" aria-label="Administration">
            <?php foreach ($adminLinks as $key => $link): ?><a href="<?php echo siteUrl($link['path']); ?>"<?php echo $adminSection === $key ? ' aria-current="page"' : ''; ?>><?php echo htmlspecialchars($link['label']); ?></a><?php endforeach; ?>
        </nav>
        <div class="ad-header-actions">
            <span class="ad-admin-name" title="<?php echo authEscape($adminName); ?>"><?php echo authEscape($adminName); ?><small>Administrator</small></span>
            <form action="<?php echo siteUrl('logout.php'); ?>" method="post"><input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>"><button class="ad-signout" type="submit">Sign out</button></form>
            <details class="lp-mobile-menu"><summary aria-label="Open navigation"><span></span><span></span><span></span></summary><nav aria-label="Mobile administration">
                <?php foreach ($adminLinks as $key => $link): ?><a href="<?php echo siteUrl($link['path']); ?>"<?php echo $adminSection === $key ? ' aria-current="page"' : ''; ?>><?php echo htmlspecialchars($link['label']); ?></a><?php endforeach; ?>
                <a href="<?php echo siteUrl('admin-register.php'); ?>">Add administrator</a><a href="<?php echo siteUrl('index.php'); ?>">View storefront</a>
            </nav></details>
        </div>
    </div>
</header>
