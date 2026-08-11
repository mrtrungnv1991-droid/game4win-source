<?php
/**
 * Cron: Dynamic Pricing — chạy mỗi 12h
 * Tự động điều chỉnh giá theo competitor
 */
define("IN_SITE", true);
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../libs/db.php');
require_once(__DIR__ . '/../libs/lang.php');
require_once(__DIR__ . '/../libs/helper.php');
require_once(__DIR__ . '/../libs/dynamic_pricing.php');

$CMSNT = new DB();
$dp = new DynamicPricing($CMSNT);

echo "=== Dynamic Pricing Cron ===\n";

$result = $dp->runEngine(false);
echo json_encode([
    'status' => $result['status'],
    'adjusted' => $result['adjusted'] ?? 0,
    'skipped' => $result['skipped'] ?? 0,
], JSON_UNESCAPED_UNICODE) . "\n";

if (!empty($result['changes'])) {
    foreach ($result['changes'] as $c) {
        echo "  {$c['product']}: {$c['old_price']} -> {$c['new_price']} (competitor: {$c['competitor_price']})\n";
    }
}

$CMSNT->insert('logs', [
    'user_id' => 0,
    'ip' => '127.0.0.1',
    'device' => 'Cron',
    'createdate' => date('Y-m-d H:i:s'),
    'action' => "💲 Dynamic pricing: adjusted=" . ($result['adjusted'] ?? 0),
]);

echo "Done.\n";
