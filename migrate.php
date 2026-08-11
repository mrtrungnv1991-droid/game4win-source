<?php
/**
 * GameTopup Migration Script
 * 
 * Chạy khi deploy lên production lần đầu hoặc update.
 * Usage: php migrate.php
 */

define('IN_SITE', true);
require_once(__DIR__ . '/source/libs/db.php');
require_once(__DIR__ . '/source/config.php');
require_once(__DIR__ . '/source/libs/helper.php');

$CMSNT = new DB();

echo "=== GameTopup Migration ===\n";
echo "Started: " . date('Y-m-d H:i:s') . "\n\n";

// 1. Add topup columns to product_order (if not exists)
echo "1. Checking product_order columns...\n";
try {
    $CMSNT->query("ALTER TABLE `product_order` 
        ADD COLUMN IF NOT EXISTS `topup_tier_id` INT DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS `topup_status` ENUM('pending','processing','success','failed','refunded') DEFAULT 'pending',
        ADD COLUMN IF NOT EXISTS `game_uid` VARCHAR(100) DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS `provider_order_id` VARCHAR(255) DEFAULT NULL");
    echo "   OK\n";
} catch (Exception $e) {
    echo "   SKIP: " . $e->getMessage() . "\n";
}

// 2. Create topup tables (if not exists)
echo "2. Creating topup tables...\n";
$tables = [
    "games" => "CREATE TABLE IF NOT EXISTS `games` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `full_name` VARCHAR(500) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `category` VARCHAR(100) DEFAULT NULL,
        `icon` VARCHAR(10) DEFAULT NULL,
        `image` VARCHAR(500) DEFAULT NULL,
        `uid_pattern` VARCHAR(100) DEFAULT NULL,
        `uid_help` VARCHAR(255) DEFAULT NULL,
        `currency_name` VARCHAR(100) DEFAULT NULL,
        `currency_unit` VARCHAR(10) DEFAULT NULL,
        `status` TINYINT(1) DEFAULT 1,
        `sort_order` INT DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_status` (`status`, `sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "topup_tiers" => "CREATE TABLE IF NOT EXISTS `topup_tiers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `game_id` INT NOT NULL,
        `type` ENUM('gem','pack','allpack') DEFAULT 'gem',
        `label` VARCHAR(255) NOT NULL,
        `amount` INT DEFAULT 0,
        `price` INT DEFAULT 0,
        `cost` INT DEFAULT 0,
        `provider_id` INT DEFAULT 1,
        `status` TINYINT(1) DEFAULT 1,
        `sort_order` INT DEFAULT 0,
        KEY `idx_game_type` (`game_id`, `type`, `status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "topup_providers" => "CREATE TABLE IF NOT EXISTS `topup_providers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `type` ENUM('rest_api','mock','webhook') DEFAULT 'rest_api',
        `api_endpoint` VARCHAR(500) DEFAULT NULL,
        `api_key` VARCHAR(500) DEFAULT NULL,
        `api_secret` VARCHAR(500) DEFAULT NULL,
        `http_method` ENUM('GET','POST') DEFAULT 'POST',
        `timeout_ms` INT DEFAULT 15000,
        `retry_count` INT DEFAULT 3,
        `retry_delay_ms` INT DEFAULT 2000,
        `status` TINYINT(1) DEFAULT 1,
        `priority` INT DEFAULT 0,
        `fee_percent` DECIMAL(5,2) DEFAULT 0,
        `fee_fixed` INT DEFAULT 0,
        `last_check` DATETIME DEFAULT NULL,
        `response_time_ms` INT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "topup_api_logs" => "CREATE TABLE IF NOT EXISTS `topup_api_logs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `order_id` INT NOT NULL,
        `game_id` INT DEFAULT NULL,
        `request_data` TEXT DEFAULT NULL,
        `response_data` TEXT DEFAULT NULL,
        `status_code` INT DEFAULT NULL,
        `duration_ms` INT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        KEY `idx_order` (`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($tables as $name => $sql) {
    try {
        $CMSNT->query($sql);
        echo "   $name: OK\n";
    } catch (Exception $e) {
        echo "   $name: " . $e->getMessage() . "\n";
    }
}

// 3. Seed mock provider (if empty)
echo "3. Seeding default provider...\n";
$count = $CMSNT->num_rows("SELECT id FROM topup_providers");
if ($count == 0) {
    $CMSNT->insert('topup_providers', [
        'name' => 'Mock Provider',
        'slug' => 'mock',
        'type' => 'mock',
        'status' => 1,
        'priority' => 0
    ]);
    echo "   Mock provider created\n";
} else {
    echo "   Already has $count provider(s)\n";
}

// 4. Update settings
echo "4. Checking settings...\n";
$settings = [
    'home_page' => 'topup-home',
    'domains' => "UPDATE settings SET value = CONCAT(value, ',127.0.0.1:8080') WHERE name='domains' AND value NOT LIKE '%127.0.0.1%'",
    'thoi_gian_mua_cach_nhau' => '3',
    'type_password' => 'md5',
];

foreach ($settings as $key => $val) {
    if (strpos($val, 'UPDATE') === 0) {
        $CMSNT->query($val);
    } else {
        $exists = $CMSNT->num_rows("SELECT id FROM settings WHERE name = '$key'");
        if ($exists) {
            $CMSNT->update('settings', ['value' => $val], "name = '$key'");
        } else {
            $CMSNT->insert('settings', ['name' => $key, 'value' => $val]);
        }
    }
    echo "   $key: OK\n";
}

echo "\n=== Migration Complete ===\n";
echo "Next: Import seed data if needed (database/_topup_seed.sql)\n";
