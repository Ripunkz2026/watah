<?php
require __DIR__ . '/config.php';
$state = strtoupper(trim($_GET['state'] ?? ''));
$zip = trim($_GET['zip'] ?? '');
$result = null;

if ($state !== '' || $zip !== '') {
    $result = isShippingAllowed($state, $zip);
}

require __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 text-center">
        <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Delivery eligibility</div>
        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Check service area availability</h1>
    </div>

    <div class="grid gap-8 lg:grid-cols-[1fr_0.8fr]">
        <div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-soft">
            <form method="get" class="space-y-5">
                <div>
                    <label for="state" class="mb-2 block text-sm font-medium text-slate-700">State</label>
                    <input id="state" name="state" type="text" value="<?php echo htmlspecialchars($state ?: 'TX'); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" placeholder="e.g. TX" />
                </div>
                <div>
                    <label for="zip" class="mb-2 block text-sm font-medium text-slate-700">ZIP code</label>
                    <input id="zip" name="zip" type="text" value="<?php echo htmlspecialchars($zip ?: '77002'); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" placeholder="e.g. 77002" />
                </div>
                <button type="submit" class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Validate delivery area</button>
            </form>
        </div>

        <div class="rounded-[2rem] border border-slate-200 bg-slate-900 p-8 text-white shadow-soft">
            <?php if ($result): ?>
                <div class="mb-4 inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] <?php echo $result['allowed'] ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300'; ?>">
                    <?php echo $result['allowed'] ? 'Eligible' : 'Restricted'; ?>
                </div>
                <h2 class="text-2xl font-bold"><?php echo $result['allowed'] ? 'Delivery available' : 'Delivery unavailable'; ?></h2>
                <p class="mt-4 text-sm leading-6 text-slate-300"><?php echo htmlspecialchars($result['message']); ?></p>
                <?php if ($result['allowed'] && isset($result['rule'])): ?>
                    <div class="mt-6 rounded-2xl border border-slate-700 bg-slate-800 p-4 text-sm text-slate-200">
                        <div class="flex justify-between"><span>Zone</span><strong><?php echo htmlspecialchars($result['rule']['zone']); ?></strong></div>
                        <div class="mt-2 flex justify-between"><span>Minimum order</span><strong><?php echo $result['rule']['minOrder']; ?> pallets</strong></div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <h2 class="text-2xl font-bold">Check a destination</h2>
                <p class="mt-4 text-sm leading-6 text-slate-300">Enter a state and ZIP code to verify whether the location is eligible for pallet ordering and freight delivery.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-12 rounded-3xl border border-slate-200 bg-white p-8 shadow-soft">
        <h2 class="text-2xl font-bold text-slate-900">Approved service states</h2>
        <div class="mt-6 flex flex-wrap gap-3">
            <?php foreach ($allowedStates as $stateCode): ?>
                <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700"><?php echo $stateCode; ?></span>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
