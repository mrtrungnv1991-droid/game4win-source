<?php
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');

$msg = '';
$new_key = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // CSRF guard (Punch list #1): chan moi POST khong co token hop le
    if (!verify_csrf()) {
        http_response_code(403);
        die('CSRF token khong hop le. Vui long quay lai va thu lai.');
    }
    if ($_POST['action'] === 'create_key') {
        $name = check_string($_POST['name'] ?? 'API Key');
        $key = bin2hex(random_bytes(32));
        $CMSNT->insert('api_keys', [
            'user_id' => $getUser['id'], 'name' => $name, 'api_key' => $key,
            'permissions' => 'read,order', 'rate_limit' => intval($_POST['rate_limit'] ?? 60), 'status' => 1,
        ]);
        $new_key = $key;
    }
    if ($_POST['action'] === 'delete_key' && !empty($_POST['key_id'])) {
        $CMSNT->remove('api_keys', " `id` = " . intval($_POST['key_id']));
    }
    if ($_POST['action'] === 'toggle_key' && !empty($_POST['key_id'])) {
        $k = $CMSNT->get_row_safe("SELECT status FROM api_keys WHERE id = ?", [intval($_POST['key_id'])]);
        if ($k) $CMSNT->update('api_keys', ['status' => $k['status'] ? 0 : 1], " `id` = " . intval($_POST['key_id']));
    }
}

$keys = $CMSNT->get_list_safe("SELECT * FROM api_keys ORDER BY id DESC", []);
$logs = $CMSNT->get_list_safe("SELECT l.*, k.name as key_name FROM api_logs l LEFT JOIN api_keys k ON l.api_key_id = k.id ORDER BY l.id DESC LIMIT 30", []);

// Punch #6: stat-strip data
$ak_active = 0; $ak_requests_today = 0; $ak_near_limit = null; $ak_near_pct = 0;
foreach ($keys as $k) {
    if ($k['status'] == 1) $ak_active++;
    $ak_requests_today += intval($k['requests_today'] ?? 0);
    $limit = max(1, intval($k['rate_limit'] ?? 60));
    $pct = intval($k['requests_today'] ?? 0) / $limit;
    if ($pct > $ak_near_pct) { $ak_near_pct = $pct; $ak_near_limit = $k; }
}

$dcos_active = 'api-keys';
$body = ['title' => 'API Keys | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');
?>

<?php if ($new_key): ?>
<div class="custom-card p-4 mb-6 border-l-4 border-l-emerald-500 bg-emerald-50">
    <strong class="text-emerald-800">✅ API Key mới — copy ngay (chỉ hiện 1 lần):</strong><br>
    <code class="text-sm bg-white px-2 py-1 rounded select-all break-all"><?= $new_key ?></code><br>
    <small class="text-slate-500">Test: <code>curl -H "X-API-Key: <?= substr($new_key,0,16) ?>..." <?= base_url('api/v1/products') ?></code></small>
</div>
<?php endif; ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">API Keys</h1>
        <p class="text-sm text-slate-500 mt-1">REST API Keys — cho Bot & Supplier tích hợp</p>
    </div>
    <button onclick="document.getElementById('createKeyModal').classList.remove('hidden')" 
            class="btn-primary px-5 py-2.5 rounded-lg text-sm inline-flex items-center gap-2 shadow-lg shadow-purple-500/20">
        <i class="bx bx-plus text-xl"></i> Tạo API Key
    </button>
</div>

<!-- Punch #6: Stat strip (3 stat-card, cùng pattern left-border với các module OS khác) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <?php
    $ak_cards = [
        ['Tổng Keys Active', $ak_active, 'bx-key', 'amber', '#F59E0B'],
        ['Requests hôm nay', $ak_requests_today, 'bx-transfer', 'blue', '#2563EB'],
        ['Gần đạt rate limit nhất', $ak_near_limit ? htmlspecialchars($ak_near_limit['name']) . ' (' . intval($ak_near_limit['requests_today']) . '/' . intval($ak_near_limit['rate_limit']) . ')' : '—', 'bx-time-five', 'red', '#EF4444'],
    ];
    foreach ($ak_cards as [$label, $value, $icon, $color, $accent]):
    ?>
    <div class="custom-card stat-card" style="border-left-color: <?= $accent ?>;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1"><?= $label ?></p>
                <h3 class="text-2xl font-bold text-slate-800"><?= is_int($value) ? number_format($value) : $value ?></h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-<?= $color ?>-50 flex items-center justify-center">
                <i class="bx <?= $icon ?> text-xl text-<?= $color ?>-500"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- API Docs -->
