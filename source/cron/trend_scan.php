<?php
/**
 * Cron: Trend Scan — chạy mỗi 6h
 * Quét Reddit + Google Trends → match competitor products
 */
define("IN_SITE", true);
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../libs/db.php');
require_once(__DIR__ . '/../libs/lang.php');
require_once(__DIR__ . '/../libs/helper.php');
require_once(__DIR__ . '/../libs/trend_detection.php');

$CMSNT = new DB();
$td = new TrendDetection($CMSNT);

echo "=== Trend Scan Cron ===\n";

$r1 = $td->scanReddit(25);
echo "Reddit: " . json_encode($r1) . "\n";

$r2 = $td->scanGoogleTrends();
echo "Google Trends: " . json_encode($r2) . "\n";

$r3 = $td->matchTrendsToProducts();
echo "Match: " . json_encode($r3) . "\n";

// Log
$CMSNT->insert('logs', [
    'user_id' => 0,
    'ip' => '127.0.0.1',
    'device' => 'Cron',
    'createdate' => date('Y-m-d H:i:s'),
    'action' => "📈 Trend scan: reddit={$r1['scanned']}, gtrends={$r2['scanned']}, matched={$r3['matched']}",
]);

echo "Done.\n";
