<?php
require_once APP_ROOT . '/app/bootstrap.php';
$state = is_string($_GET['state'] ?? null) ? strtoupper(trim($_GET['state'])) : '';
$zip = is_string($_GET['zip'] ?? null) ? trim($_GET['zip']) : '';
$result = null;

if (isset($_GET['state']) || isset($_GET['zip'])) {
    $result = preg_match('/^[A-Z]{2}$/', $state) && preg_match('/^[0-9]{5}$/', $zip)
        ? isShippingAllowed($state, $zip)
        : ['allowed' => false, 'message' => 'Enter a two-letter state code and a valid 5-digit ZIP code.'];
}

$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Delivery areas';
$stateNames = ['AZ' => 'Arizona', 'CA' => 'California', 'CO' => 'Colorado', 'FL' => 'Florida', 'NV' => 'Nevada', 'NM' => 'New Mexico', 'TX' => 'Texas', 'UT' => 'Utah'];
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span><span>Delivery areas</span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">BIG DELIVERIES. CLOSER TO YOU.</p><h1>Let&rsquo;s check<br><span>your destination.</span></h1><p class="cx-intro">Good hydration starts with a good delivery plan. Enter your location to check availability and minimum order quantities.</p></div></header>
    <div class="cx-columns">
        <section class="cx-panel"><h2>Where are we headed?</h2><form method="get" class="cx-stack"><div class="cx-form-grid">
            <div class="cx-field"><label for="state">State</label><select id="state" name="state" autocomplete="address-level1" required><option value="">Select your state</option><?php foreach ($allowedStates as $stateCode): ?><option value="<?php echo htmlspecialchars($stateCode); ?>" <?php echo $state === $stateCode ? 'selected' : ''; ?>><?php echo htmlspecialchars($stateNames[$stateCode] ?? $stateCode); ?></option><?php endforeach; ?></select></div>
            <div class="cx-field"><label for="zip">ZIP code</label><input id="zip" name="zip" value="<?php echo htmlspecialchars($zip); ?>" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" maxlength="5" required placeholder="Enter your 5-digit ZIP code"></div>
        </div><button class="lp-button lp-button-primary cx-wide" type="submit">Check delivery availability <span aria-hidden="true">&rarr;</span></button><p class="cx-note">Availability and order minimums vary by location.</p></form></section>
        <section class="cx-panel cx-dark" aria-label="Delivery result">
            <?php if ($result): ?><span class="cx-badge <?php echo $result['allowed'] ? 'cx-badge-success' : 'cx-badge-wait'; ?>"><?php echo $result['allowed'] ? 'YOU ARE IN OUR SERVICE AREA' : 'OUTSIDE OUR SERVICE AREA'; ?></span><h2><?php echo $result['allowed'] ? 'Good news. We deliver here.' : 'Not quite in reach yet.'; ?></h2><p class="cx-intro cx-note"><?php echo htmlspecialchars($result['message']); ?></p>
                <?php if ($result['allowed'] && isset($result['rule'])): ?><dl class="cx-facts"><div><dt>Delivery zone</dt><dd><?php echo htmlspecialchars($result['rule']['zone']); ?></dd></div><div><dt>Minimum order</dt><dd><?php echo $result['rule']['minOrder']; ?> pallets</dd></div></dl><a class="lp-button lp-button-primary cx-wide" href="<?php echo siteUrl('shop.php'); ?>">Find your pallet &nearr;</a><?php else: ?><div class="cx-help-strip"><a class="lp-text-link" href="<?php echo siteUrl('contact.php'); ?>">Talk to us about your destination &rarr;</a></div><?php endif; ?>
            <?php else: ?><p class="lp-eyebrow">A LITTLE CHECK GOES A LONG WAY</p><h2>Your next delivery,<br>starting here.</h2><p class="cx-intro cx-note">We serve select ZIP codes across <?php echo count($allowedStates); ?> U.S. states. Check your destination before filling your cart.</p><div class="cx-help-strip"><a class="lp-text-link" href="<?php echo siteUrl('shipping-policy.php'); ?>">Read our shipping policy &rarr;</a></div><?php endif; ?>
        </section>
    </div>
    <section class="cx-panel" style="margin-top:28px"><p class="lp-eyebrow">OUR DELIVERY FOOTPRINT</p><h2>Good supply, across state lines.</h2><div class="cx-state-list"><?php foreach ($allowedStates as $stateCode): ?><span><?php echo htmlspecialchars($stateNames[$stateCode] ?? $stateCode); ?></span><?php endforeach; ?></div><p class="cx-intro cx-note">Service is available in select ZIP codes within these states. Use the checker above to confirm your address.</p></section>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>