<div class="custom-card overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-100">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">📖 REST API Endpoints</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Method</th><th class="px-6 py-4">Endpoint</th><th class="px-6 py-4">Mô tả</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <tr><td class="px-6 py-4"><span class="badge bg-emerald-100 text-emerald-700">GET</span></td><td class="px-6 py-4"><code class="text-xs bg-slate-100 px-2 py-1 rounded">/api/v1/products</code></td><td class="px-6 py-4 text-sm">Danh sách sản phẩm</td></tr>
                <tr><td class="px-6 py-4"><span class="badge bg-emerald-100 text-emerald-700">GET</span></td><td class="px-6 py-4"><code class="text-xs bg-slate-100 px-2 py-1 rounded">/api/v1/products/{code}</code></td><td class="px-6 py-4 text-sm">Chi tiết sản phẩm + tồn kho</td></tr>
                <tr><td class="px-6 py-4"><span class="badge bg-amber-100 text-amber-700">POST</span></td><td class="px-6 py-4"><code class="text-xs bg-slate-100 px-2 py-1 rounded">/api/v1/order</code></td><td class="px-6 py-4 text-sm">Mua hàng: <code class="text-xs">{"product_code":"KEY-STM10","amount":1}</code> → trả key ngay</td></tr>
                <tr><td class="px-6 py-4"><span class="badge bg-emerald-100 text-emerald-700">GET</span></td><td class="px-6 py-4"><code class="text-xs bg-slate-100 px-2 py-1 rounded">/api/v1/orders</code></td><td class="px-6 py-4 text-sm">Lịch sử đơn hàng qua API</td></tr>
                <tr><td class="px-6 py-4"><span class="badge bg-emerald-100 text-emerald-700">GET</span></td><td class="px-6 py-4"><code class="text-xs bg-slate-100 px-2 py-1 rounded">/api/v1/balance</code></td><td class="px-6 py-4 text-sm">Số dư tài khoản</td></tr>
            </tbody>
        </table>
    </div>
    <div class="px-6 py-3 bg-slate-50 text-xs text-slate-500">
        Auth: Header <code>X-API-Key: &lt;your_key&gt;</code> · Base URL: <code><?= base_url('api/v1') ?></code>
    </div>
</div>

<!-- Keys Table -->
<div class="custom-card overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">🗝️ Danh sách API Keys</h4>
        <span class="text-xs text-slate-400"><?= count($keys) ?> keys</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Tên</th><th class="px-6 py-4">API Key</th><th class="px-6 py-4">Requests</th><th class="px-6 py-4">Lần cuối dùng</th><th class="px-6 py-4">Trạng thái</th><th class="px-6 py-4 text-right">Action</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($keys as $k): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-700"><?= htmlspecialchars($k['name']) ?></td>
                    <td class="px-6 py-4"><code class="text-xs bg-slate-100 px-2 py-1 rounded"><?= substr($k['api_key'], 0, 12) ?>••••••••</code></td>
                    <td class="px-6 py-4"><span class="text-sm <?= $k['requests_today'] >= $k['rate_limit'] ? 'text-red-500 font-bold' : '' ?>"><?= $k['requests_today'] ?>/<?= $k['rate_limit'] ?></span></td>
                    <td class="px-6 py-4 text-sm text-slate-400"><?= $k['last_used'] ? date('d/m H:i', strtotime($k['last_used'])) : '—' ?></td>
                    <td class="px-6 py-4">
                        <form method="POST" style="display:inline"><?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle_key">
                            <input type="hidden" name="key_id" value="<?= $k['id'] ?>">
                            <button class="px-3 py-1 rounded-lg text-xs font-bold transition-colors <?= $k['status'] ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' ?>">
                                <?= $k['status'] ? '✅ Active' : '⏸ Disabled' ?>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form method="POST" style="display:inline" onsubmit="return confirm('Xóa API key này?')"><?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete_key">
                            <input type="hidden" name="key_id" value="<?= $k['id'] ?>">
                            <button class="text-red-500 hover:bg-red-50 p-2 rounded transition-colors" title="Xóa"><i class="bx bx-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($keys)): ?>
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Chưa có API key — bấm "Tạo API Key"</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- API Logs -->
<div class="custom-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">📊 API Request Logs (30 gần nhất)</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Thời gian</th><th class="px-6 py-4">Key</th><th class="px-6 py-4">Method</th><th class="px-6 py-4">Endpoint</th><th class="px-6 py-4">Code</th><th class="px-6 py-4">IP</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($logs as $l): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-xs text-slate-400"><?= date('d/m H:i:s', strtotime($l['created_at'])) ?></td>
                    <td class="px-6 py-4 text-sm"><?= htmlspecialchars($l['key_name'] ?? '?') ?></td>
                    <td class="px-6 py-4"><span class="badge <?= $l['method']=='POST'?'bg-amber-100 text-amber-700':'bg-emerald-100 text-emerald-700' ?>"><?= $l['method'] ?></span></td>
                    <td class="px-6 py-4"><code class="text-xs"><?= htmlspecialchars($l['endpoint']) ?></code></td>
                    <td class="px-6 py-4"><span class="badge <?= $l['response_code']==200?'bg-emerald-100 text-emerald-700':'bg-red-100 text-red-700' ?>"><?= $l['response_code'] ?></span></td>
                    <td class="px-6 py-4 text-xs text-slate-400"><?= $l['ip'] ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Chưa có request nào</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Key Modal -->
<div id="createKeyModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="modal-overlay absolute inset-0" onclick="document.getElementById('createKeyModal').classList.add('hidden')"></div>
    <div class="custom-card relative z-10 w-full max-w-md bg-white p-0 overflow-hidden">
        <form method="POST"><?= csrf_field() ?>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">Tạo API Key</h3>
                <button type="button" onclick="document.getElementById('createKeyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="create_key">
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tên (VD: Bot mua hàng)</label><input name="name" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" required placeholder="Bot mua hàng"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Rate limit (requests/ngày)</label><input name="rate_limit" type="number" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" value="60"></div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createKeyModal').classList.add('hidden')" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-sm">Tạo Key</button>
            </div>
        </form>
    </div>
</div>

<?php require_once(__DIR__ . '/dcos-layout-close.php');