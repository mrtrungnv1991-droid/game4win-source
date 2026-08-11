<?php
/**
 * Generate tier-catalog.html from database
 * Run: php generate_catalog.php
 */
$mysqli = new mysqli('localhost', 'root', 'root123', 'game4win_topup');
if ($mysqli->connect_errno) {
    die("DB Error: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// Get all games
$games = $mysqli->query("SELECT id, name, full_name, category, icon, currency_name, currency_unit, status FROM games WHERE status=1 ORDER BY sort_order, id");

$data = [];
while ($game = $games->fetch_assoc()) {
    // Get tiers for this game
    $tiers = $mysqli->query("SELECT id, type, label, amount, price, cost, status, sort_order FROM topup_tiers WHERE game_id={$game['id']} AND status=1 ORDER BY FIELD(type,'gem','pack','allpack'), sort_order, amount");
    $tierList = [];
    while ($t = $tiers->fetch_assoc()) {
        $tierList[] = [
            'type' => $t['type'],
            'label' => $t['label'],
            'amount' => (int)$t['amount'],
            'price' => (int)$t['price'],
            'cost' => (int)$t['cost'],
        ];
    }
    $data[] = [
        'name' => $game['name'],
        'icon' => $game['icon'] ?: '🎮',
        'currency' => $game['currency_name'] ?: '',
        'tiers' => $tierList,
    ];
}

$json = json_encode($data, JSON_UNESCAPED_UNICODE);

// Read template and inject
$template = file_get_contents(__DIR__ . '/tier-catalog.php');
$html = str_replace('__DATA_PLACEHOLDER__', $json, $template);

$outPath = __DIR__ . '/tier-catalog.html';
file_put_contents($outPath, $html);

echo "Done! " . count($data) . " games, " . array_sum(array_map(fn($g) => count($g['tiers']), $data)) . " tiers → $outPath\n";
