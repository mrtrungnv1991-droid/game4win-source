<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$msg = '';

// Xử lý toggle status
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $cat = $CMSNT->get_row_safe("SELECT status FROM categories WHERE id = ?", [intval($_GET['id'])]);
    if ($cat) {
        $newStatus = $cat['status'] ? 0 : 1;
        $CMSNT->update('categories', ['status' => $newStatus], "id = " . intval($_GET['id']));
        $msg = '<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã ' . ($newStatus ? 'bật' : 'tắt') . ' chuyên mục!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>';
    }
}

// Xử lý xóa
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $childCount = $CMSNT->num_rows("SELECT id FROM categories WHERE parent_id = $id");
    $productCount = $CMSNT->num_rows("SELECT id FROM games WHERE category = (SELECT name FROM categories WHERE id = $id)");
    if ($childCount > 0) {
        $msg = '<div class="alert alert-danger">Không thể xóa — chuyên mục có ' . $childCount . ' chuyên mục con!</div>';
    } elseif ($productCount > 0) {
        $msg = '<div class="alert alert-danger">Không thể xóa — chuyên mục có ' . $productCount . ' sản phẩm!</div>';
    } else {
        $CMSNT->remove('categories', "id = $id");
        $msg = '<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã xóa chuyên mục!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>';
    }
}

// Xử lý thêm mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = check_string($_POST['name'] ?? '');
    $parent_id = intval($_POST['parent_id'] ?? 0);
    $icon = check_string($_POST['icon'] ?? '');
    $slug = check_string($_POST['slug'] ?? '');
    if (empty($slug)) $slug = to_slug($name);
    if (!empty($name)) {
        $CMSNT->insert('categories', [
            'parent_id' => $parent_id, 'icon' => $icon, 'name' => $name,
            'slug' => $slug, 'status' => 1, 'stt' => 0,
            'create_date' => date('Y-m-d H:i:s')
        ]);
        $msg = '<div class="alert alert-success alert-dismissible fade show custom-alert-icon shadow-sm mb-3" role="alert">Đã thêm chuyên mục <b>' . htmlspecialchars($name) . '</b>!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i class="bi bi-x"></i></button></div>';
    }
}

// Lấy tất cả categories
$allCats = $CMSNT->get_list_safe("SELECT * FROM categories ORDER BY stt ASC, id ASC", []);
function buildTree($cats, $parent_id = 0) {
    $tree = []; foreach ($cats as $c) {
        if ($c['parent_id'] == $parent_id) { $c['children'] = buildTree($cats, $c['id']); $tree[] = $c; }
    } return $tree;
}
$catTree = buildTree($allCats, 0);
function countProducts($CMSNT, $catName) {
    return $CMSNT->num_rows("SELECT id FROM games WHERE status = 1 AND category = '" . check_string($catName) . "'");
}
$hasCats = !empty($allCats);

$body = ['title' => 'Quản lý Chuyên mục — Admin', 'desc' => 'CMSNT Panel', 'keyword' => ''];
$body['header'] = ''; $body['footer'] = '';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/../admin/header.php');
require_once(__DIR__.'/../admin/sidebar.php');
require_once(__DIR__.'/../admin/nav.php');
?>

