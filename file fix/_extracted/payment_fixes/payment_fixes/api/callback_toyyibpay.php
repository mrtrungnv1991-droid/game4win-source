<?php
/**
 * FILE NÀY TRƯỚC ĐÂY RỖNG (0 byte) — nghĩa là khách nạp tiền qua ToyyibPay đã
 * thanh toán thành công nhưng KHÔNG BAO GIỜ được cộng tiền vào ví, vì không có
 * đoạn code nào xử lý callback trả về từ ToyyibPay để cộng tiền.
 *
 * Định dạng POST callback của ToyyibPay (theo tài liệu chính thức):
 *   refno, status (1=success, 2=pending, 3=fail), reason, billcode,
 *   order_id, amount (đơn vị cents), transaction_time
 *
 * Lưu ý bảo mật: ToyyibPay KHÔNG ký (sign) dữ liệu callback, nên không thể chỉ
 * tin vào giá trị POST gửi lên (ai cũng có thể giả POST request tới URL này).
 * Vì vậy bắt buộc phải gọi ngược lại API getBillTransactions() của ToyyibPay để
 * xác nhận từ chính server ToyyibPay rằng đơn hàng đã thanh toán thật, trước khi
 * cộng tiền.
 */

define("IN_SITE", true);
require_once(__DIR__ . "/../libs/db.php");
require_once(__DIR__ . "/../config.php");
require_once(__DIR__ . "/../libs/lang.php");
require_once(__DIR__ . "/../libs/helper.php");
require_once(__DIR__ . "/../libs/toyyibpay.php");
require_once(__DIR__ . "/../libs/database/users.php");
$CMSNT = new DB();

if ($CMSNT->site('toyyibpay_status') != 1) {
    die('Cổng thanh toán này chưa được kích hoạt');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    exit("Method Not Allowed");
}

$order_id = isset($_POST['order_id']) ? validate_alphanumeric($_POST['order_id'], 255) : '';
$billcode = isset($_POST['billcode']) ? validate_alphanumeric($_POST['billcode'], 255) : '';
$status   = isset($_POST['status']) ? validate_string($_POST['status'], 5) : '';

if ($order_id === false || $billcode === false || empty($order_id) || empty($billcode)) {
    exit("fail");
}

// Chỉ xử lý khi ToyyibPay báo trạng thái thành công (status = 1)
if ($status != '1') {
    exit("ok");
}

$row = $CMSNT->get_row_safe(" SELECT * FROM `payment_toyyibpay` WHERE `trans_id` = ? AND `BillCode` = ? AND `status` = 0 ", [$order_id, $billcode]);
if (!$row) {
    exit("ok");
}

// XÁC MINH LẠI với server ToyyibPay (không tin dữ liệu POST vì không có chữ ký)
$toyyibpay = new toyyibpay($CMSNT->site('toyyibpay_userSecretKey'));
$verifyResult = $toyyibpay->getBillTransactions(['billCode' => $billcode]);
$verifyResult = json_decode($verifyResult, true);

$isReallyPaid = false;
if (is_array($verifyResult)) {
    foreach ($verifyResult as $tx) {
        if (isset($tx['billpaymentStatus']) && $tx['billpaymentStatus'] == '1'
            && isset($tx['billExternalReferenceNo']) && $tx['billExternalReferenceNo'] == $order_id) {
            $isReallyPaid = true;
            break;
        }
    }
}

if (!$isReallyPaid) {
    exit("fail");
}

$claimed = $CMSNT->update('payment_toyyibpay', [
    'status'         => 1,
    'update_gettime' => gettime(),
    'notication'     => 1
], " `id` = ? AND `status` = 0 ", [$row['id']]);

if ($claimed) {
    $user = new users;
    $isCong = $user->AddCredits($row['user_id'], $row['amount'], __('Recharge').' ToyyibPay #'.$order_id, 'TOPUP_toyyibpay_'.$order_id);
    if ($isCong) {
        $CMSNT->insert('deposit_log', [
            'user_id'     => $row['user_id'],
            'method'      => 'ToyyibPay',
            'amount'      => $row['amount'],
            'received'    => $row['amount'],
            'create_time' => time(),
            'is_virtual'  => 0
        ]);

        $my_text = $CMSNT->site('noti_recharge');
        $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
        $my_text = str_replace('{username}', getRowRealtime('users', $row['user_id'], 'username'), $my_text);
        $my_text = str_replace('{method}', 'ToyyibPay', $my_text);
        $my_text = str_replace('{amount}', $row['amount'], $my_text);
        $my_text = str_replace('{price}', format_currency($row['amount']), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
    }
}

echo "ok";
