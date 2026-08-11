<?php
/**
 * Digital Commerce OS — REST API v1
 * Endpoints chuẩn cho bot/supplier gọi: Account / Key / Giftcard
 * 
 * Auth: Header "X-API-Key: <key>" (tạo trong Admin → API Keys)
 * 
 * GET  /api/v1/              — API info
 * GET  /api/v1/products      — Danh sách sản phẩm (filter: category, search, limit)
 * GET  /api/v1/products/CODE — Chi tiết sản phẩm + stock
 * POST /api/v1/order         — Mua hàng {product_code, amount} → trả key
 * GET  /api/v1/orders        — Lịch sử đơn của API key
 * GET  /api/v1/balance       — Số dư user sở hữu API key
 */
define("IN_SITE", true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../libs/db.php');
require_once(__DIR__ . '/../../libs/lang.php');
require_once(__DIR__ . '/../../libs/helper.php');

$CMSNT = new DB();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

function api_response($data, $code = 200) {
    http_response_code($code);
    die(json_encode($data, JSON_UNESCAPED_UNICODE));
}

// ===== AUTH =====
$api_key_header = $_SERVER['HTTP_X_API_KEY'] ?? ($_GET['api_key'] ?? '');
$api_key_row = null;
if ($api_key_header) {
    $api_key_row = $CMSNT->get_row_safe(
        "SELECT * FROM api_keys WHERE api_key = ? AND status = 1", [$api_key_header]
    );
}

// Parse route: /api/v1/xxx
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = preg_replace('#^/api/v1/?#', '', $path);
$segments = array_values(array_filter(explode('/', $path)));
$endpoint = $segments[0] ?? '';

// ===== PUBLIC: API INFO =====
if ($endpoint === '') {
    api_response([
        'status' => 'success',
        'name' => 'Digital Commerce OS API',
        'version' => 'v1',
        'endpoints' => [
            'GET /api/v1/products' => 'Danh sách sản phẩm (category, search, limit)',
            'GET /api/v1/products/{code}' => 'Chi tiết sản phẩm + tồn kho',
            'POST /api/v1/order' => 'Mua hàng: {product_code, amount}',
            'GET /api/v1/orders' => 'Lịch sử đơn hàng',
            'GET /api/v1/balance' => 'Số dư tài khoản',
        ],
        'auth' => 'Header X-API-Key (tạo tại Admin → API Keys)',
    ]);
}

// Tất cả endpoint còn lại cần auth
if (!$api_key_row) {
    api_response(['status' => 'error', 'msg' => 'Missing or invalid API key. Pass header X-API-Key.'], 401);
}

// Rate limit
if ($api_key_row['requests_today'] >= $api_key_row['rate_limit']) {
    api_response(['status' => 'error', 'msg' => 'Rate limit exceeded (' . $api_key_row['rate_limit'] . '/day)'], 429);
}
$CMSNT->cong('api_keys', 'requests_today', 1, " `id` = ? ", [$api_key_row['id']]);
$CMSNT->update('api_keys', ['last_used' => date('Y-m-d H:i:s')], " `id` = " . intval($api_key_row['id']));

$owner = $CMSNT->get_row_safe("SELECT * FROM users WHERE id = ?", [$api_key_row['user_id']]);
if (!$owner) api_response(['status' => 'error', 'msg' => 'API key owner not found'], 401);

function log_api($api_key_id, $endpoint, $method, $params, $code) {
    global $CMSNT;
    $CMSNT->insert('api_logs', [
        'api_key_id' => $api_key_id,
        'endpoint' => $endpoint,
        'method' => $method,
        'params' => json_encode($params, JSON_UNESCAPED_UNICODE),
        'response_code' => $code,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
}

// ===== GET /products =====
if ($endpoint === 'products' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = min(100, max(1, intval($_GET['limit'] ?? 20)));

    $where = "p.status = 1 AND p.hide_in_shop = 0";
    $params = [];
    if ($category) {
        $where .= " AND c.slug = ?";
        $params[] = $category;
    }
    if ($search) {
        $where .= " AND p.name LIKE ?";
        $params[] = "%$search%";
    }

    $rows = $CMSNT->get_list_safe(
        "SELECT p.id, p.code, p.name, p.slug, p.price, p.discount, p.short_desc, p.sold,
                c.name as category, c.slug as category_slug
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE $where
         ORDER BY p.id DESC LIMIT $limit", $params);

    $out = [];
    foreach ($rows as $r) {
        $stock = getStock($r['code']);
        $out[] = [
            'code' => $r['code'],
            'name' => $r['name'],
            'category' => $r['category'],
            'price' => (int)($r['price'] - $r['price'] * $r['discount'] / 100),
            'original_price' => (int)$r['price'],
            'discount_percent' => (float)$r['discount'],
            'stock' => (int)$stock,
            'sold' => (int)$r['sold'],
            'in_stock' => $stock > 0,
        ];
    }
    log_api($api_key_row['id'], 'products', 'GET', $_GET, 200);
    api_response(['status' => 'success', 'total' => count($out), 'products' => $out]);
}

// ===== GET /products/{code} =====
if ($endpoint === 'products' && isset($segments[1])) {
    $code = check_string($segments[1]);
    $p = $CMSNT->get_row_safe(
        "SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.code = ? AND p.status = 1", [$code]);
    if (!$p) api_response(['status' => 'error', 'msg' => 'Product not found'], 404);

    $stock_items = $CMSNT->get_list_safe(
        "SELECT COUNT(*) as cnt FROM product_stock WHERE product_code = ?", [$code]);
    $stock = (int)($stock_items[0]['cnt'] ?? 0);

    log_api($api_key_row['id'], 'products/' . $code, 'GET', [], 200);
    api_response(['status' => 'success', 'product' => [
        'code' => $p['code'],
        'name' => $p['name'],
        'category' => $p['category'],
        'description' => $p['short_desc'],
        'price' => (int)($p['price'] - $p['price'] * $p['discount'] / 100),
        'stock' => $stock,
        'sold' => (int)$p['sold'],
        'min_buy' => (int)$p['min'],
        'max_buy' => (int)$p['max'],
    ]]);
}

// ===== POST /order =====
if ($endpoint === 'order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $code = check_string($input['product_code'] ?? '');
    $amount = max(1, min(10, intval($input['amount'] ?? 1)));

    if (!$code) api_response(['status' => 'error', 'msg' => 'product_code required'], 400);

    $p = $CMSNT->get_row_safe("SELECT * FROM products WHERE code = ? AND status = 1", [$code]);
    if (!$p) api_response(['status' => 'error', 'msg' => 'Product not found'], 404);

    $price = $p['price'] - $p['price'] * $p['discount'] / 100;
    $total = $price * $amount;

    // Check stock
    $stock_rows = $CMSNT->get_list_safe(
        "SELECT * FROM product_stock WHERE product_code = ? LIMIT $amount", [$code]);
    if (count($stock_rows) < $amount) {
        api_response(['status' => 'error', 'msg' => 'Insufficient stock. Available: ' . count($stock_rows)], 400);
    }

    // Check balance
    if ($owner['money'] < $total) {
        api_response(['status' => 'error', 'msg' => 'Insufficient balance. Need ' . number_format($total) . 'đ, have ' . number_format($owner['money']) . 'đ'], 400);
    }

    // Deduct balance
    $CMSNT->update('users', ['money' => $owner['money'] - $total], " `id` = " . intval($owner['id']));
    $CMSNT->insert('dongtien', [
        'user_id' => $owner['id'],
        'sotientruoc' => $owner['money'],
        'sotienthaydoi' => -$total,
        'sotiensau' => $owner['money'] - $total,
        'thoigian' => date('Y-m-d H:i:s'),
        'noidung' => "API order: $code x$amount",
    ]);

    // Deliver keys + remove from stock
    $trans_id = 'API-' . random('QWERTYUOPASDFGHJKZXCVBNM123456789', 4) . uniqid();
    $delivered_keys = [];
    foreach ($stock_rows as $item) {
        $delivered_keys[] = $item['account'];
        $CMSNT->remove('product_stock', " `id` = " . intval($item['id']));
    }

    // Record order
    $CMSNT->insert('product_order', [
        'trans_id' => $trans_id,
        'product_id' => $p['id'],
        'supplier_id' => 0,
        'product_name' => $p['name'],
        'buyer' => $owner['id'],
        'seller' => $p['user_id'],
        'amount' => $amount,
        'money' => $total,
        'pay' => $total,
        'cost' => $p['cost'] * $amount,
        'commission_fee' => 0,
        'create_gettime' => date('Y-m-d H:i:s'),
        'update_gettime' => date('Y-m-d H:i:s'),
        'trash' => 0, 'refund' => 0,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'device' => 'API/' . $api_key_row['name'],
        'status_view_order' => 1,
        'note' => 'REST API order',
    ]);
    $CMSNT->cong('products', 'sold', $amount, " `id` = ? ", [$p['id']]);

    log_api($api_key_row['id'], 'order', 'POST', ['product_code' => $code, 'amount' => $amount], 200);
    api_response(['status' => 'success', 'msg' => 'Order completed', 'order' => [
        'trans_id' => $trans_id,
        'product' => $p['name'],
        'amount' => $amount,
        'total_paid' => (int)$total,
        'keys' => $delivered_keys,
    ]]);
}

// ===== GET /orders =====
if ($endpoint === 'orders') {
    $rows = $CMSNT->get_list_safe(
        "SELECT trans_id, product_name, amount, pay, create_gettime, note
         FROM product_order WHERE buyer = ? AND device LIKE 'API/%'
         ORDER BY id DESC LIMIT 50", [$owner['id']]);
    log_api($api_key_row['id'], 'orders', 'GET', [], 200);
    api_response(['status' => 'success', 'total' => count($rows), 'orders' => $rows]);
}

// ===== GET /balance =====
if ($endpoint === 'balance') {
    $fresh = $CMSNT->get_row_safe("SELECT money FROM users WHERE id = ?", [$owner['id']]);
    log_api($api_key_row['id'], 'balance', 'GET', [], 200);
    api_response(['status' => 'success', 'balance' => (int)$fresh['money'], 'currency' => 'VND']);
}

api_response(['status' => 'error', 'msg' => 'Unknown endpoint: ' . $endpoint], 404);
