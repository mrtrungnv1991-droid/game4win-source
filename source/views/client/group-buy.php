<?php if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../libs/group_buy.php');

$gb = new GroupBuy($CMSNT);
$category = $_GET['category'] ?? '';
$deals = $gb->getActiveDeals($category);

$body = [
    'title' => '👥 Group Buy — Save Together! | ' . $CMSNT->site('title'),
    'desc' => 'Join group buys to unlock massive discounts on game keys, gift cards & software',
];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/nav.php');
?>

<section class="py-4">
<div class="container">

<div class="text-center mb-4">
    <h2>👥 Group Buy — Save Together!</h2>
    <p class="text-muted">Join forces with other buyers to unlock wholesale prices. The more people join, the lower the price!</p>
</div>

<!-- Category Filter -->
<div class="mb-3 text-center">
    <a href="?module=client&action=group-buy" class="btn btn-sm <?= $category==''?'btn-primary':'btn-outline-primary' ?> m-1">All</a>
    <a href="?module=client&action=group-buy&category=game_key" class="btn btn-sm <?= $category=='game_key'?'btn-primary':'btn-outline-primary' ?> m-1">🎮 Game Keys</a>
    <a href="?module=client&action=group-buy&category=gift_card" class="btn btn-sm <?= $category=='gift_card'?'btn-primary':'btn-outline-primary' ?> m-1">💳 Gift Cards</a>
    <a href="?module=client&action=group-buy&category=software" class="btn btn-sm <?= $category=='software'?'btn-primary':'btn-outline-primary' ?> m-1">💻 Software</a>
</div>

<!-- Deals Grid -->
<div class="row g-3">
<?php if (empty($deals)): ?>
    <div class="col-12 text-center py-5">
        <h4 class="text-muted">🚀 No active deals right now — check back soon!</h4>
    </div>
<?php else: foreach ($deals as $d): 
    $pct = $d['min_participants'] > 0 ? min(100, round(($d['current_participants']/$d['min_participants'])*100)) : 0;
    $deal = $gb->getDeal($d['id']);
?>
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 border-<?= $d['status']=='filled'?'success':'primary' ?> shadow-sm">
            <?php if ($d['image_url']): ?>
            <img src="<?= $d['image_url'] ?>" class="card-img-top" style="height:180px;object-fit:cover">
            <?php endif; ?>
            <div class="card-body">
                <span class="badge bg-danger float-end">-<?= $d['discount_percent'] ?>%</span>
                <h5 class="card-title"><?= htmlspecialchars($d['title']) ?></h5>
                <p class="text-muted small"><?= htmlspecialchars(substr($d['product_description'] ?? '', 0, 100)) ?>...</p>
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-decoration-line-through text-muted">$<?= number_format($d['original_price'],2) ?></span>
                    <span class="fs-4 fw-bold text-success">$<?= number_format($d['group_price'],2) ?></span>
                </div>

                <!-- Progress -->
                <div class="mb-2">
                    <small class="text-muted"><?= $d['current_participants'] ?>/<?= $d['min_participants'] ?> joined</small>
                    <div class="progress" style="height:12px">
                        <div class="progress-bar bg-<?= $pct>=100?'success':'warning' ?> progress-bar-striped <?= $d['status']!='completed'?'progress-bar-animated':'' ?>"
                             style="width:<?= $pct ?>%"></div>
                    </div>
                </div>

                <?php if ($d['status'] === 'active'): ?>
                <button class="btn btn-primary w-100 join-deal-btn" data-deal-id="<?= $d['id'] ?>" data-price="<?= $d['group_price'] ?>">
                    🎯 Join Now — $<?= number_format($d['group_price'],2) ?>
                </button>
                <?php elseif ($d['status'] === 'filled'): ?>
                <button class="btn btn-warning w-100" disabled>⏳ Deal Filled — Processing</button>
                <?php elseif ($d['status'] === 'completed'): ?>
                <button class="btn btn-success w-100" disabled>✅ Completed</button>
                <?php endif; ?>

                <?php if ($deal && !empty($deal['participants'])): ?>
                <div class="mt-2">
                    <small class="text-muted">Recent joiners:</small>
                    <?php foreach (array_slice($deal['participants'], 0, 3) as $pp): ?>
                    <span class="badge bg-light text-dark"><?= htmlspecialchars($pp['username']) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; endif; ?>
</div>

</div>
</section>

<script>
// AJAX join group buy
document.querySelectorAll('.join-deal-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const dealId = this.dataset.dealId;
        const price = this.dataset.price;
        if (!confirm(`Join this group buy for $${price}? Amount will be deducted from your wallet.`)) return;
        
        this.disabled = true;
        this.innerHTML = '⏳ Processing...';
        
        try {
            const formData = new FormData();
            formData.append('action', 'join');
            formData.append('deal_id', dealId);
            
            const resp = await fetch('<?= BASE_URL() ?>ajaxs/client/group-buy.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });
            const json = await resp.json();
            
            if (json.status === 'success') {
                if (json.filled) {
                    alert('🎉 Deal is now FULL! Keys will be delivered shortly.');
                } else {
                    alert('✅ ' + json.msg);
                }
                location.reload();
            } else {
                alert('❌ ' + json.msg);
                this.disabled = false;
                this.innerHTML = `🎯 Join Now — $${price}`;
            }
        } catch(e) {
            alert('Network error. Please try again.');
            this.disabled = false;
            this.innerHTML = `🎯 Join Now — $${price}`;
        }
    });
});
</script>

<?php require_once(__DIR__ . '/footer.php'); ?>
