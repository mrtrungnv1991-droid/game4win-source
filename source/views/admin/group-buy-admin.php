<?php
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/../../libs/group_buy.php');

$gb = new GroupBuy($CMSNT);
$stats = $gb->getStats();

// Handle actions
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // CSRF guard (Punch list #1): chan moi POST khong co token hop le
    if (!verify_csrf()) {
        http_response_code(403);
        die('CSRF token khong hop le. Vui long quay lai va thu lai.');
    }
    if ($_POST['action'] === 'create_deal') {
        $result = $gb->createDeal([
            'title' => $_POST['title'] ?? '',
            'product_name' => $_POST['product_name'] ?? '',
            'original_price' => (float)($_POST['original_price'] ?? 0),
            'group_price' => (float)($_POST['group_price'] ?? 0),
            'min_participants' => (int)($_POST['min_participants'] ?? 5),
            'product_type' => $_POST['product_type'] ?? 'game_key',
            'status' => $_POST['status'] ?? 'draft',
        ]);
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 '.($result['status']=='success'?'border-l-emerald-500 bg-emerald-50 text-emerald-800':'border-l-red-500 bg-red-50 text-red-800').' text-sm font-medium">'.$result['msg'].'</div>';
    }
    if ($_POST['action'] === 'fulfill' && !empty($_POST['deal_id'])) {
        $result = $gb->fulfillDeal((int)$_POST['deal_id']);
        $msg = '<div class="custom-card p-4 mb-6 border-l-4 border-l-blue-500 bg-blue-50 text-blue-800 text-sm font-medium">Fulfilled '.$result['fulfilled'].'/'.$result['total'].' keys</div>';
    }
}

$deals = $CMSNT->get_list_safe("SELECT * FROM group_buy_deals ORDER BY created_at DESC", []);

$dcos_active = 'group-buy-admin';
$body = ['title' => 'Group Buy | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');
?>

<?= $msg ?>

<div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Group Buy</h1>
        <p class="text-sm text-slate-500 mt-1">Crowd-Buying Management — mua chung giá sỉ</p>
    </div>
    <button onclick="document.getElementById('createModal').classList.remove('hidden')" 
            class="btn-primary px-5 py-2.5 rounded-lg text-sm inline-flex items-center gap-2 shadow-lg shadow-purple-500/20">
        <i class="bx bx-plus text-xl"></i> New Deal
    </button>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php
    $stat_cards = [
        ['Active Deals', $stats['active_deals'], 'bx:bx-shopping-bag', 'indigo', 'indigo'],
        ['Participants', $stats['total_participants'], 'bx:bx-user-plus', 'purple', 'purple'],
        ['Completed', $stats['completed_deals'], 'bx:bx-check-double', 'emerald', 'emerald'],
        ['Pending Delivery', $stats['pending_delivery'], 'bx:bx-time-five', 'amber', 'amber'],
    ];
    foreach ($stat_cards as [$label, $value, $icon, $color, $border]):
    ?>
    <div class="custom-card stat-card" style="border-left-color: var(--<?= $border ?>-500, #8b5cf6);">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1"><?= $label ?></p>
                <h3 class="text-2xl font-bold text-slate-800"><?= number_format($value) ?></h3>
            </div>
            <div class="w-10 h-10 rounded-lg bg-<?= $color ?>-50 flex items-center justify-center">
                <i class=\"bx <?= str_replace('bx:', '', $icon) ?> text-xl text-<?= $color ?>-500\"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Deals Table -->
<div class="custom-card overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h4 class="font-bold text-slate-800 uppercase text-xs tracking-widest">📦 All Group Buy Deals</h4>
        <span class="text-xs text-slate-400"><?= count($deals) ?> deals</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Title</th><th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Original</th><th class="px-6 py-4">Group Price</th>
                    <th class="px-6 py-4">Discount</th><th class="px-6 py-4">Progress</th>
                    <th class="px-6 py-4">Status</th><th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($deals as $d): 
                    $pct = $d['min_participants'] > 0 ? round(($d['current_participants']/$d['min_participants'])*100) : 0;
                    $status_class = match($d['status']) {
                        'active' => 'bg-blue-100 text-blue-700',
                        'filled' => 'bg-amber-100 text-amber-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                        'cancelled' => 'bg-slate-100 text-slate-500',
                        'expired' => 'bg-red-100 text-red-500',
                        default => 'bg-slate-100 text-slate-500'
                    };
                    $bar_color = $pct >= 100 ? 'bg-emerald-500' : 'bg-indigo-500';
                ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-700"><?= htmlspecialchars($d['title']) ?></span>
                            <span class="text-[10px] text-slate-400">ID: GB-<?= $d['id'] ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="badge bg-indigo-100 text-indigo-700"><?= $d['product_type'] ?></span></td>
                    <td class="px-6 py-4 text-sm text-slate-400 line-through">$<?= number_format($d['original_price'],2) ?></td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-800">$<?= number_format($d['group_price'],2) ?></td>
                    <td class="px-6 py-4"><span class="badge bg-green-100 text-green-700">-<?= $d['discount_percent'] ?>%</span></td>
                    <td class="px-6 py-4">
                        <div class="w-32">
                            <div class="flex justify-between mb-1.5">
                                <span class="text-[10px] font-bold text-slate-500 uppercase"><?= $d['current_participants'] ?>/<?= $d['min_participants'] ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill <?= $bar_color ?>" style="width:<?= min(100,$pct) ?>%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="badge <?= $status_class ?>"><?= $d['status'] ?></span></td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <?php if ($d['status'] === 'filled'): ?>
                            <form method="POST" style="display:inline"><?= csrf_field() ?>
                                <input type="hidden" name="action" value="fulfill">
                                <input type="hidden" name="deal_id" value="<?= $d['id'] ?>">
                                <button class="px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-600 hover:text-white transition-colors">Fulfill</button>
                            </form>
                            <?php endif; ?>
                            <a href="<?= base_url_admin('group-buy-detail&id='.$d['id']) ?>" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-200 transition-colors">View</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($deals)): ?>
                <tr><td colspan="8" class="px-6 py-8 text-center text-slate-400">Chưa có deal — bấm "New Deal" để tạo</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="modal-overlay absolute inset-0" onclick="document.getElementById('createModal').classList.add('hidden')"></div>
    <div class="custom-card relative z-10 w-full max-w-lg bg-white p-0 overflow-hidden">
        <form method="POST"><?= csrf_field() ?>
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-800 text-lg">New Group Buy Deal</h3>
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" name="action" value="create_deal">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Title</label><input name="title" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" required placeholder="e.g. Steam Wallet $10"></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Product Name</label><input name="product_name" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" required></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Original Price ($)</label><input name="original_price" type="number" step="0.01" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" required></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Group Price ($)</label><input name="group_price" type="number" step="0.01" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" required></div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Min Participants</label><input name="min_participants" type="number" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none" value="5"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Product Type</label>
                        <select name="product_type" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none">
                            <option value="game_key">Game Key</option><option value="gift_card">Gift Card</option>
                            <option value="software">Software</option><option value="account">Account</option>
                        </select>
                    </div>
                    <div><label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500/20 outline-none">
                            <option value="draft">Draft</option><option value="active">Active (live)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Cancel</button>
                <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-sm">Create Deal</button>
            </div>
        </form>
    </div>
</div>

<?php require_once(__DIR__ . '/dcos-layout-close.php');