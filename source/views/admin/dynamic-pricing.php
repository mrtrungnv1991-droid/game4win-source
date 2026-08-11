<?php
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/../../libs/dynamic_pricing.php');

$dp = new DynamicPricing($CMSNT);
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // CSRF guard (Punch list #1): chan moi POST khong co token hop le
    if (!verify_csrf()) {
        http_response_code(403);
        die('CSRF token khong hop le. Vui long quay lai va thu lai.');
    }
    $styles = ['success'=>'border-l-emerald-500 bg-emerald-50 text-emerald-800','error'=>'border-l-red-500 bg-red-50 text-red-800'];
    if ($_POST['action'] === 'run_engine') {
        $r = $dp->runEngine(false);
        $s = $r['status'] === 'success' ? 'success' : 'error';
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.$styles[$s].' text-sm font-medium">'.($r['status']==='success' ? "✅ Engine chạy xong: điều chỉnh {$r['adjusted']} giá, bỏ qua {$r['skipped']}" : "❌ ".$r['msg']).'</div>';
    }
    if ($_POST['action'] === 'dry_run') {
        $r = $dp->runEngine(true);
        $s = $r['status'] === 'success' ? 'success' : 'error';
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 border-l-blue-500 bg-blue-50 text-blue-800 text-sm font-medium">'.($r['status']==='success' ? "🔎 DRY RUN: sẽ điều chỉnh {$r['adjusted']} giá (không lưu thật)" : "❌ ".$r['msg']).'</div>';
    }
    if ($_POST['action'] === 'save_rule' && !empty($_POST['rule_id'])) {
        $CMSNT->update('pricing_rules', [
            'strategy' => in_array($_POST['strategy'], ['undercut','match','margin_floor']) ? $_POST['strategy'] : 'undercut',
            'undercut_percent' => floatval($_POST['undercut_percent'] ?? 5),
            'min_margin_percent' => floatval($_POST['min_margin_percent'] ?? 15),
            'max_price_change_percent' => floatval($_POST['max_price_change_percent'] ?? 10),
        ], " `id` = " . intval($_POST['rule_id']));
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 border-l-emerald-500 bg-emerald-50 text-emerald-800 text-sm font-medium">✅ Đã lưu pricing rule</div>';
    }
}

$stats = $dp->getStats();

$dcos_active = 'dynamic-pricing';
$body = ['title' => 'Dynamic Pricing | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');
?>

<?= $msg ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Dynamic Pricing</h1>
        <p class="text-sm text-slate-500 mt-1">Tự động điều chỉnh giá theo thị trường — luôn cạnh tranh nhất</p>
    </div>
    <div class="flex gap-2">
        <form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="dry_run">
            <button class="px-4 py-2.5 bg-blue-500 text-white rounded-lg text-xs font-bold uppercase tracking-tight hover:bg-blue-600 transition-colors inline-flex items-center gap-2"><i class="bx bx-show"></i> Dry Run</button>
        </form>
        <form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="run_engine">
            <button class="btn-primary px-5 py-2.5 rounded-lg text-sm inline-flex items-center gap-2"><i class="bx bx-zap"></i> Chạy Engine</button>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="custom-card stat-card" style="border-left-color: var(--primary);">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Products linked</p><h3 class="text-2xl font-bold text-slate-800"><?= $stats['linked_products'] ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center"><i class="bx bx-link text-xl text-purple-500"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color: #10b981;">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lần điều chỉnh giá</p><h3 class="text-2xl font-bold text-slate-800"><?= $stats['total_adjustments'] ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="bx bx-transfer text-xl text-emerald-500"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color: #f59e0b;">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Rules đang bật</p><h3 class="text-2xl font-bold text-slate-800"><?= $stats['active_rules'] ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center"><i class="bx bx-slider text-xl text-amber-500"></i></div>
        </div>
    </div>
</div>

<!-- Info -->
<div class="custom-card p-4 mb-8 bg-slate-50 border-slate-200 text-sm text-slate-600">
    <strong>🧠 Chiến lược:</strong>
    <code class="bg-white px-1.5 py-0.5 rounded text-purple-600 font-bold">undercut</code> = bán rẻ hơn competitor X% ·
    <code class="bg-white px-1.5 py-0.5 rounded text-purple-600 font-bold">match</code> = bán bằng giá ·
    <code class="bg-white px-1.5 py-0.5 rounded text-purple-600 font-bold">margin_floor</code> = giữ biên lợi nhuận tối thiểu.
    Cron: <code class="bg-slate-200 px-1 rounded">cron/dynamic_pricing.php</code> (mỗi 12h)
