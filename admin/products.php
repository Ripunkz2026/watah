<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../includes/header.php';

$products = [
    ['sku' => 'PL-330-1', 'name' => 'Pure Life 330ml', 'price' => 248.00, 'inventory' => 46, 'status' => 'In stock', 'warehouse' => 'Phoenix'],
    ['sku' => 'PL-500-1', 'name' => 'Pure Life 500ml', 'price' => 312.00, 'inventory' => 38, 'status' => 'In stock', 'warehouse' => 'Dallas'],
    ['sku' => 'PL-1L-1', 'name' => 'Pure Life 1L', 'price' => 368.00, 'inventory' => 22, 'status' => 'Limited', 'warehouse' => 'Las Vegas'],
];
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Administration</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Product & inventory management</h1>
        </div>
        <button class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">New product</button>
    </div>

    <div class="mb-8 flex gap-3 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-soft">
        <a href="<?php echo siteUrl('admin/index.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Overview</a>
        <a href="<?php echo siteUrl('admin/products.php'); ?>" class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">Products</a>
        <a href="<?php echo siteUrl('admin/states.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">States</a>
        <a href="<?php echo siteUrl('admin/orders.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Orders</a>
    </div>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="pb-3 font-medium">SKU</th>
                        <th class="pb-3 font-medium">Product</th>
                        <th class="pb-3 font-medium">Price</th>
                        <th class="pb-3 font-medium">Inventory</th>
                        <th class="pb-3 font-medium">Warehouse</th>
                        <th class="pb-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="border-b border-slate-100">
                            <td class="py-3 font-semibold text-slate-900"><?php echo htmlspecialchars($product['sku']); ?></td>
                            <td class="py-3"><?php echo htmlspecialchars($product['name']); ?></td>
                            <td class="py-3"><?php echo formatPrice($product['price']); ?></td>
                            <td class="py-3"><?php echo $product['inventory']; ?> pallets</td>
                            <td class="py-3"><?php echo htmlspecialchars($product['warehouse']); ?></td>
                            <td class="py-3"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"><?php echo htmlspecialchars($product['status']); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
