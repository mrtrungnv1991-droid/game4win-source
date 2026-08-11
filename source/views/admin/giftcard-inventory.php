<?php if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');

$code = check_string($_GET['code'] ?? '');
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Thêm nhánh mới (brand)
    if ($_POST['action'] === 'add_branch' && !empty($_POST['branch_type']) && !empty($_POST['branch_name'])) {
        $bt = check_string($_POST['branch_type']);
        $bn = check_string($_POST['branch_name']);
        if ($bt === 'brand') {
            $exists = $CMSNT->get_row_safe("SELECT id FROM inventory_branches WHERE branch_type = ? AND name = ?", [$bt, $bn]);
            if (!$exists) {
                $CMSNT->insert('inventory_branches', ['branch_type' => $bt, 'name' => $bn, 'status' => 1]);
                $msg = "✅ Đã thêm brand mới: $bn";
            } else {
                $msg = "⚠️ Brand '$bn' đã tồn tại";
            }
        }
    }
    if ($_POST['action'] === 'add_card' && !empty($_POST['product_code'])) {
        $cards = array_filter(array_map('trim', explode("\n", $_POST['cards'] ?? '')));
        $added = 0;
        foreach ($cards as $c) {
            if (strlen($c) < 5) continue;
            $CMSNT->insert('giftcard_inventory', [
                'product_code' => check_string($_POST['product_code']),
                'card_code' => $c,
                'brand' => check_string($_POST['brand'] ?? 'Steam'),
                'face_value' => floatval($_POST['face_value'] ?? 10),
                'currency' => check_string($_POST['currency'] ?? 'USD'),
                'status' => 'available',
            ]);
            $added++;
        }
        $msg = "✅ Đã nhập $added gift cards vào kho";
    }
    if ($_POST['action'] === 'delete_card' && !empty($_POST['card_id'])) {
        $CMSNT->remove('giftcard_inventory', " `id` = " . intval($_POST['card_id']));
        $msg = "Đã xóa card";
    }
    if ($_POST['action'] === 'block_card' && !empty($_POST['card_id'])) {
        $CMSNT->update('giftcard_inventory', ['status' => 'blocked'], " `id` = " . intval($_POST['card_id']));
        $msg = "Đã block card";
    }
}

$product = null;
if ($code) {
    $product = $CMSNT->get_row_safe("SELECT * FROM products WHERE code = ?", [$code]);
}

$filter_status = $_GET['status'] ?? '';
$sql = "SELECT * FROM giftcard_inventory WHERE 1=1";
$params = [];
if ($code) { $sql .= " AND product_code = ?"; $params[] = $code; }
if ($filter_status) { $sql .= " AND status = ?"; $params[] = $filter_status; }
$sql .= " ORDER BY id DESC LIMIT 200";
$cards = $CMSNT->get_list_safe($sql, $params);

$stats = [
    'available' => $CMSNT->num_rows_safe("SELECT id FROM giftcard_inventory WHERE status = 'available'" . ($code ? " AND product_code = ?" : ""), $code ? [$code] : []),
    'sold' => $CMSNT->num_rows_safe("SELECT id FROM giftcard_inventory WHERE status = 'sold'" . ($code ? " AND product_code = ?" : ""), $code ? [$code] : []),
    'blocked' => $CMSNT->num_rows_safe("SELECT id FROM giftcard_inventory WHERE status = 'blocked'" . ($code ? " AND product_code = ?" : ""), $code ? [$code] : []),
];

$body = ['title' => '💳 Kho Gift Card | ' . $CMSNT->site('title')];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
?>

<div class="main-content app-content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>💳 Kho Gift Card <?= $product ? '— ' . htmlspecialchars($product['name']) : '(tất cả)' ?></h3>
    <div>
        <?php if ($code): ?><a href="<?= base_url_admin('giftcard-inventory') ?>" class="btn btn-sm btn-outline-secondary">← Tất cả kho</a><?php endif; ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCardModal">+ Nhập Cards</button>
    </div>
</div>

<?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>

<div class="alert alert-info">
    <strong>📦 Module riêng cho GIFT CARD</strong> (không phải account!). Card = mã nạp tiền mệnh giá cố định.
    Brands: <span class="badge bg-dark">Steam Wallet</span> <span class="badge bg-success">Google Play</span>
    <span class="badge bg-secondary">App Store</span> <span class="badge bg-danger">Roblox</span>
