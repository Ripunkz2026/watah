<?php
$products = require __DIR__ . '/../data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Wholesale Pallet Store</title>
    <meta name="description" content="Pure Life bottled water pallets for wholesale customers across select U.S. states.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#0d6efd',
                            navy: '#0f172a',
                            sky: '#e0f2fe',
                            ice: '#f8fbff'
                        }
                    },
                    boxShadow: {
                        soft: '0 12px 30px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .page-shell { background: linear-gradient(180deg, #f8fbff 0%, #f5f7fb 100%); }
    </style>
</head>
<body class="page-shell text-slate-800">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur-lg">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-20 items-center justify-between gap-6">
                <a href="<?php echo siteUrl('index.php'); ?>" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 text-lg font-extrabold text-white shadow-soft">P</div>
                    <div>
                        <div class="text-lg font-bold tracking-tight text-slate-900">Pure Life</div>
                        <div class="text-[11px] uppercase tracking-[0.18em] text-slate-500">Wholesale</div>
                    </div>
                </a>

                <nav class="hidden items-center gap-7 md:flex">
                    <a href="<?php echo siteUrl('index.php'); ?>" class="<?php echo currentPage('/watah/index.php'); ?> text-sm font-medium">Home</a>
                    <a href="<?php echo siteUrl('shop.php'); ?>" class="<?php echo currentPage('/watah/shop.php'); ?> text-sm font-medium">Shop</a>
                    <a href="<?php echo siteUrl('service-area.php'); ?>" class="<?php echo currentPage('/watah/service-area.php'); ?> text-sm font-medium">Service Area</a>
                    <a href="<?php echo siteUrl('faq.php'); ?>" class="<?php echo currentPage('/watah/faq.php'); ?> text-sm font-medium">FAQ</a>
                    <a href="<?php echo siteUrl('contact.php'); ?>" class="<?php echo currentPage('/watah/contact.php'); ?> text-sm font-medium">Contact</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="<?php echo siteUrl('account.php'); ?>" class="hidden rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:border-blue-200 hover:text-blue-700 sm:inline-flex">Account</a>
                    <a href="<?php echo siteUrl('login.php'); ?>" class="hidden rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:border-blue-200 hover:text-blue-700 sm:inline-flex">Sign in</a>
                    <a href="<?php echo siteUrl('admin/login.php'); ?>" class="hidden rounded-full border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:border-blue-200 hover:text-blue-700 sm:inline-flex">Admin</a>
                    <a href="<?php echo siteUrl('cart.php'); ?>" class="relative inline-flex items-center justify-center rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-soft hover:bg-slate-800">
                        Cart
                        <span class="ml-2 inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-blue-600 px-2 text-xs font-bold text-white"><?php echo cartCount(); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>
