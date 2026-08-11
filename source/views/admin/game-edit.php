<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($game_id <= 0) { redirect(base_url('admin/game-manager')); }

$game = $CMSNT->get_row_safe("SELECT * FROM `games` WHERE `id` = ?", [$game_id]);
if (!$game) { redirect(base_url('admin/game-manager')); }

$tiers = $CMSNT->get_list_safe("SELECT * FROM `topup_tiers` WHERE `game_id` = ? ORDER BY FIELD(`type`,'gem','pack','allpack'), `sort_order` ASC", [$game_id]);

// Handle POST updates
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_game'])) {
        $CMSNT->update('games', [
            'name' => check_string($_POST['name']),
            'full_name' => check_string($_POST['full_name']),
            'category' => check_string($_POST['category']),
            'icon' => check_string($_POST['icon']),
            'currency_name' => check_string($_POST['currency_name']),
            'currency_unit' => check_string($_POST['currency_unit']),
            'status' => isset($_POST['status']) ? 1 : 0
        ], "`id` = {$game_id}");
        $msg = '<div class="alert alert-success">Đã cập nhật game!</div>';
        // Refresh
        $game = $CMSNT->get_row_safe("SELECT * FROM `games` WHERE `id` = ?", [$game_id]);
    }
    if (isset($_POST['add_tier'])) {
        $CMSNT->insert('topup_tiers', [
            'game_id' => $game_id,
            'type' => check_string($_POST['tier_type']),
            'label' => check_string($_POST['tier_label']),
            'amount' => intval($_POST['tier_amount']),
            'price' => intval($_POST['tier_price']),
            'cost' => intval($_POST['tier_cost']),
            'status' => 1,
            'sort_order' => intval($_POST['tier_sort'])
        ]);
        $msg = '<div class="alert alert-success">Đã thêm gói nạp!</div>';
        $tiers = $CMSNT->get_list_safe("SELECT * FROM `topup_tiers` WHERE `game_id` = ? ORDER BY FIELD(`type`,'gem','pack','allpack'), `sort_order` ASC", [$game_id]);
    }
    if (isset($_POST['delete_tier'])) {
        $tid = intval($_POST['delete_tier']);
        $CMSNT->remove('topup_tiers', "`id` = {$tid}");
        $msg = '<div class="alert alert-warning">Đã xoá gói nạp!</div>';
        $tiers = $CMSNT->get_list_safe("SELECT * FROM `topup_tiers` WHERE `game_id` = ? ORDER BY FIELD(`type`,'gem','pack','allpack'), `sort_order` ASC", [$game_id]);
    }
}

$body = ['title' => 'Sửa Game: ' . $game['name'] . ' — Admin'];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
require_once(__DIR__ . '/nav.php');
?>

<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1>🎮 Sửa Game: <?= htmlspecialchars($game['name']) ?></h1>
    <a href="<?= base_url('admin/game-manager') ?>" class="btn btn-default">← Quay lại</a>
  </div>
  <div class="container-fluid">
    <?= $msg ?>

    <div class="row">
      <!-- Game Info -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Thông tin game</h3></div>
          <div class="card-body">
            <form method="POST">
              <div class="form-group">
                <label>Tên game</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($game['name']) ?>" required>
              </div>
              <div class="form-group">
                <label>Tên đầy đủ</label>
                <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($game['full_name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label>Thể loại</label>
                <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($game['category'] ?? '') ?>">
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Icon (emoji)</label>
                    <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($game['icon'] ?? '') ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tên currency</label>
                    <input type="text" name="currency_name" class="form-control" value="<?= htmlspecialchars($game['currency_name'] ?? '') ?>">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Unit (emoji)</label>
                    <input type="text" name="currency_unit" class="form-control" value="<?= htmlspecialchars($game['currency_unit'] ?? '') ?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>
                  <input type="checkbox" name="status" value="1" <?= $game['status'] ? 'checked' : '' ?>>
                  Kích hoạt
                </label>
              </div>
              <button type="submit" name="update_game" class="btn btn-primary">💾 Lưu thay đổi</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Add Tier -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Thêm gói nạp mới</h3></div>
          <div class="card-body">
            <form method="POST">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Loại</label>
                    <select name="tier_type" class="form-control">
                      <option value="gem">💎 Gem / Currency</option>
                      <option value="pack">📦 Pack / Gói</option>
                      <option value="allpack">🎁 All Pack / Combo</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Thứ tự</label>
                    <input type="number" name="tier_sort" class="form-control" value="0">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Tên gói</label>
                <input type="text" name="tier_label" class="form-control" placeholder="VD: 60 Kim cương" required>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Số lượng</label>
                    <input type="number" name="tier_amount" class="form-control" value="0">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Giá bán (VND)</label>
                    <input type="number" name="tier_price" class="form-control" value="0" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Giá vốn</label>
                    <input type="number" name="tier_cost" class="form-control" value="0">
                  </div>
                </div>
              </div>
              <button type="submit" name="add_tier" class="btn btn-success">➕ Thêm gói</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Tiers List -->
    <div class="card mt-3">
      <div class="card-header">
        <h3 class="card-title">📋 Danh sách gói nạp (<?= count($tiers) ?> gói)</h3>
      </div>
      <div class="card-body table-responsive p-0">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>ID</th><th>Loại</th><th>Tên gói</th><th>SL</th><th>Giá</th><th>Giá vốn</th><th>TT</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if(empty($tiers)): ?>
            <tr><td colspan="8" class="text-center text-muted py-3">Chưa có gói nạp nào</td></tr>
            <?php else: foreach($tiers as $t): 
              $typeLabels = ['gem'=>'💎 Gem', 'pack'=>'📦 Pack', 'allpack'=>'🎁 Combo'];
            ?>
            <tr>
              <td><?= $t['id'] ?></td>
              <td><?= $typeLabels[$t['type']] ?? $t['type'] ?></td>
              <td><b><?= htmlspecialchars($t['label']) ?></b></td>
              <td><?= number_format($t['amount']) ?></td>
              <td class="text-right font-weight-bold"><?= number_format($t['price']) ?>đ</td>
              <td class="text-right"><?= number_format($t['cost']) ?>đ</td>
              <td><?= $t['sort_order'] ?></td>
              <td>
                <form method="POST" style="display:inline" onsubmit="return confirm('Xoá gói này?')">
                  <button type="submit" name="delete_tier" value="<?= $t['id'] ?>" class="btn btn-xs btn-danger">🗑️ Xoá</button>
                </form>
              </td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<?php require_once(__DIR__ . '/footer.php'); ?>
