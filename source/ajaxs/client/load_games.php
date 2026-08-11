<?php
/**
 * API: Load games & tiers from database
 * Endpoint: /ajaxs/client/load_games.php
 * Returns JSON with all active games + tiers
 */
define("IN_SITE", true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../libs/db.php');
require_once(__DIR__ . '/../../libs/lang.php');
require_once(__DIR__ . '/../../libs/helper.php');

$CMSNT = new DB();

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'default';

// Query games
$sql = "SELECT g.*, 
        (SELECT COUNT(*) FROM topup_tiers t WHERE t.game_id = g.id AND t.status = 1) as tier_count
        FROM games g WHERE g.status = 1";
$params = [];

if ($category) {
    $sql .= " AND g.category = ?";
    $params[] = $category;
}
if ($search) {
    $sql .= " AND (g.name LIKE ? OR g.full_name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY (SELECT MIN(t.price) FROM topup_tiers t WHERE t.game_id = g.id) ASC"; break;
    case 'price_desc': $sql .= " ORDER BY (SELECT MAX(t.price) FROM topup_tiers t WHERE t.game_id = g.id) DESC"; break;
    case 'name_asc': $sql .= " ORDER BY g.name ASC"; break;
    default: $sql .= " ORDER BY g.id ASC";
}

$games = $CMSNT->get_list_safe($sql, $params);

// Get categories for filters
$cats = $CMSNT->get_list_safe(
    "SELECT DISTINCT category, COUNT(*) as cnt FROM games WHERE status = 1 GROUP BY category ORDER BY cnt DESC", []
);

// Get tiers for each game
$result = [];
foreach ($games as $game) {
    $tiers = $CMSNT->get_list_safe(
        "SELECT * FROM topup_tiers WHERE game_id = ? AND status = 1 ORDER BY type, price ASC",
        [$game['id']]
    );
    
    $tier_data = [];
    foreach ($tiers as $t) {
        $tier_data[] = [
            'id' => $t['id'],
            'type' => $t['type'],
            'label' => $t['label'],
            'price' => (int)$t['price'],
            'original_price' => (int)($t['original_price'] ?? $t['price']),
            'uid_pattern' => $t['uid_pattern'] ?? '',
            'uid_placeholder' => $t['uid_placeholder'] ?? 'Enter UID',
        ];
    }
    
    $result[] = [
        'id' => $game['id'],
        'name' => $game['name'],
        'full_name' => $game['full_name'] ?? $game['name'],
        'category' => $game['category'] ?? 'Other',
        'image' => $game['image'] ?? '',
        'tier_count' => (int)$game['tier_count'],
        'tiers' => $tier_data,
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'success',
    'total' => count($result),
    'categories' => $cats,
    'games' => $result,
], JSON_UNESCAPED_UNICODE);
