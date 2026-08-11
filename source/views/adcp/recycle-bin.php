<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$msg = '';
// Restore
if (isset($_GET['restore']) && isset($_GET['id'])) {
    $CMSNT->update('product_order', ['trash'=>0], "id = ".intval($_GET['id']));
    $msg = '<div class="alert alert-success">Đã khôi phục đơn hàng!</div>';
}
// Permanent delete
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $CMSNT->remove('product_order', "id = ".intval($_GET['id']));
    $msg = '<div class="alert alert-danger">Đã xóa vĩnh viễn!</div>';
}

$trashed = $CMSNT->get_list_safe(
    "SELECT po.*, u.username as buyer_name FROM product_order po 
     LEFT JOIN users u ON po.buyer = u.id 
     WHERE po.trash = 1 
     ORDER BY po.id DESC LIMIT 50", []);

$body = ['title' => 'Thùng rác — Admin'];
require_once(__DIR__ . '/../admin/header.php');
require_once(__DIR__ . '/../admin/sidebar.php');

?>
<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1>🗑️ Thùng rác</h1>
    <a href="<?= BASE_URL() ?>" class="btn btn-sm btn-default" style="margin-left:12px">← Về shop</a>
  </div>
  <div class="container-fluid">
    <?= $msg ?>
    <div class="card">
      <div class="card-header"><h3 class="card-title">Đơn hàng đã xóa (<?= count($trashed) ?>)</h3></div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover">
          <thead><tr><th>ID</th><th>Mã ĐH</th><th>Khách</th><th>Sản phẩm</th><th>Giá</th><th>Ngày xóa</th><th>Hành động</th></tr></thead>
          <tbody>
            <?php if(empty($trashed)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">Thùng rác trống</td></tr>
            <?php else: foreach($trashed as $o): ?>
            <tr>
              <td><?= $o['id'] ?></td>
              <td><code><?= $o['trans_id'] ?></code></td>
              <td><?= htmlspecialchars($o['buyer_name'] ?? '#'.$o['buyer']) ?></td>
              <td><?= htmlspecialchars($o['product_name']) ?></td>
              <td><?= number_format($o['pay']) ?>đ</td>
              <td><small><?= date('d/m/Y H:i', strtotime($o['update_gettime'])) ?></small></td>
              <td>
                <a href="?module=adcp&action=recycle-bin&restore=1&id=<?= $o['id'] ?>" class="btn btn-xs btn-success" onclick="return confirm('Khôi phục?')">↩ Khôi phục</a>
                <a href="?module=adcp&action=recycle-bin&delete=1&id=<?= $o['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('XÓA VĨNH VIỄN?')">❌ Xóa</a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once(__DIR__ . '/../admin/footer.php'); ?>
