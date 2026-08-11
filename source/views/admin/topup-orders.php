<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$body = ['title' => 'Đơn nạp game — Admin'];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
require_once(__DIR__ . '/nav.php');

// Lấy danh sách đơn topup từ product_order
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 30;
$offset = ($page - 1) * $limit;

$where = "WHERE `topup_tier_id` IS NOT NULL AND `topup_tier_id` > 0";
$whereParams = [];

if (!empty($_GET['status'])) {
    $st = check_string($_GET['status']);
    if(in_array($st, ['pending','processing','success','failed','refunded'])) {
        $where .= " AND `topup_status` = ?";
        $whereParams[] = $st;
    }
}
if (!empty($_GET['search'])) {
    $q = '%' . check_string($_GET['search']) . '%';
    $where .= " AND (`trans_id` LIKE ? OR `game_uid` LIKE ? OR `product_name` LIKE ?)";
    $whereParams[] = $q; $whereParams[] = $q; $whereParams[] = $q;
}

$total = $CMSNT->num_rows_safe("SELECT id FROM `product_order` {$where}", $whereParams);
$orders = $CMSNT->get_list_safe("SELECT * FROM `product_order` {$where} ORDER BY `id` DESC LIMIT {$limit} OFFSET {$offset}", $whereParams);
$totalPages = ceil($total / $limit);

$todayOrders = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0 AND DATE(`create_gettime`) = CURDATE()");
$todayRevenue = $CMSNT->get_row("SELECT COALESCE(SUM(`pay`),0) as total FROM `product_order` WHERE `topup_tier_id` > 0 AND DATE(`create_gettime`) = CURDATE() AND `topup_status` = 'success'");
$pendingCount = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0 AND `topup_status` = 'pending'");
?>

