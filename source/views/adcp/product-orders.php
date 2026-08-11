<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = '';
if (isset($_GET['refund']) && isset($_GET['id'])) { $id=intval($_GET['id']); $o=$CMSNT->get_row_safe("SELECT * FROM product_order WHERE id=?",[$id]); if($o&&$o['topup_status']!='refunded'){$CMSNT->update('product_order',['topup_status'=>'refunded','refund'=>1],"id=$id");if($o['buyer']>0&&$o['money']>0)$CMSNT->cong('users','money',$o['money'],"id=".$o['buyer']);$msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã hoàn tiền đơn #'.htmlspecialchars($o['trans_id']).'!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';} }
if (isset($_GET['delete']) && isset($_GET['id'])) { $CMSNT->update('product_order',['trash'=>1],"id=".intval($_GET['id'])); $msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã xóa!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['order_id']) && isset($_POST['note'])) { $CMSNT->update('product_order',['note'=>check_string($_POST['note'])],"id=".intval($_POST['order_id'])); $msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã lưu ghi chú!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }

$status=$_GET['status']??'';$transId=$_GET['trans_id']??'';$tierId=$_GET['tier_id']??'';
$where="o.trash=0";if($status)$where.=" AND o.topup_status='".check_string($status)."'";if($transId)$where.=" AND o.trans_id LIKE '%".check_string($transId)."%'";if($tierId)$where.=" AND o.topup_tier_id=".intval($tierId);
$orders=$CMSNT->get_list_safe("SELECT o.*, u.username, u.money as user_balance, t.label as tier_label, g.name as game_name FROM product_order o LEFT JOIN users u ON o.buyer=u.id LEFT JOIN topup_tiers t ON o.topup_tier_id=t.id LEFT JOIN games g ON t.game_id=g.id WHERE $where ORDER BY o.id DESC LIMIT 30",[]);

$totalOrders=$CMSNT->num_rows("SELECT id FROM product_order WHERE trash=0");
$pendingOrders=$CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status='pending' AND trash=0");
$processingOrders=$CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status='processing' AND trash=0");
$completedOrders=$CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status='success' AND trash=0");
$cancelledOrders=$CMSNT->num_rows("SELECT id FROM product_order WHERE topup_status IN ('failed','refunded') AND trash=0");

$body=['title'=>'Quản lý Đơn hàng','desc'=>'CMSNT Panel','keyword'=>''];$body['header']='';$body['footer']='';
require_once(__DIR__.'/../../models/is_admin.php');require_once(__DIR__.'/../admin/header.php');require_once(__DIR__.'/../admin/sidebar.php');require_once(__DIR__.'/../admin/nav.php');
?>
<div class="main-content app-content"><div class="container-fluid">
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
  <h1 class="page-title fw-semibold fs-18 mb-0">🛒 Quản lý đơn hàng</h1>
  <div class="ms-md-1 ms-0"><nav><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="?module=adcp&action=products">Sản phẩm</a></li><li class="breadcrumb-item active">Đơn hàng</li></ol></nav></div>
</div>
<?=$msg?>
<div class="row">
  <div class="col-md-2 col-4"><div class="card custom-card"><div class="card-body text-center"><p class="mb-1 fs-5 fw-semibold"><?=$totalOrders?></p><p class="mb-0 text-muted small">🛒 Tổng</p></div></div></div>
  <div class="col-md-2 col-4"><div class="card custom-card"><div class="card-body text-center"><p class="mb-1 fs-5 fw-semibold text-warning"><?=$pendingOrders?></p><p class="mb-0 text-muted small">⏳ Chờ XL</p></div></div></div>
  <div class="col-md-2 col-4"><div class="card custom-card"><div class="card-body text-center"><p class="mb-1 fs-5 fw-semibold text-primary"><?=$processingOrders?></p><p class="mb-0 text-muted small">⚙️ Đang XL</p></div></div></div>
  <div class="col-md-2 col-4"><div class="card custom-card"><div class="card-body text-center"><p class="mb-1 fs-5 fw-semibold text-success"><?=$completedOrders?></p><p class="mb-0 text-muted small">✅ HT</p></div></div></div>
  <div class="col-md-2 col-4"><div class="card custom-card"><div class="card-body text-center"><p class="mb-1 fs-5 fw-semibold text-danger"><?=$cancelledOrders?></p><p class="mb-0 text-muted small">❌ Hủy</p></div></div></div>
</div>
<div class="card custom-card"><div class="card-body">
  <form method="GET" class="row row-cols-lg-auto g-3">
    <input type="hidden" name="module" value="adcp"><input type="hidden" name="action" value="product-orders">
    <div class="col-lg col-md-4 col-6"><input class="form-control form-control-sm" name="trans_id" value="<?=htmlspecialchars($transId)?>" placeholder="Mã đơn..."></div>
    <div class="col-lg col-md-4 col-6"><select name="status" class="form-select form-select-sm"><option value="">Tất cả TT</option><option value="pending" <?=$status=='pending'?'selected':''?>>⏳ Chờ XL</option><option value="processing" <?=$status=='processing'?'selected':''?>>⚙️ Đang XL</option><option value="success" <?=$status=='success'?'selected':''?>>✅ HT</option><option value="failed" <?=$status=='failed'?'selected':''?>>❌ Thất bại</option><option value="refunded" <?=$status=='refunded'?'selected':''?>>↩️ Hoàn</option></select></div>
    <div class="col-12"><button class="btn btn-sm btn-primary"><i class="bx bx-search"></i> Lọc</button> <a href="?module=adcp&action=product-orders" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i> Bỏ lọc</a></div>
  </form>
</div></div>
<div class="card custom-card"><div class="card-body"><div class="table-responsive">
  <table class="table text-nowrap table-striped table-hover table-bordered mb-0">
    <thead class="table"><tr><th>Bên mua</th><th>Đơn hàng</th><th>Gói</th><th class="text-end">Thanh toán</th><th>Trạng thái</th><th>Ghi chú</th><th>Ngày</th><th>Tác vụ</th></tr></thead>
    <tbody>
      <?php if(empty($orders)):?><tr><td colspan="8" class="text-center py-4"><div class="empty-state"><i class="ri-inbox-line fs-48 text-muted"></i><p class="text-muted mt-2">Không có đơn hàng</p></div></td></tr>
      <?php else: foreach($orders as $o):
        $sb=['pending'=>'warning','processing'=>'primary','success'=>'success','failed'=>'danger','refunded'=>'secondary'];
        $st=['pending'=>'⏳ Chờ XL','processing'=>'⚙️ Đang XL','success'=>'✅ HT','failed'=>'❌ TB','refunded'=>'↩️ Hoàn'];
        $profit=$o['money']-$o['cost'];
      ?>
      <tr>
        <td><b><?=htmlspecialchars($o['username']??'User #'.$o['buyer'])?></b><br><small class="text-muted">ID <?=$o['buyer']?> | 💰 <?=number_format($o['user_balance'])?>đ</small></td>
        <td><b><?=htmlspecialchars($o['trans_id'])?></b><?php if($o['api_transid']):?><br><small class="text-muted">API: <?=htmlspecialchars($o['api_transid'])?></small><?php endif?><?php if($o['game_uid']):?><br><small>🎮 <?=htmlspecialchars($o['game_uid'])?></small><?php endif?></td>
        <td><?=htmlspecialchars($o['game_name']?:'')?><br><small><?=htmlspecialchars($o['tier_label']??$o['product_name']??'—')?></small></td>
        <td class="text-end">SL: <b><?=$o['amount']?></b><br>💳 <b class="text-primary"><?=number_format($o['money'])?>đ</b><?php if($o['cost']>0):?><br><small class="text-<?=$profit>=0?'success':'danger'?>">Lãi: <?=$profit>=0?'+':''?><?=number_format($profit)?>đ</small><?php endif?></td>
        <td><span class="badge bg-<?=$sb[$o['topup_status']]??'secondary'?>"><?=$st[$o['topup_status']]??$o['topup_status']?></span></td>
        <td><form method="POST"><input type="hidden" name="order_id" value="<?=$o['id']?>"><input type="text" name="note" class="form-control form-control-sm" value="<?=htmlspecialchars($o['note']??'')?>" placeholder="Ghi chú..." style="width:100px" onchange="this.form.submit()"></form></td>
        <td><small><?=date('d/m/Y H:i',strtotime($o['create_gettime']))?></small></td>
        <td><?php if($o['topup_status']!='refunded'):?><a href="?module=adcp&action=product-orders&refund=1&id=<?=$o['id']?>" class="btn btn-sm btn-warning" onclick="return confirm('Hoàn tiền?')">↩️</a><?php endif?> <a href="?module=adcp&action=product-orders&delete=1&id=<?=$o['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">🗑️</a></td>
      </tr>
      <?php endforeach; endif;?>
    </tbody>
  </table>
</div></div></div>
</div></div>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
