<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';

$orders = [
    ['id' => 'PL-10024', 'status' => 'Pending Freight Quote', 'date' => '2026-10-09', 'total' => 1599.00],
    ['id' => 'PL-10011', 'status' => 'Ready for Shipment', 'date' => '2026-09-15', 'total' => 1865.00],
    ['id' => 'PL-9978', 'status' => 'Delivered', 'date' => '2026-08-30', 'total' => 2190.00],
];
?>

<main class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex items-end justify-between gap-4">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">My account</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Business profile</h1>
        </div>
        <a href="<?php echo siteUrl('dashboard.php'); ?>" class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Customer dashboard</a>
    </div>

    <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
        <aside class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-xl font-bold text-blue-700">AM</div>
                <div>
                    <div class="text-xl font-bold text-slate-900">Alex Morgan</div>
                    <div class="text-sm text-slate-500">BluePeak Foods</div>
                </div>
            </div>

            <div class="mt-6 space-y-4 text-sm text-slate-600">
                <div class="flex justify-between"><span>Email</span><span class="font-medium text-slate-900">alex@bluepeakfoods.com</span></div>
                <div class="flex justify-between"><span>Phone</span><span class="font-medium text-slate-900">(214) 555-0198</span></div>
                <div class="flex justify-between"><span>Territory</span><span class="font-medium text-slate-900">Texas</span></div>
                <div class="flex justify-between"><span>Status</span><span class="font-medium text-emerald-700">Active</span></div>
            </div>
        </aside>

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
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
