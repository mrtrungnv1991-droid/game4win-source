<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = '';

if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $game = $CMSNT->get_row_safe("SELECT status FROM games WHERE id = ?", [intval($_GET['id'])]);
    if ($game) { $ns = $game['status'] ? 0 : 1; $CMSNT->update('games', ['status' => $ns], "id = " . intval($_GET['id'])); $msg = '<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã '.($ns?'bật':'tắt').' sản phẩm!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
}
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']); $tc = $CMSNT->num_rows("SELECT id FROM topup_tiers WHERE game_id = $id");
    if ($tc > 0) { $msg = '<div class="alert alert-danger">Không thể xóa — có '.$tc.' gói nạp!</div>'; }
    else { $CMSNT->remove('games', "id = $id"); $msg = '<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã xóa!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
}

$q = $_GET['q'] ?? ''; $category = $_GET['category'] ?? '';
$where = "1=1";
if ($category) $where .= " AND g.category = '" . check_string($category) . "'";
if ($q) $where .= " AND g.name LIKE '%" . check_string($q) . "%'";

$games = $CMSNT->get_list_safe("SELECT g.*, (SELECT COUNT(*) FROM topup_tiers WHERE game_id = g.id AND status = 1) as tier_count, (SELECT COUNT(*) FROM topup_tiers WHERE game_id = g.id) as total_tiers FROM games g WHERE $where ORDER BY g.sort_order ASC, g.id DESC LIMIT 50", []);
$totalGames = $CMSNT->num_rows("SELECT id FROM games WHERE status = 1");
$totalHidden = $CMSNT->num_rows("SELECT id FROM games WHERE status = 0");
$totalTiers = $CMSNT->num_rows("SELECT id FROM topup_tiers WHERE status = 1");
$totalProviders = $CMSNT->num_rows("SELECT id FROM topup_providers");
$allCats = $CMSNT->get_list_safe("SELECT DISTINCT category FROM games WHERE status = 1 AND category IS NOT NULL AND category != '' ORDER BY category", []);

$body = ['title' => 'Quản lý Sản phẩm', 'desc' => 'CMSNT Panel', 'keyword' => ''];
$body['header'] = ''; $body['footer'] = '';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/../admin/header.php');
require_once(__DIR__.'/../admin/sidebar.php');
require_once(__DIR__.'/../admin/nav.php');
?>

