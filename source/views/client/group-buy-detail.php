<?php
/**
 * Group Buy Detail — Trang chi tiết deal mua chung
 * Route: /?module=client&action=group-buy-detail&id=X
 */
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/../../libs/group_buy.php');

$gb = new GroupBuy($CMSNT);
$deal_id = intval($_GET['id'] ?? 0);
$deal = $deal_id > 0 ? $gb->getDeal($deal_id) : null;

if (!$deal) {
    require_once(__DIR__ . '/../common/404.php');
    exit();
}

$progress = min(100, $deal['progress_percent'] ?? 0);
$spots_left = max(0, $deal['min_participants'] - $deal['current_participants']);
$end_ts = strtotime($deal['end_date']);
$seconds_left = max(0, $end_ts - time());

$body = [
    'title' => $deal['title'] . ' | Group Buy | ' . $CMSNT->site('title'),
    'desc'  => 'Tham gia mua chung ' . $deal['product_name'] . ' — tiết kiệm ' . $deal['discount_percent'] . '%',
];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/nav.php');
?>

<section class="py-4">
<div class="container" style="max-width: 900px;">

<a href="?module=client&action=group-buy" class="btn btn-sm btn-outline-primary mb-3">← Tất cả Group Buy</a>

<div class="card shadow-sm">
  <div class="card-body p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h2 class="mb-1"><?= htmlspecialchars($deal['title']) ?></h2>
        <p class="text-muted mb-2"><?= htmlspecialchars($deal['product_name']) ?></p>
      </div>
      <span class="badge bg-danger fs-6">-<?= $deal['discount_percent'] ?>%</span>
    </div>

    <?php if (!empty($deal['product_description'])): ?>
    <p class="text-muted"><?= nl2br(htmlspecialchars($deal['product_description'])) ?></p>
    <?php endif; ?>

    <!-- Price -->
    <div class="d-flex align-items-center gap-3 my-3">
      <span class="fs-3 fw-bold text-primary"><?= number_format($deal['group_price']) ?>đ</span>
      <span class="text-muted text-decoration-line-through"><?= number_format($deal['original_price']) ?>đ</span>
      <span class="badge bg-success">Tiết kiệm <?= number_format($deal['original_price'] - $deal['group_price']) ?>đ</span>
    </div>

    <!-- Progress -->
    <div class="mb-2 d-flex justify-content-between">
      <small class="fw-bold">👥 <?= $deal['current_participants'] ?>/<?= $deal['min_participants'] ?> người tham gia</small>
      <small class="text-muted"><?= $progress ?>%</small>
    </div>
    <div class="progress mb-3" style="height: 12px;">
      <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: <?= $progress ?>%"></div>
    </div>

    <!-- Countdown -->
    <div class="alert alert-warning py-2 d-flex justify-content-between align-items-center">
      <span>⏰ Kết thúc sau: <b id="gbCountdown">--</b></span>
      <small><?= date('d/m/Y H:i', $end_ts) ?></small>
    </div>

    <?php if ($deal['status'] === 'completed'): ?>
      <div class="alert alert-success">✅ Deal đã hoàn thành — key đã được giao cho tất cả người tham gia!</div>
    <?php elseif ($deal['status'] === 'filled'): ?>
      <div class="alert alert-info">🎉 Deal đã đủ người! Đang chờ hệ thống giao key...</div>
    <?php elseif ($seconds_left <= 0): ?>
      <div class="alert alert-secondary">Deal đã hết hạn.</div>
    <?php else: ?>
      <button class="btn btn-primary btn-lg w-100" id="gbDetailJoinBtn" data-deal="<?= $deal['id'] ?>" data-price="<?= number_format($deal['group_price']) ?>">
        🎯 Tham gia ngay — <?= number_format($deal['group_price']) ?>đ
      </button>
      <small class="text-muted d-block text-center mt-2">Cần thêm <?= $spots_left ?> người để deal kích hoạt. Tiền được giữ trong ví, hoàn lại nếu deal hủy.</small>
    <?php endif; ?>
  </div>
</div>

<!-- Participants -->
<div class="card shadow-sm mt-3">
  <div class="card-header bg-white"><b>👥 Người tham gia (<?= count($deal['participants']) ?>)</b></div>
  <div class="card-body p-0">
    <?php if (empty($deal['participants'])): ?>
      <p class="text-muted text-center py-4 mb-0">Chưa có ai tham gia — hãy là người đầu tiên!</p>
    <?php else: ?>
    <table class="table table-hover mb-0">
      <thead><tr><th>#</th><th>Người dùng</th><th>Số lượng</th><th>Đã trả</th><th>Thời gian</th><th>Key</th></tr></thead>
      <tbody>
        <?php foreach ($deal['participants'] as $i => $p): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><b><?= htmlspecialchars($p['username'] ?? 'User #' . $p['user_id']) ?></b></td>
          <td><?= $p['quantity'] ?></td>
          <td><?= number_format($p['total_price']) ?>đ</td>
          <td><small><?= date('d/m/Y H:i', strtotime($p['joined_at'])) ?></small></td>
          <td>
            <?php if ($p['key_delivered']): ?>
              <span class="badge bg-success">✅ Đã giao</span>
            <?php elseif ($p['payment_status'] === 'refunded'): ?>
              <span class="badge bg-secondary">↩ Đã hoàn</span>
            <?php else: ?>
              <span class="badge bg-warning text-dark">⏳ Chờ</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

</div>
</section>

<script>
// Countdown
var gbEndTs = <?= $end_ts ?>;
function gbTick(){
  var el = document.getElementById('gbCountdown');
  if(!el) return;
  var s = gbEndTs - Math.floor(Date.now()/1000);
  if(s <= 0){ el.textContent = 'Đã kết thúc'; return; }
  var d = Math.floor(s/86400); s -= d*86400;
  var h = Math.floor(s/3600); s -= h*3600;
  var m = Math.floor(s/60); s -= m*60;
  el.textContent = (d>0 ? d+' ngày ' : '') + h+'h '+m+'m '+s+'s';
}
gbTick();
setInterval(gbTick, 1000);

// Join button
var gbBtn = document.getElementById('gbDetailJoinBtn');
if(gbBtn){
  gbBtn.addEventListener('click', async function(){
    var dealId = this.dataset.deal;
    var price = this.dataset.price;
    if(!confirm('Tham gia Group Buy này với giá ' + price + 'đ? Tiền sẽ được trừ từ ví của bạn.')) return;
    this.disabled = true;
    this.innerHTML = '⏳ Đang xử lý...';
    try {
      var fd = new FormData();
      fd.append('action', 'join');
      fd.append('deal_id', dealId);
      var resp = await fetch('<?= BASE_URL() ?>ajaxs/client/group-buy.php', { method: 'POST', body: fd, credentials: 'same-origin' });
      var json = await resp.json();
      if(json.status === 'success'){
        alert(json.filled ? '🎉 Deal đã ĐỦ NGƯỜI! Key sẽ được giao sớm.' : '✅ ' + json.msg);
        location.reload();
      } else {
        alert('❌ ' + json.msg);
        this.disabled = false;
        this.innerHTML = '🎯 Tham gia ngay — ' + price + 'đ';
      }
    } catch(e){
      alert('Lỗi kết nối. Vui lòng thử lại.');
      this.disabled = false;
      this.innerHTML = '🎯 Tham gia ngay — ' + price + 'đ';
    }
  });
}
</script>

<?php require_once(__DIR__ . '/footer.php'); ?>
