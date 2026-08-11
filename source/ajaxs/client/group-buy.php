<?php
define("IN_SITE", true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../libs/db.php');
require_once(__DIR__ . '/../../libs/lang.php');
require_once(__DIR__ . '/../../libs/helper.php');
require_once(__DIR__ . '/../../libs/database/users.php');
require_once(__DIR__ . '/../../libs/group_buy.php');

$CMSNT = new DB();
$gb = new GroupBuy($CMSNT);

// Must be logged in
$user = $CMSNT->get_row_safe("SELECT * FROM users WHERE token = ?", [$_COOKIE['user_login'] ?? '']);
if (!$user) {
    die(json_encode(['status' => 'error', 'msg' => 'Please login first']));
}

if ($_POST['action'] === 'join') {
    $deal_id = (int)($_POST['deal_id'] ?? 0);
    if ($deal_id <= 0) {
        die(json_encode(['status' => 'error', 'msg' => 'Invalid deal']));
    }
    $result = $gb->joinDeal($deal_id, $user['id']);
    die(json_encode($result));
}

die(json_encode(['status' => 'error', 'msg' => 'Invalid action']));
