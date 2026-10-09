<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../includes/header.php';
?>

<main class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-soft">
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Admin access</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Warehouse management</h1>
            <p class="mt-3 text-slate-600">Secure login for order management, pricing, fulfillment, and eligible territory updates.</p>

            <form class="mt-8 space-y-5">
                <div>
                    <label for="admin-email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                    <input id="admin-email" type="email" value="admin@purelifewholesale.demo" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                    <label for="admin-password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                    <input id="admin-password" type="password" value="admin123" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                </div>
                <button type="submit" class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Sign in to dashboard</button>
            </form>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-slate-900 p-8 text-white shadow-soft">
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-300">Admin tools</div>
            <h2 class="mt-2 text-3xl font-extrabold">Protected workflows</h2>
            <ul class="mt-6 space-y-3 text-sm text-slate-300">
                <li>• Inventory and price updates</li>
                <li>• State / ZIP eligibility rules</li>
                <li>• Freight quote approval</li>
                <li>• Payment and refund review</li>
                <li>• Audit logs and order exports</li>
            </ul>
            <a href="<?php echo siteUrl('admin/index.php'); ?>" class="mt-8 inline-flex rounded-full border border-slate-700 bg-slate-800 px-5 py-3 text-sm font-semibold text-white hover:border-blue-400 hover:text-blue-200">Preview admin dashboard</a>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../includes/footer.php'; ?>
