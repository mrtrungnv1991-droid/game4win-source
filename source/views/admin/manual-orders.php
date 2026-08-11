<?php
/**
 * Đơn hàng thủ công — Quản lý toàn bộ đơn Top-up (input Topup → output đơn thủ công).
 * Data source: product_order WHERE topup_tier_id > 0
 * Layout: DCOS (Tailwind) — đồng bộ các module Digital Commerce OS.
 */
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');

// ===== XỬ LÝ POST ACTIONS (trước khi output HTML) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $act = $_POST['action'];

    // Lưu ghi chú nội bộ (AJAX)
    if ($act === 'save_note') {
        $oid = intval($_POST['order_id'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        if ($oid > 0) {
            $CMSNT->update('product_order', ['admin_note' => $note], "`id` = ?", [$oid]);
        }
        die(json_encode(['status' => 'success', 'msg' => 'Đã lưu ghi chú']));
    }

    // Đổi trạng thái đơn
    if (in_array($act, ['mark_success', 'mark_processing', 'mark_failed', 'refund', 'delete'])) {
        $oid = intval($_POST['order_id'] ?? 0);
        $order = $oid > 0 ? $CMSNT->get_row_safe("SELECT * FROM `product_order` WHERE `id` = ? AND `topup_tier_id` > 0", [$oid]) : null;
        if (!$order) {
            die(json_encode(['status' => 'error', 'msg' => 'Đơn hàng không tồn tại']));
        }

        if ($act === 'mark_success') {
            $CMSNT->update('product_order', ['topup_status' => 'success', 'update_gettime' => gettime()], "`id` = ?", [$oid]);
            die(json_encode(['status' => 'success', 'msg' => 'Đã đánh dấu hoàn thành']));
        }
        if ($act === 'mark_processing') {
            $CMSNT->update('product_order', ['topup_status' => 'processing', 'update_gettime' => gettime()], "`id` = ?", [$oid]);
            die(json_encode(['status' => 'success', 'msg' => 'Đang xử lý đơn']));
        }
        if ($act === 'mark_failed') {
            $CMSNT->update('product_order', ['topup_status' => 'failed', 'update_gettime' => gettime()], "`id` = ?", [$oid]);
            die(json_encode(['status' => 'success', 'msg' => 'Đã hủy đơn']));
        }
        if ($act === 'refund') {
            if ($order['topup_status'] !== 'refunded') {
                $CMSNT->update('product_order', ['topup_status' => 'refunded', 'refund' => 1, 'update_gettime' => gettime()], "`id` = ?", [$oid]);
                // Hoàn tiền cho buyer
                $before = $CMSNT->get_row_safe("SELECT `money` FROM `users` WHERE `id` = ?", [$order['buyer']])['money'] ?? 0;
                $CMSNT->cong('users', 'money', $order['pay'], "`id` = ?", [$order['buyer']]);
                $CMSNT->insert('dongtien', [
                    'user_id'         => $order['buyer'],
                    'sotientruoc'     => $before,
                    'sotienthaydoi'   => $order['pay'],
                    'sotiensau'       => $before + $order['pay'],
                    'thoigian'        => gettime(),
                    'noidung'         => "Hoàn tiền đơn topup #" . $order['trans_id'],
                    'transid'         => $order['trans_id']
                ]);
            }
            die(json_encode(['status' => 'success', 'msg' => 'Đã hoàn tiền']));
        }
        if ($act === 'delete') {
            $CMSNT->remove('product_order', "`id` = ?", [$oid]);
            die(json_encode(['status' => 'success', 'msg' => 'Đã xóa đơn']));
        }
    }

    // Dọn dẹp đơn cũ (đã hoàn thành / đã hủy quá N ngày)
    if ($act === 'cleanup') {
        $days = max(1, intval($_POST['days'] ?? 30));
        $cutoff = date('Y-m-d H:i:s', time() - $days * 86400);
        $CMSNT->remove('product_order', "`topup_tier_id` > 0 AND `topup_status` IN ('success','failed','refunded') AND `create_gettime` < ?", [$cutoff]);
        die(json_encode(['status' => 'success', 'msg' => 'Đã dọn dẹp đơn cũ hơn ' . $days . ' ngày']));
    }

    die(json_encode(['status' => 'error', 'msg' => 'Thao tác không hợp lệ']));
}

// ===== QUERY DATA =====
$page  = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

$where = "WHERE `topup_tier_id` IS NOT NULL AND `topup_tier_id` > 0";
$params = [];

if (!empty($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'success', 'failed', 'refunded'])) {
    $where .= " AND `topup_status` = ?";
    $params[] = $_GET['status'];
}
if (!empty($_GET['search'])) {
    $q = '%' . check_string($_GET['search']) . '%';
    $where .= " AND (`trans_id` LIKE ? OR `game_uid` LIKE ? OR `product_name` LIKE ?)";
    $params[] = $q; $params[] = $q; $params[] = $q;
}

