<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-8 lg:grid-cols-2">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-soft">
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Customer login</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Welcome back</h1>
            <p class="mt-3 text-slate-600">Track your pallet orders, review delivery status, and manage your account.</p>

            <form class="mt-8 space-y-5">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" type="email" value="customer@demo.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" type="password" value="secret" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                </div>
                <button type="submit" class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Sign in</button>
            </form>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-slate-900 p-8 text-white shadow-soft">
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-300">New account</div>
            <h2 class="mt-2 text-3xl font-extrabold">Register for wholesale ordering</h2>
            <p class="mt-4 text-slate-300">Create a business account to manage orders, receive status updates, and access warehouse fulfillment details.</p>
            <ul class="mt-6 space-y-3 text-sm text-slate-200">
                <li>• Order history</li>
                <li>• Delivery status tracking</li>
                <li>• Approved territory validation</li>
                <li>• Bulk freight approval workflows</li>
            </ul>
            <button class="mt-8 rounded-full border border-slate-700 bg-slate-800 px-5 py-3 text-sm font-semibold text-white hover:border-blue-400 hover:text-blue-200">Create account</button>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