</div>

<!-- Stats -->
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body text-center">
        <h4><?= $stats['available'] ?></h4><small>Cards sẵn sàng</small></div></div></div>
    <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body text-center">
        <h4><?= $stats['sold'] ?></h4><small>Đã bán</small></div></div></div>
    <div class="col-md-4"><div class="card bg-danger text-white"><div class="card-body text-center">
        <h4><?= $stats['blocked'] ?></h4><small>Blocked</small></div></div></div>
</div>

<!-- Filter -->
<div class="mb-3">
    <a href="?module=admin&action=giftcard-inventory<?= $code ? '&code='.$code : '' ?>" class="btn btn-sm <?= !$filter_status?'btn-dark':'btn-outline-dark' ?>">Tất cả</a>
    <a href="?module=admin&action=giftcard-inventory<?= $code ? '&code='.$code : '' ?>&status=available" class="btn btn-sm <?= $filter_status=='available'?'btn-success':'btn-outline-success' ?>">Available</a>
    <a href="?module=admin&action=giftcard-inventory<?= $code ? '&code='.$code : '' ?>&status=sold" class="btn btn-sm <?= $filter_status=='sold'?'btn-primary':'btn-outline-primary' ?>">Sold</a>
    <a href="?module=admin&action=giftcard-inventory<?= $code ? '&code='.$code : '' ?>&status=blocked" class="btn btn-sm <?= $filter_status=='blocked'?'btn-danger':'btn-outline-danger' ?>">Blocked</a>
</div>

<!-- Cards table -->
<div class="card"><div class="card-body"><div class="table-responsive">
<table class="table table-bordered table-hover">
    <thead class="table-dark"><tr><th>ID</th><th>Product</th><th>Card Code</th><th>Brand</th><th>Mệnh giá</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
    <?php if (empty($cards)): ?>
        <tr><td colspan="7" class="text-center text-muted">Kho trống — bấm "+ Nhập Cards"</td></tr>
    <?php else: foreach ($cards as $c):
        $badge = ['available'=>'success','sold'=>'primary','blocked'=>'danger','used'=>'secondary'][$c['status']] ?? 'secondary';
    ?>
    <tr>
        <td>#<?= $c['id'] ?></td>
        <td><code><?= $c['product_code'] ?></code></td>
        <td><code><?= $c['status'] === 'available' ? substr($c['card_code'],0,8).'••••' : htmlspecialchars(substr($c['card_code'],0,20)) ?></code></td>
        <td><span class="badge bg-info"><?= $c['brand'] ?></span></td>
        <td class="fw-bold">$<?= number_format($c['face_value']) ?> <?= $c['currency'] ?></td>
        <td><span class="badge bg-<?= $badge ?>"><?= $c['status'] ?></span></td>
        <td>
            <?php if ($c['status'] === 'available'): ?>
            <form method="POST" style="display:inline">
                <input type="hidden" name="action" value="block_card">
                <input type="hidden" name="card_id" value="<?= $c['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">🚫</button>
            </form>
            <?php endif; ?>
            <form method="POST" style="display:inline" onsubmit="return confirm('Xóa card?')">
                <input type="hidden" name="action" value="delete_card">
                <input type="hidden" name="card_id" value="<?= $c['id'] ?>">
                <button class="btn btn-sm btn-outline-secondary">🗑</button>
            </form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>
</div></div></div>

</div></div>

