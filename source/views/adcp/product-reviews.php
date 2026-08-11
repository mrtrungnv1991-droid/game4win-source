<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = ''; $hasTable = false;
try { $check = $CMSNT->get_row_safe("SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name='product_reviews'", []); $hasTable = !empty($check); } catch (Exception $e) { $hasTable = false; }

if ($hasTable) {
    if (isset($_GET['approve'])) { $CMSNT->update('product_reviews', ['status'=>1], "id=".intval($_GET['approve'])); $msg='<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã duyệt!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
    if (isset($_GET['delete'])) { $CMSNT->remove('product_reviews', "id=".intval($_GET['delete'])); $msg='<div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã xóa!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
    $status=$_GET['status']??'';$where="1=1";if($status==='pending')$where.=" AND pr.status=0";elseif($status==='approved')$where.=" AND pr.status=1";
    $reviews=$CMSNT->get_list_safe("SELECT pr.*, u.username, po.product_name FROM product_reviews pr LEFT JOIN users u ON pr.user_id=u.id LEFT JOIN product_order po ON pr.order_id=po.id WHERE $where ORDER BY FIELD(pr.status,0,1), pr.id DESC LIMIT 50",[]);
    $pendingCount=$CMSNT->num_rows("SELECT id FROM product_reviews WHERE status=0");
}

$body=['title'=>'Quản lý Reviews','desc'=>'CMSNT Panel','keyword'=>''];$body['header']='';$body['footer']='';
require_once(__DIR__.'/../../models/is_admin.php');require_once(__DIR__.'/../admin/header.php');require_once(__DIR__.'/../admin/sidebar.php');require_once(__DIR__.'/../admin/nav.php');
?>
<div class="main-content app-content"><div class="container-fluid">
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
  <h1 class="page-title fw-semibold fs-18 mb-0">⭐ Quản lý Reviews <?php if($hasTable):?><span class="badge bg-warning ms-2"><?=$pendingCount?> chờ duyệt</span><?php endif?></h1>
  <div class="ms-md-1 ms-0"><nav><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="#">ADCP</a></li><li class="breadcrumb-item active">Reviews</li></ol></nav></div>
</div>
<?=$msg?>
<?php if($hasTable):?>
<div class="card custom-card"><div class="card-header">
  <a href="?module=adcp&action=product-reviews" class="btn btn-sm btn-<?=$status===''?'primary':'outline-secondary'?>">Tất cả</a>
  <a href="?module=adcp&action=product-reviews&status=pending" class="btn btn-sm btn-<?=$status==='pending'?'warning':'outline-warning'?> ms-1">⏳ Chờ duyệt (<?=$pendingCount?>)</a>
  <a href="?module=adcp&action=product-reviews&status=approved" class="btn btn-sm btn-<?=$status==='approved'?'success':'outline-success'?> ms-1">✅ Đã duyệt</a>
</div></div>
<div class="card custom-card"><div class="card-body"><div class="table-responsive">
  <table class="table text-nowrap table-striped table-hover table-bordered mb-0">
    <thead class="table"><tr><th style="width:50px">ID</th><th>User</th><th>Sản phẩm</th><th style="width:100px">Rating</th><th>Comment</th><th style="width:100px">TT</th><th style="width:90px">Ngày</th><th style="width:90px">Tác vụ</th></tr></thead>
    <tbody>
      <?php if(empty($reviews)):?><tr><td colspan="8" class="text-center py-4"><div class="empty-state"><i class="ri-inbox-line fs-48 text-muted"></i><p class="text-muted mt-2">Chưa có review</p></div></td></tr>
      <?php else: foreach($reviews as $r):?>
      <tr>
        <td>#<?=$r['id']?></td><td><b><?=htmlspecialchars($r['username']??'N/A')?></b></td><td><?=htmlspecialchars($r['product_name']??'—')?></td>
        <td><?=str_repeat('⭐',$r['rating'])?> (<?=$r['rating']?>/5)</td>
        <td><?=htmlspecialchars(mb_strlen($r['comment']??'')>80?mb_substr($r['comment'],0,80).'...':($r['comment']??''))?></td>
        <td><span class="badge bg-<?=$r['status']?'success':'warning'?>"><?=$r['status']?'✅ Đã duyệt':'⏳ Chờ'?></span></td>
        <td><small><?=date('d/m/Y',strtotime($r['created_at']))?></small></td>
        <td><?php if(!$r['status']):?><a href="?module=adcp&action=product-reviews&approve=<?=$r['id']?>" class="btn btn-sm btn-success">✅</a><?php endif?> <a href="?module=adcp&action=product-reviews&delete=<?=$r['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')">🗑️</a></td>
      </tr>
      <?php endforeach; endif;?>
    </tbody>
  </table>
</div></div></div>
<?php else:?>
<div class="card custom-card"><div class="card-body text-center py-5"><h4>📋 Chưa có dữ liệu</h4><p class="text-muted">Bảng <code>product_reviews</code> chưa được tạo.</p></div></div>
<?php endif?>
</div></div>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
