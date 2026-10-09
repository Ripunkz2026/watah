<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';

$orders = [
    ['id' => 'PL-10024', 'status' => 'Awaiting payment', 'date' => '2026-09-28', 'total' => 1320.00],
    ['id' => 'PL-10011', 'status' => 'Ready for shipment', 'date' => '2026-09-15', 'total' => 1865.00],
    ['id' => 'PL-9978', 'status' => 'Delivered', 'date' => '2026-08-30', 'total' => 2190.00],
];
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex items-end justify-between gap-4">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Customer dashboard</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Welcome back, Alex</h1>
        </div>
        <a href="<?php echo siteUrl('shop.php'); ?>" class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Order more pallets</a>
    </div>

    <?php if (!empty($_SESSION['checkout_notice'])): ?>
        <div class="mb-8 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700"><?php echo htmlspecialchars($_SESSION['checkout_notice']); unset($_SESSION['checkout_notice']); ?></div>
    <?php endif; ?>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
            <div class="text-sm uppercase tracking-[0.12em] text-slate-500">Orders</div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">12</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
            <div class="text-sm uppercase tracking-[0.12em] text-slate-500">Active freight</div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">2</div>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
            <div class="text-sm uppercase tracking-[0.12em] text-slate-500">Total spend</div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900"><?php echo formatPrice(23640.00); ?></div>
        </div>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
            <h2 class="text-xl font-bold text-slate-900">Recent orders</h2>
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="pb-3 font-medium">Order</th>
                            <th class="pb-3 font-medium">Date</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="border-b border-slate-100">
                                <td class="py-3 font-semibold text-slate-900"><?php echo htmlspecialchars($order['id']); ?></td>
                                <td class="py-3"><?php echo htmlspecialchars($order['date']); ?></td>
                                <td class="py-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"><?php echo htmlspecialchars($order['status']); ?></span></td>
                                <td class="py-3 font-semibold text-slate-900"><?php echo formatPrice($order['total']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="rounded-[2rem] border border-slate-200 bg-slate-900 p-6 text-white shadow-soft">
            <h2 class="text-xl font-bold">Account snapshot</h2>
            <div class="mt-6 space-y-4 text-sm text-slate-300">
                <div class="flex justify-between"><span>Company</span><span class="font-medium text-white">BluePeak Foods</span></div>
                <div class="flex justify-between"><span>Primary contact</span><span class="font-medium text-white">Alex Morgan</span></div>
                <div class="flex justify-between"><span>Delivery state</span><span class="font-medium text-white">Texas</span></div>
                <div class="flex justify-between"><span>Freight status</span><span class="font-medium text-white">Pending approval</span></div>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