<!-- Add Card Modal — NHÁNH CHIA RIÊNG CHO GIFT CARD: Brand > Mệnh giá > Sản phẩm -->
<div class="modal fade" id="addCardModal" tabindex="-1">
<div class="modal-dialog modal-lg"><div class="modal-content">
<form method="POST">
<div class="modal-header bg-primary text-white"><h5>+ Nhập Gift Cards</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
<div class="modal-body">
    <input type="hidden" name="action" value="add_card">

    <div class="alert alert-info py-2 small">
        <strong>💳 Nhánh chia Gift Card:</strong> Chọn <strong>Brand</strong> → <strong>Mệnh giá</strong> → <strong>Sản phẩm</strong> (chỉ hiện gift card của brand đó) → nhập codes.
    </div>

    <!-- NHÁNH 1: Brand (load từ DB, có thể thêm mới) -->
    <div class="mb-2"><label class="form-label">🏷️ Nhánh 1: Brand</label>
        <div class="input-group">
            <select name="brand" id="cardBrand" class="form-select" required onchange="filterCardByBrand()">
                <option value="">— Chọn brand —</option>
                <?php foreach ($CMSNT->get_list_safe("SELECT name FROM inventory_branches WHERE branch_type='brand' AND status=1 ORDER BY name", []) as $b): ?>
                <option><?= htmlspecialchars($b['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-outline-success" onclick="addBranch('brand')" title="Thêm brand mới">+ Thêm</button>
        </div>
    </div>

    <!-- NHÁNH 2: Mệnh giá -->
    <div class="mb-2"><label class="form-label">💵 Nhánh 2: Mệnh giá</label>
        <div class="row g-2">
            <div class="col-8">
                <input name="face_value" id="cardFaceValue" type="number" step="0.01" class="form-control" placeholder="VD: 10, 25, 50" required>
            </div>
            <div class="col-4">
                <select name="currency" class="form-select"><option>USD</option><option>EUR</option><option>VND</option></select>
            </div>
        </div>
    </div>

    <!-- NHÁNH 3: Sản phẩm (lọc theo brand) -->
    <div class="mb-2"><label class="form-label">📦 Nhánh 3: Sản phẩm Gift Card</label>
        <div class="input-group">
            <select name="product_code" id="cardProductSelect" class="form-select" required>
                <option value="">— Chọn brand trước —</option>
                <?php $prods = $CMSNT->get_list_safe("SELECT code, name, platform FROM products WHERE product_type = 'gift_card' AND status = 1 ORDER BY platform, name", []); ?>
                <?php foreach ($prods as $p): ?>
                <option value="<?= $p['code'] ?>" data-brand="<?= htmlspecialchars($p['platform']) ?>" style="display:none" disabled><?= htmlspecialchars($p['name']) ?> (<?= $p['code'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn btn-success" onclick="createNewCardProduct()" title="Tạo sản phẩm Gift Card mới">+ Tạo mới</button>
        </div>
        <small class="text-muted">Chưa có sản phẩm? Bấm <strong>"+ Tạo mới"</strong> — sẽ mở form tạo với Brand đã chọn sẵn.</small>
    </div>

    <div class="mb-2"><label class="form-label">Card codes (mỗi dòng 1 code)</label>
        <textarea name="cards" class="form-control" rows="8" placeholder="ABCD-EFGH-IJKL-MNOP&#10;QRST-UVWX-YZAB-CDEF" required></textarea></div>
</div>
<div class="modal-footer"><button class="btn btn-primary" type="submit">Nhập kho</button></div>
</form>
</div></div></div>

<script>
// Nhánh chia Gift Card: lọc sản phẩm theo brand đã chọn
function filterCardByBrand(){
    var brand = document.getElementById('cardBrand').value;
    var sel = document.getElementById('cardProductSelect');
    var opts = sel.options;
    var visibleCount = 0;
    for (var i = 0; i < opts.length; i++) {
        var optBrand = opts[i].getAttribute('data-brand');
        if (brand && optBrand === brand) {
            opts[i].style.display = '';
            opts[i].disabled = false;
            visibleCount++;
        } else {
            opts[i].style.display = 'none';
            opts[i].disabled = true;
        }
    }
    sel.selectedIndex = 0;
    if (!brand) {
        sel.options[0].textContent = '— Chọn brand trước —';
    } else if (visibleCount === 0) {
        sel.options[0].textContent = '— Chưa có gift card ' + brand + ' — bấm "+ Tạo mới" bên cạnh';
    } else {
        sel.options[0].textContent = '— Chọn gift card ' + brand + ' (' + visibleCount + ') —';
    }
}

// Tạo sản phẩm Gift Card mới — pre-fill brand đã chọn, quay về kho sau khi tạo
function createNewCardProduct(){
    var brand = document.getElementById('cardBrand').value || 'Steam';
    var url = '<?= base_url_admin('product-add') ?>&product_type=gift_card&platform=' + encodeURIComponent(brand) + '&return=giftcard-inventory';
    window.location.href = url;
}

// Thêm nhánh mới (brand) — prompt rồi submit form
function addBranch(type){
    var name = prompt('Nhập tên Brand mới:');
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
