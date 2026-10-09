<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../includes/header.php';

$overview = [
    ['label' => 'Total orders', 'value' => '128', 'trend' => '+12.5%'],
    ['label' => 'Revenue', 'value' => '$84,240', 'trend' => '+18.2%'],
    ['label' => 'Open freight', 'value' => '06', 'trend' => '-3.1%'],
    ['label' => 'Low inventory', 'value' => '03', 'trend' => '2 alerts'],
];

$products = [
    ['sku' => 'PL-330-1', 'inventory' => 46, 'status' => 'In stock'],
    ['sku' => 'PL-500-1', 'inventory' => 38, 'status' => 'In stock'],
    ['sku' => 'PL-1L-1', 'inventory' => 22, 'status' => 'Limited'],
];
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Administrator</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Operations dashboard</h1>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo siteUrl('shop.php'); ?>" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">View storefront</a>
            <a href="<?php echo siteUrl('admin/states.php'); ?>" class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Manage states</a>
        </div>
    </div>

    <div class="mb-8 flex gap-3 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-soft">
        <a href="<?php echo siteUrl('admin/index.php'); ?>" class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">Overview</a>
        <a href="<?php echo siteUrl('admin/products.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Products</a>
        <a href="<?php echo siteUrl('admin/states.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">States</a>
        <a href="<?php echo siteUrl('admin/orders.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Orders</a>
    </div>

    <div class="grid gap-6 md:grid-cols-4">
        <?php foreach ($overview as $stat): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <div class="text-sm uppercase tracking-[0.12em] text-slate-500"><?php echo htmlspecialchars($stat['label']); ?></div>
                <div class="mt-3 text-3xl font-extrabold text-slate-900"><?php echo htmlspecialchars($stat['value']); ?></div>
                <div class="mt-2 text-sm font-medium text-emerald-600"><?php echo htmlspecialchars($stat['trend']); ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-10 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
            <h2 class="text-xl font-bold text-slate-900">Inventory overview</h2>
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-600">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="pb-3 font-medium">SKU</th>
                            <th class="pb-3 font-medium">Available</th>
                            <th class="pb-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr class="border-b border-slate-100">
                                <td class="py-3 font-semibold text-slate-900"><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td class="py-3"><?php echo $product['inventory']; ?></td>
                                <td class="py-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"><?php echo htmlspecialchars($product['status']); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="rounded-[2rem] border border-slate-200 bg-slate-900 p-6 text-white shadow-soft">
            <h2 class="text-xl font-bold">Territory controls</h2>
            <ul class="mt-6 space-y-3 text-sm text-slate-300">
                <li>• Approved states: AZ, CA, CO, FL, NV, NM, TX, UT</li>
                <li>• ZIP validation: enabled</li>
                <li>• Freight quotes: required before payment</li>
                <li>• Role-based admin access: active</li>
            </ul>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
