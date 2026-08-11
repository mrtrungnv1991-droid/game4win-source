<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = ''; $status = $_GET['status'] ?? ''; $where = "1=1";
if ($status==='active') $where .= " AND status=1 AND NOW() BETWEEN start_time AND end_time";
elseif ($status==='upcoming') $where .= " AND status=1 AND NOW() < start_time";
elseif ($status==='ended') $where .= " AND (status=0 OR NOW() > end_time)";
if (!empty($_GET['q'])) $where .= " AND name LIKE '%".check_string($_GET['q'])."%'";
if (isset($_GET['delete'])) { $CMSNT->remove('flash_sales',"id=".intval($_GET['delete'])); $msg='<div class="alert alert-success">Đã xóa!</div>'; }
if (isset($_GET['toggle'])) { $fs=$CMSNT->get_row("SELECT status FROM flash_sales WHERE id=".intval($_GET['toggle'])); $CMSNT->update('flash_sales',['status'=>$fs['status']?0:1],"id=".intval($_GET['toggle'])); }
$flashSales = $CMSNT->get_list("SELECT * FROM flash_sales WHERE $where ORDER BY id DESC LIMIT 50");
$body=['title'=>'Quản lý Flash Sale — Admin'];
require_once(__DIR__.'/../admin/header.php'); require_once(__DIR__.'/../admin/sidebar.php');
?>
<div class="main-content app-content"><div class="container-fluid">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1 class="page-title mb-0">⚡ Quản lý Flash Sale <a href="?module=adcp&action=flash-sale-add" class="btn btn-sm btn-danger ml-2">+ Thêm</a></h1>
  </div>
  <?= $msg ?>
  <div class="card custom-card"><div class="card-body">
    <form method="GET" class="row g-2">
      <input type="hidden" name="module" value="adcp"><input type="hidden" name="action" value="flash-sales">
      <div class="col-auto"><input type="text" name="q" class="form-control form-control-sm" placeholder="Tên CT..." value="<?= htmlspecialchars($_GET['q']??'') ?>"></div>
      <div class="col-auto"><select name="status" class="form-control form-control-sm"><option value="">Tất cả</option><option value="active" <?= $status==='active'?'selected':'' ?>>Đang diễn ra</option><option value="upcoming" <?= $status==='upcoming'?'selected':'' ?>>Sắp diễn ra</option><option value="ended" <?= $status==='ended'?'selected':'' ?>>Đã kết thúc</option></select></div>
      <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">🔍 Lọc</button></div>
      <div class="col-auto"><a href="?module=adcp&action=flash-sales" class="btn btn-sm btn-outline-secondary">Bỏ lọc</a></div>
    </form>
  </div></div>
  <div class="card custom-card"><div class="card-body table-responsive p-0">
    <table class="table table-hover table-striped mb-0">
      <thead><tr><th>Tên chương trình</th><th style="width:110px">Giảm giá</th><th style="width:180px">Thời gian</th><th style="width:80px">GH</th><th style="width:80px">GH/user</th><th style="width:130px">Trạng thái</th><th style="width:110px">Ngày tạo</th><th style="width:100px">Hành động</th></tr></thead>
      <tbody>
        <?php if(empty($flashSales)): ?><tr><td colspan="8" class="text-center text-muted py-4">Chưa có Flash Sale</td></tr>
        <?php else: foreach($flashSales as $fs): $now=date('Y-m-d H:i:s'); $stLabel='Đang diễn ra'; $stClass='success';
          if($fs['status']==0){$stLabel='Đã tắt';$stClass='secondary';} elseif($now<$fs['start_time']){$stLabel='Sắp diễn ra';$stClass='info';} elseif($now>$fs['end_time']){$stLabel='Đã kết thúc';$stClass='secondary';} ?>
        <tr><td><strong>⚡ <?= htmlspecialchars($fs['name']) ?></strong></td>
          <td><?= $fs['discount_type']==='percent'?$fs['discount_value'].'%':number_format($fs['discount_value']).'đ' ?><?php if($fs['max_discount']>0):?><br><small class="text-muted">Tối đa <?= number_format($fs['max_discount']) ?>đ</small><?php endif ?></td>
          <td><small>▶ <?= date('d/m/Y H:i',strtotime($fs['start_time'])) ?><br>⏹ <?= date('d/m/Y H:i',strtotime($fs['end_time'])) ?></small></td>
          <td><?= $fs['usage_limit']?number_format($fs['usage_limit']):'∞' ?></td><td><?= $fs['limit_per_user']?:'∞' ?></td>
          <td><span class="badge bg-<?= $stClass ?>"><?= $stLabel ?></span><br><a href="?module=adcp&action=flash-sales&toggle=<?= $fs['id'] ?>"><small><?= $fs['status']?'🟢 ON':'🔴 OFF' ?></small></a></td>
          <td><small><?= date('d/m/Y H:i',strtotime($fs['created_at'])) ?></small></td>
          <td><a href="?module=adcp&action=flash-sale-edit&id=<?= $fs['id'] ?>" class="btn btn-xs btn-info mr-1">✏️</a><a href="?module=adcp&action=flash-sales&delete=<?= $fs['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Xóa?')">🗑️</a></td></tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div></div>
</div></div>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
