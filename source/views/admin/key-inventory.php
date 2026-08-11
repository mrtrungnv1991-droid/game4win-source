<?php if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');

$code = check_string($_GET['code'] ?? '');
$msg = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Thêm nhánh mới (platform / region)
    if ($_POST['action'] === 'add_branch' && !empty($_POST['branch_type']) && !empty($_POST['branch_name'])) {
        $bt = check_string($_POST['branch_type']);
        $bn = check_string($_POST['branch_name']);
        if (in_array($bt, ['platform', 'region'])) {
            $exists = $CMSNT->get_row_safe("SELECT id FROM inventory_branches WHERE branch_type = ? AND name = ?", [$bt, $bn]);
            if (!$exists) {
                $CMSNT->insert('inventory_branches', ['branch_type' => $bt, 'name' => $bn, 'status' => 1]);
                $msg = "✅ Đã thêm nhánh mới: $bn";
            } else {
                $msg = "⚠️ Nhánh '$bn' đã tồn tại";
            }
        }
    }
    if ($_POST['action'] === 'add_key' && !empty($_POST['product_code'])) {
        $keys = array_filter(array_map('trim', explode("\n", $_POST['keys'] ?? '')));
        $added = 0;
        foreach ($keys as $k) {
            if (strlen($k) < 5) continue;
            $CMSNT->insert('key_inventory', [
                'product_code' => check_string($_POST['product_code']),
                'key_code' => $k,
                'platform' => check_string($_POST['platform'] ?? 'Steam'),
                'region' => check_string($_POST['region'] ?? 'GLOBAL'),
                'status' => 'available',
            ]);
            $added++;
        }
        $msg = "✅ Đã nhập $added keys vào kho";
    }
    if ($_POST['action'] === 'delete_key' && !empty($_POST['key_id'])) {
        $CMSNT->remove('key_inventory', " `id` = " . intval($_POST['key_id']));
        $msg = "Đã xóa key";
    }
    if ($_POST['action'] === 'block_key' && !empty($_POST['key_id'])) {
        $CMSNT->update('key_inventory', ['status' => 'blocked'], " `id` = " . intval($_POST['key_id']));
        $msg = "Đã block key";
    }
}

// Load product info
$product = null;
if ($code) {
    $product = $CMSNT->get_row_safe("SELECT * FROM products WHERE code = ?", [$code]);
}

// Load keys
$filter_status = $_GET['status'] ?? '';
$sql = "SELECT * FROM key_inventory WHERE 1=1";
$params = [];
if ($code) { $sql .= " AND product_code = ?"; $params[] = $code; }
if ($filter_status) { $sql .= " AND status = ?"; $params[] = $filter_status; }
$sql .= " ORDER BY id DESC LIMIT 200";
$keys = $CMSNT->get_list_safe($sql, $params);

// Stats
$stats = [
    'available' => $CMSNT->num_rows_safe("SELECT id FROM key_inventory WHERE status = 'available'" . ($code ? " AND product_code = ?" : ""), $code ? [$code] : []),
    'sold' => $CMSNT->num_rows_safe("SELECT id FROM key_inventory WHERE status = 'sold'" . ($code ? " AND product_code = ?" : ""), $code ? [$code] : []),
    'blocked' => $CMSNT->num_rows_safe("SELECT id FROM key_inventory WHERE status = 'blocked'" . ($code ? " AND product_code = ?" : ""), $code ? [$code] : []),
];

$body = ['title' => '🎮 Kho Game Key | ' . $CMSNT->site('title')];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
?>

<div class="main-content app-content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>🎮 Kho Game Key <?= $product ? '— ' . htmlspecialchars($product['name']) : '(tất cả)' ?></h3>
    <div>
        <?php if ($code): ?><a href="<?= base_url_admin('key-inventory') ?>" class="btn btn-sm btn-outline-secondary">← Tất cả kho</a><?php endif; ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKeyModal">+ Nhập Keys</button>
    </div>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<div class="alert alert-info">
    <strong>📦 Module riêng cho GAME KEY</strong> (không phải account!). Mỗi key = 1 mã bản quyền dùng 1 lần.
    Platform: <span class="badge bg-dark"><?= $product ? htmlspecialchars($product['platform'] ?: 'Steam') : 'Steam/Epic/GOG' ?></span>
    Region: <span class="badge bg-secondary"><?= $product ? htmlspecialchars($product['region'] ?: 'GLOBAL') : 'GLOBAL' ?></span>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body text-center">
        <h4><?= $stats['available'] ?></h4><small>Keys sẵn sàng</small></div></div></div>
    <div class="col-md-3"><div class="card bg-primary text-white"><div class="card-body text-center">
        <h4><?= $stats['sold'] ?></h4><small>Đã bán</small></div></div></div>
    <div class="col-md-3"><div class="card bg-danger text-white"><div class="card-body text-center">
        <h4><?= $stats['blocked'] ?></h4><small>Blocked</small></div></div></div>
