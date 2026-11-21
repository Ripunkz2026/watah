<?php
require_once APP_ROOT . '/app/bootstrap.php';
$isExpressPage = true;
$isContactPage = true;
$pageTitle = 'Contact us';
$pageDescription = 'Talk to the Aqua Dash team about pallet orders, delivery requirements, and your next restock.';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ep-page ep-contact lp-wrap">
    <a class="ep-back" href="<?php echo siteUrl('index.php'); ?>"><span aria-hidden="true">&larr;</span> Back to home</a>
    <div class="ep-contact-layout">
        <section class="ep-contact-copy" aria-labelledby="contact-title">
            <p class="lp-eyebrow">A LITTLE HELP WITH YOUR NEXT BIG ORDER</p>
            <h1 id="contact-title">Let&rsquo;s talk<br><span>pallets.</span></h1>
            <p class="ep-contact-intro">A question about your order? Planning a bigger restock? We&rsquo;re here to help you work out the details.</p>
            <div class="ep-contact-details">
                <div class="ep-contact-detail"><span class="ep-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 6 9 7 9-7"/></svg></span><div><h2>Email our team</h2><p>sales@purelifewholesale.demo</p></div></div>
                <div class="ep-contact-detail"><span class="ep-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m7 3 3 5-3 3a15 15 0 0 0 6 6l3-3 5 3c-1 4-3 5-6 4A21 21 0 0 1 3 9C2 6 3 4 7 3Z"/></svg></span><div><h2>Give us a call</h2><p>(800) 555-0147</p></div></div>
                <div class="ep-contact-detail"><span class="ep-detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span><div><h2>Support hours</h2><p>Monday&ndash;Friday<br>8:00 AM&ndash;5:00 PM CST</p></div></div>
            </div>
            <p class="ep-demo-contact">Contact details shown are for this demo storefront.</p>
            <div class="ep-contact-tip"><span class="lp-eyebrow">LET&rsquo;S GET A HEAD START</span><p>Include your ZIP code, preferred product, and pallet quantity so we can understand what you need.</p></div>
        </section>
        <section class="ep-contact-panel" aria-labelledby="message-title">
            <div class="ep-panel-heading"><span class="ep-form-symbol" aria-hidden="true">&nearr;</span><p class="lp-eyebrow">START A CONVERSATION</p><h2 id="message-title">What can we help with?</h2><p>Tell us a little about your business and your order.</p></div>
            <form class="ep-form" id="contact-form" method="post" action="<?php echo siteUrl('contact.php'); ?>" aria-describedby="contact-demo-note">
                <div class="ep-field-row">
                    <div class="ep-field"><label for="contact-name">Your name</label><input id="contact-name" name="name" type="text" autocomplete="name" placeholder="Full name" maxlength="120" required></div>
                    <div class="ep-field"><label for="contact-company">Company <span>(optional)</span></label><input id="contact-company" name="company" type="text" autocomplete="organization" placeholder="Business name" maxlength="160"></div>
                </div>
                <div class="ep-field"><label for="contact-email">Email address</label><input id="contact-email" name="email" type="email" autocomplete="email" placeholder="you@company.com" maxlength="254" required></div>
                <div class="ep-field"><label for="contact-topic">How can we help?</label><select id="contact-topic" name="topic" required><option value="">Choose a topic</option><option>Product &amp; pallet questions</option><option>Delivery &amp; freight</option><option>Help with an order</option><option>Account support</option><option>Something else</option></select></div>
                <div class="ep-field"><label for="contact-message">Your message</label><textarea id="contact-message" name="message" rows="5" maxlength="3000" placeholder="Tell us what you have in mind..." required></textarea></div>
                <button class="lp-button lp-button-primary ep-submit" id="contact-preview-button" type="submit" disabled>Preview message <span aria-hidden="true">&rarr;</span></button>
                <p class="ep-form-note" id="contact-demo-note">Demo form: preview your message here. Messages are not sent.</p>
                <noscript><p class="ep-form-note">Enable JavaScript to preview a message.</p></noscript>
            </form>
            <section class="ep-message-preview" id="contact-preview" aria-labelledby="preview-title" tabindex="-1" hidden><p class="lp-eyebrow">YOUR MESSAGE PREVIEW</p><h3 id="preview-title">Here&rsquo;s what you wrote.</h3><dl><dt>From</dt><dd id="preview-from"></dd><dt>Topic</dt><dd id="preview-topic"></dd><dt>Message</dt><dd id="preview-message"></dd></dl><p class="ep-form-note" role="status">Preview only. Your message has not been sent.</p></section>
        </section>
    </div>
    <section class="ep-help-links" aria-labelledby="quick-help-title"><div><p class="lp-eyebrow">A QUICK WAY FORWARD</p><h2 id="quick-help-title">Your answer might be right here.</h2></div><a href="<?php echo siteUrl('service-area.php'); ?>"><span>Check delivery areas<small>See if we serve your ZIP code.</small></span><span aria-hidden="true">&nearr;</span></a><a href="<?php echo siteUrl('faq.php'); ?>"><span>Explore our FAQs<small>The essentials, all in one place.</small></span><span aria-hidden="true">&nearr;</span></a></section>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>