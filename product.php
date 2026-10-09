<?php
require __DIR__ . '/config.php';
$products = require __DIR__ . '/data.php';
$productId = (int)($_GET['id'] ?? 0);
$product = null;
foreach ($products as $item) {
    if ((int)$item['id'] === $productId) {
        $product = $item;
        break;
    }
}

if (!$product) {
    header('Location: ' . siteUrl('shop.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    addToCart($product['id'], $quantity);
    header('Location: ' . siteUrl('cart.php'));
    exit;
}

require __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-2">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-soft">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-full max-h-[640px] w-full object-cover" />
        </div>
        <div>
            <div class="mb-4 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-blue-700"><?php echo htmlspecialchars($product['category']); ?></div>
            <h1 class="text-4xl font-extrabold tracking-tight text-slate-900"><?php echo htmlspecialchars($product['name']); ?></h1>
            <div class="mt-6 flex items-center gap-6 border-y border-slate-200 py-4">
                <div>
                    <div class="text-sm text-slate-500">Price per pallet</div>
                    <div class="text-3xl font-extrabold text-slate-900"><?php echo formatPrice($product['price_per_pallet']); ?></div>
                </div>
                <div class="text-sm text-slate-600">
                    <div class="font-semibold text-slate-900"><?php echo $product['inventory']; ?> pallets available</div>
                    <div>Min order: <?php echo $product['min_order']; ?> pallet(s)</div>
                </div>
            </div>

            <p class="mt-6 text-base leading-7 text-slate-600"><?php echo htmlspecialchars($product['description']); ?></p>

            <form method="post" class="mt-8 space-y-4">
                <input type="hidden" name="add_to_cart" value="1">
                <div class="flex items-center gap-4">
                    <label for="quantity" class="text-sm font-medium text-slate-600">Pallet quantity</label>
                    <select id="quantity" name="quantity" class="w-24 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="w-full rounded-full bg-blue-600 px-6 py-3 text-base font-semibold text-white hover:bg-blue-700">Add to cart</button>
            </form>

            <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-lg font-bold text-slate-900">Product specification</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-sm text-slate-500">SKU</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($product['sku']); ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Bottle size</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($product['bottle_size']); ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Bottles per case</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo $product['bottles_per_case']; ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Cases per pallet</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo $product['cases_per_pallet']; ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Total bottles</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo $product['total_bottles_per_pallet']; ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Pallet dimensions</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($product['pallet_dimensions']); ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Pallet weight</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($product['pallet_weight']); ?></dd></div>
                    <div><dt class="text-sm text-slate-500">Warehouse</dt><dd class="mt-1 font-semibold text-slate-900"><?php echo htmlspecialchars($product['warehouse']); ?></dd></div>
                </dl>
            </div>
        </div>
    </div>

    <section class="mt-16 rounded-3xl border border-slate-200 bg-white p-8 shadow-soft">
        <h2 class="text-2xl font-bold text-slate-900">Highlights</h2>
        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <?php foreach ($product['features'] as $feature): ?>
                <div class="rounded-2xl bg-slate-50 p-4 text-sm text-slate-700"><?php echo htmlspecialchars($feature); ?></div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
