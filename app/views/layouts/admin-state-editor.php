<?php if ($saveError): ?><div class="cx-alert cx-alert-error" role="alert"><?php echo authEscape($saveError); ?></div><?php endif; ?>
<?php if (isset($_GET['saved'])): ?><div class="cx-alert" role="status">Delivery rules saved. New checkouts use these requirements.</div><?php endif; ?>
<?php if ($editingState): $value = fn($key,$fallback) => authEscape($_SERVER['REQUEST_METHOD'] === 'POST' ? requestText($_POST,$key) : (string)$fallback); ?>
<section class="cx-panel" style="margin-bottom:28px"><div class="cx-panel-head"><h2>Edit <?php echo authEscape($editingState['name']); ?></h2><a class="cx-link" href="<?php echo siteUrl('admin-states.php'); ?>">Close</a></div>
    <form method="post" class="cx-stack"><input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>"><input type="hidden" name="code" value="<?php echo authEscape($editingState['code']); ?>"><div class="cx-form-grid">
        <div class="cx-field"><label for="enabled">Delivery status</label><select id="enabled" name="enabled"><option value="1" <?php echo $value('enabled',$editingState['enabled']) === '1' ? 'selected' : ''; ?>>Enabled</option><option value="0" <?php echo $value('enabled',$editingState['enabled']) === '0' ? 'selected' : ''; ?>>Disabled</option></select></div>
        <div class="cx-field"><label for="min-order">Minimum pallets</label><input id="min-order" name="min_order" type="number" min="1" max="999" value="<?php echo $value('min_order',$editingState['min_order']); ?>" required></div>
        <div class="cx-field"><label for="zone">Delivery zone</label><input id="zone" name="zone" maxlength="80" value="<?php echo $value('zone',$editingState['zone']); ?>" required></div>
        <div class="cx-field"><label for="restrict-zips">ZIP coverage</label><select id="restrict-zips" name="restrict_zips"><option value="1" <?php echo $value('restrict_zips',$editingState['restrict_zips']) === '1' ? 'selected' : ''; ?>>Listed ZIP codes only</option><option value="0" <?php echo $value('restrict_zips',$editingState['restrict_zips']) === '0' ? 'selected' : ''; ?>>All ZIP codes in this state</option></select></div>
        <div class="cx-field cx-field-wide"><label for="zips">Eligible ZIP codes</label><textarea id="zips" name="zips" rows="4"><?php echo $value('zips',implode(', ',$editingState['zips'])); ?></textarea><small>Separate 5-digit ZIP codes with commas or new lines.</small></div>
    </div><button class="lp-button lp-button-primary" type="submit">Save delivery rules &rarr;</button></form>
</section>
<?php endif; ?>
