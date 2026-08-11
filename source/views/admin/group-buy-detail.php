<?php if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/../../libs/group_buy.php');

$gb = new GroupBuy($CMSNT);
$deal_id = (int)($_GET['id'] ?? 0);
$deal = $gb->getDeal($deal_id);

if (!$deal) {
    echo '<div class="main-content app-content"><div class="container-fluid"><div class="alert alert-danger">Deal not found</div></div></div>';
    require_once(__DIR__ . '/footer.php');
    exit();
}

$body = ['title' => 'Deal #' . $deal_id . ' | Group Buy'];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
?>

<div class="main-content app-content">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>👥 Deal #<?= $deal_id ?> — <?= htmlspecialchars($deal['title']) ?></h3>
    <a href="<?= base_url_admin('group-buy-admin') ?>" class="btn btn-sm btn-default">← Back</a>
</div>

<div class="row g-3">
    <!-- Deal Info -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>📋 Deal Info</strong></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td>Status</td><td><span class="badge bg-<?= match($deal['status']){'active'=>'primary','filled'=>'warning','completed'=>'success','cancelled'=>'secondary',default:'dark'} ?>"><?= $deal['status'] ?></span></td></tr>
                    <tr><td>Type</td><td><?= $deal['product_type'] ?></td></tr>
                    <tr><td>Original</td><td>$<?= number_format($deal['original_price'],2) ?></td></tr>
                    <tr><td>Group Price</td><td class="text-success fw-bold">$<?= number_format($deal['group_price'],2) ?></td></tr>
                    <tr><td>Discount</td><td><span class="badge bg-danger">-<?= $deal['discount_percent'] ?>%</span></td></tr>
                    <tr><td>Progress</td><td><?= $deal['current_participants'] ?>/<?= $deal['min_participants'] ?> (<?= $deal['progress_percent'] ?>%)</td></tr>
                    <tr><td>Spots Left</td><td><?= $deal['spots_left'] ?></td></tr>
                </table>
                
                <?php if ($deal['status'] === 'filled'): ?>
                <form method="POST" action="<?= base_url_admin('group-buy-admin') ?>">
                    <input type="hidden" name="action" value="fulfill">
                    <input type="hidden" name="deal_id" value="<?= $deal_id ?>">
                    <button class="btn btn-success w-100">🚀 Fulfill Now</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Participants -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><strong>👤 Participants (<?= count($deal['participants'] ?? []) ?>)</strong></div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark"><tr><th>#</th><th>User</th><th>Qty</th><th>Price</th><th>Total</th><th>Status</th><th>Key Delivered</th><th>Joined</th></tr></thead>
                    <tbody>
                    <?php if (empty($deal['participants'])): ?>
                        <tr><td colspan="8" class="text-center text-muted">No participants yet</td></tr>
                    <?php else: foreach ($deal['participants'] as $i => $p): ?>
                    <tr>
                        <td><?= $i+1 ?></td>
                        <td><strong><?= htmlspecialchars($p['username']) ?></strong></td>
                        <td><?= $p['quantity'] ?></td>
                        <td>$<?= number_format($p['unit_price'],2) ?></td>
                        <td class="fw-bold">$<?= number_format($p['total_price'],2) ?></td>
                        <td><span class="badge bg-<?= $p['payment_status']=='paid'?'success':($p['payment_status']=='refunded'?'warning':'secondary') ?>"><?= $p['payment_status'] ?></span></td>
                        <td><?= $p['key_delivered'] ? '✅ '.$p['delivered_at'] : '❌' ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($p['joined_at'])) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

</div></div>
<?php require_once(__DIR__ . '/footer.php'); ?>