<div class="main-content app-content">
  <div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
      <h1 class="page-title fw-semibold fs-18 mb-0">🎮 Quản lý sản phẩm</h1>
      <div class="ms-md-1 ms-0">
        <nav><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="#">ADCP</a></li><li class="breadcrumb-item active">Sản phẩm</li></ol></nav>
      </div>
    </div>
    <?= $msg ?>

    <div class="row">
      <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?= $totalGames ?></p><p class="mb-0 text-muted">🟢 Đang hiển thị</p></div><div class="ms-2"><span class="avatar text-bg-primary rounded-circle fs-20"><i class="bx bx-show"></i></span></div></div></div></div></div>
      <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?= $totalHidden ?></p><p class="mb-0 text-muted">🔴 Đã ẩn</p></div><div class="ms-2"><span class="avatar text-bg-secondary rounded-circle fs-20"><i class="bx bx-hide"></i></span></div></div></div></div></div>
      <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?= $totalTiers ?></p><p class="mb-0 text-muted">📦 Gói nạp</p></div><div class="ms-2"><span class="avatar text-bg-success rounded-circle fs-20"><i class="bx bx-package"></i></span></div></div></div></div></div>
      <div class="col-xl-3 col-6"><div class="card custom-card"><div class="card-body"><div class="d-flex align-items-center"><div class="flex-fill"><p class="mb-1 fs-5 fw-semibold"><?= $totalProviders ?></p><p class="mb-0 text-muted">🔌 Providers</p></div><div class="ms-2"><span class="avatar text-bg-warning rounded-circle fs-20"><i class="bx bx-plug"></i></span></div></div></div></div></div>
    </div>

    <div class="card custom-card">
      <div class="card-header justify-content-between">
        <div class="card-title">🔍 Bộ lọc</div>
        <div class="d-flex gap-2">
          <a href="?module=admin&action=game-manager" class="btn btn-sm btn-primary"><i class="bx bx-plus"></i> Thêm</a>
        </div>
      </div>
      <div class="card-body">
        <form method="GET" class="row row-cols-lg-auto g-3">
          <input type="hidden" name="module" value="adcp"><input type="hidden" name="action" value="products">
          <div class="col-lg col-md-4 col-6"><input class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Tìm sản phẩm..."></div>
          <div class="col-lg col-md-4 col-6"><select name="category" class="form-select form-select-sm"><option value="">Tất cả danh mục</option><?php foreach($allCats as $c): ?><option value="<?= htmlspecialchars($c['category']) ?>" <?= $category==$c['category']?'selected':'' ?>><?= htmlspecialchars($c['category']) ?></option><?php endforeach ?></select></div>
          <div class="col-12"><button class="btn btn-sm btn-primary"><i class="bx bx-search"></i> Lọc</button> <a href="?module=adcp&action=products" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i> Bỏ lọc</a></div>
        </form>
      </div>
    </div>

    <div class="card custom-card">
      <div class="card-body"><div class="table-responsive">
        <table class="table text-nowrap table-striped table-hover table-bordered mb-0">
          <thead class="table"><tr><th style="width:30px"><input type="checkbox"></th><th style="width:35px"></th><th>Tên sản phẩm</th><th>Chuyên mục</th><th style="width:90px">Nguồn</th><th style="width:80px">Gói</th><th style="width:90px">Trạng thái</th><th style="width:110px">Ngày tạo</th><th style="width:120px">Thao tác</th></tr></thead>
          <tbody>
            <?php if (empty($games)): ?>
            <tr><td colspan="9" class="text-center py-4"><div class="empty-state"><i class="ri-inbox-line fs-48 text-muted"></i><p class="text-muted mt-2">Không tìm thấy sản phẩm</p></div></td></tr>
            <?php else: foreach($games as $g): ?>
            <tr>
              <td><input type="checkbox" value="<?= $g['id'] ?>"></td>
              <td style="cursor:grab;color:#adb5bd">⠿</td>
              <td>
                <div class="d-flex align-items-center" style="gap:8px">
                  <span style="font-size:1.4rem"><?= htmlspecialchars($g['icon'] ?: '🎮') ?></span>
                  <div><b><?= htmlspecialchars($g['name']) ?></b><?php if ($g['full_name']): ?><br><small class="text-muted"><?= htmlspecialchars($g['full_name']) ?></small><?php endif ?><br><small class="text-muted">#<?= $g['id'] ?></small></div>
                </div>
              </td>
              <td><span class="badge bg-light text-dark"><?= htmlspecialchars($g['category'] ?: '—') ?></span></td>
              <td><?= $totalProviders > 0 ? '<span class="badge bg-info">🔌 API</span>' : '<span class="badge bg-secondary">✋ TC</span>' ?></td>
              <td><a href="?module=adcp&action=product-plans-all&game_id=<?= $g['id'] ?>" class="badge bg-primary text-decoration-none">📦 <?= $g['tier_count'] ?>/<?= $g['total_tiers'] ?></a></td>
              <td><div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" <?= $g['status']?'checked':'' ?> onchange="window.location='?module=adcp&action=products&toggle=1&id=<?= $g['id'] ?>'"></div></td>
              <td><small><?= date('d/m/Y', strtotime($g['created_at'])) ?></small></td>
              <td>
                <a href="?module=adcp&action=product-plans-all&game_id=<?= $g['id'] ?>" class="btn btn-sm btn-info" title="Gói">📦</a>
                <a href="?module=admin&action=game-edit&id=<?= $g['id'] ?>" class="btn btn-sm btn-warning" title="Sửa">✏️</a>
                <a href="?module=adcp&action=products&delete=1&id=<?= $g['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa?')" title="Xóa">🗑️</a>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div></div>
    </div>
  </div>
</div>
<?php require_once(__DIR__.'/../admin/footer.php'); ?>
