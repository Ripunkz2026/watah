<?php
require_once APP_ROOT . '/app/bootstrap.php';
bootstrapCart();
$products = require APP_ROOT . '/app/data/products.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_qty'])) {
        $id = (int)($_POST['product_id'] ?? 0);
        $qty = min(999, max(0, (int)($_POST['quantity'] ?? 0)));
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
$shipping = 0.00;
$tax = $subtotal * 0.075;
$total = $subtotal + $shipping + $tax;

$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Your cart';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span><a href="<?php echo siteUrl('shop.php'); ?>">Shop pallets</a><span aria-hidden="true">/</span><span>Your cart</span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">GOOD THINGS, BY THE PALLET</p><h1>Your next <span>restock.</span></h1><p class="cx-intro">A quick look at what&rsquo;s heading your way. Review your pallets before checking out.</p></div><span class="cx-badge"><?php echo cartCount(); ?> pallet<?php echo cartCount() === 1 ? '' : 's'; ?> in your cart</span></header>
    <nav class="cx-step-nav" aria-label="Order steps"><span aria-current="step">01 &nbsp; Your cart</span><span aria-hidden="true">&rarr;</span><span>02 &nbsp; Delivery details</span><span aria-hidden="true">&rarr;</span><span>03 &nbsp; Freight review</span></nav>
    <?php if (!$cartItems): ?>
    <section class="cx-panel cx-empty"><span class="cx-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M7 8V6a5 5 0 0 1 10 0v2M4 8h16l1 13H3L4 8Z"/></svg></span><h2>A fresh start. An empty cart.</h2><p class="cx-note">Keep your business stocked with familiar water and sports drink brands, by the full or half pallet.</p><a class="lp-button lp-button-primary" href="<?php echo siteUrl('shop.php'); ?>">Find your pallet <span aria-hidden="true">&nearr;</span></a></section>
    <?php else: ?>
    <div class="cx-columns"><div class="cx-stack">
        <?php foreach ($cartItems as $item): $product = $item['product']; ?>
        <article class="cx-panel cx-cart-item"><a class="cx-cart-image" href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>" tabindex="-1" aria-hidden="true"><img src="<?php echo htmlspecialchars($product['image']); ?>" alt="" width="110" height="135"></a><div><h2><a href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($product['name']); ?></a></h2><p class="cx-note"><?php echo htmlspecialchars($product['pallet_type'] ?? 'Full pallet'); ?> &middot; <?php echo $product['cases_per_pallet']; ?> cases &middot; <?php echo htmlspecialchars($product['bottle_size']); ?></p><div class="cx-cart-bottom"><form method="post" class="cx-quantity"><input type="hidden" name="product_id" value="<?php echo $product['id']; ?>"><input type="hidden" name="update_qty" value="1"><label class="cx-sr-only" for="qty-<?php echo $product['id']; ?>">Quantity for <?php echo htmlspecialchars($product['name']); ?></label><select id="qty-<?php echo $product['id']; ?>" name="quantity"><?php for ($i = 0; $i <= max(8, (int)$item['quantity']); $i++): ?><option value="<?php echo $i; ?>" <?php echo (int)$item['quantity'] === $i ? 'selected' : ''; ?>><?php echo $i; ?></option><?php endfor; ?></select><button type="submit">Update</button></form><strong class="cx-line-total"><?php echo formatPrice($product['price_per_pallet'] * $item['quantity']); ?></strong></div><div class="cx-cart-footer"><p class="cx-note"><?php echo formatPrice($product['price_per_pallet']); ?> per pallet</p><form method="post"><input type="hidden" name="product_id" value="<?php echo $product['id']; ?>"><button type="submit" name="remove_item" value="1" class="cx-remove" aria-label="Remove <?php echo htmlspecialchars($product['name']); ?>">Remove</button></form></div></div></article>
        <?php endforeach; ?>
        <a class="lp-text-link" href="<?php echo siteUrl('shop.php'); ?>">&larr; Continue shopping</a>
    </div><aside class="cx-panel cx-summary"><h2>Order summary</h2><dl class="cx-facts"><div><dt>Product subtotal</dt><dd><?php echo formatPrice($subtotal); ?></dd></div><div><dt>Freight</dt><dd>Pending quote</dd></div><div><dt>Estimated tax</dt><dd><?php echo formatPrice($tax); ?></dd></div></dl><div class="cx-total"><span>Before freight</span><span><?php echo formatPrice($total); ?></span></div><p class="cx-note">Final freight is reviewed before payment is collected.</p><a class="lp-button lp-button-primary cx-wide" href="<?php echo siteUrl('checkout.php'); ?>">Continue to checkout <span aria-hidden="true">&rarr;</span></a><a class="cx-button-secondary cx-wide" href="<?php echo siteUrl('service-area.php'); ?>">Check delivery availability</a></aside></div>
    <?php endif; ?>
    <div class="cx-help-strip"><p class="cx-note">Questions about quantities or delivery? We&rsquo;re here to help.</p><a class="lp-text-link" href="<?php echo siteUrl('contact.php'); ?>">Talk to our team &rarr;</a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
