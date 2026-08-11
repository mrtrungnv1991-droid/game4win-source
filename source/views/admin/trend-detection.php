<?php
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/../../libs/trend_detection.php');

$td = new TrendDetection($CMSNT);
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // CSRF guard (Punch list #1): chan moi POST khong co token hop le
    if (!verify_csrf()) {
        http_response_code(403);
        die('CSRF token khong hop le. Vui long quay lai va thu lai.');
    }
    $styles = ['success'=>'border-l-emerald-500 bg-emerald-50 text-emerald-800','error'=>'border-l-red-500 bg-red-50 text-red-800'];
    if ($_POST['action'] === 'scan_reddit') {
        $r = $td->scanReddit(25);
        $s = $r['status'] === 'success' ? 'success' : 'error';
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.$styles[$s].' text-sm font-medium">'.($r['status']==='success' ? "✅ Reddit: quét {$r['scanned']} trends" : "❌ ".$r['msg']).'</div>';
    }
    if ($_POST['action'] === 'scan_trends') {
        $r = $td->scanGoogleTrends();
        $s = $r['status'] === 'success' ? 'success' : 'error';
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.$styles[$s].' text-sm font-medium">'.($r['status']==='success' ? "✅ Google Trends: {$r['scanned']} items" : "❌ ".$r['msg']).'</div>';
    }
    if ($_POST['action'] === 'match') {
        $r = $td->matchTrendsToProducts();
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 border-l-blue-500 bg-blue-50 text-blue-800 text-sm font-medium">✅ Matched '.$r['matched'].' trends với competitor products</div>';
    }
    if ($_POST['action'] === 'auto_list' && !empty($_POST['trend_id'])) {
        $r = $td->autoListing(intval($_POST['trend_id']), intval($_POST['markup'] ?? 20));
        $s = $r['status'] === 'success' ? 'success' : 'error';
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.$styles[$s].' text-sm font-medium">'.($r['status']==='success' ? "✅ {$r['msg']} (SP #{$r['product_id']})" : "❌ ".$r['msg']).'</div>';
    }
    if ($_POST['action'] === 'ignore' && !empty($_POST['trend_id'])) {
        $CMSNT->update('trend_items', ['status' => 'ignored'], " `id` = " . intval($_POST['trend_id']));
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 border-l-slate-500 bg-slate-50 text-slate-700 text-sm font-medium">Đã bỏ qua trend</div>';
    }
}

$stats = $td->getStats();

$dcos_active = 'trend-detection';
$body = ['title' => 'Trend Detection | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');
?>

<?= $msg ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Trend Detection</h1>
        <p class="text-sm text-slate-500 mt-1">AI Trend Detection — bắt game hot tự động từ Reddit & Google Trends</p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="scan_reddit">
            <button class="px-4 py-2.5 bg-red-500 text-white rounded-lg text-xs font-bold uppercase tracking-tight hover:bg-red-600 transition-colors inline-flex items-center gap-2"><i class="bx bx-reddit"></i> Quét Reddit</button>
        </form>
        <form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="scan_trends">
            <button class="px-4 py-2.5 bg-blue-500 text-white rounded-lg text-xs font-bold uppercase tracking-tight hover:bg-blue-600 transition-colors inline-flex items-center gap-2"><i class="bx bx-trending-up"></i> Google Trends</button>
        </form>
        <form method="POST" style="display:inline"><?= csrf_field() ?><input type="hidden" name="action" value="match">
            <button class="px-4 py-2.5 bg-amber-500 text-white rounded-lg text-xs font-bold uppercase tracking-tight hover:bg-amber-600 transition-colors inline-flex items-center gap-2"><i class="bx bx-link"></i> Match Products</button>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php
    $tstat_cards = [
        ['Tổng trends', $stats['total_trends'], 'bx:bx-globe', 'slate'],
        ['Mới (chưa xử lý)', $stats['new_trends'], 'bx:bx-bulb', 'blue'],
        ['Đã duyệt', $stats['approved'], 'bx:bx-check-circle', 'amber'],
        ['Đã auto-listing', $stats['listed'], 'bx:bx-rocket', 'emerald'],
    ];
    foreach ($tstat_cards as [$label, $value, $icon, $color]):
    ?>
    <div class="custom-card stat-card" style="border-left-color: var(--<?= $color ?>-500, #64748b);">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1"><?= $label ?></p><h3 class="text-2xl font-bold text-slate-800"><?= number_format($value) ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-<?= $color ?>-50 flex items-center justify-center"><i class=\"bx <?= str_replace('bx:', '', $icon) ?> text-xl text-<?= $color ?>-500\"></i></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Info -->
