<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$body = ['title' => 'Quản lý Tickets — Admin'];
$status = $_GET['status'] ?? ''; $where = '';
if ($status === 'open') $where = "WHERE t.status = 'open'";
elseif ($status === 'closed') $where = "WHERE t.status = 'closed'";
$tickets = $CMSNT->get_list_safe("SELECT t.*, u.username FROM tickets t LEFT JOIN users u ON t.user_id = u.id $where ORDER BY FIELD(t.status,'open','answered','closed'), t.updated_at DESC LIMIT 50", []);
$openCount = $CMSNT->num_rows("SELECT id FROM tickets WHERE status IN ('open','answered')");
require_once(__DIR__ . '/../admin/header.php');
require_once(__DIR__ . '/../admin/sidebar.php');
?>
<div class="main-content app-content">
  <div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
      <h1 class="page-title mb-0">🎫 Quản lý Tickets</h1>
      <a href="<?= BASE_URL() ?>" class="btn btn-sm btn-outline-light">← Về shop</a>
    </div>
    <div class="row">
      <div class="col-lg-3 col-6">
        <div class="small-box bg-warning"><div class="inner"><h3><?= $openCount ?></h3><p>Đang mở</p></div><div class="icon"><i class="fas fa-ticket-alt"></i></div></div>
      </div>
    </div>
    <div class="card custom-card">
      <div class="card-header">
        <a href="?module=adcp&action=ticket-list" class="btn btn-sm btn-<?= $status===''?'primary':'outline-secondary' ?>">Tất cả</a>
        <a href="?module=adcp&action=ticket-list&status=open" class="btn btn-sm btn-<?= $status==='open'?'warning':'outline-warning' ?> ml-1">🟡 Đang mở (<?= $openCount ?>)</a>
        <a href="?module=adcp&action=ticket-list&status=closed" class="btn btn-sm btn-<?= $status==='closed'?'secondary':'outline-secondary' ?> ml-1">⚫ Đã đóng</a>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped mb-0">
          <thead><tr><th style="width:50px">ID</th><th>User</th><th>Tiêu đề</th><th style="width:80px">Ưu tiên</th><th style="width:100px">Trạng thái</th><th style="width:120px">Cập nhật</th><th style="width:60px"></th></tr></thead>
          <tbody>
            <?php if(empty($tickets)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Chưa có ticket nào</td></tr>
            <?php else: foreach($tickets as $t):
              $pLabels=['low'=>'🟢 Thấp','medium'=>'🟡 TB','high'=>'🔴 Cao'];
              $sLabels=['open'=>'🟡 Mở','answered'=>'🔵 Đã TL','closed'=>'⚫ Đóng'];
              $sBg=['open'=>'warning','answered'=>'info','closed'=>'secondary'];
            ?>
            <tr>
              <td>#<?= $t['id'] ?></td><td><b><?= htmlspecialchars($t['username']??'N/A') ?></b></td>
              <td><?= htmlspecialchars($t['subject']) ?></td><td><?= $pLabels[$t['priority']] ?></td>
              <td><span class="badge bg-<?= $sBg[$t['status']] ?>"><?= $sLabels[$t['status']] ?></span></td>
              <td><small><?= date('d/m/Y H:i', strtotime($t['updated_at'])) ?></small></td>
              <td><a href="?module=adcp&action=ticket-detail&id=<?= $t['id'] ?>" class="btn btn-xs btn-primary">Xem</a></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once(__DIR__ . '/../admin/footer.php'); ?>
