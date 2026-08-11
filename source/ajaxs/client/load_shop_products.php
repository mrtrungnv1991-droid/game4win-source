<?php
/**
 * API: Load digital products (Account/Key/GiftCard) cho homepage
 * Endpoint: /ajaxs/client/load_shop_products.php
 * Trả JSON theo phân nhánh: account / game_key / gift_card
 */
define("IN_SITE", true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../libs/db.php');
require_once(__DIR__ . '/../../libs/lang.php');
require_once(__DIR__ . '/../../libs/helper.php');

$CMSNT = new DB();
header('Content-Type: application/json; charset=utf-8');

$products = $CMSNT->get_list_safe(
    "SELECT p.id, p.code, p.name, p.slug, p.price, p.discount, p.product_type, p.platform, p.region,
            p.short_desc, p.sold, p.images, c.name as category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.status = 1 AND p.hide_in_shop = 0
       AND p.product_type IN ('account','game_key','gift_card','software','subscription')
     ORDER BY p.product_type, p.id DESC", []);

$out = ['account' => [], 'game_key' => [], 'gift_card' => [], 'software' => [], 'subscription' => []];

foreach ($products as $p) {
    $price = (int)($p['price'] - $p['price'] * $p['discount'] / 100);
    $stock = getStock($p['code']);
    $type = $p['product_type'] ?? 'account';

    $item = [
        'id' => (int)$p['id'],
        'code' => $p['code'],
        'name' => $p['name'],
        'slug' => $p['slug'],
        'price' => $price,
        'original_price' => (int)$p['price'],
        'discount' => (float)$p['discount'],
        'type' => $type,
        'platform' => $p['platform'] ?: '',
        'region' => $p['region'] ?: '',
        'category' => $p['category_name'] ?: '',
        'stock' => (int)$stock,
        'sold' => (int)$p['sold'],
        'in_stock' => $stock > 0,
        'image' => $p['images'] ? explode(',', $p['images'])[0] : '',
    ];

    if (isset($out[$type])) {
        $out[$type][] = $item;
    }
}

echo json_encode([
    'status' => 'success',
    'total' => count($products),
    'products' => $out,
], JSON_UNESCAPED_UNICODE);
