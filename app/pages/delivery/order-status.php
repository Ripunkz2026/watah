<?php
require_once APP_ROOT . '/app/bootstrap.php';
require_once APP_ROOT . '/app/auth/auth.php';
requireCustomer();
$number = requestText($_GET, 'order');
if ($number === '') {
    $latest = customerOrders((int)$_SESSION['customer']['id']);
    $number = $latest[0]['id'] ?? '';
}
$order = $number === '' ? null : findOrder($number, (int)$_SESSION['customer']['id']);
if ($number !== '' && !$order) http_response_code(404);
$isExpressPage = $isCustomerPage = true;
$pageTitle = !empty($showFreight) ? 'Freight & delivery' : 'Order status';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('account.php'); ?>">My account</a><span aria-hidden="true">/</span><span><?php echo authEscape($pageTitle); ?></span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">FROM OUR WAREHOUSE TO YOUR DOOR</p><h1>Your restock,<br><span>step by step.</span></h1></div><a class="lp-button lp-button-primary" href="<?php echo siteUrl('shop.php'); ?>">Shop pallets &nearr;</a></header>
    <?php if (!empty($_SESSION['checkout_notice'])): ?><div class="cx-alert" role="status"><?php echo authEscape($_SESSION['checkout_notice']); unset($_SESSION['checkout_notice']); ?></div><?php endif; ?>
    <?php if ($order): ?>
        <?php require APP_ROOT . '/app/views/layouts/order-detail.php'; ?>
    <?php else: ?><section class="cx-panel cx-empty"><h2><?php echo $number === '' ? 'Your first restock awaits.' : 'Order not found.'; ?></h2><p class="cx-note"><?php echo $number === '' ? 'Your orders and delivery updates will appear here after checkout.' : 'Choose an order from your account to view its details.'; ?></p><a class="lp-button lp-button-primary" href="<?php echo siteUrl('account.php'); ?>">Back to my account &rarr;</a></section><?php endif; ?>
    <div class="cx-help-strip"><p class="cx-note">Have a question about your order?</p><a class="lp-text-link" href="<?php echo siteUrl('contact.php'); ?>">Talk to our team &rarr;</a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>