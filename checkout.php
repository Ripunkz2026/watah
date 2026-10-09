<?php
require __DIR__ . '/config.php';
bootstrapCart();
$products = require __DIR__ . '/data.php';

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

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $state = strtoupper(trim($_POST['state'] ?? ''));
    $zip = trim($_POST['zip'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $city = trim($_POST['city'] ?? '');

    if (empty($firstName) || empty($lastName) || empty($email) || empty($state) || empty($zip) || empty($city)) {
        $errors[] = 'Please complete all required customer and shipping details.';
    }

    $validation = isShippingAllowed($state, $zip);
    if (!$validation['allowed']) {
        $errors[] = $validation['message'];
    }

    if (empty($cartItems)) {
        $errors[] = 'Your cart is empty and cannot be checked out.';
    }

    if (empty($errors)) {
        $_SESSION['checkout_notice'] = 'Order submitted successfully. Freight approval is pending before payment can be collected.';
        header('Location: ' . siteUrl('order-status.php'));
        exit;
    }
}

require __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10">
        <div class="text-sm font-semibold uppercase tracking-[0.16em] text-blue-700">Checkout</div>
        <h1 class="mt-2 text-4xl font-extrabold tracking-tight text-slate-900">Complete your wholesale order</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-8 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <ul class="list-disc space-y-2 pl-5">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
        <form method="post" class="space-y-8">
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-xl font-bold text-slate-900">Customer details</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-2 block text-sm font-medium text-slate-700">First name</label>
                        <input id="first_name" name="first_name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                    <div>
                        <label for="last_name" class="mb-2 block text-sm font-medium text-slate-700">Last name</label>
                        <input id="last_name" name="last_name" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                    <div class="sm:col-span-2">
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Business email</label>
                        <input id="email" name="email" type="email" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
                <h2 class="text-xl font-bold text-slate-900">Shipping address</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="street" class="mb-2 block text-sm font-medium text-slate-700">Street address</label>
                        <input id="street" name="street" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                    <div>
                        <label for="city" class="mb-2 block text-sm font-medium text-slate-700">City</label>
                        <input id="city" name="city" type="text" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                    <div>
                        <label for="state" class="mb-2 block text-sm font-medium text-slate-700">State</label>
                        <input id="state" name="state" type="text" value="TX" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                    <div>
                        <label for="zip" class="mb-2 block text-sm font-medium text-slate-700">ZIP code</label>
                        <input id="zip" name="zip" type="text" value="77002" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-800 focus:border-blue-400 focus:outline-none" />
                    </div>
                </div>
            </section>

            <section class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6 shadow-soft">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-[0.12em] text-amber-700">Freight status</div>
                        <h3 class="mt-2 text-xl font-bold text-slate-900">Quote required before payment</h3>
                    </div>
                    <span class="rounded-full bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.12em] text-amber-700">Pending</span>
                </div>
                <p class="mt-4 text-sm leading-6 text-slate-700">A final freight rate will be reviewed before payment is collected. This protects the business and prevents premature billing for pallet logistics.</p>
            </section>

            <button type="submit" class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700">Submit order</button>
        </form>

        <aside class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-soft">
            <h2 class="text-xl font-bold text-slate-900">Order summary</h2>
            <div class="mt-6 space-y-4">
                <?php foreach ($cartItems as $item): ?>
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3 last:border-b-0">
                        <div>
                            <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($item['product']['name']); ?></div>
                            <div class="text-xs uppercase tracking-[0.12em] text-slate-500"><?php echo $item['quantity']; ?> pallet(s)</div>
                        </div>
                        <div class="font-bold text-slate-900"><?php echo formatPrice((float)$item['product']['price_per_pallet'] * (int)$item['quantity']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 space-y-3 border-t border-slate-200 pt-5 text-sm text-slate-600">
                <div class="flex justify-between"><span>Item subtotal</span><span class="font-semibold text-slate-900"><?php echo formatPrice($subtotal); ?></span></div>
                <div class="flex justify-between"><span>Freight</span><span class="font-semibold text-slate-900"><?php echo formatPrice($shipping); ?></span></div>
                <div class="flex justify-between"><span>Tax</span><span class="font-semibold text-slate-900"><?php echo formatPrice($tax); ?></span></div>
                <div class="flex justify-between text-base font-bold text-slate-900"><span>Total</span><span><?php echo formatPrice($total); ?></span></div>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
