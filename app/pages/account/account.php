<?php
require_once APP_ROOT . '/app/bootstrap.php';
require_once APP_ROOT . '/app/auth/auth.php';
requireCustomer();
$customer = $_SESSION['customer'];
$orders = customerOrders((int)$customer['id']);
$isDemoAccount = false;
require APP_ROOT . '/app/views/layouts/customer-account.php';