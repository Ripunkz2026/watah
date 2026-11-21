<?php
require_once APP_ROOT . '/app/bootstrap.php';
require_once APP_ROOT . '/app/auth/auth.php';
if (empty($_SESSION['customer'])) $_SESSION['after_login'] = 'checkout.php';
requireCustomer();
bootstrapCart();
$products = require APP_ROOT . '/app/data/products.php';
$cartItems = [];
foreach ($_SESSION['cart'] as $productId => $quantity) {
    foreach ($products as $product) {
        if ($product['id'] === (int)$productId) $cartItems[] = ['product'=>$product,'quantity'=>$quantity];
    }
}
$subtotal = array_sum(array_map(fn($item) => $item['product']['price_per_pallet'] * $item['quantity'], $cartItems));
$shipping = 0.0;
$tax = round($subtotal * 0.075, 2);
$total = $subtotal + $tax;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verifyCsrf();
        $token = requestText($_POST, 'checkout_token');
        if (!hash_equals($_SESSION['checkout_token'] ?? '', $token)) throw new InvalidArgumentException('Checkout has changed. Please review your cart and submit again.');
        $number = createCustomerOrder((int)$_SESSION['customer']['id'], $_SESSION['cart'], $_POST, $token);
        $_SESSION['cart'] = [];
        $_SESSION['checkout_notice'] = 'Order submitted. Your delivery rate will be reviewed before payment.';
        header('Location: ' . siteUrl('order-status.php?order=' . rawurlencode($number)));
        exit;
    } catch (InvalidArgumentException $error) {
        $errors[] = $error->getMessage();
    } catch (Throwable $error) {
        error_log('Checkout: ' . $error->getMessage());
        $errors[] = 'Your order could not be saved. Please try again. Your cart has been kept.';
    }
}
$cartFingerprint = hash('sha256', json_encode($_SESSION['cart']));
if (empty($_SESSION['checkout_token']) || ($_SERVER['REQUEST_METHOD'] !== 'POST' && ($_SESSION['checkout_cart'] ?? '') !== $cartFingerprint)) {
    $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
    $_SESSION['checkout_cart'] = $cartFingerprint;
}
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Checkout';
$checkoutDefaults = ['state' => $_SESSION['customer']['state'] ?? '', 'zip' => $_SESSION['customer']['zip'] ?? ''];
$checkoutValue = fn($key) => htmlspecialchars($_SERVER['REQUEST_METHOD'] === 'POST' ? (is_string($_POST[$key] ?? null) ? $_POST[$key] : '') : ($checkoutDefaults[$key] ?? ''), ENT_QUOTES, 'UTF-8');
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span><a href="<?php echo siteUrl('cart.php'); ?>">Your cart</a><span aria-hidden="true">/</span><span>Checkout</span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">ONE STEP CLOSER TO WELL STOCKED</p><h1>Where are<br><span>we headed?</span></h1><p class="cx-intro">Tell us a little about you and your delivery destination. We&rsquo;ll review freight before payment.</p></div><a class="lp-text-link" href="<?php echo siteUrl('cart.php'); ?>">&larr; Back to your cart</a></header>
    <nav class="cx-step-nav" aria-label="Order steps"><a href="<?php echo siteUrl('cart.php'); ?>">01 &nbsp; Your cart</a><span aria-hidden="true">&rarr;</span><span aria-current="step">02 &nbsp; Delivery details</span><span aria-hidden="true">&rarr;</span><span>03 &nbsp; Freight review</span></nav>
    <?php if ($errors): ?><div class="cx-alert cx-alert-error" role="alert"><ul><?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <?php require APP_ROOT . '/app/views/layouts/customer-delivery-notice.php'; ?>
    <?php if (!$cartItems): ?>
    <section class="cx-panel cx-empty"><h2>Let&rsquo;s pick your pallets first.</h2><p class="cx-note">Your cart is empty. Add a full or half pallet to continue to delivery details.</p><a class="lp-button lp-button-primary" href="<?php echo siteUrl('shop.php'); ?>">Explore the pallets &nearr;</a></section>
    <?php else: ?>
    <div class="cx-columns">
        <form method="post" class="cx-stack"><input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>"><input type="hidden" name="checkout_token" value="<?php echo authEscape($_SESSION['checkout_token']); ?>">
            <section class="cx-panel"><h2>01 &nbsp; Your details</h2><p class="cx-note">Who should we contact about this order?</p><div class="cx-form-grid">
                <div class="cx-field"><label for="first_name">First name</label><input id="first_name" name="first_name" autocomplete="given-name" value="<?php echo $checkoutValue('first_name'); ?>" required></div>
                <div class="cx-field"><label for="last_name">Last name</label><input id="last_name" name="last_name" autocomplete="family-name" value="<?php echo $checkoutValue('last_name'); ?>" required></div>
                <div class="cx-field cx-field-wide"><label for="email">Business email</label><input id="email" name="email" type="email" autocomplete="email" placeholder="you@company.com" value="<?php echo $checkoutValue('email'); ?>" required></div>
            </div></section>
            <section class="cx-panel"><h2>02 &nbsp; Delivery address</h2><p class="cx-note">Delivery is available in select U.S. states and ZIP codes.</p><div class="cx-form-grid">
                <div class="cx-field cx-field-wide"><label for="street">Street address</label><input id="street" name="street" autocomplete="street-address" value="<?php echo $checkoutValue('street'); ?>" required></div>
                <div class="cx-field"><label for="city">City</label><input id="city" name="city" autocomplete="address-level2" value="<?php echo $checkoutValue('city'); ?>" required></div>
                <div class="cx-field"><label for="state">State</label><select id="state" name="state" autocomplete="address-level1" required><option value="">Select your state</option><?php foreach ($allowedStates as $stateCode): ?><option value="<?php echo htmlspecialchars($stateCode); ?>" <?php echo $checkoutValue('state') === $stateCode ? 'selected' : ''; ?>><?php echo htmlspecialchars($stateCode); ?></option><?php endforeach; ?></select></div>
                <div class="cx-field"><label for="zip">ZIP code</label><input id="zip" name="zip" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" maxlength="5" placeholder="5-digit ZIP code" value="<?php echo $checkoutValue('zip'); ?>" required></div>
            </div></section>
            <section class="cx-panel cx-freight-note"><span class="cx-badge">GOOD TO KNOW</span><h3>Freight first. Payment after.</h3><p class="cx-note">Your final delivery rate will be reviewed before payment is collected. Submitting your order does not charge you.</p></section>
            <button type="submit" class="lp-button lp-button-primary cx-wide">Submit order for freight review <span aria-hidden="true">&rarr;</span></button>
        </form>
        <aside class="cx-panel cx-summary"><div class="cx-panel-head"><h2>Your restock</h2><a class="cx-link" href="<?php echo siteUrl('cart.php'); ?>">Edit cart</a></div>
            <?php foreach ($cartItems as $item): ?><div class="cx-order-line"><div><?php echo htmlspecialchars($item['product']['name']); ?><small><?php echo $item['quantity']; ?> pallet<?php echo $item['quantity'] === 1 ? '' : 's'; ?></small></div><strong><?php echo formatPrice($item['product']['price_per_pallet'] * $item['quantity']); ?></strong></div><?php endforeach; ?>
            <dl class="cx-facts"><div><dt>Product subtotal</dt><dd><?php echo formatPrice($subtotal); ?></dd></div><div><dt>Freight</dt><dd>Pending quote</dd></div><div><dt>Estimated tax</dt><dd><?php echo formatPrice($tax); ?></dd></div></dl><div class="cx-total"><span>Before freight</span><span><?php echo formatPrice($total); ?></span></div><p class="cx-note">Freight is subject to final review for your destination.</p>
        </aside>
    </div>
    <?php endif; ?>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
