<?php
// Router for the isolated integration-test server. No application internals served.
if (PHP_SAPI !== 'cli-server' || !preg_match('/^watah_test_[a-f0-9]+$/', getenv('DB_NAME') ?: '')) { http_response_code(404); exit; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/watah/') $path = '/watah/index.php';
if (!preg_match('~^/watah/([a-z-]+\.php)$~', $path, $match)) { http_response_code(404); exit; }
$file = dirname(__DIR__) . '/' . $match[1];
if (!is_file($file)) { http_response_code(404); exit; }
require $file;