<div class="main-content app-content">
  <div class="container-fluid">
    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
      <h1 class="page-title fw-semibold fs-18 mb-0">📂 Quản lý chuyên mục</h1>
      <div class="ms-md-1 ms-0">
        <nav><ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="#">ADCP</a></li>
          <li class="breadcrumb-item active" aria-current="page">Chuyên mục</li>
        </ol></nav>
      </div>
    </div>

    <?= $msg ?>

    <div class="d-flex justify-content-end mb-3">
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCatModal">
        <i class="bx bx-plus"></i> Thêm chuyên mục cha
      </button>
    </div>

    <?php if ($hasCats): ?>
    <div class="card custom-card">
      <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
          <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-all">📋 Tất cả</a></li>
          <li class="nav-item"><a class="nav-link text-warning" data-bs-toggle="tab" href="#tab-orphan">🔧 Mồ côi</a></li>
        </ul>
      </div>
      <div class="card-body">
        <?php
        function renderTree($nodes, $CMSNT, $level = 0) {
            foreach ($nodes as $node):
                $pCount = countProducts($CMSNT, $node['name']);
                $childCount = count($node['children']);
                $hasChildren = $childCount > 0;
                $paddingLeft = $level * 28;
        ?>
        <div class="category-item mb-1" style="padding-left: <?= $paddingLeft ?>px">
          <div class="d-flex align-items-center p-2 border rounded" style="gap: 10px; background: #f8f9fa;">
            <span style="cursor: grab; color: #adb5bd; font-size: 1.1rem">⠿</span>
            <span style="font-size: 1.4rem"><?= htmlspecialchars($node['icon'] ?: '📁') ?></span>
            <div style="flex: 1">
              <b><?= htmlspecialchars($node['name']) ?></b>
              <small class="text-muted ms-2">
                📦 <?= $pCount ?> SP
                <?php if ($hasChildren): ?> | 📂 <?= $childCount ?> con<?php endif ?>
              </small>
            </div>
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" <?= $node['status'] ? 'checked' : '' ?>
                     onchange="window.location='?module=adcp&action=categories&toggle=1&id=<?= $node['id'] ?>'">
            </div>
            <?php if ($hasChildren): ?>
            <button class="btn btn-xs btn-outline-secondary toggle-children" data-target="children-<?= $node['id'] ?>" style="padding:2px 8px">▼</button>
            <?php endif ?>
            <a href="?module=adcp&action=categories&delete=1&id=<?= $node['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa?')" style="padding:2px 8px">🗑️</a>
          </div>
          <?php if ($hasChildren): ?>
          <div class="children-container" id="children-<?= $node['id'] ?>" style="display: none">
            <?php renderTree($node['children'], $CMSNT, $level + 1) ?>
          </div>
          <?php endif ?>
        </div>
        <?php endforeach;
        }
        renderTree($catTree, $CMSNT);
        ?>
        <?php if (empty($catTree)): ?>
        <div class="text-center text-muted py-4">Chưa có chuyên mục nào. Nhấn "Thêm chuyên mục cha" để tạo.</div>
        <?php endif ?>
      </div>
    </div>

    <?php else: ?>
    <?php
    $gameCats = $CMSNT->get_list("SELECT DISTINCT category, COUNT(*) as cnt FROM games WHERE status = 1 AND category IS NOT NULL AND category != '' GROUP BY category ORDER BY cnt DESC");
    ?>
    <div class="card custom-card">
      <div class="card-body"><div class="table-responsive">
        <table class="table text-nowrap table-striped table-hover table-bordered mb-0">
          <thead class="table"><tr><th>Icon</th><th>Tên danh mục</th><th>Số Game</th><th>Hành động</th></tr></thead>
          <tbody>
            <?php if(empty($gameCats)): ?>
            <tr><td colspan="4" class="text-center text-muted py-4">Chưa có danh mục nào</td></tr>
            <?php else: foreach($gameCats as $c): ?>
            <tr>
              <td style="font-size:1.5rem">📁</td>
              <td><b><?= htmlspecialchars($c['category']) ?></b></td>
              <td><span class="badge bg-info"><?= $c['cnt'] ?> games</span></td>
              <td><a href="?module=adcp&action=products&category=<?= urlencode($c['category']) ?>" class="btn btn-sm btn-primary">Xem</a></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div></div>
    </div>
    <?php endif ?>
  </div>
</div>

<!-- Modal Thêm -->
<div class="modal fade" id="addCatModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST">
      <div class="modal-header"><h5 class="modal-title">➕ Thêm chuyên mục mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Tên <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Chuyên mục cha</label><select name="parent_id" class="form-select"><option value="0">— Gốc —</option><?php foreach ($allCats as $pc): ?><option value="<?= $pc['id'] ?>"><?= htmlspecialchars($pc['name']) ?></option><?php endforeach ?></select></div>
        <div class="mb-3"><label class="form-label">Icon (emoji)</label><input type="text" name="icon" class="form-control" placeholder="📁"></div>
        <div class="mb-3"><label class="form-label">Slug</label><input type="text" name="slug" class="form-control" placeholder="tien-ich"></div>
      </div>
      <div class="modal-footer"><button type="submit" name="add_category" class="btn btn-primary">✅ Thêm</button><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button></div>
    </form>
  </div></div>
</div>

<script>
document.querySelectorAll('.toggle-children').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var t = document.getElementById(this.getAttribute('data-target'));
    if (t) { var s = t.style.display === 'none'; t.style.display = s ? 'block' : 'none'; this.textContent = s ? '▲' : '▼'; }
  });
});
</script>

<?php require_once(__DIR__.'/../admin/footer.php'); ?>
