<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/admin-guard.php';
$number = requestText($_GET, 'order');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verifyCsrf();
        updateOrderByAdmin($number, (int)$_SESSION['admin']['id'], $_POST);
        header('Location: ' . siteUrl('admin-order.php?' . http_build_query(['order'=>$number,'saved'=>1])));
        exit;
    } catch (InvalidArgumentException $exception) { $error = $exception->getMessage(); }
    catch (Throwable $exception) { error_log('Order update: '.$exception->getMessage()); $error = 'The update could not be saved. Please try again.'; }
}
$order = findOrder($number);
if (!$order) http_response_code(404);
$isExpressPage = $isCustomerPage = $isAdminPage = true;
$adminSection = 'orders'; $pageTitle = 'Manage order';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('admin-orders.php'); ?>">Orders</a><span aria-hidden="true">/</span><span>Manage order</span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">FROM ORDER TO DELIVERY</p><h1>Keep the next<br><span>restock moving.</span></h1></div></header>
    <?php if (!$order): ?><div class="cx-alert">Order not found.</div><?php else: ?>
    <?php if ($error): ?><div class="cx-alert cx-alert-error" role="alert"><?php echo authEscape($error); ?></div><?php endif; ?>
    <?php if (isset($_GET['saved'])): ?><div class="cx-alert" role="status">Order updated. The customer can see the latest status.</div><?php endif; ?>
    <section class="cx-panel" style="margin-bottom:28px"><h2>Manage this order</h2>
        <?php if ($order['status'] === 'Pending freight quote'): ?>
        <form method="post" class="cx-stack"><input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="quote"><input type="hidden" name="expected_status" value="<?php echo authEscape($order['status']); ?>"><div class="cx-form-grid">
            <div class="cx-field"><label for="carrier">Carrier</label><input id="carrier" name="carrier" maxlength="160" required value="<?php echo authEscape(requestText($_POST,'carrier')); ?>"></div><div class="cx-field"><label for="service">Delivery service</label><input id="service" name="service" maxlength="180" required value="<?php echo authEscape(requestText($_POST,'service')); ?>"></div><div class="cx-field"><label for="rate">Freight rate (USD)</label><input id="rate" name="rate" type="number" min="0" max="9999999.99" step="0.01" required value="<?php echo authEscape(requestText($_POST,'rate')); ?>"></div></div><button class="lp-button lp-button-primary" type="submit">Confirm freight &amp; request payment &rarr;</button></form>
        <?php endif; ?>
        <?php $transitions = orderTransitions()[$order['status']] ?? []; if ($transitions): ?>
        <form method="post" class="cx-stack"><input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>"><input type="hidden" name="action" value="status"><input type="hidden" name="expected_status" value="<?php echo authEscape($order['status']); ?>"><div class="cx-form-grid"><div class="cx-field"><label for="status">Next status</label><select id="status" name="status" required><option value="">Choose a status</option><?php foreach ($transitions as $status): ?><option><?php echo authEscape($status); ?></option><?php endforeach; ?></select></div><div class="cx-field"><label for="note">Note visible to the customer (optional)</label><input id="note" name="note" maxlength="500"></div></div><?php if ($order['status'] === 'Awaiting payment'): ?><label class="cx-note"><input type="checkbox" name="payment_confirmed" value="1"> I confirm payment has been received (required for Ready for shipment).</label><?php endif; ?><button class="cx-button-secondary" type="submit">Update order status</button></form>
        <?php else: ?><p class="cx-intro cx-note">This order is <?php echo strtolower(authEscape($order['status'])); ?>. No further status changes are available.</p><?php endif; ?>
    </section>
    <?php require APP_ROOT . '/app/views/layouts/order-detail.php'; ?>
    <?php endif; ?>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
