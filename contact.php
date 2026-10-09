<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-8 lg:grid-cols-2">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Contact us</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Let’s talk pallet logistics</h1>
            <p class="mt-4 text-slate-600">For order planning, facility delivery needs, or territories outside the current service area, reach out to the wholesale team.</p>

            <div class="mt-8 space-y-4 text-sm text-slate-600">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="font-semibold text-slate-900">Email</div>
                    <div class="mt-1">sales@purelifewholesale.demo</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="font-semibold text-slate-900">Phone</div>
                    <div class="mt-1">(800) 555-0147</div>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                    <div class="font-semibold text-slate-900">Support hours</div>
                    <div class="mt-1">Monday–Friday, 8:00 AM to 5:00 PM CST</div>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-soft">
            <form class="space-y-5">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Name</label>
                    <input type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Message</label>
                    <textarea rows="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none"></textarea>
                </div>
                <button type="submit" class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Send message</button>
            </form>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