<div class="custom-card p-4 mb-8 bg-slate-50 border-slate-200 text-sm text-slate-600">
    <strong>🤖 Flow AI:</strong> Quét Reddit r/gamedeals + Google Trends → chấm điểm trend (upvotes + comments×2) → match với competitor products → auto tạo listing kèm badge 🔥 TRENDING.
    Cron: <code class="bg-slate-200 px-1 rounded">cron/trend_scan.php</code> (mỗi 6h)
</div>

<!-- Trends Table -->
<div class="custom-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">🔥 Trends (xếp theo điểm)</h4>
        <span class="text-xs text-slate-400"><?= count($stats['top_trends'] ?? []) ?> trends</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Keyword</th><th class="px-6 py-4">Nguồn</th><th class="px-6 py-4">Điểm</th><th class="px-6 py-4">Mentions</th><th class="px-6 py-4">Match SP</th><th class="px-6 py-4">Trạng thái</th><th class="px-6 py-4 text-right">Action</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($stats['top_trends'] ?? [] as $t):
                    $status_badge = match($t['status']) {
                        'new' => 'bg-blue-100 text-blue-700',
                        'approved' => 'bg-amber-100 text-amber-700',
                        'listed' => 'bg-emerald-100 text-emerald-700',
                        'ignored' => 'bg-slate-100 text-slate-500',
                        default => 'bg-slate-100 text-slate-500'
                    };
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-slate-700"><?= htmlspecialchars($t['keyword']) ?></span>
                        <?php if ($t['url']): ?><a href="<?= htmlspecialchars($t['url']) ?>" target="_blank" class="text-xs text-slate-400 hover:text-blue-500 ml-1">↗</a><?php endif; ?>
                    </td>
                    <td class="px-6 py-4"><span class="badge <?= $t['source']=='reddit'?'bg-red-100 text-red-700':'bg-blue-100 text-blue-700' ?>"><?= $t['source'] ?></span></td>
                    <td class="px-6 py-4 font-bold text-slate-700"><?= number_format($t['score']) ?></td>
                    <td class="px-6 py-4"><?= $t['mentions'] ?></td>
                    <td class="px-6 py-4"><?= $t['matched_competitor_id'] > 0 ? '<span class="text-emerald-600 text-xs font-bold">✅ #'.$t['matched_competitor_id'].'</span>' : '<span class="text-slate-400">—</span>' ?></td>
                    <td class="px-6 py-4"><span class="badge <?= $status_badge ?>"><?= $t['status'] ?></span></td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($t['status'] === 'approved' && ($t['auto_listing_product_id'] ?? 0) == 0): ?>
                        <form method="POST" style="display:inline"><?= csrf_field() ?>
                            <input type="hidden" name="action" value="auto_list">
                            <input type="hidden" name="trend_id" value="<?= $t['id'] ?>">
                            <input type="hidden" name="markup" value="20">
                            <button class="px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold hover:bg-emerald-600 hover:text-white transition-colors">🚀 Auto-List</button>
                        </form>
                        <?php elseif ($t['status'] === 'new'): ?>
                        <form method="POST" style="display:inline"><?= csrf_field() ?>
                            <input type="hidden" name="action" value="ignore">
                            <input type="hidden" name="trend_id" value="<?= $t['id'] ?>">
                            <button class="px-3 py-1.5 bg-slate-100 text-slate-500 rounded-lg text-xs font-bold hover:bg-slate-200 transition-colors">Bỏ qua</button>
                        </form>
                        <?php elseif ($t['status'] === 'listed'): ?>
                        <span class="badge bg-emerald-100 text-emerald-700">SP #<?= $t['auto_listing_product_id'] ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($stats['top_trends'])): ?>
                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">Chưa có trend — bấm "Quét Reddit"</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(__DIR__ . '/dcos-layout-close.php');