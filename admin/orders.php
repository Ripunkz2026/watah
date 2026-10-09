<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../includes/header.php';

$orders = [
    ['id' => 'PL-10024', 'customer' => 'BluePeak Foods', 'status' => 'Awaiting payment', 'total' => 1320.00, 'state' => 'TX'],
    ['id' => 'PL-10011', 'customer' => 'Summit Hospitality', 'status' => 'Ready for shipment', 'total' => 1865.00, 'state' => 'FL'],
    ['id' => 'PL-9978', 'customer' => 'Canyon Retail', 'status' => 'Delivered', 'total' => 2190.00, 'state' => 'AZ'],
    ['id' => 'PL-9904', 'customer' => 'North Plains Group', 'status' => 'Pending freight quote', 'total' => 1725.00, 'state' => 'CO'],
];
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Administration</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Order management</h1>
        </div>
        <button class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Export CSV</button>
    </div>

    <div class="mb-8 flex gap-3 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-soft">
        <a href="<?php echo siteUrl('admin/index.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Overview</a>
        <a href="<?php echo siteUrl('admin/products.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Products</a>
        <a href="<?php echo siteUrl('admin/states.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">States</a>
        <a href="<?php echo siteUrl('admin/orders.php'); ?>" class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">Orders</a>
    </div>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="pb-3 font-medium">Order ID</th>
                        <th class="pb-3 font-medium">Customer</th>
                        <th class="pb-3 font-medium">State</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr class="border-b border-slate-100">
                            <td class="py-3 font-semibold text-slate-900"><?php echo htmlspecialchars($order['id']); ?></td>
                            <td class="py-3"><?php echo htmlspecialchars($order['customer']); ?></td>
                            <td class="py-3"><?php echo htmlspecialchars($order['state']); ?></td>
                            <td class="py-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"><?php echo htmlspecialchars($order['status']); ?></span></td>
                            <td class="py-3 font-semibold text-slate-900"><?php echo formatPrice($order['total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