</div>

<!-- Filter -->
<div class="mb-3">
    <a href="?module=admin&action=key-inventory<?= $code ? '&code='.$code : '' ?>" class="btn btn-sm <?= !$filter_status?'btn-dark':'btn-outline-dark' ?>">Tất cả</a>
    <a href="?module=admin&action=key-inventory<?= $code ? '&code='.$code : '' ?>&status=available" class="btn btn-sm <?= $filter_status=='available'?'btn-success':'btn-outline-success' ?>">Available</a>
    <a href="?module=admin&action=key-inventory<?= $code ? '&code='.$code : '' ?>&status=sold" class="btn btn-sm <?= $filter_status=='sold'?'btn-primary':'btn-outline-primary' ?>">Sold</a>
    <a href="?module=admin&action=key-inventory<?= $code ? '&code='.$code : '' ?>&status=blocked" class="btn btn-sm <?= $filter_status=='blocked'?'btn-danger':'btn-outline-danger' ?>">Blocked</a>
</div>

<!-- Keys table -->
<div class="card"><div class="card-body"><div class="table-responsive">
<table class="table table-bordered table-hover">
    <thead class="table-dark"><tr><th>ID</th><th>Product</th><th>Key Code</th><th>Platform</th><th>Region</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php if (empty($keys)): ?>
        <tr><td colspan="7" class="text-center text-muted">Kho trống — bấm "+ Nhập Keys"</td></tr>
    <?php else: foreach ($keys as $k):
        $badge = ['available'=>'success','sold'=>'primary','blocked'=>'danger','used'=>'secondary'][$k['status']] ?? 'secondary';
    ?>
    <tr>
        <td>#<?= $k['id'] ?></td>
        <td><code><?= $k['product_code'] ?></code></td>
        <td><code><?= $k['status'] === 'available' ? substr($k['key_code'],0,8).'••••' : htmlspecialchars(substr($k['key_code'],0,20)) ?></code></td>
        <td><?= $k['platform'] ?></td>
        <td><?= $k['region'] ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= $k['status'] ?></span></td>
        <td>
            <?php if ($k['status'] === 'available'): ?>
            <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="block_key">
                <input type="hidden" name="key_id" value="<?= $k['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">🚫</button>
            </form>
            <?php endif; ?>
            <form method="POST" style="display:inline" onsubmit="return confirm('Xóa key?')">
                <input type="hidden" name="action" value="delete_key">
                <input type="hidden" name="key_id" value="<?= $k['id'] ?>">
                <button class="btn btn-sm btn-outline-secondary">🗑</button>
            </form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>
</div></div></div>

</div></div>

