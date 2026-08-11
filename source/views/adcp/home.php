<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

// ==================== STATS ====================
$totalUsers = $CMSNT->num_rows("SELECT id FROM users");
$totalOrders = $CMSNT->num_rows("SELECT id FROM product_order");
$totalRevenue = $CMSNT->get_row("SELECT COALESCE(SUM(pay),0) as total FROM product_order WHERE topup_status = 'success'")['total'];
$totalProfit = $CMSNT->get_row("SELECT COALESCE(SUM(pay - cost),0) as total FROM product_order WHERE topup_status = 'success'")['total'];
$pendingOrders = $CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status IN ('pending','processing')");
$pendingTickets = $CMSNT->num_rows("SELECT id FROM tickets WHERE status IN ('open','answered')");
$pendingWithdrawals = $CMSNT->num_rows("SELECT id FROM aff_log WHERE (reason LIKE '%rút%' OR reason LIKE '%withdraw%')");
$unreadMessages = $CMSNT->num_rows("SELECT id FROM messages WHERE is_read = 0");

// Chart data: revenue last 7 days
$chartLabels = []; $chartData = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d/m', strtotime($d));
    $rev = $CMSNT->get_row("SELECT COALESCE(SUM(pay),0) as total FROM product_order WHERE DATE(create_gettime) = '$d' AND topup_status = 'success'")['total'];
    $chartData[] = (int)$rev;
}

