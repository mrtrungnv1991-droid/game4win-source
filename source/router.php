<?php
/**
 * Router for PHP Built-in Development Server
 * Mô phỏng .htaccess rewrite rules
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$queryString = $_SERVER['QUERY_STRING'] ?? '';

// 1. Serve static files directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // Block sensitive files
    $blocked = ['.env', '.sql', '.sqlite', '.sqlite3', '.db', '.db3', '.DS_Store'];
    $ext = pathinfo($uri, PATHINFO_EXTENSION);
    $basename = basename($uri);
    $sensitive = ['.env', '.htaccess'];
    
    if (in_array($basename, $sensitive) || in_array('.' . $ext, ['.sql', '.sqlite', '.sqlite3', '.db', '.db3'])) {
        http_response_code(403);
        die('Forbidden');
    }
    
    // Serve static file (PHP files will be executed by built-in server)
    return false;
}

// 2. Route all other requests to index.php with proper query params
// Map friendly URLs to module/action

$uri = rtrim($uri, '/');

// REST API v1 — route to api/v1/index.php
if (preg_match('#^/api/v1#', $uri)) {
    require __DIR__ . '/api/v1/index.php';
    exit;
}

// CTV routes
if (preg_match('#^/ctv/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'ctv';
    $_GET['action'] = $m[1];
} elseif ($uri === '/ctv') {
    $_GET['module'] = 'ctv';
    $_GET['action'] = '';
}
// Admin routes
elseif (preg_match('#^/admin/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'admin';
    $_GET['action'] = $m[1];
} elseif ($uri === '/admin') {
    // Đồng bộ: /admin → trang chủ admin panel duy nhất (?module=admin&action=home)
    header('Location: /?module=admin&action=home');
    exit;
}
// ADCP routes
elseif (preg_match('#^/adcp/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'adcp';
    $_GET['action'] = $m[1];
}
// Client routes
// /client/login, /client/register → redirect về homepage (đăng nhập/đăng ký bằng modal trên trang chủ)
elseif ($uri === '/login' || $uri === '/Auth/Login' || $uri === '/Dashbroad' || $uri === '/client/login' || $uri === '/client/register' || $uri === '/register') {
    header('Location: /');
    exit;
} elseif (preg_match('#^/client/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = $m[1];
} elseif ($uri === '/client') {
    $_GET['module'] = 'client';
    $_GET['action'] = '';
}
// Blog
elseif (preg_match('#^/blog/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'blog';
    $_GET['slug'] = $m[1];
} elseif ($uri === '/blogs') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'blogs';
}
// Product
elseif (preg_match('#^/product/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'product';
    $_GET['slug'] = $m[1];
}
// Category
elseif (preg_match('#^/category/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'home';
    $_GET['slug'] = $m[1];
}
// Affiliate join
elseif (preg_match('#^/join/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'home';
    $_GET['aff'] = $m[1];
}
// Product order
elseif (preg_match('#^/product-order/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'product-order';
    $_GET['trans_id'] = $m[1];
} elseif ($uri === '/product-orders') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'product-orders';
}
// Document API
elseif ($uri === '/document-api') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'document-api';
}
// Tools
elseif ($uri === '/tool/random-face') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'tool-random-face';
} elseif ($uri === '/tool/icon-facebook') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'tool-icon-facebook';
} elseif ($uri === '/tool/get-2fa') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'tool-2fa';
} elseif ($uri === '/tool/check-live-facebook') {
    $_GET['module'] = 'client';
    $_GET['action'] = 'tool-checklive-fb';
}
// API routes
elseif ($uri === '/api/buy_product') {
    require __DIR__ . '/ajaxs/client/product.php';
    return true;
}
// Language switch
elseif (preg_match('#^/([a-z]{2,5})/?$#', $uri, $m)) {
    require __DIR__ . '/views/client/set-language.php';
    return true;
}
// Recharge manual
elseif (preg_match('#^/recharge-manual/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'recharge-manual';
    $_GET['slug'] = $m[1];
}
// Payment
elseif (preg_match('#^/payment/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'payment';
    $_GET['trans_id'] = $m[1];
}
// Common
elseif (preg_match('#^/common/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'common';
    $_GET['action'] = $m[1];
}
// Client home with shop
elseif (preg_match('#^/client/home/([A-Za-z0-9-]+)$#', $uri, $m)) {
    $_GET['module'] = 'client';
    $_GET['action'] = 'home';
    $_GET['shop'] = $m[1];
}
// Verify google login
elseif ($uri === '/verify-google-login') {
    require __DIR__ . '/api/callback_google_login.php';
    return true;
}
// Default: pass through existing query params to index.php
else {
    // Keep existing GET params if already set via query string
}

// Route to main index.php
require __DIR__ . '/index.php';
