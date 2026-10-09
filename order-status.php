<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';

$timeline = [
    ['label' => 'Order submitted', 'date' => '2026-10-09 09:14 AM', 'status' => 'complete'],
    ['label' => 'Territory validation', 'date' => '2026-10-09 09:17 AM', 'status' => 'complete'],
    ['label' => 'Freight quote requested', 'date' => '2026-10-09 09:20 AM', 'status' => 'active'],
    ['label' => 'Awaiting payment', 'date' => 'Pending', 'status' => 'pending'],
    ['label' => 'Shipment scheduled', 'date' => 'Pending', 'status' => 'pending'],
];

$order = [
    'id' => 'PL-10024',
    'customer' => 'BluePeak Foods',
    'status' => 'Pending Freight Quote',
    'state' => 'Texas',
    'zip' => '77002',
    'subtotal' => 1320.00,
    'shipping' => 180.00,
    'tax' => 99.00,
    'total' => 1599.00,
];
?>

<main class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Order status</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Order <?php echo htmlspecialchars($order['id']); ?></h1>
        </div>
        <a href="<?php echo siteUrl('shop.php'); ?>" class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Order more pallets</a>
    </div>

    <?php if (!empty($_SESSION['checkout_notice'])): ?>
        <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700"><?php echo htmlspecialchars($_SESSION['checkout_notice']); unset($_SESSION['checkout_notice']); ?></div>
    <?php endif; ?>

    <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="space-y-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm uppercase tracking-[0.12em] text-slate-500">Current status</div>
                        <div class="mt-2 text-2xl font-extrabold text-slate-900"><?php echo htmlspecialchars($order['status']); ?></div>
                    </div>
                    <span class="rounded-full bg-amber-50 px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-amber-700">Quote required</span>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-xl font-bold text-slate-900">Order progress</h2>
                <div class="mt-6 space-y-5">
                    <?php $timelineCount = count($timeline); ?>
                    <?php foreach ($timeline as $index => $step): ?>
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="flex h-6 w-6 items-center justify-center rounded-full border-2 <?php echo $step['status'] === 'complete' ? 'border-emerald-500 bg-emerald-500 text-white' : ($step['status'] === 'active' ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-300 bg-slate-50 text-slate-400'); ?> text-[10px] font-bold">
                                    <?php echo $step['status'] === 'complete' ? '✓' : ($step['status'] === 'active' ? '•' : ''); ?>
                                </div>
                                <?php if ($index < $timelineCount - 1): ?>
                                    <div class="mt-2 h-10 w-px bg-slate-200"></div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($step['label']); ?></div>
                                <div class="text-sm text-slate-500"><?php echo htmlspecialchars($step['date']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <aside class="space-y-8">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-xl font-bold text-slate-900">Shipping summary</h2>
                <div class="mt-5 space-y-3 text-sm text-slate-600">
                    <div class="flex justify-between"><span>Delivery state</span><span class="font-medium text-slate-900"><?php echo htmlspecialchars($order['state']); ?></span></div>
                    <div class="flex justify-between"><span>ZIP code</span><span class="font-medium text-slate-900"><?php echo htmlspecialchars($order['zip']); ?></span></div>
                    <div class="flex justify-between"><span>Service type</span><span class="font-medium text-slate-900">Freight delivery</span></div>
                    <div class="flex justify-between"><span>Ramp / liftgate</span><span class="font-medium text-slate-900">Not requested</span></div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-slate-900 p-6 text-white shadow-soft">
                <h2 class="text-xl font-bold">Order totals</h2>
                <div class="mt-5 space-y-3 text-sm text-slate-300">
                    <div class="flex justify-between"><span>Item subtotal</span><span class="font-medium text-white"><?php echo formatPrice($order['subtotal']); ?></span></div>
                    <div class="flex justify-between"><span>Freight</span><span class="font-medium text-white"><?php echo formatPrice($order['shipping']); ?></span></div>
                    <div class="flex justify-between"><span>Tax</span><span class="font-medium text-white"><?php echo formatPrice($order['tax']); ?></span></div>
                    <div class="mt-4 border-t border-slate-700 pt-4 flex justify-between text-base font-bold text-white"><span>Total</span><span><?php echo formatPrice($order['total']); ?></span></div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-xl font-bold text-slate-900">Next steps</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">The freight quote is being reviewed. Once approved, the system will move the order to Awaiting Payment and then to shipment scheduling.</p>
                <a href="<?php echo siteUrl('freight-quote.php'); ?>" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">View freight approval</a>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
