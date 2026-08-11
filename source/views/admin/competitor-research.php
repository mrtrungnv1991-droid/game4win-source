<?php
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/../../libs/competitor_research.php');

$cr = new CompetitorResearch($CMSNT);
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // CSRF guard (Punch list #1): chan moi POST khong co token hop le
    if (!verify_csrf()) {
        http_response_code(403);
        die('CSRF token khong hop le. Vui long quay lai va thu lai.');
    }
    if ($_POST['action'] === 'crawl') {
        $result = $cr->crawlDeals(intval($_POST['limit'] ?? 30), intval($_POST['min_discount'] ?? 20));
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.($result['status']==='success'?'border-l-emerald-500 bg-emerald-50 text-emerald-800':'border-l-red-500 bg-red-50 text-red-800').' text-sm font-medium">'.($result['status']==='success' ? "✅ Crawl xong: {$result['imported']} mới, {$result['updated']} cập nhật (tổng {$result['total_crawled']} deals)" : "❌ ".$result['msg']).'</div>';
    }
    if ($_POST['action'] === 'import' && !empty($_POST['competitor_id'])) {
        $result = $cr->importToShop(intval($_POST['competitor_id']), intval($_POST['markup'] ?? 15));
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.($result['status']==='success'?'border-l-emerald-500 bg-emerald-50 text-emerald-800':'border-l-red-500 bg-red-50 text-red-800').' text-sm font-medium">'.($result['status']==='success' ? "✅ {$result['msg']} — giá bán ".number_format($result['sell_price'])."đ" : "❌ ".$result['msg']).'</div>';
    }
    if ($_POST['action'] === 'delete' && !empty($_POST['competitor_id'])) {
        $result = $cr->deleteDeal(intval($_POST['competitor_id']));
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 border-l-slate-500 bg-slate-50 text-slate-700 text-sm font-medium">🗑️ '.$result['msg'].'</div>';
    }
}

$stats = $cr->getStats();
$comparisons = $cr->comparePrices();

$dcos_active = 'competitor-research';
$body = ['title' => 'Competitor Research | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');
?>

<?= $msg ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Competitor Research</h1>
        <p class="text-sm text-slate-500 mt-1">Học từ G2A/Eneba/Kinguin — theo dõi giá thị trường real-time</p>
    </div>
    <button onclick="document.getElementById('crawlModal').classList.remove('hidden')" 
            class="btn-primary px-5 py-2.5 rounded-lg text-sm inline-flex items-center gap-2 shadow-lg shadow-purple-500/20">
        <i class="bx bx-download text-xl"></i> Crawl Deals Mới
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="custom-card stat-card" style="border-left-color: var(--primary);">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Sản phẩm đã crawl</p><h3 class="text-2xl font-bold text-slate-800"><?= $stats['total_competitor_products'] ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center"><i class="bx bx-target-lock text-xl text-purple-500"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color: #10b981;">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Đã import vào shop</p><h3 class="text-2xl font-bold text-slate-800"><?= $stats['imported_count'] ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="bx bx-import text-xl text-emerald-500"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color: #f59e0b;">
        <div class="flex justify-between items-start">
            <div><p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Nguồn dữ liệu</p><h3 class="text-2xl font-bold text-slate-800"><?= count($stats['sources']) ?></h3></div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center"><i class="bx bx-data text-xl text-amber-500"></i></div>
        </div>
    </div>
</div>

<!-- Info alert -->
<div class="custom-card p-4 mb-8 bg-slate-50 border-slate-200 text-sm text-slate-600">
    <strong>📚 Nguồn dữ liệu:</strong> CheapShark API — aggregate giá real-time từ G2A, Steam, Green Man Gaming, Fanatical, GamersGate...
    <span class="text-slate-400">(G2A/Eneba/Kinguin chặn bot trực tiếp nên dùng aggregator).</span>
</div>

