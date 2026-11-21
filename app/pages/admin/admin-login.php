<?php
// Preserve old bookmarks and form submissions through the unified login route.
header('Location: ' . siteUrl('login.php'), true, $_SERVER['REQUEST_METHOD'] === 'POST' ? 307 : 302);
exit;
