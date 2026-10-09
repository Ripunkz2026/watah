<?php
require __DIR__ . '/config.php';
bootstrapCart();
$products = require __DIR__ . '/data.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty'])) {
        $id = (int)($_POST['product_id'] ?? 0);
        $qty = max(0, (int)($_POST['quantity'] ?? 0));
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }

    if (isset($_POST['remove_item'])) {
        unset($_SESSION['cart'][(int)($_POST['product_id'] ?? 0)]);
    }

    header('Location: ' . siteUrl('cart.php'));
    exit;
}

$cartItems = [];
foreach ($_SESSION['cart'] ?? [] as $productId => $quantity) {
    foreach ($products as $product) {
        if ((int)$product['id'] === (int)$productId) {
            $cartItems[] = ['product' => $product, 'quantity' => $quantity];
        }
    }
}

$subtotal = 0.0;
foreach ($cartItems as $item) {
    $subtotal += (float)$item['product']['price_per_pallet'] * (int)$item['quantity'];
}
$shipping = count($cartItems) > 0 ? 180.00 : 0.00;
$tax = $subtotal * 0.075;
$total = $subtotal + $shipping + $tax;

require __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex items-end justify-between gap-4">
        <div>
            <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Shopping cart</div>
            <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Your pallet order</h1>
        </div>
        <div class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 shadow-soft"><?php echo count($cartItems); ?> item(s)</div>
    </div>

    <?php if (empty($cartItems)): ?>
        <div class="rounded-[2rem] border border-dashed border-slate-300 bg-white p-12 text-center shadow-soft">
            <h2 class="text-2xl font-bold text-slate-900">Your cart is empty</h2>
            <p class="mt-3 text-slate-600">Browse pallet options to order full cases and pallet quantities.</p>
            <a href="<?php echo siteUrl('shop.php'); ?>" class="mt-6 inline-flex rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Shop pallets</a>
        </div>
    <?php else: ?>
        <div class="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div class="space-y-6">
                <?php foreach ($cartItems as $item): ?>
                    <div class="flex flex-col gap-5 rounded-[2rem] border border-slate-200 bg-white p-5 shadow-soft sm:flex-row sm:items-center">
                        <img src="<?php echo $item['product']['image']; ?>" alt="<?php echo htmlspecialchars($item['product']['name']); ?>" class="h-28 w-full rounded-2xl object-cover sm:w-32" />
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-slate-900"><?php echo htmlspecialchars($item['product']['name']); ?></h2>
                            <p class="mt-1 text-sm text-slate-500"><?php echo htmlspecialchars($item['product']['warehouse']); ?></p>
                            <div class="mt-3 flex items-center justify-between gap-4">
                                <div>
                                    <span class="text-sm text-slate-500">Unit price</span>
                                    <div class="font-bold text-slate-900"><?php echo formatPrice($item['product']['price_per_pallet']); ?></div>
                                </div>
                                <form method="post" class="flex items-center gap-3">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                    <input type="hidden" name="update_qty" value="1">
                                    <label for="qty-<?php echo $item['product']['id']; ?>" class="sr-only">Quantity</label>
                                    <select id="qty-<?php echo $item['product']['id']; ?>" name="quantity" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-blue-400 focus:outline-none">
                                        <?php for ($i = 0; $i <= 8; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php echo $item['quantity'] === $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <button type="submit" class="text-sm font-medium text-blue-700">Update</button>
                                </form>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-xs uppercase tracking-[0.12em] text-slate-500">Line total</div>
                                <div class="text-xl font-extrabold text-slate-900"><?php echo formatPrice((float)$item['product']['price_per_pallet'] * (int)$item['quantity']); ?></div>
                            </div>
                            <form method="post">
                                <input type="hidden" name="remove_item" value="1">
                                <input type="hidden" name="product_id" value="<?php echo $item['product']['id']; ?>">
                                <button type="submit" class="rounded-full border border-slate-200 px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-slate-600 hover:border-red-200 hover:text-red-700">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <aside class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-xl font-bold text-slate-900">Order summary</h2>
                <div class="mt-6 space-y-4 text-sm text-slate-600">
                    <div class="flex justify-between"><span>Subtotal</span><span class="font-semibold text-slate-900"><?php echo formatPrice($subtotal); ?></span></div>
                    <div class="flex justify-between"><span>Freight</span><span class="font-semibold text-slate-900"><?php echo formatPrice($shipping); ?></span></div>
                    <div class="flex justify-between"><span>Tax</span><span class="font-semibold text-slate-900"><?php echo formatPrice($tax); ?></span></div>
                </div>
                <div class="mt-6 border-t border-slate-200 pt-5">
                    <div class="flex justify-between text-lg font-bold text-slate-900"><span>Total</span><span><?php echo formatPrice($total); ?></span></div>
                    <a href="<?php echo siteUrl('checkout.php'); ?>" class="mt-6 block rounded-full bg-blue-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-blue-700">Proceed to checkout</a>
                    <a href="<?php echo siteUrl('shop.php'); ?>" class="mt-3 block rounded-full border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:border-blue-200 hover:text-blue-700">Continue shopping</a>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
