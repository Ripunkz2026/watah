<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';

$quote = [
    'carrier' => 'FreightPro Logistics',
    'service' => 'Liftgate and residential delivery',
    'rate' => 185.00,
    'valid_until' => '2026-10-10 05:00 PM',
    'origin' => 'Phoenix, AZ',
    'destination' => 'Houston, TX',
    'pallets' => 3,
    'weight' => '3,860 lbs',
];
?>

<main class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 text-center">
        <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Freight quote</div>
        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Quote approval required</h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-soft">
            <h2 class="text-xl font-bold text-slate-900">Pending freight estimate</h2>
            <div class="mt-6 space-y-4 text-sm text-slate-600">
                <div class="flex justify-between"><span>Carrier</span><span class="font-semibold text-slate-900"><?php echo htmlspecialchars($quote['carrier']); ?></span></div>
                <div class="flex justify-between"><span>Service</span><span class="font-semibold text-slate-900"><?php echo htmlspecialchars($quote['service']); ?></span></div>
                <div class="flex justify-between"><span>Origin</span><span class="font-semibold text-slate-900"><?php echo htmlspecialchars($quote['origin']); ?></span></div>
                <div class="flex justify-between"><span>Destination</span><span class="font-semibold text-slate-900"><?php echo htmlspecialchars($quote['destination']); ?></span></div>
                <div class="flex justify-between"><span>Pallets</span><span class="font-semibold text-slate-900"><?php echo $quote['pallets']; ?></span></div>
                <div class="flex justify-between"><span>Weight</span><span class="font-semibold text-slate-900"><?php echo htmlspecialchars($quote['weight']); ?></span></div>
                <div class="flex justify-between"><span>Quote valid until</span><span class="font-semibold text-slate-900"><?php echo htmlspecialchars($quote['valid_until']); ?></span></div>
            </div>
        </section>

        <aside class="rounded-[2rem] border border-slate-200 bg-slate-900 p-8 text-white shadow-soft">
            <div class="text-sm uppercase tracking-[0.12em] text-blue-300">Estimated rate</div>
            <div class="mt-3 text-4xl font-extrabold"><?php echo formatPrice($quote['rate']); ?></div>
            <p class="mt-4 text-sm leading-6 text-slate-300">This price is a quote estimate and must be approved before the order can continue to payment processing.</p>
            <div class="mt-8 space-y-3">
                <button class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-500">Approve quote</button>
                <button class="w-full rounded-full border border-slate-700 bg-slate-800 px-5 py-3 text-sm font-semibold text-white hover:border-slate-500">Request revised quote</button>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
