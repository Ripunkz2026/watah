<?php
require_once APP_ROOT . '/app/bootstrap.php';
require APP_ROOT . '/app/auth/auth.php';
$customerDestination = ($_SESSION['after_login'] ?? '') === 'checkout.php' ? 'checkout.php' : 'account.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (!empty($_SESSION['admin']) || !empty($_SESSION['customer']))) {
    $destination = !empty($_SESSION['admin']) ? 'admin-index.php' : $customerDestination;
    header('Location: ' . siteUrl($destination));
    exit;
}
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = is_string($_POST['email'] ?? null) ? trim($_POST['email']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    if (!is_string($_POST['csrf_token'] ?? null) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Your session expired. Please try again.';
    } elseif ((!filter_var($email, FILTER_VALIDATE_EMAIL) && strtolower($email) !== 'admin') || $password === '' || strlen($email) > 254) {
        $error = 'Enter your username or email address and password.';
    } else {
        try {
            // Admin takes precedence only when its password also matches.
            foreach (['admin' => 'admins', 'customer' => 'customers'] as $sessionKey => $table) {
                $deliveryColumns = $sessionKey === 'customer' ? ', state, zip' : '';
                $statement = authDatabase()->prepare('SELECT id, name, company, email, password_hash' . $deliveryColumns . ' FROM ' . $table . ' WHERE email = ?');
                $statement->execute([strtolower($email)]);
                $account = $statement->fetch();
                if ($account && password_verify($password, $account['password_hash'])) {
                    session_regenerate_id(true);
                    unset($account['password_hash']);
                    unset($_SESSION['admin'], $_SESSION['customer'], $_SESSION['after_login']);
                    $_SESSION[$sessionKey] = $account;
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $destination = $sessionKey === 'admin' ? 'admin-index.php' : $customerDestination;
                    header('Location: ' . siteUrl($destination));
                    exit;
                }
            }
            $error = 'The username, email, or password is incorrect. Please try again.';
        } catch (Throwable $exception) {
            error_log('Account login: ' . $exception->getMessage());
            $error = 'Sign-in is temporarily unavailable. Please try again later.';
        }
    }
}
$isExpressPage = true;
$pageTitle = 'Sign in';
$pageDescription = 'Sign in to your Aqua Dash customer or administrator account.';
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="ep-page ep-login lp-wrap">
    <a class="ep-back" href="<?php echo siteUrl('index.php'); ?>"><span aria-hidden="true">&larr;</span> Back to home</a>
    <div class="ep-login-layout">
        <aside class="ep-login-story" aria-labelledby="story-title">
            <p class="lp-eyebrow">YOUR BUSINESS. WELL SUPPLIED.</p>
            <h2 id="story-title">Your next restock.<br><span>One sign-in away.</span></h2>
            <p>Good hydration starts with a little planning. Keep your business profile and your next pallet order in one place.</p>
            <div class="ep-login-image"><span class="ep-image-label">READY FOR YOUR NEXT BIG DAY</span><img src="<?php echo assetUrl('images/Aqua%20Dash%20Delivery%20Truck%20Landing%20Page%202.png'); ?>" width="1448" height="1086" alt="Aqua Dash delivery truck — fast and convenient" fetchpriority="high"><div><span>Familiar brands.</span><strong>Business-sized supply.</strong></div></div>
            <div class="ep-story-note"><span aria-hidden="true">&#10003;</span> Full &amp; half pallets <span aria-hidden="true">&middot;</span> Water &amp; sports drinks</div>
        </aside>
        <section class="ep-login-panel" aria-labelledby="login-title">
            <div class="ep-login-panel-inner">
                <p class="lp-eyebrow">WELCOME BACK</p>
                <h1 id="login-title">Good to see<br>you again.</h1>
                <p class="ep-intro">Sign in to your customer or administrator account.</p>
                <?php if (!empty($_SESSION['registration_success'])): ?><div class="ep-notice ep-success" role="status">Your account is ready. Sign in to get started.</div><?php unset($_SESSION['registration_success']); endif; ?>
                <?php if ($error): ?><div class="ep-notice ep-error" role="alert"><?php echo authEscape($error); ?></div><?php endif; ?>
                <form class="ep-form" method="post" action="<?php echo siteUrl('login.php'); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo authEscape($_SESSION['csrf_token']); ?>">
                    <div class="ep-field"><label for="email">Username or email address</label><input id="email" name="email" type="text" autocomplete="username" maxlength="254" placeholder="admin or you@company.com" value="<?php echo authEscape($email); ?>" required></div>
                    <div class="ep-field"><label for="password">Password</label><div class="ep-password-field"><input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required><button type="button" data-toggle-password="password" aria-controls="password" aria-pressed="false">Show</button></div></div>
                    <button class="lp-button lp-button-primary ep-submit" type="submit">Sign in <span aria-hidden="true">&rarr;</span></button>
                </form>
                <p class="ep-register-link">New to Aqua Dash?<br><a href="<?php echo siteUrl('register.php'); ?>">Create a business account <span aria-hidden="true">&nearr;</span></a></p>
                <div class="ep-login-help">Need a hand signing in? <a href="<?php echo siteUrl('contact.php'); ?>">Let&rsquo;s talk.</a></div>
            </div>
        </section>
    </div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>