</div>

<!-- Pricing Rules -->
<div class="custom-card overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-100">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">⚙️ Pricing Rules</h4>
    </div>
    <div class="p-6 space-y-4">
        <?php foreach ($stats['rules'] as $rule): ?>
        <form method="POST" class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end p-4 bg-slate-50 rounded-lg"><?= csrf_field() ?>
            <input type="hidden" name="action" value="save_rule">
            <input type="hidden" name="rule_id" value="<?= $rule['id'] ?>">
            <div><label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tên rule</label><input class="w-full px-3 py-2 bg-white border border-slate-200 rounded text-sm" value="<?= htmlspecialchars($rule['name']) ?>" disabled></div>
            <div><label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Chiến lược</label>
                <select name="strategy" class="w-full px-3 py-2 bg-white border border-slate-200 rounded text-sm">
                    <option value="undercut" <?= ($rule['strategy']??'')=='undercut'?'selected':'' ?>>Undercut</option>
                    <option value="match" <?= ($rule['strategy']??'')=='match'?'selected':'' ?>>Match</option>
                    <option value="margin_floor" <?= ($rule['strategy']??'')=='margin_floor'?'selected':'' ?>>Margin floor</option>
                </select>
            </div>
            <div><label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Undercut %</label><input name="undercut_percent" type="number" step="0.5" class="w-full px-3 py-2 bg-white border border-slate-200 rounded text-sm" value="<?= $rule['undercut_percent'] ?>"></div>
            <div><label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Margin tối thiểu %</label><input name="min_margin_percent" type="number" step="0.5" class="w-full px-3 py-2 bg-white border border-slate-200 rounded text-sm" value="<?= $rule['min_margin_percent'] ?>"></div>
            <div><label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Max biến động %</label><input name="max_price_change_percent" type="number" step="0.5" class="w-full px-3 py-2 bg-white border border-slate-200 rounded text-sm" value="<?= $rule['max_price_change_percent'] ?>"></div>
            <div><button type="submit" class="w-full btn-primary px-4 py-2 rounded-lg text-xs">💾 Lưu</button></div>
        </form>
        <?php endforeach; ?>
        <?php if (empty($stats['rules'])): ?>
        <p class="text-center text-slate-400 py-4">Chưa có pricing rules</p>
        <?php endif; ?>
    </div>
</div>

<!-- Price History -->
<div class="custom-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">📜 Lịch sử thay đổi giá</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Thời gian</th><th class="px-6 py-4">Sản phẩm</th><th class="px-6 py-4">Giá cũ</th><th class="px-6 py-4">Giá mới</th><th class="px-6 py-4">Giá competitor</th><th class="px-6 py-4">Lý do</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($stats['recent_history'] ?? [] as $h): $up = $h['new_price'] > $h['old_price']; ?>
                <?php /* Punch #7 — QUYẾT ĐỊNH màu tăng/giảm giá (Master Prompt v3 §5.7):
                   Dùng framing CHỦ SHOP (người bán), vì đây là admin panel của người bán:
                   - Tăng giá = emerald (▲) → lợi nhuận/margin tăng = tín hiệu TỐT cho chủ shop.
                   - Giảm giá = đỏ (▼)     → margin giảm = cần chú ý.
                   KHÔNG dùng framing người mua (tăng=đỏ) vì gây hiểu lầm trong ngữ cảnh quản trị. */ ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-xs text-slate-400"><?= date('d/m H:i', strtotime($h['created_at'])) ?></td>
                    <td class="px-6 py-4 text-sm"><?= htmlspecialchars($h['product_name'] ?? 'SP #'.$h['product_id']) ?></td>
                    <td class="px-6 py-4 text-sm text-slate-400"><?= number_format($h['old_price']) ?>đ</td>
                    <td class="px-6 py-4 text-sm font-bold <?= $up ? 'text-emerald-600' : 'text-red-500' ?>"><?= $up ? '▲' : '▼' ?> <?= number_format($h['new_price']) ?>đ</td>
                    <td class="px-6 py-4 text-sm"><?= number_format($h['competitor_price']) ?>đ</td>
                    <td class="px-6 py-4 text-xs text-slate-500"><?= htmlspecialchars($h['reason']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($stats['recent_history'])): ?>
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Chưa có lần điều chỉnh nào — import competitor products rồi chạy Engine</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(__DIR__ . '/dcos-layout-close.php');