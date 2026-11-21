<?php
require_once APP_ROOT . '/app/bootstrap.php';
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Shipping & delivery';
require APP_ROOT . '/app/views/layouts/header.php';
?>

<main id="main-content" class="cx-page lp-wrap">
    <header class="cx-heading"><div>
        <div class="lp-eyebrow">Shipping & delivery</div>
        <h1 >Shipping policy</h1></div></header>

    <div class="cx-panel cx-prose">
        <p>All orders are for full or half pallet quantities and are delivered within approved service areas only. Freight charges may be quoted after the address has been validated and the order has been reviewed for pallet logistics requirements.</p>
        <p>For destinations where carrier pricing is not yet available, orders remain in a pending freight quote state until a final shipping cost is approved. Payment is not completed until freight is confirmed.</p>
        <p>Customers are responsible for confirming site access, unloading requirements, and any other facility conditions that may affect delivery scheduling. Additional handling or delivery services may require review before shipment.</p>
    </div>
</main>

<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
