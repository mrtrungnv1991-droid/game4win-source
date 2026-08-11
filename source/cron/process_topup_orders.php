<?php
/**
 * Cron Job: Xử lý đơn nạp game đang pending/processing
 * 
 * Chạy mỗi 2 phút:
 * - Tìm đơn processing > 2 phút → retry provider
 * - Tìm đơn processing > 30 phút → auto refund
 * - Tìm đơn pending > 10 phút → retry provider
 * 
 * Usage: php cron/process_topup_orders.php
 */

define('IN_SITE', true);
require_once(__DIR__ . '/../libs/db.php');
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../libs/lang.php');
require_once(__DIR__ . '/../libs/helper.php');
require_once(__DIR__ . '/../libs/database/users.php');
require_once(__DIR__ . '/../libs/topup_provider.php');

$CMSNT = new DB();
$User = new users();

echo "[" . date('Y-m-d H:i:s') . "] Topup Cron started\n";

// 1. Xử lý đơn processing (chưa có kết quả từ provider)
$processingOrders = $CMSNT->get_list_safe(
    "SELECT * FROM `product_order` 
     WHERE `topup_tier_id` IS NOT NULL 
       AND `topup_status` = 'processing'
       AND `create_gettime` < DATE_SUB(NOW(), INTERVAL 2 MINUTE)
       AND `create_gettime` > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
     ORDER BY `id` ASC
     LIMIT 20",
    []
);

foreach ($processingOrders as $order) {
    echo "  Retrying order #{$order['trans_id']} ({$order['product_name']})... ";
    
    try {
        $note = json_decode($order['note'] ?? '{}', true);
        $tier = $CMSNT->get_row_safe("SELECT * FROM `topup_tiers` WHERE `id` = ?", [$order['topup_tier_id']]);
        $provider_id = $tier['provider_id'] ?? 1;
        
        $provider = new TopupProvider($provider_id);
        $result = $provider->submit([
            'order_id' => $order['id'],
            'trans_id' => $order['trans_id'],
            'game_uid' => $order['game_uid'],
            'game_name' => $note['game_name'] ?? '',
            'tier_label' => $note['tier_label'] ?? '',
            'amount' => $tier['amount'] ?? 0,
            'price' => $order['pay']
        ]);
        
        if ($result['status'] === 'success') {
            $CMSNT->update('product_order', [
                'topup_status' => 'success',
                'provider_order_id' => $result['provider_order_id'],
                'update_gettime' => gettime()
            ], "`id` = " . $order['id']);
            echo "SUCCESS\n";
        } else {
            $CMSNT->update('product_order', [
                'update_gettime' => gettime()
            ], "`id` = " . $order['id']);
            echo "still processing\n";
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        $CMSNT->update('product_order', [
            'update_gettime' => gettime()
        ], "`id` = " . $order['id']);
    }
}

// 2. Đơn quá 30 phút → đánh dấu pending (admin tự xem xét, không auto refund)
$expiredOrders = $CMSNT->get_list_safe(
    "SELECT * FROM `product_order`
     WHERE `topup_tier_id` IS NOT NULL
       AND `topup_status` IN ('processing', 'pending')
       AND `create_gettime` < DATE_SUB(NOW(), INTERVAL 30 MINUTE)
     ORDER BY `id` ASC
     LIMIT 10",
    []
);

foreach ($expiredOrders as $order) {
    echo "  Marking pending: order #{$order['trans_id']} (expired, admin will review)... ";
    $CMSNT->update('product_order', [
        'topup_status' => 'pending',
        'update_gettime' => gettime()
    ], "`id` = " . $order['id']);
    echo "DONE\n";
}

// 3. Đếm thống kê
$pending = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` IS NOT NULL AND `topup_status` = 'pending'");
$processing = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` IS NOT NULL AND `topup_status` = 'processing'");
$success = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` IS NOT NULL AND `topup_status` = 'success'");

echo "Done. Pending: {$pending}, Processing: {$processing}, Success: {$success}\n";
