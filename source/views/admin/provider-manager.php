<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$msg = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_provider'])) {
        $CMSNT->insert('topup_providers', [
            'name' => check_string($_POST['name']),
            'slug' => check_string($_POST['slug']),
            'type' => check_string($_POST['type']),
            'api_endpoint' => check_string($_POST['api_endpoint']),
            'api_key' => check_string($_POST['api_key']),
            'api_secret' => check_string($_POST['api_secret']),
            'status' => isset($_POST['status']) ? 1 : 0,
            'priority' => intval($_POST['priority']),
            'timeout_ms' => intval($_POST['timeout_ms']),
            'retry_count' => intval($_POST['retry_count']),
        ]);
        $msg = '<div class="alert alert-success">Đã thêm provider!</div>';
    }
    if (isset($_POST['toggle_status'])) {
        $pid = intval($_POST['provider_id']);
        $p = $CMSNT->get_row_safe("SELECT status FROM topup_providers WHERE id = ?", [$pid]);
        $newStatus = $p['status'] ? 0 : 1;
        $CMSNT->update('topup_providers', ['status' => $newStatus], "`id` = {$pid}");
        $msg = '<div class="alert alert-info">Đã ' . ($newStatus ? 'bật' : 'tắt') . ' provider!</div>';
    }
    if (isset($_POST['health_check'])) {
        $pid = intval($_POST['provider_id']);
        require_once(__DIR__ . '/../../libs/topup_provider.php');
        try {
            $provider = new TopupProvider($pid);
            $result = $provider->healthCheck();
            $msg = '<div class="alert alert-' . ($result['ok'] ? 'success' : 'danger') . '">Health check: ' . ($result['ok'] ? 'OK' : 'FAIL') . ' (' . $result['duration_ms'] . 'ms)</div>';
        } catch (Exception $e) {
            $msg = '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
        }
    }
}

$providers = $CMSNT->get_list_safe("SELECT * FROM `topup_providers` ORDER BY `priority` ASC, `id` ASC", []);

$body = ['title' => 'Quản lý Providers — Admin'];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
require_once(__DIR__ . '/nav.php');
?>

<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">
    <h1>🔌 Quản lý Topup Providers</h1>
    <a href="<?= BASE_URL() ?>" class="btn btn-sm btn-default" style="margin-left:12px">← Về shop</a>
  </div>
  <div class="container-fluid">
    <?= $msg ?>

    <div class="row">
      <!-- Provider List -->
      <div class="col-md-7">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Danh sách Providers (<?= count($providers) ?>)</h3></div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <thead>
                <tr><th>ID</th><th>Tên</th><th>Type</th><th>Status</th><th>Latency</th><th>Last Check</th><th>Action</th></tr>
              </thead>
              <tbody>
                <?php if(empty($providers)): ?>
                <tr><td colspan="7" class="text-center text-muted py-3">Chưa có provider nào</td></tr>
                <?php else: foreach($providers as $p): ?>
                <tr>
                  <td><?= $p['id'] ?></td>
                  <td><b><?= htmlspecialchars($p['name']) ?></b><br><small class="text-muted"><?= $p['slug'] ?></small></td>
                  <td><span class="badge badge-info"><?= $p['type'] ?></span></td>
                  <td>
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="provider_id" value="<?= $p['id'] ?>">
                      <button type="submit" name="toggle_status" class="btn btn-xs btn-<?= $p['status'] ? 'success' : 'secondary' ?>">
                        <?= $p['status'] ? '✅ ON' : '⛔ OFF' ?>
                      </button>
                    </form>
                  </td>
                  <td><?= $p['response_time_ms'] ? $p['response_time_ms'] . 'ms' : '-' ?></td>
                  <td><small><?= $p['last_check'] ?? '-' ?></small></td>
                  <td>
                    <form method="POST" style="display:inline">
                      <input type="hidden" name="provider_id" value="<?= $p['id'] ?>">
                      <button type="submit" name="health_check" class="btn btn-xs btn-warning">🩺 Check</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Add Provider -->
      <div class="col-md-5">
        <div class="card">
          <div class="card-header"><h3 class="card-title">Thêm Provider Mới</h3></div>
          <div class="card-body">
            <form method="POST">
              <div class="form-group">
                <label>Tên provider</label>
                <input type="text" name="name" class="form-control" required placeholder="VD: Napthe247">
              </div>
              <div class="form-group">
                <label>Slug (unique)</label>
                <input type="text" name="slug" class="form-control" required placeholder="napthe247">
              </div>
              <div class="form-group">
                <label>Loại</label>
                <select name="type" class="form-control">
                  <option value="rest_api">REST API</option>
                  <option value="mock">Mock</option>
                  <option value="webhook">Webhook</option>
                </select>
              </div>
              <div class="form-group">
                <label>API Endpoint</label>
                <input type="text" name="api_endpoint" class="form-control" placeholder="https://api.provider.com/topup">
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>API Key</label>
                    <input type="text" name="api_key" class="form-control">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>API Secret</label>
                    <input type="text" name="api_secret" class="form-control">
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Timeout (ms)</label>
                    <input type="number" name="timeout_ms" class="form-control" value="15000">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Retry count</label>
                    <input type="number" name="retry_count" class="form-control" value="3">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Priority</label>
                    <input type="number" name="priority" class="form-control" value="0">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label><input type="checkbox" name="status" value="1" checked> Kích hoạt</label>
              </div>
              <button type="submit" name="add_provider" class="btn btn-primary">➕ Thêm Provider</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once(__DIR__ . '/footer.php'); ?>
