<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/admin-guard.php';
$isExpressPage = true;
$isAdminPage = true;
$adminSection = 'orders';
$pageTitle = 'Orders';
$orders = allOrders();
$statusStyles = ['Pending freight quote'=>'ad-stock-pending','Awaiting payment'=>'ad-stock-pending','Ready for shipment'=>'ad-stock-neutral','Shipped'=>'ad-stock-neutral','Delivered'=>'','Cancelled'=>'ad-stock-pending'];
$query = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$status = is_string($_GET['status'] ?? null) && array_key_exists($_GET['status'], $statusStyles) ? $_GET['status'] : '';
$filteredOrders = array_filter($orders, fn($order) => ($status === '' || $order['status'] === $status) && ($query === '' || stripos($order['id'] . ' ' . $order['customer'] . ' ' . $order['state'], $query) !== false));
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="orders.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'Customer', 'State', 'Status', 'Total (USD)']);
    foreach ($filteredOrders as $order) {
        $cells = [$order['id'], $order['customer'], $order['state'], $order['status'], number_format($order['total'], 2, '.', '')];
        fputcsv($output, array_map(fn($value) => preg_match('/^[\s]*[=+@-]/', $value) ? "'" . $value : $value, $cells));
    }
    fclose($output);
    exit;
}
$exportUrl = siteUrl('admin-orders.php?' . http_build_query(['q' => $query, 'status' => $status, 'export' => 'csv']));
$activeCount = count(array_filter($orders, fn($order) => !in_array($order['status'], ['Delivered','Cancelled'], true)));
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ad-page ad-list-page lp-wrap">
    <section class="ad-welcome" aria-labelledby="page-title">
        <div><p class="lp-eyebrow">FROM ORDER TO DELIVERY</p><h1 id="page-title">Every order.<br><span>In view.</span></h1><p class="ad-intro">Review customers, destinations, and order progress in one organized workspace.</p></div>
        <a class="lp-button lp-button-primary" href="<?php echo htmlspecialchars($exportUrl); ?>">Export CSV <span aria-hidden="true">&darr;</span></a>
    </section>
    <div class="ad-summary-grid" aria-label="Order summary">
        <div><span class="ad-summary-icon" aria-hidden="true">01</span><div><strong><?php echo count($orders); ?></strong><span>Orders</span></div></div>
        <div><span class="ad-summary-icon" aria-hidden="true">02</span><div><strong><?php echo $activeCount; ?></strong><span>Active orders</span></div></div>
        <div><span class="ad-summary-icon" aria-hidden="true">03</span><div><strong><?php echo formatPrice(array_sum(array_column($orders, 'total'))); ?></strong><span>Combined order value</span></div></div>
    </div>
    <section class="ad-catalog ad-list-panel" aria-labelledby="list-title">
        <div class="ad-panel-heading"><div><p class="lp-eyebrow">KEEP THE NEXT DELIVERY IN VIEW</p><h2 id="list-title">Order overview</h2></div><span class="ad-count"><?php echo count($filteredOrders); ?> of <?php echo count($orders); ?> orders</span></div>
        <form class="ad-filter-form" method="get" action="<?php echo siteUrl('admin-orders.php'); ?>">
            <div class="ad-search-field"><label for="order-search">Find an order</label><input id="order-search" type="search" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Order ID, customer, or state"></div>
            <div><label for="order-status">Order status</label><select id="order-status" name="status"><option value="">All statuses</option><?php foreach ($statusStyles as $option => $style): ?><option value="<?php echo htmlspecialchars($option); ?>"<?php echo $status === $option ? ' selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option><?php endforeach; ?></select></div>
            <button class="ad-filter-button" type="submit">Apply filters</button><a class="ad-reset" href="<?php echo siteUrl('admin-orders.php'); ?>">Reset</a>
        </form>
        <div class="ad-table-scroll" role="region" aria-labelledby="list-title" tabindex="0">
            <table class="ad-table ad-orders-table"><caption class="ad-visually-hidden">Orders matching the selected filters</caption><thead><tr><th scope="col">Order</th><th scope="col">Customer</th><th scope="col">Destination</th><th scope="col">Status</th><th scope="col">Total</th></tr></thead><tbody>
                <?php foreach ($filteredOrders as $order): ?><tr><td><a class="ad-row-link" href="<?php echo siteUrl('admin-order.php?order=' . rawurlencode($order['id'])); ?>"><?php echo htmlspecialchars($order['id']); ?> &rarr;</a><small class="ad-cell-note"><?php echo htmlspecialchars($order['date']); ?> UTC</small></td><td class="ad-customer-name"><?php echo htmlspecialchars($order['customer']); ?></td><td><span class="ad-destination"><?php echo htmlspecialchars($order['state']); ?></span></td><td><span class="ad-stock <?php echo $statusStyles[$order['status']]; ?>"><?php echo htmlspecialchars($order['status']); ?></span></td><td class="ad-product-price"><?php echo formatPrice($order['total']); ?></td></tr><?php endforeach; ?>
            </tbody></table>
        </div>
        <?php if (!$filteredOrders): ?><div class="ad-empty"><h3>No orders match your search.</h3><p>Try another customer, order ID, or status.</p><a class="lp-text-link" href="<?php echo siteUrl('admin-orders.php'); ?>">Show all orders <span aria-hidden="true">&rarr;</span></a></div><?php endif; ?>
        <div class="ad-table-footer"><span>Showing <?php echo count($filteredOrders); ?> orders</span><span>Displayed total <strong><?php echo formatPrice(array_sum(array_column($filteredOrders, 'total'))); ?></strong></span></div>
    </section>
    <div class="ad-context-note"><span aria-hidden="true">&nearr;</span><div><strong>Know the destination before arranging delivery.</strong><p>Review approved states and ZIP codes for the next order.</p></div><a class="lp-text-link" href="<?php echo siteUrl('admin-states.php'); ?>">Delivery areas <span aria-hidden="true">&rarr;</span></a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
