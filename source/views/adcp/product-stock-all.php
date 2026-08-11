<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stock_id']) && isset($_POST['stock_value'])) { $CMSNT->update('topup_tiers', ['amount'=>intval($_POST['stock_value'])], "id=".intval($_POST['stock_id'])); $msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã cập nhật!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
if (isset($_GET['delete']) && isset($_GET['id'])) { $CMSNT->remove('topup_tiers',"id=".intval($_GET['id'])); $msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã xóa!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }

$gameId=$_GET['game_id']??'';$statusFilter=$_GET['status']??'';
$where="1=1";if($gameId)$where.=" AND t.game_id=".intval($gameId);if($statusFilter==='active')$where.=" AND t.status=1";elseif($statusFilter==='inactive')$where.=" AND t.status=0";
$stocks=$CMSNT->get_list_safe("SELECT t.*, g.name as game_name, g.icon as game_icon FROM topup_tiers t LEFT JOIN games g ON t.game_id=g.id WHERE $where ORDER BY t.id DESC LIMIT 30",[]);
$totalStock=$CMSNT->get_row_safe("SELECT SUM(amount) as total FROM topup_tiers",[]);
$activeStock=$CMSNT->get_row_safe("SELECT SUM(amount) as total FROM topup_tiers WHERE status=1",[]);
$soldItems=$CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status='success' AND trash=0");
$games=$CMSNT->get_list_safe("SELECT id,name FROM games WHERE status=1 ORDER BY name",[]);

$body=['title'=>'Quản lý Kho hàng','desc'=>'CMSNT Panel','keyword'=>''];$body['header']='';$body['footer']='';
require_once(__DIR__.'/../../models/is_admin.php');require_once(__DIR__.'/../admin/header.php');require_once(__DIR__.'/../admin/sidebar.php');require_once(__DIR__.'/../admin/nav.php');
?>
<div class="main-content app-content"><div class="container-fluid">
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
  <h1 class="page-title fw-semibold fs-18 mb-0">📊 Quản lý kho hàng</h1>
  <div class="ms-md-1 ms-0"><nav><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="?module=adcp&action=product-plans-all">Gói SP</a></li><li class="breadcrumb-item active">Kho</li></ol></nav></div>
</div>
<?=$msg?>
<div class="row">
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?=number_format($totalStock['total']??0)?></p><p class="mb-0 text-muted">📦 Tổng số lượng</p></div><div class="ms-2"><span class="avatar text-bg-info rounded-circle fs-20"><i class="bx bx-cube"></i></span></div></div></div></div></div>
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?=number_format($activeStock['total']??0)?></p><p class="mb-0 text-muted">🟢 Còn hàng</p></div><div class="ms-2"><span class="avatar text-bg-success rounded-circle fs-20"><i class="bx bx-check"></i></span></div></div></div></div></div>
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?=$soldItems?></p><p class="mb-0 text-muted">🛒 Đã bán</p></div><div class="ms-2"><span class="avatar text-bg-danger rounded-circle fs-20"><i class="bx bx-cart"></i></span></div></div></div></div></div>
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold">0</p><p class="mb-0 text-muted">💀 Die</p></div><div class="ms-2"><span class="avatar text-bg-dark rounded-circle fs-20"><i class="bx bx-error"></i></span></div></div></div></div></div>
</div>
<div class="card custom-card"><div class="card-body">
  <form method="GET" class="row row-cols-lg-auto g-3 align-items-end">
    <input type="hidden" name="module" value="adcp"><input type="hidden" name="action" value="product-stock-all">
    <div class="col-lg col-md-4 col-6"><label class="form-label small">Sản phẩm</label><select name="game_id" class="form-select form-select-sm"><option value="">Tất cả</option><?php foreach($games as $g):?><option value="<?=$g['id']?>" <?=$gameId==$g['id']?'selected':''?>><?=htmlspecialchars($g['name'])?></option><?php endforeach?></select></div>
    <div class="col-lg col-md-4 col-6"><label class="form-label small">Trạng thái</label><select name="status" class="form-select form-select-sm"><option value="">Tất cả</option><option value="active" <?=$statusFilter=='active'?'selected':''?>>Còn hàng</option><option value="inactive" <?=$statusFilter=='inactive'?'selected':''?>>Đã tắt</option></select></div>
    <div class="col-12"><button class="btn btn-sm btn-primary"><i class="bx bx-filter"></i> Lọc</button> <a href="?module=adcp&action=product-stock-all" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i> Bỏ lọc</a></div>
  </form>
</div></div>
<div class="card custom-card"><div class="card-body"><div class="table-responsive">
  <table class="table text-nowrap table-striped table-hover table-bordered mb-0">
    <thead class="table"><tr><th style="width:60px">ID</th><th>Gói sản phẩm</th><th style="width:200px">Giá trị kho</th><th style="width:100px">Trạng thái</th><th style="width:100px">Thao tác</th></tr></thead>
    <tbody>
      <?php if(empty($stocks)):?><tr><td colspan="5" class="text-center py-4"><div class="empty-state"><i class="ri-inbox-line fs-48 text-muted"></i><p class="text-muted mt-2">Không có dữ liệu</p></div></td></tr>
      <?php else: foreach($stocks as $s):?>
      <tr>
        <td><code>#<?=$s['id']?></code></td>
        <td><b><?=htmlspecialchars($s['label'])?></b><br><small class="text-muted"><?=htmlspecialchars($s['game_name'])?></small></td>
        <td>
          <form method="POST" class="d-flex gap-1"><input type="hidden" name="stock_id" value="<?=$s['id']?>"><input type="number" name="stock_value" class="form-control form-control-sm" value="<?=$s['amount']?>" min="0" style="width:120px"><button type="submit" class="btn btn-outline-success btn-sm">💾</button></form>
        </td>
        <td><span class="badge bg-<?=$s['status']?'success':'secondary'?>"><?=$s['status']?'🟢 Còn hàng':'⚫ Tắt'?></span></td>
        <td><a href="?module=adcp&action=product-stock-all&delete=1&id=<?=$s['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">🗑️</a></td>
      </tr>
      <?php endforeach; endif;?>
    </tbody>
  </table>
</div></div></div>
</div></div>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