<!-- Add Key Modal — NHÁNH CHIA RIÊNG CHO GAME KEY: Platform > Region > Sản phẩm -->
<div class="modal fade" id="addKeyModal" tabindex="-1">
<div class="modal-dialog modal-lg"><div class="modal-content">
<form method="POST">
<div class="modal-header bg-primary text-white"><h5>+ Nhập Game Keys</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <input type="hidden" name="action" value="add_key">

    <div class="alert alert-info py-2 small">
        <strong>🎮 Nhánh chia Game Key:</strong> Chọn <strong>Platform</strong> → <strong>Region</strong> → <strong>Sản phẩm</strong> (chỉ hiện game key của platform đó) → nhập keys.
    </div>

    <!-- NHÁNH 1: Platform (load từ DB, có thể thêm mới) -->
    <div class="mb-2"><label class="form-label">🎯 Nhánh 1: Platform</label>
        <div class="input-group">
            <select name="platform" id="keyPlatform" class="form-select" required onchange="filterKeyByPlatform()">
                <option value="">— Chọn platform —</option>
                <?php foreach ($CMSNT->get_list_safe("SELECT name FROM inventory_branches WHERE branch_type='platform' AND status=1 ORDER BY name", []) as $b): ?>
                <option><?= htmlspecialchars($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-success" onclick="addBranch('platform')" title="Thêm platform mới">+ Thêm</button>
        </div>
    </div>

    <!-- NHÁNH 2: Region (load từ DB, có thể thêm mới) -->
    <div class="mb-2"><label class="form-label">🌍 Nhánh 2: Region</label>
        <div class="input-group">
            <select name="region" id="keyRegion" class="form-select" required>
                <?php foreach ($CMSNT->get_list_safe("SELECT name FROM inventory_branches WHERE branch_type='region' AND status=1 ORDER BY name", []) as $b): ?>
                <option><?= htmlspecialchars($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-success" onclick="addBranch('region')" title="Thêm region mới">+ Thêm</button>
        </div>
    </div>

    <!-- NHÁNH 3: Sản phẩm (lọc theo platform) -->
    <div class="mb-2"><label class="form-label">📦 Nhánh 3: Sản phẩm Game Key</label>
        <div class="input-group">
            <select name="product_code" id="keyProductSelect" class="form-select" required>
                <option value="">— Chọn platform trước —</option>
                <?php $prods = $CMSNT->get_list_safe("SELECT code, name, platform FROM products WHERE product_type IN ('game_key','software','subscription') AND status = 1 ORDER BY platform, name", []); ?>
                <?php foreach ($prods as $p): ?>
                <option value="<?= $p['code'] ?>" data-platform="<?= htmlspecialchars($p['platform']) ?>" style="display:none" disabled><?= htmlspecialchars($p['name']) ?> (<?= $p['code'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-success" onclick="createNewKeyProduct()" title="Tạo sản phẩm Game Key mới">+ Tạo mới</button>
        </div>
        <small class="text-muted">Chưa có sản phẩm? Bấm <strong>"+ Tạo mới"</strong> — sẽ mở form tạo với Platform/Region đã chọn sẵn.</small>
    </div>

    <div class="mb-2"><label class="form-label">Keys (mỗi dòng 1 key)</label>
        <textarea name="keys" class="form-control" rows="8" placeholder="XXXXX-XXXXX-XXXXX-XXXXX&#10;YYYYY-YYYYY-YYYYY-YYYYY" required></textarea></div>
</div>
<div class="modal-footer"><button class="btn btn-primary" type="submit">Nhập kho</button></div>
</form>
</div></div></div>

<script>
// Nhánh chia Game Key: lọc sản phẩm theo platform đã chọn
function filterKeyByPlatform(){
    var pf = document.getElementById('keyPlatform').value;
    var sel = document.getElementById('keyProductSelect');
    var opts = sel.options;
    var visibleCount = 0;
    for (var i = 0; i < opts.length; i++) {
        var optPf = opts[i].getAttribute('data-platform');
        if (pf && optPf === pf) {
            opts[i].style.display = '';
            opts[i].disabled = false;
            visibleCount++;
        } else if (pf) {
            opts[i].style.display = 'none';
            opts[i].disabled = true;
        } else {
            opts[i].style.display = 'none';
            opts[i].disabled = true;
        }
    }
    // Reset selection
    sel.selectedIndex = 0;
    if (!pf) {
        sel.options[0].textContent = '— Chọn platform trước —';
    } else if (visibleCount === 0) {
        sel.options[0].textContent = '— Chưa có sản phẩm ' + pf + ' — bấm "+ Tạo mới" bên cạnh';
    } else {
        sel.options[0].textContent = '— Chọn sản phẩm ' + pf + ' (' + visibleCount + ') —';
    }
}

// Tạo sản phẩm Game Key mới — pre-fill platform/region đã chọn, quay về kho sau khi tạo
function createNewKeyProduct(){
    var pf = document.getElementById('keyPlatform').value || 'Steam';
    var rg = document.getElementById('keyRegion').value || 'GLOBAL';
    var url = '<?= base_url_admin('product-add') ?>&product_type=game_key&platform=' + encodeURIComponent(pf) + '&region=' + encodeURIComponent(rg) + '&return=key-inventory';
    window.location.href = url;
}

// Thêm nhánh mới (platform / region) — prompt rồi submit form
function addBranch(type){
    var label = type === 'platform' ? 'Platform' : 'Region';
    var name = prompt('Nhập tên ' + label + ' mới:');
    if(!name || !name.trim()) return;
    var form = document.createElement('form');
    form.method = 'POST';
    var fields = {action:'add_branch', branch_type:type, branch_name:name.trim()};
    for(var k in fields){
        var inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = k; inp.value = fields[k];
        form.appendChild(inp);
    }
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require_once(__DIR__ . '/footer.php'); ?>
