<?php
require_once APP_ROOT . '/app/bootstrap.php';
$products = require APP_ROOT . '/app/data/products.php';
$isLanding = true;
$halfPallets = array_filter($products, fn($item) => ($item['pallet_type'] ?? '') === 'Half pallet');
$startingPrice = min(array_column($halfPallets ?: $products, 'price_per_pallet'));
$heroProduct = current(array_filter($products, fn($item) => $item['id'] === 6)) ?: $products[0];
$stateNames = ['AZ' => 'Arizona', 'CA' => 'California', 'CO' => 'Colorado', 'FL' => 'Florida', 'NV' => 'Nevada', 'NM' => 'New Mexico', 'TX' => 'Texas', 'UT' => 'Utah'];
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="landing">
    <section class="lp-hero lp-wrap" aria-labelledby="hero-title">
        <canvas class="lp-hero-dots" aria-hidden="true"></canvas>
        <div class="lp-hero-copy">
            <p class="lp-eyebrow"><span class="lp-status-dot"></span> BIG ON HYDRATION. SIMPLE TO ORDER.</p>
            <h1 id="hero-title" class="lp-hero-art"><img src="<?php echo assetUrl('images/Aqua%20Dash%20Delivery%20Truck%20Landing%20Page%201.png'); ?>" alt="Aqua Dash — fast and convenient water delivery" width="1280" height="1280" fetchpriority="high"></h1>
            <p class="lp-hero-description">Keep your business stocked and your people refreshed. Your favorite water and sports drinks, by the full or half pallet.</p>
            <div class="lp-actions">
                <a class="lp-button lp-button-primary" href="#pallets">Find your pallet <span aria-hidden="true">&nearr;</span></a>
                <a class="lp-text-link" href="#delivery">Check delivery <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="lp-hero-note"><span class="lp-check" aria-hidden="true">&#10003;</span> Full &amp; half pallets <span class="lp-note-divider"></span> From <?php echo formatPrice($startingPrice); ?></div>
        </div>
        <div class="lp-showcase">
            <div class="lp-showcase-top"><span>YOUR NEXT RESTOCK</span><span class="lp-small-pill">By the pallet</span></div>
            <div class="lp-showcase-circle" aria-hidden="true"></div>
            <img class="lp-hero-product" src="<?php echo htmlspecialchars($heroProduct['image']); ?>" alt="Pure Life bottled water full pallet with a bottle and case in front" width="660" height="660" fetchpriority="high">
            <div class="lp-showcase-bottom">
                <div><span class="lp-mini-label">LESS RUNNING LOW. MORE READY TO GO.</span><strong><?php echo number_format($heroProduct['total_bottles_per_pallet']); ?> bottles. One pallet.</strong></div>
                <a href="<?php echo siteUrl('product.php?id=' . $heroProduct['id']); ?>" class="lp-circle-link" aria-label="View Pure Life full pallet">&nearr;</a>
            </div>
        </div>
    </section>
    <section class="lp-brand-strip" aria-label="Available brands">
        <div class="lp-wrap lp-brand-row">
            <p>FAMILIAR BRANDS.<br> BUSINESS-SIZED SUPPLY.</p>
            <div class="lp-brand-marquee" tabindex="0" role="group" aria-label="Brand showcase. Hover or focus to pause scrolling.">
                <div class="lp-brand-track">
                    <div class="lp-brand-group">
                        <span class="lp-brand-pure">Pure Life</span>
                        <span class="lp-brand-members">Member&rsquo;s Mark</span>
                        <span class="lp-brand-gatorade">GATORADE<span aria-hidden="true">&#9889;</span></span>
                    </div>
                    <div class="lp-brand-group" aria-hidden="true">
                        <span class="lp-brand-pure">Pure Life</span>
                        <span class="lp-brand-members">Member&rsquo;s Mark</span>
                        <span class="lp-brand-gatorade">GATORADE<span aria-hidden="true">&#9889;</span></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="lp-catalog lp-wrap lp-section" id="pallets" aria-labelledby="catalog-title">
        <div class="lp-section-heading">
            <div><p class="lp-eyebrow">THE PALLET COLLECTION</p><h2 id="catalog-title">A little restock.<br>Or a whole lot.</h2></div>
            <p>From break rooms to busy event days.<br> Choose the brand and quantity that fit your needs.</p>
        </div>
        <div class="lp-catalog-toolbar">
            <div class="lp-filters" role="group" aria-label="Filter products by pallet size" hidden>
                <button type="button" data-pallet-filter="all" aria-pressed="true">All pallets</button>
                <button type="button" data-pallet-filter="full" aria-pressed="false">Full pallets</button>
                <button type="button" data-pallet-filter="half" aria-pressed="false">Half pallets</button>
            </div>
            <p class="lp-product-count" aria-live="polite" aria-atomic="true"><?php echo count($products); ?> products</p>
        </div>
        <div class="lp-product-grid">
            <?php foreach ($products as $product):
                $isHalf = ($product['pallet_type'] ?? '') === 'Half pallet';
                $displayName = str_replace(' - Half Pallet', '', $product['name']);
            ?>
            <article class="lp-product" data-pallet-size="<?php echo $isHalf ? 'half' : 'full'; ?>">
                <a class="lp-product-image" href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>" tabindex="-1" aria-hidden="true">
                    <span class="lp-product-badge"><?php echo $isHalf ? 'Half pallet' : 'Full pallet'; ?></span>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="" width="360" height="300" loading="lazy">
                </a>
                <div class="lp-product-content">
                    <p class="lp-product-category"><?php echo htmlspecialchars($product['category']); ?> <span aria-hidden="true">/</span> <?php echo htmlspecialchars($product['bottle_size']); ?></p>
                    <h3><a href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>"><?php echo htmlspecialchars($displayName); ?></a></h3>
                    <p class="lp-product-spec"><?php echo $product['cases_per_pallet']; ?> cases <span aria-hidden="true">&middot;</span> <?php echo number_format($product['total_bottles_per_pallet']); ?> bottles</p>
                    <div class="lp-product-buy"><div><strong><?php echo formatPrice($product['price_per_pallet']); ?></strong><span>per <?php echo $isHalf ? 'half' : 'full'; ?> pallet</span></div><a class="lp-product-link" href="<?php echo siteUrl('product.php?id=' . $product['id']); ?>" aria-label="Shop <?php echo htmlspecialchars($product['name']); ?>">Shop <span aria-hidden="true">&nearr;</span></a></div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="lp-catalog-foot"><p>Product prices shown. Check your destination for delivery eligibility and order requirements.</p><a class="lp-text-link" href="<?php echo siteUrl('shop.php'); ?>">Explore the shop <span aria-hidden="true">&rarr;</span></a></div>
    </section>
    <section class="lp-how-section" id="how-it-works" aria-labelledby="how-title">
        <div class="lp-wrap lp-how-grid">
            <div class="lp-how-intro"><p class="lp-eyebrow">FROM OUR CATALOG TO YOUR DOOR</p><h2 id="how-title">Your next restock,<br>in three steps.</h2><a class="lp-text-link" href="<?php echo siteUrl('faq.php'); ?>">A few helpful answers <span aria-hidden="true">&rarr;</span></a></div>
            <ol class="lp-steps">
                <li><span class="lp-step-number">01</span><div><h3>Pick your pallet.</h3><p>Choose your brand, then go full or half. Every listing shows exactly how many cases and bottles you get.</p></div></li>
                <li><span class="lp-step-number">02</span><div><h3>Check your destination.</h3><p>Enter your state and ZIP code to check availability. Order minimums depend on your delivery area.</p></div></li>
                <li><span class="lp-step-number">03</span><div><h3>Get delivery details.</h3><p>Review freight requirements and request a quote for your order before arranging your restock.</p></div></li>
            </ol>
        </div>
    </section>
    <section class="lp-wrap lp-section" id="delivery" aria-labelledby="delivery-title">
        <div class="lp-delivery">
            <div class="lp-delivery-copy"><p class="lp-eyebrow">LET&rsquo;S SEE IF WE DELIVER TO YOU</p><h2 id="delivery-title">Big deliveries.<br>Closer to you.</h2><p>Serving select ZIP codes across <?php echo count($allowedStates); ?> U.S. states. Check your location before you fill your cart.</p><div class="lp-state-list" aria-label="Service states"><?php foreach ($allowedStates as $state): ?><span title="<?php echo htmlspecialchars($stateNames[$state] ?? $state); ?>"><?php echo htmlspecialchars($state); ?></span><?php endforeach; ?></div><a class="lp-delivery-link" href="<?php echo siteUrl('shipping-policy.php'); ?>">Read our shipping policy <span aria-hidden="true">&rarr;</span></a></div>
            <form class="lp-delivery-form" action="<?php echo siteUrl('service-area.php'); ?>" method="get">
                <span class="lp-form-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 10c0 6-8 11-8 11S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                <h3>Where are we headed?</h3><p>Find out if your address is in our service area.</p>
                <label for="delivery-state">State</label><select id="delivery-state" name="state" autocomplete="address-level1" required><option value="">Select your state</option><?php foreach ($allowedStates as $state): ?><option value="<?php echo htmlspecialchars($state); ?>"><?php echo htmlspecialchars($stateNames[$state] ?? $state); ?></option><?php endforeach; ?></select>
                <label for="delivery-zip">ZIP code</label><input id="delivery-zip" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" maxlength="5" placeholder="Enter your 5-digit ZIP code" required>
                <button class="lp-button lp-button-primary" type="submit">Check delivery availability <span aria-hidden="true">&rarr;</span></button>
                <small>Availability and minimum orders vary by location.</small>
            </form>
        </div>
    </section>
    <section class="lp-wrap lp-faq" aria-labelledby="faq-title">
        <div><p class="lp-eyebrow">GOOD TO KNOW</p><h2 id="faq-title">Before you<br>stock up.</h2><a class="lp-text-link" href="<?php echo siteUrl('contact.php'); ?>">Talk to our team <span aria-hidden="true">&rarr;</span></a></div>
        <div class="lp-faq-list">
            <details><summary>What&rsquo;s the difference between a full and half pallet?<span aria-hidden="true">+</span></summary><p>Our water full pallets contain 48 cases and 1,920 bottles; half pallets contain 24 cases and 960 bottles. Gatorade full pallets contain 54 cases and 1,296 bottles; half pallets contain 27 cases and 648 bottles.</p></details>
            <details><summary>Can I order just one pallet?<span aria-hidden="true">+</span></summary><p>Product quantities start at one pallet, but your delivery area may require a larger order. Use the delivery checker to see the minimum for your destination.</p></details>
            <details><summary>Is shipping included in the product price?<span aria-hidden="true">+</span></summary><p>The listed price is for the product. Freight is separate and depends on your destination and order. <a href="<?php echo siteUrl('freight-quote.php'); ?>">Request a freight quote</a> for delivery details.</p></details>
            <details><summary>Where do you deliver?<span aria-hidden="true">+</span></summary><p>We serve select ZIP codes in Arizona, California, Colorado, Florida, Nevada, New Mexico, Texas, and Utah. Enter your state and ZIP code in the delivery checker to confirm eligibility.</p></details>
        </div>
    </section>
    <section class="lp-final-cta"><div class="lp-wrap"><div><p class="lp-eyebrow">READY WHEN YOU ARE</p><h2>Keep the good stuff flowing.</h2></div><a class="lp-button lp-button-light" href="<?php echo siteUrl('shop.php'); ?>">Shop all pallets <span aria-hidden="true">&nearr;</span></a></div></section>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
