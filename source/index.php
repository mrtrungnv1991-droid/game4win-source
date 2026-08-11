<?php
/**
 * GameTopup — Main Router
 * ShopClone7 v6.4.0 — Adapted for Game Topup
 */
define("IN_SITE", true);
require_once(__DIR__ . '/libs/db.php');
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/libs/lang.php');
require_once(__DIR__ . '/libs/helper.php');
require_once(__DIR__ . '/libs/database/users.php');
$CMSNT = new DB();

define('VIEWS_PATH', __DIR__ . '/views');

$module = !empty($_GET['module']) ? validate_path($_GET['module']) : 'client';
if ($module === false) { $module = 'client'; }

// Default home: dùng layout marketplace G2A-style
$homeSetting = $CMSNT->site('home_page');
$home = ($module == 'client') ? 'home-marketplace' : ($homeSetting ?: 'home');

$action = !empty($_GET['action']) ? validate_path($_GET['action']) : $home;
if ($action === false) { $action = $home; }

// === ALLOWED ACTIONS ===
$allowed_actions = [
    // Client — Marketplace G2A-style + TOPUP + GROUP BUY
    'home-marketplace', 'topup-home', 'topup', 'topup-product', 'topup-history', 'group-buy', 'group-buy-detail',
    // Client gốc
    'aff','affiliate-history','affiliate-withdraw','affiliates','blog','blogs',
    'change-password','contact','document-api','faq','favorites','forgot-password',
    'home','login','logout','logs','policy','product','product-order','product-orders',
    'profile','recharge-bakong','recharge-bank','recharge-card','recharge-crypto',
    'vat-invoice','recharge-flutterwave','recharge-korapay','recharge-pocketfi',
    'recharge-paymentpoint','recharge-dsociopay','recharge-manual','recharge-momo',
    'recharge-openpix','recharge-paypal','recharge-perfectmoney','recharge-squadco',
    'recharge-thesieure','recharge-tmweasyapi','recharge-toyyibpay','recharge-xipay',
    'recharge-lempay','recharge-zinipay','recharge-tripay','register','reset-password',
    'security','set-language','tool-2fa','tool-checklive-fb','tool-icon-facebook',
    'tool-random-face','transactions','verify_2fa','verify_otp','widget_tools',
    // Admin
    'admin-logs','affiliate-config','affiliate-history','affiliate-links','affiliate-withdraw',
    'automation-edit','automations','block-ip','blog-add','blog-category','blog-category-edit',
    'blog-edit','blogs','categories','category-add','category-edit','coupon-edit','coupons',
    'ctv-config','ctv-pending-products','ctv-statistics','ctv-withdraw','currency-edit',
    'currency-list','email-campaign-add','email-campaign-edit','email-campaigns','email-queue',
    'email-sending-view','failed-attempts-logs','home','language-edit','language-list',
    'log-auto-bank','login-user','logs','menu-edit','menu-list','product-add','product-api',
    'product-api-add','product-api-edit','product-api-manager','product-edit','product-orders',
    'product-sold','product-stock','product-warehouse','products','promotions','recharge-bakong',
    'recharge-bank','recharge-bank-config','recharge-bank-edit','recharge-card','recharge-crypto',
    'recharge-flutterwave','recharge-korapay','recharge-pocketfi','recharge-paymentpoint',
    'recharge-dsociopay','recharge-manual','recharge-manual-edit','recharge-momo',
    'recharge-openpix','recharge-paypal','recharge-perfectmoney','recharge-squadco',
    'recharge-thesieure','recharge-tmweasyapi','recharge-toyyibpay','recharge-xipay',
    'recharge-lempay','recharge-zinipay','recharge-tripay','role-edit','roles','settings',
    'telegram-logs','telegram-queue','theme','transactions','translate-list','user-edit','users',
    'game-manager','game-edit','topup-orders','provider-manager','manual-orders',
    // Smart Routing + Group Buy
    'smart-routing','group-buy-admin','group-buy-detail',
    // Digital Commerce OS modules
    'api-keys','competitor-research','trend-detection','dynamic-pricing',
    // Inventory modules (tách riêng theo loại SP)
    'key-inventory','giftcard-inventory',
    // Client support
    'support','ticket-detail',
    // ADCP
    'ticket-list','ticket-detail','home','about','recycle-bin','messages','flash-sales','flash-sale-add','flash-sale-edit','product-reviews','categories','products','product-plans-all','product-stock-all','product-orders','product-api',
    // CTV
    'ctv-withdraw','home','product-add','product-edit','product-orders','product-stock','products','settings',
    // Common
    '404','banned','block-ip','maintenance'
];

$allowed_actions[] = $home;

if (!in_array($action, $allowed_actions, true)) {
    require_once(VIEWS_PATH . '/common/404.php');
    exit();
}

// Auth checks
if (($module == 'admin' || $module == 'adcp') && !isSecureCookie('admin_login')) { redirect(base_url('client/login')); }
if (($module == 'admin' || $module == 'adcp')) { require_once __DIR__ . '/models/is_admin.php'; }
if ($module == 'ctv' && !isSecureCookie('ctv_login')) { redirect(base_url('client/login')); }
if ($module == 'ctv') { require_once __DIR__ . '/models/is_ctv.php'; }

// UTM tracking
if (isset($_GET['utm_source'])) {
    $utm_source = validate_string($_GET['utm_source'], 100);
    if ($utm_source !== false) { setcookie('utm_source', $utm_source, time() + (86400 * 30), "/"); }
}

// Affiliate tracking
if (isset($_GET['aff'])) {
    $aff = validate_int($_GET['aff'], 1);
    if ($aff !== false) {
        setcookie('aff', $aff, time() + (86400 * 30), "/");
        if ($user_ref = $CMSNT->get_row_safe("SELECT id FROM `users` WHERE `id` = ?", [$aff])) {
            $CMSNT->cong('users', 'ref_click', 1, " `id` = ? ", [$user_ref['id']]);
        }
        $clean_url = strtok($_SERVER['REQUEST_URI'], '?');
        $params = $_GET; unset($params['aff']);
        if (!empty($params)) { $clean_url .= '?' . http_build_query($params); }
        header('Location: ' . $clean_url);
        exit();
    }
}

// Route to view
$path = VIEWS_PATH . '/' . $module . '/' . $action . '.php';
if (file_exists($path) && strpos(realpath($path), realpath(VIEWS_PATH)) === 0) {
    require_once($path);
    exit();
} else {
    require_once(VIEWS_PATH . '/common/404.php');
    exit();
}