// Top 5 games by orders
$topGames = $CMSNT->get_list_safe(
    "SELECT product_name, COUNT(*) as cnt, COALESCE(SUM(pay),0) as total 
     FROM product_order WHERE topup_tier_id IS NOT NULL 
     GROUP BY product_name ORDER BY cnt DESC LIMIT 5", []);

// Latest 5 users
$latestUsers = $CMSNT->get_list_safe("SELECT * FROM users ORDER BY id DESC LIMIT 5", []);

// System info
$phpVersion = PHP_VERSION;
$dbSize = $CMSNT->get_row("SELECT ROUND(SUM(data_length + index_length)/1024/1024, 2) as size FROM information_schema.tables WHERE table_schema = 'game4win_topup'")['size'] ?? 0;

// Today stats
$todayOrders = $CMSNT->num_rows("SELECT id FROM product_order WHERE DATE(create_gettime) = CURDATE()");
$todayRevenue = $CMSNT->get_row("SELECT COALESCE(SUM(pay),0) as total FROM product_order WHERE DATE(create_gettime) = CURDATE() AND topup_status = 'success'")['total'];
$todayUsers = $CMSNT->num_rows("SELECT id FROM users WHERE DATE(create_date) = CURDATE()");

// Topup stats
$totalTopupOrders = $CMSNT->num_rows("SELECT id FROM product_order WHERE topup_tier_id IS NOT NULL");
$totalGames = $CMSNT->num_rows("SELECT id FROM games WHERE status = 1");
$totalTiers = $CMSNT->num_rows("SELECT id FROM topup_tiers WHERE status = 1");

// Recent orders (top 10)
$recentOrders = $CMSNT->get_list_safe(
    "SELECT po.*, u.username as buyer_name FROM product_order po 
     LEFT JOIN users u ON po.buyer = u.id 
     ORDER BY po.id DESC LIMIT 10", []);

// Recent tickets
$recentTickets = $CMSNT->get_list_safe(
    "SELECT t.*, u.username FROM tickets t 
     LEFT JOIN users u ON t.user_id = u.id 
     ORDER BY t.update_date DESC LIMIT 5", []);

$body = ['title' => 'Dashboard — Admin'];
require_once(__DIR__ . '/../admin/header.php');
require_once(__DIR__ . '/../admin/sidebar.php');
?>
<style>
.row>[class*="col-"]{display:flex;padding:6px}
.row>[class*="col-"]>.small-box,.row>[class*="col-"]>.info-box,.row>[class*="col-"]>.card{flex:1;width:100%}
.small-box{height:100%;border:2px solid rgba(0,0,0,0.08);border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.06)}
.small-box .inner{text-align:center}
.small-box .inner h3{font-size:1.8rem;font-weight:900}
.small-box .inner p{font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:.8rem}
</style>

<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1>📊 Dashboard</h1>
    <a href="<?= BASE_URL() ?>" class="btn btn-sm btn-default" style="margin-left:12px">← Về shop</a>
  </div>
  <div class="container-fluid">

    <!-- ===== 4 Overview Cards ===== -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner"><h3><?= number_format($totalRevenue) ?>đ</h3><p>Doanh thu</p></div>
          <div class="icon"><i class="fas fa-dollar-sign"></i></div>
          <a href="?module=admin&action=topup-orders" class="small-box-footer">Xem chi tiết <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner"><h3><?= $totalOrders ?></h3><p>Tổng đơn hàng</p></div>
          <div class="icon"><i class="fas fa-shopping-cart"></i></div>
          <a href="?module=admin&action=topup-orders" class="small-box-footer">Xem chi tiết <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner"><h3><?= $pendingOrders ?></h3><p>Đơn chờ xử lý</p></div>
          <div class="icon"><i class="fas fa-clock"></i></div>
          <a href="?module=admin&action=topup-orders&status=pending" class="small-box-footer">Xem tất cả <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner"><h3><?= $totalUsers ?></h3><p>Thành viên</p></div>
          <div class="icon"><i class="fas fa-users"></i></div>
          <a href="?module=admin&action=users" class="small-box-footer">Quản lý <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>

    <!-- ===== Quick Action Widgets ===== -->
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
          <div class="inner"><h3><?= $pendingOrders ?></h3><p>📦 Đơn chờ xử lý</p></div>
          <div class="icon"><i class="fas fa-clock"></i></div>
          <a href="?module=admin&action=topup-orders&status=pending" class="small-box-footer">Xem tất cả <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner"><h3><?= $pendingTickets ?></h3><p>🎫 Tickets chờ</p></div>
          <div class="icon"><i class="fas fa-ticket-alt"></i></div>
          <a href="?module=adcp&action=ticket-list" class="small-box-footer">Xem tất cả <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-purple" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9)!important">
          <div class="inner"><h3>0</h3><p>⭐ Reviews chờ</p></div>
          <div class="icon"><i class="fas fa-star"></i></div>
          <a href="?module=adcp&action=product-reviews" class="small-box-footer">Xem tất cả <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
          <div class="inner"><h3><?= $pendingWithdrawals ?></h3><p>💵 Rút tiền chờ</p></div>
          <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
          <a href="?module=admin&action=affiliate-withdraw" class="small-box-footer">Xem tất cả <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
    </div>

    <!-- ===== Today Stats ===== -->
    <div class="row">
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><h3 class="card-title">📅 Hôm nay</h3></div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2"><span>Đơn hàng:</span><b><?= $todayOrders ?></b></div>
            <div class="d-flex justify-content-between mb-2"><span>Doanh thu:</span><b style="color:#28a745"><?= number_format($todayRevenue) ?>đ</b></div>
            <div class="d-flex justify-content-between"><span>Thành viên mới:</span><b><?= $todayUsers ?></b></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><h3 class="card-title">🎮 Game Topup</h3></div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2"><span>Games:</span><b><?= $totalGames ?></b></div>
            <div class="d-flex justify-content-between mb-2"><span>Gói nạp:</span><b><?= $totalTiers ?></b></div>
            <div class="d-flex justify-content-between"><span>Đơn nạp:</span><b><?= $totalTopupOrders ?></b></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><h3 class="card-title">🔌 Providers</h3></div>
          <div class="card-body">
            <?php $providers = $CMSNT->get_list_safe("SELECT * FROM topup_providers ORDER BY id", []); ?>
            <?php foreach($providers as $p): ?>
            <div class="d-flex justify-content-between mb-1"><span><?= htmlspecialchars($p['name']) ?> (<?= $p['type'] ?>)</span><span class="badge badge-<?= $p['status']?'success':'secondary' ?>"><?= $p['status']?'ON':'OFF' ?></span></div>
            <?php endforeach; ?>
            <?php if(empty($providers)): ?><p class="text-muted">Chưa có provider</p><?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Recent Orders ===== -->
    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">🛒 Đơn hàng gần đây</h3>
            <a href="?module=admin&action=topup-orders" class="btn btn-sm btn-primary">Xem tất cả</a>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover mb-0">
              <thead><tr><th>Mã ĐH</th><th>Khách</th><th>Sản phẩm</th><th class="text-right">Giá</th><th>Trạng thái</th><th>TG</th></tr></thead>
              <tbody>
                <?php if(empty($recentOrders)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Chưa có đơn hàng</td></tr>
                <?php else: foreach($recentOrders as $o): 
                  $note = json_decode($o['note'] ?? '{}', true);
                  $stColors = ['pending'=>'warning','processing'=>'info','success'=>'success','failed'=>'danger','refunded'=>'secondary'];
                  $stLabels = ['pending'=>'⏳','processing'=>'🔄','success'=>'✅','failed'=>'❌','refunded'=>'↩'];
                ?>
                <tr>
                  <td><code style="font-size:.7rem"><?= substr($o['trans_id'],0,12) ?></code></td>
                  <td><?= htmlspecialchars($o['buyer_name'] ?? '#'.$o['buyer']) ?></td>
                  <td style="font-size:.85rem"><?= htmlspecialchars(strlen($note['game_name']??$o['product_name'])>30?substr($note['game_name']??$o['product_name'],0,30).'...':($note['game_name']??$o['product_name'])) ?></td>
                  <td class="text-right"><?= number_format($o['pay']) ?>đ</td>
                  <td><span class="badge badge-<?= $stColors[$o['topup_status']??'pending'] ?>"><?= $stLabels[$o['topup_status']??'pending'] ?></span></td>
                  <td><small><?= date('d/m H:i', strtotime($o['create_gettime'])) ?></small></td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <!-- Recent Tickets -->
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">🎫 Tickets gần đây</h3>
            <a href="?module=adcp&action=ticket-list" class="btn btn-sm btn-info">Xem tất cả</a>
          </div>
          <div class="card-body p-0">
            <?php if(empty($recentTickets)): ?>
            <p class="text-muted text-center py-4">Chưa có ticket</p>
            <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach($recentTickets as $t): 
                $tColors = ['open'=>'warning','answered'=>'info','closed'=>'secondary'];
              ?>
              <li class="list-group-item" style="cursor:pointer" onclick="location.href='?module=adcp&action=ticket-detail&id=<?= $t['id'] ?>'">
                <div class="d-flex justify-content-between">
                  <span><b>#<?= $t['id'] ?></b> <?= htmlspecialchars($t['subject']) ?></span>
                  <span class="badge badge-<?= $tColors[$t['status']] ?>"><?= $t['status'] ?></span>
                </div>
                <small class="text-muted"><?= htmlspecialchars($t['username']) ?> · <?= date('d/m H:i', strtotime($t['updated_at'])) ?></small>
              </li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="card mt-3">
          <div class="card-header"><h3 class="card-title">⚡ Truy cập nhanh</h3></div>
          <div class="card-body">
            <a href="?module=admin&action=game-manager" class="btn btn-block btn-outline-primary mb-2">🎮 Quản lý Games</a>
            <a href="?module=admin&action=provider-manager" class="btn btn-block btn-outline-info mb-2">🔌 Quản lý Providers</a>
            <a href="?module=adcp&action=messages" class="btn btn-block btn-outline-danger mb-2">📬 Messages (<?= $unreadMessages ?> mới)</a>
            <a href="?module=admin&action=promotions" class="btn btn-block btn-outline-warning mb-2">⚡ Flash Sale</a>
            <a href="?module=admin&action=coupons" class="btn btn-block btn-outline-purple mb-2">🏷️ Mã giảm giá</a>
            <a href="?module=admin&action=settings" class="btn btn-block btn-outline-secondary mb-2">⚙️ Cài đặt</a>
            <a href="?module=admin&action=users" class="btn btn-block btn-outline-dark">👥 Thành viên</a>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== Chart + Top Games + Users ===== -->
    <div class="row mt-3">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header"><h3 class="card-title">📈 Doanh thu 7 ngày</h3></div>
          <div class="card-body"><canvas id="revenueChart" height="200"></canvas></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><h3 class="card-title">🖥️ Hệ thống</h3></div>
          <div class="card-body">
            <div class="d-flex justify-content-between mb-2"><span>PHP:</span><b><?= $phpVersion ?></b></div>
            <div class="d-flex justify-content-between mb-2"><span>DB Size:</span><b><?= $dbSize ?> MB</b></div>
            <div class="d-flex justify-content-between mb-2"><span>Games:</span><b><?= $totalGames ?></b></div>
            <div class="d-flex justify-content-between mb-2"><span>Tiers:</span><b><?= $totalTiers ?></b></div>
            <div class="d-flex justify-content-between mb-2"><span>Users:</span><b><?= $totalUsers ?></b></div>
            <div class="d-flex justify-content-between"><span>Orders:</span><b><?= $totalOrders ?></b></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-3">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">🎯 Top Games</h3></div>
          <div class="card-body table-responsive p-0">
            <table class="table table-sm mb-0">
              <thead><tr><th>#</th><th>Game</th><th>Đơn</th><th>Doanh thu</th></tr></thead>
              <tbody>
                <?php if(empty($topGames)): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
                <?php else: $rank=1; foreach($topGames as $g): ?>
                <tr>
                  <td><?= $rank++ ?></td>
                  <td style="font-size:.85rem"><?= htmlspecialchars(strlen($g['product_name'])>30?substr($g['product_name'],0,30).'...':$g['product_name']) ?></td>
                  <td><?= $g['cnt'] ?></td>
                  <td class="text-right"><?= number_format($g['total']) ?>đ</td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">👥 Thành viên mới</h3></div>
          <div class="card-body table-responsive p-0">
            <table class="table table-sm mb-0">
              <thead><tr><th>ID</th><th>Username</th><th>Balance</th><th>Ngày tham gia</th></tr></thead>
              <tbody>
                <?php if(empty($latestUsers)): ?>
                <tr><td colspan="4" class="text-center text-muted py-3">Chưa có</td></tr>
                <?php else: foreach($latestUsers as $u): ?>
                <tr>
                  <td>#<?= $u['id'] ?></td>
                  <td><b><?= htmlspecialchars($u['username']) ?></b><?= $u['admin']?' <span class="badge badge-danger">ADMIN</span>':'' ?></td>
                  <td><?= number_format($u['money']) ?>đ</td>
                  <td><small><?= date('d/m/Y', strtotime($u['create_date'])) ?></small></td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

<script>
new Chart(document.getElementById('revenueChart'), {
  type: 'line',
  data: {
    labels: <?= json_encode($chartLabels) ?>,
    datasets: [{
      label: 'Doanh thu (VNĐ)',
      data: <?= json_encode($chartData) ?>,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59,130,246,0.1)',
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointBackgroundColor: '#3b82f6'
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { callback: function(v){ return v.toLocaleString()+'đ'; } } }
    }
  }
});
</script>
      </div>
    </div>

  </div>
</div>

<?php require_once(__DIR__ . '/../admin/footer.php'); ?>
