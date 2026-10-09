<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/header.php';

$faqs = [
    ['question' => 'Do you sell individual bottles or only full pallets?', 'answer' => 'This storefront is designed for full pallet wholesale orders only. Individual bottle sales are not available.'],
    ['question' => 'How do I know if my city is in the delivery area?', 'answer' => 'Use the service area checker to validate the state and ZIP code before checkout. Orders outside approved states or ZIP restrictions are blocked.'],
    ['question' => 'What if freight pricing is not available yet?', 'answer' => 'Orders may remain marked as pending freight quote until a final shipping cost is approved by the administrator.'],
    ['question' => 'Can product pricing or inventory be updated?', 'answer' => 'Yes. Product pricing, inventory, and territory controls are intended to be managed from the administration dashboard without code changes.'],
];
?>

<main class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 text-center">
        <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Frequently asked questions</div>
        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Wholesale ordering questions</h1>
    </div>

    <div class="space-y-5">
        <?php foreach ($faqs as $faq): ?>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($faq['question']); ?></h2>
                <p class="mt-3 text-sm leading-6 text-slate-600"><?php echo htmlspecialchars($faq['answer']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
