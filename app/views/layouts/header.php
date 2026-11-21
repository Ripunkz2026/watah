<?php
$products = require APP_ROOT . '/app/data/products.php';
$useExpressLayout = !empty($isLanding) || !empty($isExpressPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((isset($pageTitle) ? $pageTitle . ' | ' : '') . APP_NAME); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription ?? ($useExpressLayout ? 'Shop full and half pallets of Pure Life, Member\'s Mark water, and Gatorade at Aqua Dash. Check delivery availability for your business.' : 'Pure Life bottled water pallets for wholesale customers across select U.S. states.')); ?>">
    <?php if (!$useExpressLayout): ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#0077b6',
                            navy: '#123349',
                            sky: '#def2fa',
                            ice: '#f7fbfd',
                            red: '#d71920'
                        },
                        blue: { 50: '#edf8fc', 100: '#def2fa', 200: '#b8deef', 500: '#0077b6', 600: '#0077b6', 700: '#00558a' }
                    },
                    boxShadow: {
                        soft: '0 12px 30px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        };
    </script>
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <?php if ($useExpressLayout): ?><link rel="stylesheet" href="<?php echo assetUrl('css/landing.css') . '?v=' . filemtime(APP_ROOT . '/assets/css/landing.css'); ?>"><?php endif; ?>
    <?php if (!empty($isExpressPage)): ?><link rel="stylesheet" href="<?php echo assetUrl('css/express-pages.css'); ?>"><?php endif; ?>
    <?php if (!empty($isProductPage)): ?><link rel="stylesheet" href="<?php echo assetUrl('css/product.css'); ?>"><?php endif; ?>
    <?php if (!empty($isCustomerPage)): ?><link rel="stylesheet" href="<?php echo assetUrl('css/customer.css'); ?>"><?php endif; ?>
    <?php if (!empty($isAdminPage)): ?><link rel="stylesheet" href="<?php echo assetUrl('css/admin.css'); ?>"><?php endif; ?>
    <?php if (!empty($isAuthPage)): ?><link rel="stylesheet" href="<?php echo assetUrl('css/auth.css'); ?>"><?php endif; ?>
    <link rel="stylesheet" href="<?php echo assetUrl('css/brand.css') . '?v=' . filemtime(APP_ROOT . '/assets/css/brand.css'); ?>">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="page-shell text-slate-800 <?php echo $useExpressLayout ? 'landing-page' : ''; ?>">
    <?php if (!empty($isAdminPage)): ?>
        <?php require APP_ROOT . '/app/views/layouts/admin-header.php'; ?>
    <?php elseif ($useExpressLayout): ?>
        <?php require APP_ROOT . '/app/views/layouts/landing-header.php'; ?>
    <?php else: ?>
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur-lg">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between gap-6">
                <a href="<?php echo siteUrl('index.php'); ?>" class="flex items-center gap-3" aria-label="Aqua Dash home">
                    <?php require APP_ROOT . '/app/views/layouts/brand-logo.php'; ?>
                </a>

                <nav class="hidden items-center gap-7 md:flex">
                    <a href="<?php echo siteUrl('index.php'); ?>" class="<?php echo currentPage('/watah/index.php'); ?> text-sm font-medium">Home</a>
                    <a href="<?php echo siteUrl('shop.php'); ?>" class="<?php echo currentPage('/watah/shop.php'); ?> text-sm font-medium">Shop</a>
                    <a href="<?php echo siteUrl('service-area.php'); ?>" class="<?php echo currentPage('/watah/service-area.php'); ?> text-sm font-medium">Service Area</a>
                    <a href="<?php echo siteUrl('faq.php'); ?>" class="<?php echo currentPage('/watah/faq.php'); ?> text-sm font-medium">FAQ</a>
                    <a href="<?php echo siteUrl('contact.php'); ?>" class="<?php echo currentPage('/watah/contact.php'); ?> text-sm font-medium">Contact</a>
                </nav>

                <div class="flex items-center gap-3">
                    <?php if (!empty($isLanding)): ?>
                    <details class="mobile-menu"><summary aria-label="Open navigation">&#9776;</summary><nav aria-label="Mobile navigation">
                        <?php foreach (['index.php' => 'Home', 'shop.php' => 'Shop pallets', 'service-area.php' => 'Service area', 'faq.php' => 'FAQ', 'contact.php' => 'Contact', 'account.php' => 'Account', 'login.php' => 'Sign in'] as $path => $label): ?>
                        <a href="<?php echo siteUrl($path); ?>"><?php echo $label; ?></a>
                        <?php endforeach; ?>
                    </nav></details>
                    <?php endif; ?>
                    
                    <a href="<?php echo siteUrl('login.php'); ?>" class="hidden rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:border-blue-200 hover:text-blue-700 sm:inline-flex">Sign in</a>
                    <a href="<?php echo siteUrl('cart.php'); ?>" class="relative inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:bg-slate-800">
                        Cart
                        <span class="ml-2 inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-blue-600 px-2 text-xs font-bold text-white"><?php echo cartCount(); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>
