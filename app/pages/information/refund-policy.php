<?php
require_once APP_ROOT . '/app/bootstrap.php';
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Refunds & cancellations';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<main id="main-content" class="cx-page lp-wrap">
    <header class="cx-heading"><div>
        <div class="lp-eyebrow">Refunds & cancellations</div>
        <h1 >Policy overview</h1></div></header>

    <div class="cx-panel cx-prose">
        <p>Orders may be canceled before payment is finalized or before final freight approval depending on fulfillment status. Requests are reviewed by the administrator and subject to warehouse or shipping constraints.</p>
        <p>Once fulfillment or freight processing has begun, cancellations may be subject to processing fees or non-refundable charges. Refund approvals depend on the specific order details, payment status, and fulfillment timeline.</p>
        <p>Customers should contact the support team promptly if an order must be adjusted or if there is a delivery issue. This demo storefront is intended for client presentation and should be reviewed with the business before launch.</p>
    </div>
</main>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