<!-- Products Table -->
<div class="custom-card overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">🛒 Sản phẩm Competitor</h4>
        <span class="text-xs text-slate-400"><?= count($stats['recent'] ?? []) ?> items</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Tên game</th><th class="px-6 py-4">Platform</th><th class="px-6 py-4">Giá sale</th><th class="px-6 py-4">Giá gốc</th><th class="px-6 py-4">Giảm</th><th class="px-6 py-4">Trạng thái</th><th class="px-6 py-4 text-right">Action</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($stats['recent'] ?? [] as $cp):
                    $disc = $cp['retail_price'] > 0 ? round((1 - $cp['price']/$cp['retail_price'])*100) : 0;
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-700"><?= htmlspecialchars($cp['name']) ?></td>
                    <td class="px-6 py-4"><span class="badge bg-blue-100 text-blue-700"><?= $cp['platform'] ?></span></td>
                    <td class="px-6 py-4 text-emerald-600 font-bold"><?= number_format($cp['price']) ?>đ</td>
                    <td class="px-6 py-4 text-sm text-slate-400 line-through"><?= number_format($cp['retail_price']) ?>đ</td>
                    <td class="px-6 py-4"><span class="badge bg-green-100 text-green-700">-<?= $disc ?>%</span></td>
                    <td class="px-6 py-4">
                        <?php if ($cp['imported_product_id'] > 0): ?>
                            <span class="flex items-center gap-1.5 text-xs text-emerald-600 font-medium"><i class="bx bx-check-circle text-emerald-500"></i> Imported #<?= $cp['imported_product_id'] ?></span>
                        <?php else: ?>
                            <span class="flex items-center gap-1.5 text-xs text-slate-400 font-medium"><i class="bx bx-circle text-slate-300"></i> Chưa import</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($cp['imported_product_id'] == 0): ?>
                        <form method="POST" style="display:inline"><?= csrf_field() ?>
                            <input type="hidden" name="action" value="import">
                            <input type="hidden" name="competitor_id" value="<?= $cp['id'] ?>">
                            <input type="hidden" name="markup" value="15">
                            <button class="text-purple-500 hover:bg-purple-50 p-2 rounded transition-colors" title="Import +15%"><i class="bx bx-import"></i></button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Xóa deal này?')"><?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="competitor_id" value="<?= $cp['id'] ?>">
                            <button class="text-red-500 hover:bg-red-50 p-2 rounded transition-colors"><i class="bx bx-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($stats['recent'])): ?>
                <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">Chưa có data — bấm "Crawl Deals Mới"</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Price Comparison -->
<div class="custom-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">⚖️ So sánh giá của mình vs Competitor</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr><th class="px-6 py-4">Sản phẩm</th><th class="px-6 py-4">Giá mình</th><th class="px-6 py-4">Giá competitor</th><th class="px-6 py-4">Chênh lệch</th><th class="px-6 py-4">Cạnh tranh?</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($comparisons as $c): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-slate-700"><?= htmlspecialchars($c['name']) ?></td>
                    <td class="px-6 py-4 text-sm"><?= number_format($c['my_price']) ?>đ</td>
                    <td class="px-6 py-4 text-sm"><?= number_format($c['competitor_price']) ?>đ</td>
                    <td class="px-6 py-4 text-sm font-bold <?= $c['diff_percent'] > 0 ? 'text-red-500' : 'text-emerald-600' ?>"><?= $c['diff_percent'] > 0 ? '+' : '' ?><?= $c['diff_percent'] ?>%</td>
                    <td class="px-6 py-4"><?= $c['competitive'] ? '<span class="badge bg-emerald-100 text-emerald-700">✅ Competitive</span>' : '<span class="badge bg-amber-100 text-amber-700">⚠️ Cao hơn</span>' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($comparisons)): ?>
                <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Import sản phẩm từ competitor để so sánh giá</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Crawl Modal -->
<div id="crawlModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="modal-overlay absolute inset-0" onclick="document.getElementById('crawlModal').classList.add('hidden')"></div>
    <div class="custom-card relative z-10 w-full max-w-md bg-white p-0 overflow-hidden">
        <form method="POST"><?= csrf_field() ?>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">Crawl Deals</h3>
                <button type="button" onclick="document.getElementById('crawlModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="crawl">
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Số deals tối đa</label><input name="limit" type="number" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" value="30" min="5" max="60"></div>
                <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Giảm giá tối thiểu (%)</label><input name="min_discount" type="number" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" value="20" min="0" max="90"></div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('crawlModal').classList.add('hidden')" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-sm">Crawl ngay</button>
            </div>
        </form>
    </div>
</div>

<?php require_once(__DIR__ . '/dcos-layout-close.php');