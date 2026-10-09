<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../includes/header.php';

$states = [
    ['state' => 'AZ', 'status' => 'Enabled', 'min_order' => 2, 'zone' => 'Southwest', 'zip_codes' => '85001, 85301, 86001'],
    ['state' => 'CA', 'status' => 'Enabled', 'min_order' => 3, 'zone' => 'West', 'zip_codes' => '90001, 90210, 94105'],
    ['state' => 'CO', 'status' => 'Enabled', 'min_order' => 2, 'zone' => 'Mountain', 'zip_codes' => '80001, 80111, 80202'],
    ['state' => 'FL', 'status' => 'Enabled', 'min_order' => 2, 'zone' => 'Southeast', 'zip_codes' => '33004, 33131, 33602'],
    ['state' => 'NV', 'status' => 'Enabled', 'min_order' => 2, 'zone' => 'West', 'zip_codes' => '89101, 89431, 89501'],
    ['state' => 'NM', 'status' => 'Disabled', 'min_order' => 2, 'zone' => 'Southwest', 'zip_codes' => '87101, 87001, 88201'],
    ['state' => 'TX', 'status' => 'Enabled', 'min_order' => 2, 'zone' => 'South', 'zip_codes' => '75001, 75201, 77002'],
    ['state' => 'UT', 'status' => 'Enabled', 'min_order' => 2, 'zone' => 'Mountain', 'zip_codes' => '84101, 84001, 84601'],
];
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Administration</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Eligible states & ZIP validation</h1>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo siteUrl('admin/index.php'); ?>" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:border-blue-200 hover:text-blue-700">Dashboard</a>
            <button class="rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Add state</button>
        </div>
    </div>

    <div class="mb-8 flex gap-3 overflow-x-auto rounded-2xl border border-slate-200 bg-white p-3 shadow-soft">
        <a href="<?php echo siteUrl('admin/index.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Overview</a>
        <a href="<?php echo siteUrl('admin/products.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Products</a>
        <a href="<?php echo siteUrl('admin/states.php'); ?>" class="rounded-xl bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">States</a>
        <a href="<?php echo siteUrl('admin/orders.php'); ?>" class="rounded-xl px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Orders</a>
    </div>

    <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="pb-3 font-medium">State</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Min order</th>
                        <th class="pb-3 font-medium">Zone</th>
                        <th class="pb-3 font-medium">ZIP codes</th>
                        <th class="pb-3 font-medium">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($states as $state): ?>
                        <tr class="border-b border-slate-100">
                            <td class="py-3 font-semibold text-slate-900"><?php echo htmlspecialchars($state['state']); ?></td>
                            <td class="py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?php echo $state['status'] === 'Enabled' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'; ?>"><?php echo htmlspecialchars($state['status']); ?></span>
                            </td>
                            <td class="py-3"><?php echo $state['min_order']; ?> pallets</td>
                            <td class="py-3"><?php echo htmlspecialchars($state['zone']); ?></td>
                            <td class="py-3"><?php echo htmlspecialchars($state['zip_codes']); ?></td>
                            <td class="py-3">
                                <button class="text-sm font-medium text-blue-700">Edit</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
