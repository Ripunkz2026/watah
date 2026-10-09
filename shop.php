<?php
require __DIR__ . '/config.php';
$products = require __DIR__ . '/data.php';
require __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    addToCart($productId, $quantity);
    header('Location: ' . siteUrl('cart.php'));
    exit;
}
?>

<main class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Catalog</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Wholesale pallet products</h1>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 shadow-soft">Full pallet sales only</div>
    </div>

    <div class="mb-10 grid gap-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-soft md:grid-cols-4">
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-600">Product type</label>
            <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                <option>All products</option>
                <option>Spring Water</option>
                <option>Retail Ready</option>
                <option>Hospitality</option>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-600">Bottle size</label>
            <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                <option>All sizes</option>
                <option>330ml</option>
                <option>500ml</option>
                <option>1L</option>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-600">Min order</label>
            <select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                <option>Any</option>
                <option>2 pallets</option>
                <option>3 pallets</option>
            </select>
        </div>
        <div class="flex items-end">
            <button class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Apply filters</button>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        <?php foreach ($products as $product): ?>
            <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-soft">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-60 w-full object-cover" />
                <div class="p-6">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-blue-700"><?php echo htmlspecialchars($product['category']); ?></span>
                        <span class="text-xs uppercase tracking-[0.12em] text-slate-500"><?php echo htmlspecialchars($product['warehouse']); ?></span>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600"><?php echo htmlspecialchars($product['description']); ?></p>

                    <div class="mt-5 grid gap-2 text-sm text-slate-600">
                        <div class="flex justify-between"><span>Bottle size</span><span class="font-medium text-slate-900"><?php echo $product['bottle_size']; ?></span></div>
                        <div class="flex justify-between"><span>Cases per pallet</span><span class="font-medium text-slate-900"><?php echo $product['cases_per_pallet']; ?></span></div>
                        <div class="flex justify-between"><span>Total bottles</span><span class="font-medium text-slate-900"><?php echo $product['total_bottles_per_pallet']; ?></span></div>
                    </div>

                    <div class="mt-6 flex items-end justify-between gap-4">
                        <div>
                            <div class="text-sm text-slate-500">Price per pallet</div>
                            <div class="text-2xl font-extrabold text-slate-900"><?php echo formatPrice($product['price_per_pallet']); ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-slate-500">Inventory</div>
                            <div class="text-base font-bold text-slate-900"><?php echo $product['inventory']; ?> available</div>
                        </div>
                    </div>

                    <form method="post" class="mt-6 space-y-3">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <input type="hidden" name="add_to_cart" value="1">
                        <div class="flex gap-2">
                            <label class="sr-only" for="qty-<?php echo $product['id']; ?>">Quantity</label>
                            <select id="qty-<?php echo $product['id']; ?>" name="quantity" class="w-24 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" class="flex-1 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Add to cart</button>
                        </div>
                        <a href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>" class="block rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:border-blue-300 hover:text-blue-700">View product</a>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
