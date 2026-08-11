<?php
/**
 * Cron: Auto-fulfill Group Buy Deals
 * Run every 5 minutes: checks for filled deals and delivers keys
 */
define("IN_SITE", true);
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../libs/db.php');
require_once(__DIR__ . '/../libs/lang.php');
require_once(__DIR__ . '/../libs/helper.php');
require_once(__DIR__ . '/../libs/group_buy.php');
require_once(__DIR__ . '/../libs/smart_router.php');

$CMSNT = new DB();
$gb = new GroupBuy($CMSNT);

// Find all filled deals with auto_fulfill enabled
$filled_deals = $CMSNT->get_list_safe(
    "SELECT id, title FROM group_buy_deals WHERE status = 'filled' AND auto_fulfill = 1", []
);

foreach ($filled_deals as $deal) {
    $result = $gb->fulfillDeal($deal['id']);
    echo "Deal #{$deal['id']} ({$deal['title']}): fulfilled {$result['fulfilled']}/{$result['total']} keys\n";
    
    // Log to admin logs
    $CMSNT->insert('logs', [
        'user_id' => 0,
        'ip' => '127.0.0.1',
        'device' => 'Cron',
        'createdate' => date('Y-m-d H:i:s'),
        'action' => "🤖 Auto-fulfilled Group Buy #{$deal['id']}: {$result['fulfilled']} keys delivered",
    ]);
}

echo "Done. Processed " . count($filled_deals) . " deals.\n";
