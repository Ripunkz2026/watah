<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/admin-guard.php';
$isExpressPage = true;
$isAdminPage = true;
$adminSection = 'states';
$pageTitle = 'Delivery areas';
$isCustomerPage = true;
$saveError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verifyCsrf();
        $editCode = requestText($_POST,'code');
        updateDeliveryState($editCode, $_POST);
        header('Location: ' . siteUrl('admin-states.php?' . http_build_query(['edit'=>$editCode,'saved'=>1])));
        exit;
    } catch (InvalidArgumentException $error) { $saveError = $error->getMessage(); }
    catch (Throwable $error) { error_log('Delivery update: '.$error->getMessage()); $saveError = 'The delivery rules could not be saved. Please try again.'; }
}
$states = deliveryStates();
$editCode = requestText($_GET, 'edit') ?: requestText($_POST,'code');
$editingState = current(array_filter($states, fn($state) => $state['code'] === $editCode)) ?: null;
$zones = array_values(array_unique(array_column($states, 'zone')));
sort($zones);
$query = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$zone = is_string($_GET['zone'] ?? null) && in_array($_GET['zone'], $zones, true) ? $_GET['zone'] : '';
$filteredStates = array_filter($states, fn($state) => ($zone === '' || $state['zone'] === $zone) && ($query === '' || stripos($state['code'] . ' ' . $state['name'] . ' ' . implode(' ', $state['zips']), $query) !== false));
$zipCount = array_sum(array_map(fn($state) => count($state['zips']), $states));
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ad-page ad-list-page lp-wrap">
    <section class="ad-welcome" aria-labelledby="page-title">
        <div><p class="lp-eyebrow">YOUR DELIVERY FOOTPRINT</p><h1 id="page-title">Delivery,<br><span>clearly defined.</span></h1><p class="ad-intro">A clear view of the states, ZIP codes, and order minimums your storefront uses.</p></div>
        <a class="lp-button lp-button-primary" href="<?php echo siteUrl('service-area.php'); ?>">Check a destination <span aria-hidden="true">&nearr;</span></a>
    </section>
    <?php require APP_ROOT . '/app/views/layouts/admin-state-editor.php'; ?>
    <div class="ad-summary-grid" aria-label="Delivery area summary">
        <div><span class="ad-summary-icon" aria-hidden="true">01</span><div><strong><?php echo count($states); ?></strong><span>Configured states</span></div></div>
        <div><span class="ad-summary-icon" aria-hidden="true">02</span><div><strong><?php echo $zipCount; ?></strong><span>Listed ZIP codes</span></div></div>
        <div><span class="ad-summary-icon" aria-hidden="true">03</span><div><strong><?php echo count($zones); ?></strong><span>Delivery zones</span></div></div>
    </div>
    <section class="ad-catalog ad-list-panel" aria-labelledby="list-title">
        <div class="ad-panel-heading"><div><p class="lp-eyebrow">WHERE THE NEXT RESTOCK GOES</p><h2 id="list-title">States &amp; ZIP coverage</h2></div><span class="ad-count"><?php echo count($filteredStates); ?> of <?php echo count($states); ?> states</span></div>
        <form class="ad-filter-form" method="get" action="<?php echo siteUrl('admin-states.php'); ?>">
            <div class="ad-search-field"><label for="state-search">Find a service area</label><input id="state-search" type="search" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="State name, abbreviation, or ZIP"></div>
            <div><label for="delivery-zone">Delivery zone</label><select id="delivery-zone" name="zone"><option value="">All zones</option><?php foreach ($zones as $option): ?><option value="<?php echo htmlspecialchars($option); ?>"<?php echo $zone === $option ? ' selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option><?php endforeach; ?></select></div>
            <button class="ad-filter-button" type="submit">Apply filters</button><a class="ad-reset" href="<?php echo siteUrl('admin-states.php'); ?>">Reset</a>
        </form>
        <div class="ad-table-scroll" role="region" aria-labelledby="list-title" tabindex="0">
            <table class="ad-table ad-states-table"><caption class="ad-visually-hidden">Configured delivery eligibility by state</caption><thead><tr><th scope="col">State</th><th scope="col">Status</th><th scope="col">Minimum order</th><th scope="col">Zone</th><th scope="col">Eligible ZIP codes</th><th scope="col">Manage</th></tr></thead><tbody>
                <?php foreach ($filteredStates as $state): ?><tr>
                    <td><div class="ad-state-identity"><span><?php echo htmlspecialchars($state['code']); ?></span><strong><?php echo htmlspecialchars($state['name']); ?></strong></div></td><td><span class="ad-stock"><?php echo $state['enabled'] ? 'Enabled' : 'Disabled'; ?></span></td>
                    <td class="ad-product-price"><?php echo $state['min_order'] === null ? 'To be confirmed' : (int)$state['min_order'] . ' pallets'; ?></td><td class="ad-muted"><?php echo htmlspecialchars($state['zone']); ?></td>
                    <td><div class="ad-zip-list"><?php foreach (($state['restrict_zips'] ? $state['zips'] : []) as $zip): ?><span><?php echo htmlspecialchars($zip); ?></span><?php endforeach; ?><?php if (!$state['restrict_zips']): ?>All ZIP codes<?php endif; ?></div></td><td><a class="ad-row-link" href="<?php echo siteUrl('admin-states.php?edit=' . rawurlencode($state['code'])); ?>">Edit &rarr;</a></td>
                </tr><?php endforeach; ?>
            </tbody></table>
        </div>
        <?php if (!$filteredStates): ?><div class="ad-empty"><h3>No service areas found.</h3><p>Try another state, ZIP code, or delivery zone.</p><a class="lp-text-link" href="<?php echo siteUrl('admin-states.php'); ?>">Show all states <span aria-hidden="true">&rarr;</span></a></div><?php endif; ?>
        <p class="ad-catalog-note">Coverage shown here matches the delivery checker. Listed ZIP codes apply within each approved state.</p>
    </section>
    <div class="ad-context-note"><span aria-hidden="true">&nearr;</span><div><strong>A state match is only the first step.</strong><p>Confirm the destination ZIP code and minimum order before arranging freight.</p></div><a class="lp-text-link" href="<?php echo siteUrl('service-area.php'); ?>">Check eligibility <span aria-hidden="true">&rarr;</span></a></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
