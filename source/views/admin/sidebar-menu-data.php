<?php
/**
 * sidebar-menu-data.php (Punch list #3 — Master Prompt v3 §4.1)
 * Single source of truth cho admin sidebar. Cả Shell A (sidebar.php) và
 * Shell B (dcos-layout.php) cùng đọc mảng này để render theo CSS riêng.
 * Thêm/sửa/xóa item chỉ sửa 1 chỗ.
 *
 * Cấu trúc mỗi node:
 *  - ['type'=>'category', 'label'=>...]
 *  - ['type'=>'link', 'label','href','icon','color','active'=>[], 'perm'=>?]
 *  - ['type'=>'submenu', 'label','icon','color','show'=>[], 'perm'=>?, 'children'=>[...]]
 *    children có thể là link hoặc submenu lồng nhau.
 * 'perm' = permission key kiểm tra qua checkPermission($getUser['admin'], perm).
 * 'badge' = ['type'=>'affiliate_withdraw_pending'] render badge đếm động.
 */
if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

return [
    // ===== MAIN =====
    ['type' => 'category', 'label' => 'Main'],
    ['type' => 'link', 'label' => 'Dashboard', 'href' => 'home', 'icon' => 'bx bxs-dashboard', 'color' => '#3b82f6', 'active' => ['home', '']],
    ['type' => 'submenu', 'label' => 'Lịch sử', 'icon' => 'bx bx-history', 'color' => '#8b5cf6',
        'show' => ['logs', 'transactions', 'log-auto-bank', 'telegram-logs', 'telegram-queue', 'admin-logs', 'failed-attempts-logs', 'email-queue'],
        'children' => [
            ['type' => 'link', 'label' => 'Nhật ký hoạt động', 'href' => 'logs', 'perm' => 'view_logs', 'active' => ['logs']],
            ['type' => 'link', 'label' => 'Biến động số dư', 'href' => 'transactions', 'perm' => 'view_transactions', 'active' => ['transactions']],
            ['type' => 'link', 'label' => 'Lịch sử ngân hàng', 'href' => 'log-auto-bank', 'perm' => 'view_transactions', 'active' => ['log-auto-bank']],
            ['type' => 'link', 'label' => 'Telegram Logs', 'href' => 'telegram-logs', 'perm' => 'view_telegram_logs', 'active' => ['telegram-logs']],
            ['type' => 'link', 'label' => 'Email Queue', 'href' => 'email-queue', 'perm' => 'view_logs', 'active' => ['email-queue']],
            ['type' => 'link', 'label' => 'Telegram Queue', 'href' => 'telegram-queue', 'perm' => 'view_logs', 'active' => ['telegram-queue']],
        ],
    ],
    ['type' => 'link', 'label' => 'Tự động hóa', 'href' => 'automations', 'icon' => 'bx bxs-calendar', 'color' => '#14b8a6', 'perm' => 'view_automations', 'active' => ['automations', 'automation-edit']],

    // ===== BẢO MẬT =====
    ['type' => 'category', 'label' => 'Bảo mật'],
    ['type' => 'link', 'label' => 'Block IP', 'href' => 'block-ip', 'icon' => 'bx bx-block', 'color' => '#ef4444', 'perm' => 'view_block_ip', 'active' => ['block-ip']],

    // ===== DIGITAL COMMERCE OS =====
    ['type' => 'category', 'label' => 'Digital Commerce OS'],
    ['type' => 'link', 'label' => '🧠 Smart Routing', 'href' => 'smart-routing', 'icon' => 'bx bx-network-chart', 'color' => '#2563EB', 'active' => ['smart-routing']],
    ['type' => 'link', 'label' => '👥 Group Buy', 'href' => 'group-buy-admin', 'icon' => 'bx bx-group', 'color' => '#8B5CF6', 'active' => ['group-buy-admin']],
    ['type' => 'link', 'label' => '🔑 API Keys', 'href' => 'api-keys', 'icon' => 'bx bx-key', 'color' => '#F59E0B', 'active' => ['api-keys']],
    ['type' => 'link', 'label' => '🕵️ Competitor Research', 'href' => 'competitor-research', 'icon' => 'bx bx-search-alt', 'color' => '#10B981', 'active' => ['competitor-research']],
    ['type' => 'link', 'label' => '📈 Trend Detection', 'href' => 'trend-detection', 'icon' => 'bx bx-trending-up', 'color' => '#EF4444', 'active' => ['trend-detection']],
    ['type' => 'link', 'label' => '💲 Dynamic Pricing', 'href' => 'dynamic-pricing', 'icon' => 'bx bx-dollar-circle', 'color' => '#06B6D4', 'active' => ['dynamic-pricing']],

    // ===== DỊCH VỤ =====
    ['type' => 'category', 'label' => 'Dịch vụ'],
    ['type' => 'submenu', 'label' => 'Sản phẩm', 'icon' => 'bx bx-cart', 'color' => '#f97316', 'perm' => 'view_product',
        'show' => ['category-add', 'product-warehouse', 'product-sold', 'product-api-manager', 'product-api-edit', 'product-api-add', 'product-api', 'product-orders', 'categories', 'category-edit', 'products', 'product-add', 'product-edit', 'product-stock', 'key-inventory', 'giftcard-inventory', 'game-manager', 'provider-manager', 'topup-orders', 'manual-orders'],
        'children' => [
            ['type' => 'link', 'label' => '📂 Chuyên mục', 'href' => 'categories', 'active' => ['category-add', 'categories', 'category-edit']],
            ['type' => 'link', 'label' => '📦 Tất cả sản phẩm', 'href' => 'products', 'active' => ['products', 'product-add', 'product-edit']],
            ['type' => 'submenu', 'label' => '🗄️ Kho hàng (theo loại)',
                'show' => ['product-stock', 'key-inventory', 'giftcard-inventory', 'game-manager', 'provider-manager', 'topup-orders'],
                'children' => [
                    ['type' => 'link', 'label' => '👤 Kho Account', 'href' => 'product-warehouse', 'active' => ['product-warehouse', 'product-stock']],
                    ['type' => 'link', 'label' => '🎮 Kho Game Key', 'href' => 'key-inventory', 'active' => ['key-inventory']],
                    ['type' => 'link', 'label' => '💳 Kho Gift Card', 'href' => 'giftcard-inventory', 'active' => ['giftcard-inventory']],
                    ['type' => 'link', 'label' => '📱 Top Up (121 games)', 'href' => 'game-manager', 'active' => ['game-manager', 'game-edit']],
                    ['type' => 'link', 'label' => '🔌 Providers', 'href' => 'provider-manager', 'active' => ['provider-manager']],
                ],
            ],
            ['type' => 'link', 'label' => '🔗 Kết nối API', 'href' => 'product-api', 'perm' => 'manager_suppliers', 'active' => ['product-api-manager', 'product-api-edit', 'product-api', 'product-api-add']],
            ['type' => 'link', 'label' => '🛒 Đơn hàng', 'href' => 'product-orders', 'perm' => 'view_orders_product', 'active' => ['product-orders']],
            ['type' => 'link', 'label' => '🧾 Đơn hàng thủ công', 'href' => 'manual-orders', 'perm' => 'view_orders_product', 'active' => ['manual-orders']],
            ['type' => 'link', 'label' => '✅ Đã bán', 'href' => 'product-sold', 'perm' => 'view_sold_product', 'active' => ['product-sold']],
        ],
    ],

    // ===== QUẢN LÝ =====
    ['type' => 'category', 'label' => 'Quản lý'],
    ['type' => 'submenu', 'label' => 'CTV Panel', 'icon' => 'bx bxs-group', 'color' => '#ec4899', 'perm' => 'view_ctv',
        'show' => ['ctv-withdraw', 'ctv-statistics', 'ctv-config', 'ctv-pending-products'],
        'children' => [
            ['type' => 'link', 'label' => 'Thống kê', 'href' => 'ctv-statistics', 'perm' => 'view_statistics_ctv', 'active' => ['ctv-statistics']],
            ['type' => 'link', 'label' => 'Đơn rút tiền', 'href' => 'ctv-withdraw', 'perm' => 'view_withdraw_ctv', 'active' => ['ctv-withdraw']],
            ['type' => 'link', 'label' => 'Sản phẩm chờ duyệt', 'href' => 'ctv-pending-products', 'perm' => 'view_product', 'active' => ['ctv-pending-products']],
            ['type' => 'link', 'label' => 'Cấu hình', 'href' => 'ctv-config', 'perm' => 'edit_config_ctv', 'active' => ['ctv-config']],
        ],
    ],
    ['type' => 'link', 'label' => 'Thành viên', 'href' => 'users', 'icon' => 'bx bxs-user', 'color' => '#06b6d4', 'perm' => 'view_user', 'active' => ['users', 'user-edit']],
    ['type' => 'link', 'label' => 'Admin Role', 'href' => 'roles', 'icon' => 'bx bxs-check-shield', 'color' => '#eab308', 'perm' => 'view_role', 'active' => ['roles', 'role-edit']],
    ['type' => 'submenu', 'label' => 'Nạp tiền', 'icon' => 'bx bxs-wallet-alt', 'color' => '#22c55e', 'perm' => 'view_recharge',
        'show' => ['recharge-thesieure', 'recharge-flutterwave', 'recharge-momo', 'recharge-card', 'recharge-bank', 'recharge-crypto', 'recharge-bank-edit', 'recharge-paypal', 'recharge-perfectmoney', 'recharge-toyyibpay', 'recharge-squadco', 'recharge-bank-config', 'recharge-manual', 'recharge-manual-edit', 'recharge-xipay', 'recharge-lempay', 'recharge-tripay', 'recharge-korapay', 'recharge-pocketfi', 'recharge-tmweasyapi', 'recharge-bakong', 'recharge-openpix', 'recharge-dsociopay', 'recharge-zinipay', 'recharge-paymentpoint'],
        'children' => [
            ['type' => 'link', 'label' => 'Ngân hàng', 'href' => 'recharge-bank', 'active' => ['recharge-bank', 'recharge-bank-edit', 'recharge-bank-config']],
            ['type' => 'link', 'label' => 'Nạp thẻ cào', 'href' => 'recharge-card', 'active' => ['recharge-card']],
            ['type' => 'link', 'label' => 'Crypto USDT', 'href' => 'recharge-crypto', 'active' => ['recharge-crypto']],
            ['type' => 'link', 'label' => 'Ví MOMO', 'href' => 'recharge-momo', 'active' => ['recharge-momo']],
            ['type' => 'link', 'label' => 'Ví THESIEURE', 'href' => 'recharge-thesieure', 'active' => ['recharge-thesieure']],
            ['type' => 'link', 'label' => 'Paypal', 'href' => 'recharge-paypal', 'active' => ['recharge-paypal']],
            ['type' => 'link', 'label' => 'Perfect Money', 'href' => 'recharge-perfectmoney', 'active' => ['recharge-perfectmoney']],
            ['type' => 'link', 'label' => 'Toyyibpay Malaysia', 'href' => 'recharge-toyyibpay', 'active' => ['recharge-toyyibpay']],
            ['type' => 'link', 'label' => 'Squadco Nigeria', 'href' => 'recharge-squadco', 'active' => ['recharge-squadco']],
            ['type' => 'link', 'label' => 'Flutterwave', 'href' => 'recharge-flutterwave', 'active' => ['recharge-flutterwave']],
            ['type' => 'link', 'label' => 'XiPay (AliPay, WechatPay)', 'href' => 'recharge-xipay', 'active' => ['recharge-xipay']],
            ['type' => 'link', 'label' => 'LemPay (AliPay, WechatPay, USDT)', 'href' => 'recharge-lempay', 'active' => ['recharge-lempay']],
            ['type' => 'link', 'label' => 'TriPay Indonesia', 'href' => 'recharge-tripay', 'active' => ['recharge-tripay']],
            ['type' => 'link', 'label' => 'ZiniPay (bKash, Nagad)', 'href' => 'recharge-zinipay', 'active' => ['recharge-zinipay']],
            ['type' => 'link', 'label' => 'Korapay Africa', 'href' => 'recharge-korapay', 'active' => ['recharge-korapay']],
            ['type' => 'link', 'label' => 'PocketFi Nigeria', 'href' => 'recharge-pocketfi', 'active' => ['recharge-pocketfi']],
            ['type' => 'link', 'label' => 'PaymentPoint Nigeria', 'href' => 'recharge-paymentpoint', 'active' => ['recharge-paymentpoint']],
            ['type' => 'link', 'label' => 'DSocioPay', 'href' => 'recharge-dsociopay', 'active' => ['recharge-dsociopay']],
            ['type' => 'link', 'label' => 'Tmweasyapi Thailand', 'href' => 'recharge-tmweasyapi', 'active' => ['recharge-tmweasyapi']],
            ['type' => 'link', 'label' => 'OpenPix Brazil', 'href' => 'recharge-openpix', 'active' => ['recharge-openpix']],
            ['type' => 'link', 'label' => 'Bakong Wallet Cambodia', 'href' => 'recharge-bakong', 'active' => ['recharge-bakong']],
            ['type' => 'link', 'label' => 'Manual Payment', 'href' => 'recharge-manual', 'active' => ['recharge-manual', 'recharge-manual-edit']],
        ],
    ],
    ['type' => 'submenu', 'label' => 'Affiliate Program', 'icon' => 'bx bx-group', 'color' => '#a855f7', 'perm' => 'view_affiliate',
        'show' => ['affiliate-config', 'affiliate-withdraw', 'affiliate-history', 'affiliate-links'],
        'children' => [
            ['type' => 'link', 'label' => 'Danh sách liên kết', 'href' => 'affiliate-links', 'active' => ['affiliate-links']],
            ['type' => 'link', 'label' => 'Nhật ký hoa hồng', 'href' => 'affiliate-history', 'active' => ['affiliate-history']],
            ['type' => 'link', 'label' => 'Rút tiền', 'href' => 'affiliate-withdraw', 'perm' => 'view_withdraw_affiliate', 'active' => ['affiliate-withdraw'], 'badge' => ['type' => 'affiliate_withdraw_pending']],
            ['type' => 'link', 'label' => 'Cấu hình', 'href' => 'affiliate-config', 'perm' => 'edit_affiliate', 'active' => ['affiliate-config']],
        ],
    ],
    ['type' => 'link', 'label' => 'Email Campaigns', 'href' => 'email-campaigns', 'icon' => 'bx bx-mail-send', 'color' => '#3b82f6', 'perm' => 'view_email_campaigns', 'active' => ['email-campaigns', 'email-campaign-add', 'email-campaign-edit', 'email-sending-view']],
    ['type' => 'link', 'label' => 'Mã giảm giá', 'href' => 'coupons', 'icon' => 'bx bxs-discount', 'color' => '#f97316', 'perm' => 'view_coupon', 'active' => ['coupons']],
    ['type' => 'link', 'label' => 'Khuyến mãi nạp tiền', 'href' => 'promotions', 'icon' => 'fa-solid fa-percent', 'color' => '#ec4899', 'perm' => 'view_promotion', 'active' => ['promotions']],
    ['type' => 'submenu', 'label' => 'Bài viết', 'icon' => 'bx bxl-blogger', 'color' => '#14b8a6', 'perm' => 'view_blog',
        'show' => ['blog-add', 'blogs', 'blog-edit', 'blog-category', 'blog-category-edit'],
        'children' => [
            ['type' => 'link', 'label' => 'Viết bài mới', 'href' => 'blog-add', 'active' => ['blog-add']],
            ['type' => 'link', 'label' => 'Tất cả bài viết', 'href' => 'blogs', 'active' => ['blogs', 'blog-edit']],
            ['type' => 'link', 'label' => 'Chuyên mục', 'href' => 'blog-category', 'active' => ['blog-category', 'blog-category-edit']],
        ],
    ],

    // ===== CÀI ĐẶT HỆ THỐNG =====
    ['type' => 'category', 'label' => 'Cài đặt hệ thống'],
    ['type' => 'link', 'label' => 'Ngôn ngữ', 'href' => 'language-list', 'icon' => 'las la-language', 'color' => '#6366f1', 'perm' => 'view_lang', 'active' => ['language-list', 'language-add', 'language-edit', 'translate-list']],
    ['type' => 'link', 'label' => 'Tiền tệ', 'href' => 'currency-list', 'icon' => 'bx bx-dollar', 'color' => '#10b981', 'perm' => 'view_currency', 'active' => ['currency-list', 'currency-add', 'currency-edit']],
    ['type' => 'link', 'label' => 'Giao diện', 'href' => 'theme', 'icon' => 'bx bxs-image', 'color' => '#a855f7', 'perm' => 'edit_theme', 'active' => ['theme']],
    ['type' => 'link', 'label' => 'Cài đặt', 'href' => 'settings', 'icon' => 'bx bx-cog', 'color' => '#64748b', 'perm' => 'edit_setting', 'active' => ['settings'], 'class' => 'mb-5'],
];
