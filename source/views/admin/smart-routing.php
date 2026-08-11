<?php
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/../../libs/smart_router.php');

$router = new SmartRouter($CMSNT);
$stats = $router->getStats();
$rules = $CMSNT->get_list_safe("SELECT * FROM smart_routing_rules ORDER BY id", []);

$dcos_active = 'smart-routing';
$body = ['title' => 'Smart Routing | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');
?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Smart Routing</h1>
        <p class="text-sm text-slate-500 mt-1">Intelligent Supplier Selection — tự động chọn supplier tối ưu</p>
    </div>
    <a href="<?= base_url() ?>" class="btn-outline px-4 py-2 rounded-lg text-sm inline-flex items-center gap-2">
        <i class="bx bx-arrow-back"></i> Về Shop
    </a>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="custom-card stat-card border-l-blue-500" style="border-left-color: #3b82f6;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Active Rules</p>
                <h3 class="text-2xl font-bold text-slate-800"><?= $stats['active_rules'] ?></h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="bx bx-check-shield text-xl text-blue-500"></i>
            </div>
        </div>
    </div>
    <div class="custom-card stat-card border-l-emerald-500" style="border-left-color: #10b981;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Success Rate</p>
                <h3 class="text-2xl font-bold text-slate-800"><?= $stats['success_rate'] ?>%</h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center">
                <i class="bx bx-trending-up text-xl text-emerald-500"></i>
            </div>
        </div>
    </div>
    <div class="custom-card stat-card border-l-cyan-500" style="border-left-color: #06b6d4;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Routes Today</p>
                <h3 class="text-2xl font-bold text-slate-800"><?= $stats['routes_today'] ?></h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-cyan-50 flex items-center justify-center">
                <i class="bx bx-git-branch text-xl text-cyan-500"></i>
            </div>
        </div>
    </div>
    <div class="custom-card stat-card border-l-amber-500" style="border-left-color: #f59e0b;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Active Suppliers</p>
                <h3 class="text-2xl font-bold text-slate-800"><?= $stats['total_suppliers'] ?></h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="bx bx-server text-xl text-amber-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- Routing Rules Table -->
<div class="custom-card overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">📋 Routing Rules</h4>
        <span class="text-xs text-slate-400"><?= count($rules) ?> rules</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">ID</th><th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Price</th><th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4">Speed</th><th class="px-6 py-4">Error</th>
                    <th class="px-6 py-4">Refund</th><th class="px-6 py-4">Stability</th>
                    <th class="px-6 py-4">Fallback</th><th class="px-6 py-4">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($rules as $r): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-xs text-slate-400">#<?= $r['id'] ?></td>
                    <td class="px-6 py-4 font-bold text-slate-700"><?= htmlspecialchars($r['name']) ?></td>
                    <td class="px-6 py-4"><?= round($r['price_weight']*100) ?>%</td>
                    <td class="px-6 py-4"><?= round($r['stock_weight']*100) ?>%</td>
                    <td class="px-6 py-4"><?= round($r['speed_weight']*100) ?>%</td>
                    <td class="px-6 py-4"><?= round($r['error_weight']*100) ?>%</td>
                    <td class="px-6 py-4"><?= round($r['refund_weight']*100) ?>%</td>
                    <td class="px-6 py-4"><?= round($r['stability_weight']*100) ?>%</td>
                    <td class="px-6 py-4"><span class="badge bg-slate-100 text-slate-600"><?= $r['fallback_strategy'] ?></span></td>
                    <td class="px-6 py-4"><?= $r['status'] ? '<span class="badge bg-emerald-100 text-emerald-700">✅ Active</span>' : '<span class="badge bg-slate-100 text-slate-500">❌ Off</span>' ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($rules)): ?>
                <tr><td colspan="10" class="px-6 py-8 text-center text-slate-400">Chưa có routing rules — thêm supplier để bắt đầu</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Supplier Performance -->
<div class="custom-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">📊 Supplier Performance Scores</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Supplier</th><th class="px-6 py-4">Score</th>
                    <th class="px-6 py-4">Total Orders</th><th class="px-6 py-4">Success</th>
                    <th class="px-6 py-4">Avg Response</th><th class="px-6 py-4">Success Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (!empty($stats['supplier_performance'])): foreach ($stats['supplier_performance'] as $sp):
                    $srate = $sp['total_orders'] > 0 ? round(($sp['success_orders']/$sp['total_orders'])*100) : 0;
                    $bar_color = $sp['score']>=70 ? 'bg-emerald-500' : ($sp['score']>=40 ? 'bg-amber-500' : 'bg-red-500');
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-700"><?= htmlspecialchars($sp['domain'] ?? 'Supplier #'.$sp['id']) ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="progress-bar flex-1 max-w-[120px]">
                                <div class="progress-fill <?= $bar_color ?>" style="width:<?= $sp['score'] ?>%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-600"><?= round($sp['score']) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><?= $sp['total_orders'] ?></td>
                    <td class="px-6 py-4"><?= $sp['success_orders'] ?></td>
                    <td class="px-6 py-4"><?= round($sp['avg_response_ms']/1000, 2) ?>s</td>
                    <td class="px-6 py-4">
                        <span class="badge <?= $srate>=80 ? 'bg-emerald-100 text-emerald-700' : ($srate>=50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') ?>">
                            <?= $srate ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">No performance data yet — make some orders first!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(__DIR__ . '/dcos-layout-close.php');