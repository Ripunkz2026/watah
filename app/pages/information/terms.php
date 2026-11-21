<?php
require_once APP_ROOT . '/app/bootstrap.php';
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Terms & conditions';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<main id="main-content" class="cx-page lp-wrap">
    <header class="cx-heading"><div>
        <div class="lp-eyebrow">Terms</div>
        <h1 >Terms & conditions</h1></div></header>

    <div class="cx-panel cx-prose">
        <p>Orders are subject to approval of delivery eligibility, freight requirements, and inventory availability. The business reserves the right to decline orders outside approved territories or under non-standard shipping conditions.</p>
        <p>All prices and product availability are subject to change without notice and may be updated by the administrator from the control panel. Full pallet quantities are required unless otherwise configured for a specific product or customer arrangement.</p>
        <p>Customers should review final freight pricing, tax impact, and order status before payment confirmation. This demo site should be reviewed by legal and compliance advisors before public deployment.</p>
    </div>
</main>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
