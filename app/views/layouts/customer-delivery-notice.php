<?php
$deliveryState = (string)($_SESSION['customer']['state'] ?? '');
$deliveryZip = (string)($_SESSION['customer']['zip'] ?? '');
$deliveryStatus = $deliveryState !== '' && $deliveryZip !== '' ? isShippingAllowed($deliveryState, $deliveryZip) : null;
?>
<div class="cx-alert<?php echo $deliveryStatus && !$deliveryStatus['allowed'] ? ' cx-alert-error' : ''; ?>" role="status">
    <?php if ($deliveryStatus): ?>
        <strong><?php echo $deliveryStatus['allowed'] ? 'Delivery is available' : 'Delivery is not currently available'; ?> for <?php echo authEscape($deliveryState . ' ' . $deliveryZip); ?>.</strong>
        <?php if ($deliveryStatus['allowed']): ?>
            Minimum order: <?php echo (int)$deliveryStatus['rule']['minOrder']; ?> pallet<?php echo (int)$deliveryStatus['rule']['minOrder'] === 1 ? '' : 's'; ?>. Freight is confirmed after review.
        <?php else: ?>
            <a class="cx-link" href="<?php echo siteUrl('contact.php'); ?>">Contact us about your delivery area.</a>
        <?php endif; ?>
    <?php else: ?>
        No delivery ZIP code is saved for your account. <a class="cx-link" href="<?php echo siteUrl('service-area.php'); ?>">Check delivery availability.</a>
    <?php endif; ?>
</div>
