<?php
require_once APP_ROOT . '/app/bootstrap.php';
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Privacy policy';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<main id="main-content" class="cx-page lp-wrap">
    <header class="cx-heading"><div>
        <div class="lp-eyebrow">Privacy</div>
        <h1 >Privacy policy</h1></div></header>

    <div class="cx-panel cx-prose">
        <p>Customer information is used to process orders, validate delivery territory eligibility, manage freight coordination, and provide shipment updates when applicable.</p>
        <p>Contact and order information should be stored securely and access should be restricted to authorized personnel only. Payments should be handled by a PCI-compliant provider, and card data should never be stored in the web application.</p>
        <p>Customers may request access to personal data, corrections, or account removal in accordance with applicable business requirements and privacy law.</p>
    </div>
</main>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
