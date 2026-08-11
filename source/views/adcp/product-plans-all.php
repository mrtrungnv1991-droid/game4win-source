<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = '';
if (isset($_GET['toggle']) && isset($_GET['id'])) { $t = $CMSNT->get_row_safe("SELECT status FROM topup_tiers WHERE id = ?", [intval($_GET['id'])]); if ($t) { $ns = $t['status']?0:1; $CMSNT->update('topup_tiers', ['status'=>$ns], "id=".intval($_GET['id'])); $msg = '<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã '.($ns?'bật':'tắt').' gói!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; } }
if (isset($_GET['delete']) && isset($_GET['id'])) { $id=intval($_GET['id']); $oc=$CMSNT->num_rows("SELECT id FROM topup_orders WHERE tier_id=$id"); if($oc>0){$msg='<div class="alert alert-danger">Không thể xóa — có '.$oc.' đơn!</div>';}else{$CMSNT->remove('topup_tiers',"id=$id");$msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã xóa!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';} }

$q=$_GET['q']??'';$gameId=$_GET['game_id']??'';$typeFilter=$_GET['type']??'';
$where="1=1";if($gameId)$where.=" AND t.game_id=".intval($gameId);if($q)$where.=" AND (t.label LIKE '%".check_string($q)."%' OR g.name LIKE '%".check_string($q)."%')";if($typeFilter)$where.=" AND t.type='".check_string($typeFilter)."'";
$tiers=$CMSNT->get_list_safe("SELECT t.*, g.name as game_name, g.icon as game_icon, (SELECT COUNT(*) FROM product_order WHERE topup_tier_id=t.id AND trash=0) as order_count FROM topup_tiers t LEFT JOIN games g ON t.game_id=g.id WHERE $where ORDER BY t.sort_order ASC, t.id DESC LIMIT 50",[]);
$totalTiers=$CMSNT->num_rows("SELECT id FROM topup_tiers");$activeTiers=$CMSNT->num_rows("SELECT id FROM topup_tiers WHERE status=1");$inactiveTiers=$CMSNT->num_rows("SELECT id FROM topup_tiers WHERE status=0");
$games=$CMSNT->get_list_safe("SELECT id,name FROM games WHERE status=1 ORDER BY name",[]);

$body=['title'=>'Quản lý Gói sản phẩm','desc'=>'CMSNT Panel','keyword'=>''];$body['header']='';$body['footer']='';
require_once(__DIR__.'/../../models/is_admin.php');require_once(__DIR__.'/../admin/header.php');require_once(__DIR__.'/../admin/sidebar.php');require_once(__DIR__.'/../admin/nav.php');
?>
<div class="main-content app-content"><div class="container-fluid">
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
  <h1 class="page-title fw-semibold fs-18 mb-0">📦 Quản lý gói sản phẩm</h1>
  <div class="ms-md-1 ms-0"><nav><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="?module=adcp&action=products">Sản phẩm</a></li><li class="breadcrumb-item active">Gói</li></ol></nav></div>
</div>
<?=$msg?>
<div class="row">
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?=$totalTiers?></p><p class="mb-0 text-muted">📦 Tổng số gói</p></div><div class="ms-2"><span class="avatar text-bg-info rounded-circle fs-20"><i class="bx bx-package"></i></span></div></div></div></div></div>
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?=$activeTiers?></p><p class="mb-0 text-muted">🟢 Đang hoạt động</p></div><div class="ms-2"><span class="avatar text-bg-success rounded-circle fs-20"><i class="bx bx-check"></i></span></div></div></div></div></div>
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?=$inactiveTiers?></p><p class="mb-0 text-muted">🔴 Đã tắt</p></div><div class="ms-2"><span class="avatar text-bg-danger rounded-circle fs-20"><i class="bx bx-x"></i></span></div></div></div></div></div>
  <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?= $CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status='success' AND trash=0") ?></p><p class="mb-0 text-muted">💰 Đơn thành công</p></div><div class="ms-2"><span class="avatar text-bg-warning rounded-circle fs-20"><i class="bx bx-dollar"></i></span></div></div></div></div></div>
</div>
<div class="card custom-card"><div class="card-body">
  <form method="GET" class="row row-cols-lg-auto g-3">
    <input type="hidden" name="module" value="adcp"><input type="hidden" name="action" value="product-plans-all">
    <div class="col-lg col-md-4 col-6"><input class="form-control form-control-sm" name="q" value="<?=htmlspecialchars($q)?>" placeholder="Tìm gói..."></div>
    <div class="col-lg col-md-4 col-6"><select name="game_id" class="form-select form-select-sm"><option value="">Tất cả game</option><?php foreach($games as $g):?><option value="<?=$g['id']?>" <?=$gameId==$g['id']?'selected':''?>><?=htmlspecialchars($g['name'])?></option><?php endforeach?></select></div>
    <div class="col-lg col-md-4 col-6"><select name="type" class="form-select form-select-sm"><option value="">Tất cả loại</option><option value="gem" <?=$typeFilter=='gem'?'selected':''?>>💎 Gem</option><option value="pack" <?=$typeFilter=='pack'?'selected':''?>>📦 Pack</option><option value="allpack" <?=$typeFilter=='allpack'?'selected':''?>>🎁 All Pack</option></select></div>
    <div class="col-12"><button class="btn btn-sm btn-primary"><i class="bx bx-search"></i> Lọc</button> <a href="?module=adcp&action=product-plans-all" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i> Bỏ lọc</a></div>
  </form>
</div></div>
<div class="card custom-card"><div class="card-body"><div class="table-responsive">
  <table class="table text-nowrap table-striped table-hover table-bordered mb-0">
    <thead class="table"><tr><th style="width:30px"></th><th>Tên gói</th><th style="width:80px">Nguồn</th><th style="width:90px">Giá</th><th style="width:90px">Loại</th><th style="width:80px">Tồn</th><th style="width:90px">TT</th><th style="width:90px">Đã bán</th><th style="width:90px">Tác vụ</th></tr></thead>
    <tbody>
      <?php if(empty($tiers)):?><tr><td colspan="9" class="text-center py-4"><div class="empty-state"><i class="ri-inbox-line fs-48 text-muted"></i><p class="text-muted mt-2">Không có gói nào</p></div></td></tr>
      <?php else: foreach($tiers as $t):
        $tl=['gem'=>['💎 Giao ngay','success'],'pack'=>['📦 Đặt hàng','warning'],'allpack'=>['🎁 All Pack','primary']];
        $typeLabel=$tl[$t['type']]??['❓ Khác','secondary'];
      ?>
      <tr>
        <td><input type="checkbox" value="<?=$t['id']?>"></td>
        <td><b><?=htmlspecialchars($t['label'])?></b><br><small class="text-muted"><a href="?module=adcp&action=products&q=<?=urlencode($t['game_name'])?>" class="text-muted">🎮 <?=htmlspecialchars($t['game_name'])?></a></small></td>
        <td><?=$totalProviders>0?'<span class="badge bg-info">🔌 API</span>':'<span class="badge bg-secondary">✋ TC</span>'?></td>
        <td><b class="text-success"><?=number_format($t['price'])?>đ</b></td>
        <td><span class="badge bg-<?=$typeLabel[1]?>"><?=$typeLabel[0]?></span></td>
        <td><span class="badge bg-<?=($t['amount']??0)>0?'info':'danger'?>">📦 <?=max(0,$t['amount']??0)?></span></td>
        <td><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" <?=$t['status']?'checked':''?> onchange="window.location='?module=adcp&action=product-plans-all&toggle=1&id=<?=$t['id']?>'"></div></td>
        <td><?=$t['order_count']>0?'<span class="badge bg-success">🛒 '.$t['order_count'].'</span>':'<small class="text-muted">—</small>'?></td>
        <td><a href="?module=admin&action=game-edit&id=<?=$t['game_id']?>" class="btn btn-sm btn-warning" title="Sửa">✏️</a> <a href="?module=adcp&action=product-plans-all&delete=1&id=<?=$t['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">🗑️</a></td>
      </tr>
      <?php endforeach; endif;?>
    </tbody>
  </table>
</div></div></div>
</div></div>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