<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb"><h1>📦 Đơn nạp game</h1>
    <a href="<?= BASE_URL() ?>" class="btn btn-sm btn-default" style="margin-left:12px">← Về shop</a></div>
  <div class="container-fluid">
    <!-- Stats -->
    <div class="row mb-3">
      <div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span><div class="info-box-content"><span class="info-box-text">Đơn hôm nay</span><span class="info-box-number"><?= $todayOrders ?></span></div></div></div>
      <div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span><div class="info-box-content"><span class="info-box-text">Doanh thu hôm nay</span><span class="info-box-number"><?= number_format($todayRevenue['total'] ?? 0) ?>đ</span></div></div></div>
      <div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span><div class="info-box-content"><span class="info-box-text">Chờ xử lý</span><span class="info-box-number"><?= $pendingCount ?></span></div></div></div>
      <div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-secondary"><i class="fas fa-list"></i></span><div class="info-box-content"><span class="info-box-text">Tổng đơn</span><span class="info-box-number"><?= $total ?></span></div></div></div>
    </div>

    <!-- Search + Filter -->
    <div class="card">
      <div class="card-header">
        <form method="GET" class="form-inline" style="display:flex;gap:10px;flex-wrap:wrap">
          <input type="hidden" name="module" value="admin">
          <input type="hidden" name="action" value="topup-orders">
          <input type="text" name="search" class="form-control" placeholder="Tìm mã ĐH hoặc UID..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width:250px">
          <select name="status" class="form-control" style="width:150px">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" <?= ($_GET['status']??'')=='pending'?'selected':'' ?>>⏳ Chờ xử lý</option>
            <option value="processing" <?= ($_GET['status']??'')=='processing'?'selected':'' ?>>🔄 Đang xử lý</option>
            <option value="success" <?= ($_GET['status']??'')=='success'?'selected':'' ?>>✅ Thành công</option>
            <option value="failed" <?= ($_GET['status']??'')=='failed'?'selected':'' ?>>❌ Thất bại</option>
            <option value="refunded" <?= ($_GET['status']??'')=='refunded'?'selected':'' ?>>↩ Đã hoàn</option>
          </select>
          <button type="submit" class="btn btn-primary">🔍 Lọc</button>
          <a href="?module=admin&action=topup-orders" class="btn btn-default">Xóa lọc</a>
        </form>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th><th>Mã ĐH</th><th>Game</th><th>Gói</th><th>UID</th><th>Giá</th><th>Trạng thái</th><th>Thời gian</th><th>Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if(empty($orders)): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">Chưa có đơn nạp game nào</td></tr>
            <?php else: foreach($orders as $o): 
              $note = json_decode($o['note'] ?? '{}', true);
              $statusColors = ['pending'=>'warning','processing'=>'info','success'=>'success','failed'=>'danger','refunded'=>'secondary'];
              $statusLabels = ['pending'=>'⏳ Chờ XL','processing'=>'🔄 Đang XL','success'=>'✅ OK','failed'=>'❌ Lỗi','refunded'=>'↩ Hoàn'];
            ?>
            <tr>
              <td><?= $o['id'] ?></td>
              <td><code><?= $o['trans_id'] ?></code></td>
              <td><?= htmlspecialchars($note['game_name'] ?? $o['product_name']) ?></td>
              <td><?= htmlspecialchars($note['tier_label'] ?? '') ?></td>
              <td><b><?= htmlspecialchars($o['game_uid'] ?? '') ?></b></td>
              <td class="text-right font-weight-bold"><?= number_format($o['pay']) ?>đ</td>
              <td><span class="badge badge-<?= $statusColors[$o['topup_status']] ?? 'secondary' ?>"><?= $statusLabels[$o['topup_status']] ?? $o['topup_status'] ?></span></td>
              <td><small><?= date('d/m/Y H:i', strtotime($o['create_gettime'])) ?></small></td>
              <td>
                <?php if($o['topup_status'] == 'pending'): ?>
                <a href="?module=admin&action=topup-orders&mark=success&id=<?= $o['id'] ?>" class="btn btn-xs btn-success" onclick="return confirm('Xác nhận đã nạp thành công?')">✅ Done</a>
                <?php endif; ?>
                <?php if(in_array($o['topup_status'], ['pending','processing','failed'])): ?>
                <a href="?module=admin&action=topup-orders&mark=refunded&id=<?= $o['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hoàn tiền đơn này?')">↩ Hoàn</a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
      <?php if($totalPages > 1): ?>
      <div class="card-footer">
        <nav><ul class="pagination pagination-sm m-0">
          <?php for($i=1; $i<=$totalPages; $i++): ?>
          <li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?module=admin&action=topup-orders&page=<?= $i ?>&status=<?= $_GET['status']??'' ?>&search=<?= urlencode($_GET['search']??'') ?>"><?= $i ?></a></li>
          <?php endfor; ?>
        </ul></nav>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
// Xử lý mark status
if(isset($_GET['mark'], $_GET['id'])) {
    $oid = intval($_GET['id']);
    $status = $_GET['mark'];
    $allowed = ['pending','processing','success','failed','refunded'];
    if(in_array($status, $allowed)) {
        $order = $CMSNT->get_row("SELECT * FROM `product_order` WHERE `id` = {$oid}");
        if($order) {
            $CMSNT->update('product_order', [
                'topup_status' => $status,
                'update_gettime' => gettime()
            ], "`id` = {$oid}");
            
            // Nếu hoàn tiền
            if($status == 'refunded' && $order['topup_status'] != 'refunded') {
                $CMSNT->cong('users', 'money', $order['pay'], "`id` = {$order['buyer']}");
                $CMSNT->insert('dongtien', [
                    'user_id' => $order['buyer'],
                    'sotientruoc' => 0,
                    'sotienthaydoi' => $order['pay'],
                    'sotiensau' => 0,
                    'thoigian' => gettime(),
                    'noidung' => "Hoàn tiền đơn #{$order['trans_id']}",
                    'transid' => $order['trans_id']
                ]);
            }
            echo '<script>location.href="?module=admin&action=topup-orders";</script>';
        }
    }
}
require_once(__DIR__ . '/footer.php');
?>
