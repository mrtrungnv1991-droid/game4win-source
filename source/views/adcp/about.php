<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$totalUsers = $CMSNT->num_rows("SELECT id FROM users");
$totalOrders = $CMSNT->num_rows("SELECT id FROM product_order");
$totalRevenue = $CMSNT->get_row("SELECT COALESCE(SUM(pay),0) as total FROM product_order WHERE topup_status = 'success'")['total'];
$totalGames = $CMSNT->num_rows("SELECT id FROM games WHERE status = 1");
$totalTiers = $CMSNT->num_rows("SELECT id FROM topup_tiers WHERE status = 1");
$totalProviders = $CMSNT->num_rows("SELECT id FROM topup_providers");

$body = ['title' => 'About — Admin'];
require_once(__DIR__ . '/../admin/header.php');
require_once(__DIR__ . '/../admin/sidebar.php');

?>
<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb"><h1>ℹ️ Về Hệ Thống</h1></div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">📊 Thống kê hệ thống</h3></div>
          <div class="card-body">
            <table class="table table-bordered">
              <tr><td><b>Phiên bản</b></td><td>GameTopup v1.0 (based on ShopClone7 v6.4.0)</td></tr>
              <tr><td><b>PHP Version</b></td><td><?= PHP_VERSION ?></td></tr>
              <tr><td><b>Database</b></td><td>MariaDB / MySQL</td></tr>
              <tr><td><b>Tổng Games</b></td><td><?= $totalGames ?> games</td></tr>
              <tr><td><b>Gói nạp</b></td><td><?= $totalTiers ?> tiers</td></tr>
              <tr><td><b>Providers</b></td><td><?= $totalProviders ?> providers</td></tr>
              <tr><td><b>Thành viên</b></td><td><?= $totalUsers ?> users</td></tr>
              <tr><td><b>Tổng đơn hàng</b></td><td><?= $totalOrders ?> orders</td></tr>
              <tr><td><b>Doanh thu</b></td><td style="color:#28a745;font-weight:700"><?= number_format($totalRevenue) ?>đ</td></tr>
              <tr><td><b>Server</b></td><td><?= $_SERVER['SERVER_SOFTWARE'] ?? 'PHP Built-in' ?></td></tr>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">🎮 GameTopup Features</h3></div>
          <div class="card-body">
            <ul class="list-group">
              <li class="list-group-item">✅ Nạp game tự động qua provider API (REST/Mock/Webhook)</li>
              <li class="list-group-item">✅ 121+ games, 1702 gói nạp</li>
              <li class="list-group-item">✅ Giỏ hàng + Checkout + Coupon</li>
              <li class="list-group-item">✅ Ví tiền + Nạp VND/USDT</li>
              <li class="list-group-item">✅ Loyalty Points + Membership (Silver/Gold/Diamond)</li>
              <li class="list-group-item">✅ Hệ thống Ticket Support</li>
              <li class="list-group-item">✅ Admin Dashboard tổng hợp</li>
              <li class="list-group-item">✅ Telegram Notification</li>
              <li class="list-group-item">✅ Cron job retry + xử lý đơn treo</li>
              <li class="list-group-item">✅ Dark Gaming Theme (standalone)</li>
              <li class="list-group-item">✅ VND/USDT Currency Toggle</li>
              <li class="list-group-item">✅ Affiliate System</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once(__DIR__ . '/../admin/footer.php'); ?>