$total = $CMSNT->num_rows_safe("SELECT id FROM `product_order` {$where}", $params);
$orders = $CMSNT->get_list_safe("SELECT * FROM `product_order` {$where} ORDER BY `id` DESC LIMIT {$limit} OFFSET {$offset}", $params);
$totalPages = max(1, ceil($total / $limit));

// Stats
$stTotal      = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0");
$stPending    = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0 AND `topup_status` = 'pending'");
$stProcessing = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0 AND `topup_status` = 'processing'");
$stSuccess    = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0 AND `topup_status` = 'success'");
$stFailed     = $CMSNT->num_rows("SELECT id FROM `product_order` WHERE `topup_tier_id` > 0 AND `topup_status` IN ('failed','refunded')");

$dcos_active = 'manual-orders';
$body = ['title' => 'Đơn hàng thủ công | Digital Commerce OS'];
require_once(__DIR__ . '/dcos-layout.php');

$statusMeta = [
    'pending'    => ['label' => 'Chờ xử lý',  'cls' => 'bg-amber-100 text-amber-700'],
    'processing' => ['label' => 'Đang xử lý', 'cls' => 'bg-blue-100 text-blue-700'],
    'success'    => ['label' => 'Hoàn thành', 'cls' => 'bg-emerald-100 text-emerald-700'],
    'failed'     => ['label' => 'Đã hủy',     'cls' => 'bg-red-100 text-red-700'],
    'refunded'   => ['label' => 'Đã hoàn',    'cls' => 'bg-slate-200 text-slate-600'],
];
?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
            <i class="bx bxs-cart-alt text-[var(--primary)]"></i>
            Quản lý đơn hàng thủ công
        </h1>
        <p class="text-sm text-slate-500 mt-1">Toàn bộ đơn Top-up — xử lý thủ công bởi admin</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="openCleanup()" class="btn-outline px-4 py-2 rounded-lg text-sm inline-flex items-center gap-2 text-red-500 border-red-200 hover:bg-red-50">
            <i class="bx bx-trash"></i> Dọn dẹp
        </button>
        <a href="<?= base_url_admin('topup-orders') ?>" class="btn-outline px-4 py-2 rounded-lg text-sm inline-flex items-center gap-2">
            <i class="bx bx-package"></i> Đơn nạp game
        </a>
        <a href="<?= base_url_admin('home') ?>" class="btn-outline px-4 py-2 rounded-lg text-sm inline-flex items-center gap-2">
            <i class="bx bx-arrow-back"></i> Quay lại
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
    <div class="custom-card stat-card" style="border-left-color:#845adf;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tổng đơn hàng</p>
                <p class="text-2xl font-bold text-slate-800"><?= number_format($stTotal) ?></p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center"><i class="bx bxs-cart-alt text-purple-600 text-xl"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color:#f59e0b;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Chờ xử lý</p>
                <p class="text-2xl font-bold text-slate-800"><?= number_format($stPending) ?></p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center"><i class="bx bx-time-five text-amber-600 text-xl"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color:#3b82f6;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Đang xử lý</p>
                <p class="text-2xl font-bold text-slate-800"><?= number_format($stProcessing) ?></p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center"><i class="bx bx-loader-circle text-blue-600 text-xl"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color:#10b981;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Hoàn thành</p>
                <p class="text-2xl font-bold text-slate-800"><?= number_format($stSuccess) ?></p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center"><i class="bx bxs-check-circle text-emerald-600 text-xl"></i></div>
        </div>
    </div>
    <div class="custom-card stat-card" style="border-left-color:#ef4444;">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Đã hủy / Hoàn</p>
                <p class="text-2xl font-bold text-slate-800"><?= number_format($stFailed) ?></p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center"><i class="bx bxs-x-circle text-red-600 text-xl"></i></div>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="custom-card mb-6">
    <button onclick="document.getElementById('filterBody').classList.toggle('hidden'); this.querySelector('.chev').classList.toggle('rotate-180')"
            class="w-full flex items-center justify-between px-6 py-4 text-sm font-bold text-slate-700">
        <span class="flex items-center gap-2"><i class="bx bx-filter-alt"></i> Bộ lọc tìm kiếm</span>
        <i class="bx bx-chevron-down chev transition-transform"></i>
    </button>
    <div id="filterBody" class="px-6 pb-5 <?= (!empty($_GET['search']) || !empty($_GET['status'])) ? '' : 'hidden' ?>">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="action" value="manual-orders">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-bold text-slate-400 uppercase mb-1 block">Tìm kiếm</label>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                       placeholder="Mã ĐH, UID, tên game..."
                       class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--primary)]/20 outline-none">
            </div>
            <div class="w-44">
                <label class="text-xs font-bold text-slate-400 uppercase mb-1 block">Trạng thái</label>
                <select name="status" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-[var(--primary)]/20 outline-none bg-white">
                    <option value="">Tất cả</option>
                    <?php foreach ($statusMeta as $k => $v): ?>
                    <option value="<?= $k ?>" <?= ($_GET['status'] ?? '') === $k ? 'selected' : '' ?>><?= $v['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary px-5 py-2 text-sm inline-flex items-center gap-2">
                <i class="bx bx-search"></i> Lọc
            </button>
            <a href="<?= base_url_admin('manual-orders') ?>" class="btn-outline px-4 py-2 text-sm">Xóa lọc</a>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="custom-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/50">
                    <th class="px-4 py-3">Bên mua</th>
                    <th class="px-4 py-3">Đơn hàng</th>
                    <th class="px-4 py-3">Gói / UID</th>
                    <th class="px-4 py-3 text-right">Thanh toán</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Ghi chú nội bộ</th>
                    <th class="px-4 py-3">Ngày tạo</th>
                    <th class="px-4 py-3 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($orders)): ?>
                <tr><td colspan="8" class="px-4 py-12 text-center text-slate-400">
                    <i class="bx bx-package text-4xl mb-2 block mx-auto"></i>
                    Chưa có đơn hàng top-up nào
                </td></tr>
                <?php else: foreach ($orders as $o):
                    $buyer = $CMSNT->get_row_safe("SELECT `id`,`username`,`money` FROM `users` WHERE `id` = ?", [$o['buyer']]);
                    $note = json_decode($o['note'] ?? '{}', true) ?: [];
                    $sm = $statusMeta[$o['topup_status']] ?? ['label' => $o['topup_status'], 'cls' => 'bg-slate-100 text-slate-500'];
                    $profit = ($o['pay'] ?? 0) - ($o['cost'] ?? 0);
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors" id="row-<?= $o['id'] ?>">
                    <!-- Bên mua -->
                    <td class="px-4 py-3 align-top">
                        <div class="font-bold text-slate-700 text-sm"><?= htmlspecialchars($buyer['username'] ?? '—') ?></div>
                        <div class="text-xs text-slate-400">ID: <?= $o['buyer'] ?></div>
                        <div class="text-xs text-slate-400">Số dư: <?= number_format($buyer['money'] ?? 0) ?>đ</div>
                    </td>
                    <!-- Đơn hàng -->
                    <td class="px-4 py-3 align-top">
                        <code class="text-xs bg-slate-100 px-2 py-0.5 rounded font-mono"><?= htmlspecialchars($o['trans_id']) ?></code>
                        <?php if (!empty($o['api_transid'])): ?>
                        <div class="text-xs text-slate-400 mt-1">API: <?= htmlspecialchars($o['api_transid']) ?></div>
                        <?php endif; ?>
                    </td>
                    <!-- Gói / UID -->
                    <td class="px-4 py-3 align-top">
                        <div class="text-sm font-medium text-slate-700 flex items-center gap-1">
                            <i class="bx bxs-zap text-amber-500"></i>
                            <?= htmlspecialchars($o['product_name']) ?>
                        </div>
                        <?php if (!empty($o['game_uid'])): ?>
                        <div class="text-xs text-slate-500 mt-1">UID: <b><?= htmlspecialchars($o['game_uid']) ?></b></div>
                        <?php endif; ?>
                        <?php if (!empty($note['game_name'])): ?>
                        <div class="text-xs text-slate-400"><?= htmlspecialchars($note['game_name']) ?></div>
                        <?php endif; ?>
                    </td>
                    <!-- Thanh toán -->
                    <td class="px-4 py-3 align-top text-right">
                        <div class="font-bold text-slate-800 text-sm"><?= number_format($o['pay']) ?>đ</div>
                        <?php if (($o['cost'] ?? 0) > 0): ?>
                        <div class="text-xs <?= $profit >= 0 ? 'text-emerald-600' : 'text-red-500' ?>">
                            Vốn: <?= number_format($o['cost']) ?>đ · Lãi: <?= ($profit >= 0 ? '+' : '') . number_format($profit) ?>đ
                        </div>
                        <?php endif; ?>
                        <div class="text-xs text-slate-400">SL: <?= $o['amount'] ?? 1 ?></div>
                    </td>
                    <!-- Trạng thái -->
                    <td class="px-4 py-3 align-top">
                        <span class="badge <?= $sm['cls'] ?>"><?= $sm['label'] ?></span>
                    </td>
                    <!-- Ghi chú nội bộ -->
                    <td class="px-4 py-3 align-top min-w-[160px]">
                        <textarea rows="2" data-oid="<?= $o['id'] ?>"
                                  onblur="saveNote(this)"
                                  placeholder="Nhập ghi chú..."
                                  class="w-full text-xs border border-slate-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-[var(--primary)]/20 outline-none resize-none bg-slate-50/50"><?= htmlspecialchars($o['admin_note'] ?? '') ?></textarea>
                    </td>
                    <!-- Ngày tạo -->
                    <td class="px-4 py-3 align-top">
                        <div class="text-xs text-slate-500"><?= date('d/m/Y H:i', strtotime($o['create_gettime'])) ?></div>
                    </td>
                    <!-- Thao tác -->
                    <td class="px-4 py-3 align-top">
                        <div class="flex flex-col gap-1.5 items-center">
                            <?php if ($o['topup_status'] === 'pending'): ?>
                            <button onclick="orderAction(<?= $o['id'] ?>,'mark_processing')" class="w-full text-xs px-3 py-1.5 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition-colors inline-flex items-center justify-center gap-1">
                                <i class="bx bx-loader-circle"></i> Xử lý
                            </button>
                            <?php endif; ?>
                            <?php if (in_array($o['topup_status'], ['pending', 'processing'])): ?>
                            <button onclick="orderAction(<?= $o['id'] ?>,'mark_success')" class="w-full text-xs px-3 py-1.5 rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition-colors inline-flex items-center justify-center gap-1">
                                <i class="bx bxs-check-circle"></i> Hoàn thành
                            </button>
                            <button onclick="orderAction(<?= $o['id'] ?>,'refund')" class="w-full text-xs px-3 py-1.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition-colors inline-flex items-center justify-center gap-1">
                                <i class="bx bx-refresh"></i> Hoàn tiền
                            </button>
                            <button onclick="orderAction(<?= $o['id'] ?>,'mark_failed')" class="w-full text-xs px-3 py-1.5 rounded-lg bg-orange-500 text-white hover:bg-orange-600 transition-colors inline-flex items-center justify-center gap-1">
                                <i class="bx bxs-x-circle"></i> Hủy
                            </button>
                            <?php endif; ?>
                            <?php if (in_array($o['topup_status'], ['success', 'failed', 'refunded'])): ?>
                            <button onclick="orderAction(<?= $o['id'] ?>,'delete')" class="w-full text-xs px-3 py-1.5 rounded-lg bg-red-500 text-white hover:bg-red-600 transition-colors inline-flex items-center justify-center gap-1">
                                <i class="bx bx-trash"></i> Xóa
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
        <span class="text-xs text-slate-400">Tổng <?= number_format($total) ?> đơn · Trang <?= $page ?>/<?= $totalPages ?></span>
        <div class="flex gap-1">
            <?php
            $qs = http_build_query(array_filter(['module' => 'admin', 'action' => 'manual-orders', 'status' => $_GET['status'] ?? '', 'search' => $_GET['search'] ?? '']));
            $start = max(1, $page - 2); $end = min($totalPages, $page + 2);
            for ($i = $start; $i <= $end; $i++): ?>
            <a href="?<?= $qs ?>&page=<?= $i ?>"
               class="px-3 py-1.5 rounded-lg text-xs font-bold <?= $i == $page ? 'bg-[var(--primary)] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?> transition-colors"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Cleanup Modal -->
<div id="cleanupModal" class="fixed inset-0 z-[100] hidden items-center justify-center modal-overlay">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-2 flex items-center gap-2">
            <i class="bx bx-trash text-red-500"></i> Dọn dẹp đơn cũ
        </h3>
        <p class="text-sm text-slate-500 mb-4">Xóa các đơn đã <b>Hoàn thành / Đã hủy / Đã hoàn</b> cũ hơn số ngày chỉ định.</p>
        <label class="text-xs font-bold text-slate-400 uppercase mb-1 block">Số ngày giữ lại</label>
        <input type="number" id="cleanupDays" value="30" min="1" class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm mb-4 focus:ring-2 focus:ring-[var(--primary)]/20 outline-none">
        <div class="flex gap-2 justify-end">
            <button onclick="closeCleanup()" class="btn-outline px-4 py-2 text-sm">Hủy</button>
            <button onclick="runCleanup()" class="btn-primary px-4 py-2 text-sm bg-red-500 hover:bg-red-600">Xóa đơn cũ</button>
        </div>
    </div>
</div>

<script>
function orderAction(oid, act) {
    var msgs = {
        'mark_success': 'Xác nhận đơn đã nạp thành công?',
        'mark_processing': 'Chuyển đơn sang đang xử lý?',
        'mark_failed': 'Xác nhận hủy đơn này?',
        'refund': 'Hoàn tiền đơn này cho khách?',
        'delete': 'Xóa vĩnh viễn đơn này?'
    };
    if (!confirm(msgs[act] || 'Xác nhận?')) return;
    var fd = new FormData();
    fd.append('action', act);
    fd.append('order_id', oid);
    fetch(window.location.pathname + '?module=admin&action=manual-orders', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            showToast(d.msg, d.status === 'success' ? 'success' : 'error');
            if (d.status === 'success') setTimeout(function() { location.reload(); }, 600);
        })
        .catch(function() { showToast('Lỗi kết nối', 'error'); });
}

function saveNote(el) {
    var fd = new FormData();
    fd.append('action', 'save_note');
    fd.append('order_id', el.getAttribute('data-oid'));
    fd.append('note', el.value);
    fetch(window.location.pathname + '?module=admin&action=manual-orders', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(d) { if (d.status === 'success') showToast('Đã lưu ghi chú', 'success'); });
}

function openCleanup() { var m = document.getElementById('cleanupModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeCleanup() { var m = document.getElementById('cleanupModal'); m.classList.add('hidden'); m.classList.remove('flex'); }
function runCleanup() {
    var days = document.getElementById('cleanupDays').value;
    if (!confirm('Xóa các đơn cũ hơn ' + days + ' ngày?')) return;
    var fd = new FormData();
    fd.append('action', 'cleanup');
    fd.append('days', days);
    fetch(window.location.pathname + '?module=admin&action=manual-orders', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            showToast(d.msg, d.status === 'success' ? 'success' : 'error');
            closeCleanup();
            if (d.status === 'success') setTimeout(function() { location.reload(); }, 600);
        });
}
</script>

<?php require_once(__DIR__ . '/dcos-layout-close.php'); ?>
