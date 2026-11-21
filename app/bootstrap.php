<?php
// Every public entry point loads this file before handling a request.
define('APP_ROOT', dirname(__DIR__));
session_start();

require_once APP_ROOT . '/config/app.php';
require_once APP_ROOT . '/app/support/helpers.php';
require_once APP_ROOT . '/app/data/store.php';
try {
    require APP_ROOT . '/config/shipping.php';
} catch (Throwable $error) {
    error_log('Database initialization: ' . $error->getMessage());
    http_response_code(503);
    exit('The store is temporarily unavailable. Please try again shortly.');
}
