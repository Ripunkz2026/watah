<?php
require __DIR__ . '/config.php';
$products = require __DIR__ . '/data.php';
require __DIR__ . '/includes/header.php';
?>

<main>
    <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8">
        <div class="grid items-center gap-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <span class="inline-flex rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">Wholesale pallet supply</span>
                <h1 class="mt-5 max-w-xl text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">Pure Life water for businesses that need reliable bulk hydration.</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">Full-pallet delivery for offices, retailers, hospitality groups, and institutions in approved service territories across the United States.</p>
                <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                    <a href="<?php echo siteUrl('shop.php'); ?>" class="inline-flex items-center justify-center rounded-full bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-soft transition hover:bg-blue-700">Shop pallets</a>
                    <a href="<?php echo siteUrl('service-area.php'); ?>" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700">Check delivery area</a>
                </div>
                <div class="mt-10 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                        <div class="text-2xl font-extrabold text-slate-900">2,300+</div>
                        <div class="mt-1 text-sm text-slate-600">Pallets delivered</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                        <div class="text-2xl font-extrabold text-slate-900">8</div>
                        <div class="mt-1 text-sm text-slate-600">Approved states</div>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-soft">
                        <div class="text-2xl font-extrabold text-slate-900">48h</div>
                        <div class="mt-1 text-sm text-slate-600">Order turnaround</div>
                    </div>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft">
                <div class="overflow-hidden rounded-[1.5rem] bg-slate-100">
                    <img src="<?php echo assetUrl('img/Pure%20Life%20Watter%20Pallets.jfif'); ?>" alt="Water pallets" class="h-[420px] w-full object-cover" />
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-blue-50 p-3">
                        <div class="text-xs uppercase tracking-[0.12em] text-blue-700">Bottle size</div>
                        <div class="mt-2 text-lg font-bold text-slate-900">330ml–1L</div>
                    </div>
                    <div class="rounded-2xl bg-slate-100 p-3">
                        <div class="text-xs uppercase tracking-[0.12em] text-slate-500">Per pallet</div>
                        <div class="mt-2 text-lg font-bold text-slate-900">576–1,152</div>
                    </div>
                    <div class="rounded-2xl bg-slate-100 p-3">
                        <div class="text-xs uppercase tracking-[0.12em] text-slate-500">Freight</div>
                        <div class="mt-2 text-lg font-bold text-slate-900">Quote</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-lg font-bold text-blue-700">01</div>
                    <h3 class="text-xl font-bold text-slate-900">Whole-pallet ordering</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Built for businesses that need pallet-size inventory without the complexity of individual bottle sales.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-lg font-bold text-blue-700">02</div>
                    <h3 class="text-xl font-bold text-slate-900">Territory controls</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Delivery eligibility is validated on the server and can be adjusted by the administrator without code changes.</p>
                </div>
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-6">
                    <div class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-100 text-lg font-bold text-blue-700">03</div>
                    <h3 class="text-xl font-bold text-slate-900">Freight-aware checkout</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Quote-required freight is clearly separated from payment so customers never get billed before approval.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between gap-4 pb-8">
            <div>
                <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Featured pallets</div>
                <h2 class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Available inventory</h2>
            </div>
            <a href="<?php echo siteUrl('shop.php'); ?>" class="text-sm font-semibold text-blue-700 hover:text-blue-800">View all products →</a>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <?php foreach (array_slice($products, 0, 3) as $product): ?>
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-soft">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-60 w-full object-cover" />
                    <div class="p-6">
                        <div class="mb-3 flex items-center justify-between">
                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.12em] text-emerald-700"><?php echo ucfirst($product['status']); ?></span>
                            <span class="text-sm font-medium text-slate-500"><?php echo htmlspecialchars($product['warehouse']); ?></span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="mt-5 flex items-center justify-between">
                            <div>
                                <div class="text-sm text-slate-500">Price per pallet</div>
                                <div class="text-2xl font-extrabold text-slate-900"><?php echo formatPrice($product['price_per_pallet']); ?></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-slate-500">Inventory</div>
                                <div class="text-lg font-bold text-slate-900"><?php echo $product['inventory']; ?> pallets</div>
                            </div>
                        </div>
                        <div class="mt-6 flex gap-3">
                            <a href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>" class="flex-1 rounded-full border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">Details</a>
                            <a href="<?php echo siteUrl('shop.php'); ?>" class="flex-1 rounded-full bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-700">Add to cart</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="bg-slate-900">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-[1fr_0.9fr]">
                <div>
                    <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-300">Sales territory</div>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-white">Currently serving select states with pallet freight fulfillment.</h2>
                    <p class="mt-4 max-w-xl text-slate-300">Admin-controlled eligibility lets the business expand territories as operational capacity grows. Checkout is blocked for unsupported destinations and ZIP codes.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <?php foreach ($allowedStates as $state): ?>
                            <span class="rounded-full border border-slate-700 bg-slate-800 px-3 py-1.5 text-sm font-medium text-slate-200"><?php echo $state; ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-700 bg-slate-800 p-6">
                    <h3 class="text-xl font-bold text-white">Service area check</h3>
                    <p class="mt-2 text-sm text-slate-300">Enter a destination to validate whether pallet sales are available for that ZIP code.</p>
                    <form action="<?php echo siteUrl('service-area.php'); ?>" method="get" class="mt-6 space-y-4">
                        <div>
                            <label for="state" class="mb-2 block text-sm font-medium text-slate-200">State</label>
                            <input id="state" name="state" type="text" value="TX" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-4 py-3 text-white placeholder:text-slate-400 focus:border-blue-400 focus:outline-none" />
                        </div>
                        <div>
                            <label for="zip" class="mb-2 block text-sm font-medium text-slate-200">ZIP code</label>
                            <input id="zip" name="zip" type="text" value="77002" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-4 py-3 text-white placeholder:text-slate-400 focus:border-blue-400 focus:outline-none" />
                        </div>
                        <button type="submit" class="w-full rounded-full bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-500">Check eligibility</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
