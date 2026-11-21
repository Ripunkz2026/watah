<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/auth.php';
$adminMode = !empty($adminMode);
$registerPath = $adminMode ? 'admin-register.php' : 'register.php';
$loginPath = 'login.php';
if ($adminMode) {
    requireAdmin();
}
if (!$adminMode && !empty($_SESSION['customer'])) {
    header('Location: ' . siteUrl('account.php'));
    exit;
}
$errors = [];
$values = ['name' => '', 'company' => '', 'email' => ''];
$registrationStates = require APP_ROOT . '/config/us-states.php';
if (!$adminMode) {
    $values['state'] = '';
    $values['zip'] = '';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $value) {
        $values[$key] = requestText($_POST, $key);
    }
    $password = (string)($_POST['password'] ?? '');
    if (!is_string($_POST['csrf_token'] ?? null) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Your session expired. Please try again.';
    }
    if ($values['name'] === '' || strlen($values['name']) > 120) $errors[] = 'Enter your full name (up to 120 characters).';
    if ($values['company'] === '' || strlen($values['company']) > 160) $errors[] = 'Enter your business name (up to 160 characters).';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 254) $errors[] = 'Enter a valid email address.';
    if (!$adminMode) {
        $values['state'] = strtoupper($values['state']);
        if (!isset($registrationStates[$values['state']])) $errors[] = 'Select your delivery state.';
        if (!preg_match('/^[0-9]{5}$/', $values['zip'])) $errors[] = 'Enter a valid 5-digit delivery ZIP code.';
    }
    if (strlen($password) < 12 || strlen($password) > 72) $errors[] = 'Use a password between 12 and 72 bytes long.';
    if ($password !== (string)($_POST['password_confirmation'] ?? '')) $errors[] = 'Your passwords do not match.';
    if (empty($_POST['terms'])) $errors[] = 'Please accept the terms and privacy policy.';
    if (!$errors) {
        try {
            $pdo = authDatabase();
            $table = $adminMode ? 'admins' : 'customers';
            $columns = $adminMode ? '' : ', state, zip';
            $placeholders = $adminMode ? '' : ', ?, ?';
            $parameters = [$values['name'], $values['company'], strtolower($values['email']), password_hash($password, PASSWORD_DEFAULT)];
            if (!$adminMode) {
                $parameters[] = $values['state'];
                $parameters[] = $values['zip'];
            }
            $statement = $pdo->prepare('INSERT INTO ' . $table . ' (name, company, email, password_hash' . $columns . ') VALUES (?, ?, ?, ?' . $placeholders . ')');
            $statement->execute($parameters);
            $_SESSION[$adminMode ? 'admin_registration_success' : 'registration_success'] = true;
            header('Location: ' . siteUrl($adminMode ? 'admin-index.php' : $loginPath));
            exit;
        } catch (PDOException $exception) {
            $errors[] = $exception->getCode() === '23000' ? 'An account already uses this email. Please sign in.' : 'Registration is temporarily unavailable. Please try again later.';
            error_log('Customer registration: ' . $exception->getMessage());
        } catch (Throwable $exception) {
            $errors[] = 'Registration is temporarily unavailable. Please try again later.';
            error_log('Customer registration: ' . $exception->getMessage());
        }
    }
}
$isExpressPage = true;
$isAdminPage = $adminMode;
$pageTitle = $adminMode ? 'Create administrator account' : 'Create your business account';
$pageDescription = $adminMode ? 'Create an administrator account for Aqua Dash.' : 'Create your Aqua Dash business account for your next water and sports drink restock.';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ep-page ep-register lp-wrap">
    <a class="ep-back" href="<?php echo siteUrl($adminMode && !empty($_SESSION['admin']) ? 'admin-index.php' : 'index.php'); ?>"><span aria-hidden="true">&larr;</span> <?php echo $adminMode && !empty($_SESSION['admin']) ? 'Back to your workspace' : 'Back to home'; ?></a>
    <div class="ep-login-layout ep-register-layout">
        <aside class="ep-login-story ep-register-story" aria-labelledby="story-title">
            <p class="lp-eyebrow"><?php echo $adminMode ? 'YOUR TEAM. WELL CONNECTED.' : 'YOUR BUSINESS. WELL SUPPLIED.'; ?></p>
            <h2 id="story-title"><?php echo $adminMode ? 'Your team.<br><span>One shared workspace.</span>' : 'Your next restock.<br><span>Starts here.</span>'; ?></h2>
            <p><?php echo $adminMode ? 'Bring your team together to manage the catalog, review orders, and keep delivery details in view.' : 'Familiar brands. Business-sized supply. Get your business ready for its next delivery of water and sports drinks.'; ?></p>
            <div class="ep-login-image ep-register-image">
                <span class="ep-image-label">GOOD THINGS COME BY THE PALLET</span>
                <img src="<?php echo assetUrl('images/products/3.jpg'); ?>" alt="Pure Life bottled water full pallet" width="660" height="660" fetchpriority="high">
                <div><span>Everyday hydration.</span><strong>Ready for your next big day.</strong></div>
            </div>
            <ul class="ep-register-benefits">
                <?php if ($adminMode): ?>
                    <li><span aria-hidden="true">&#10003;</span> Product and inventory overview</li>
                    <li><span aria-hidden="true">&#10003;</span> Orders and delivery areas in one place</li>
                    <li><span aria-hidden="true">&#10003;</span> One sign-in for customer and administrator accounts</li>
                <?php else: ?>
                    <li><span aria-hidden="true">&#10003;</span> Full &amp; half pallet options</li>
                    <li><span aria-hidden="true">&#10003;</span> Water and sports drinks for your team</li>
                    <li><span aria-hidden="true">&#10003;</span> Check delivery availability by ZIP code</li>
                <?php endif; ?>
            </ul>
            <p class="ep-register-story-note">HYDRATION, IN GOOD SUPPLY.</p>
        </aside>
        <section class="ep-login-panel ep-register-panel" aria-labelledby="register-title">
            <div class="ep-login-panel-inner">
                <p class="lp-eyebrow"><?php echo $adminMode ? 'GROW YOUR TEAM' : 'LET’S GET STARTED'; ?></p>
                <h1 id="register-title"><?php echo $adminMode ? 'Create an<br>admin account.' : 'Create your<br>business account.'; ?></h1>
                <p class="ep-intro"><?php echo $adminMode ? 'Set up a new administrator with their own sign-in details.' : 'A few details now. Easier restocks ahead.'; ?></p>
                <?php if ($errors): ?><div class="ep-notice ep-error ep-register-errors" role="alert"><strong>Check your details and try again.</strong><ul><?php foreach ($errors as $error): ?><li><?php echo authEscape($error); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
                <?php if ($adminMode): ?><div class="ep-notice ep-register-admin-note">Only signed-in administrators can create administrator accounts.</div><?php endif; ?>
                <form class="ep-form ep-register-form" method="post" action="<?php echo siteUrl($registerPath); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>">
                    <div class="ep-field-row">
                        <div class="ep-field"><label for="name">Full name</label><input id="name" name="name" type="text" autocomplete="name" maxlength="120" placeholder="Alex Morgan" value="<?php echo authEscape($values['name']); ?>" required></div>
                        <div class="ep-field"><label for="company">Business name</label><input id="company" name="company" type="text" autocomplete="organization" maxlength="160" placeholder="Your company" value="<?php echo authEscape($values['company']); ?>" required></div>
                    </div>
                    <div class="ep-field"><label for="email">Email address</label><input id="email" name="email" type="email" autocomplete="username" maxlength="254" placeholder="you@company.com" value="<?php echo authEscape($values['email']); ?>" required></div>
                    <?php if (!$adminMode): ?>
                    <div class="ep-field-row">
                        <div class="ep-field"><label for="state">Delivery state</label><select id="state" name="state" autocomplete="address-level1" aria-describedby="delivery-help" required><option value="">Select your state</option><?php foreach ($registrationStates as $code => $stateName): ?><option value="<?php echo $code; ?>"<?php echo $values['state'] === $code ? ' selected' : ''; ?>><?php echo authEscape($stateName); ?></option><?php endforeach; ?></select></div>
                        <div class="ep-field"><label for="zip">Delivery ZIP code</label><input id="zip" name="zip" type="text" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{5}" maxlength="5" placeholder="5-digit ZIP code" value="<?php echo authEscape($values['zip']); ?>" aria-describedby="delivery-help" required></div>
                    </div>
                    <p id="delivery-help" class="ep-password-help">We&rsquo;ll show delivery availability for your state and ZIP code after you sign in. You can register even if delivery isn&rsquo;t available yet.</p>
                    <?php endif; ?>
                    <div class="ep-field">
                        <label for="password">Password</label><div class="ep-password-field"><input id="password" name="password" type="password" minlength="12" maxlength="72" autocomplete="new-password" aria-describedby="password-help" placeholder="Create a password" required><button type="button" data-toggle-password="password" aria-controls="password" aria-pressed="false">Show</button></div>
                        <p id="password-help" class="ep-password-help">Use at least 12 characters. A longer phrase works well.</p>
                    </div>
                    <div class="ep-field"><label for="password-confirmation">Confirm password</label><div class="ep-password-field"><input id="password-confirmation" name="password_confirmation" type="password" minlength="12" maxlength="72" autocomplete="new-password" placeholder="Enter your password again" required><button type="button" data-toggle-password="password-confirmation" aria-controls="password-confirmation" aria-pressed="false">Show</button></div></div>
                    <div class="ep-terms-check"><input id="terms" type="checkbox" name="terms" value="1"<?php echo !empty($_POST['terms']) ? ' checked' : ''; ?> required><label for="terms">I agree to the <a href="<?php echo siteUrl('terms.php'); ?>">Terms</a> and <a href="<?php echo siteUrl('privacy-policy.php'); ?>">Privacy Policy</a>.</label></div>
                    <button class="lp-button lp-button-primary ep-submit" type="submit"><?php echo $adminMode ? 'Create administrator account' : 'Create business account'; ?> <span aria-hidden="true">&rarr;</span></button>
                </form>
                <?php if (!$adminMode): ?><p class="ep-register-link">Already have an account? <a href="<?php echo siteUrl($loginPath); ?>">Sign in <span aria-hidden="true">&nearr;</span></a></p><?php endif; ?>
                <div class="ep-login-help">Need a hand getting started? <a href="<?php echo siteUrl('contact.php'); ?>">Let&rsquo;s talk.</a></div>
            </div>
        </section>
    </div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
