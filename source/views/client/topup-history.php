<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

// Check login
$is_logged_in = false;
$user_orders = [];
if (isset($_COOKIE['user_login'])) {
    $token = check_string($_COOKIE['user_login']);
    $u = $CMSNT->get_row_safe("SELECT * FROM `users` WHERE `token` = ? AND `banned` = 0", [$token]);
    if ($u) {
        $is_logged_in = true;
        $user_orders = $CMSNT->get_list_safe(
            "SELECT * FROM `product_order` WHERE `buyer` = ? AND `topup_tier_id` IS NOT NULL ORDER BY `id` DESC LIMIT 50",
            [$u['id']]
        );
    }
}

if (!$is_logged_in) { redirect(base_url('client/login')); }

$body = ['title' => 'Lịch sử nạp game — ' . $CMSNT->site('title')];
?><!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $body['title'] ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--bg-deep:#06060c;--bg-surface:#0d0d18;--bg-card:#111122;--bg-card-hover:#161630;--border-subtle:rgba(255,255,255,0.06);--text-primary:#f0f0f5;--text-secondary:#9ca3af;--text-muted:#6b7280;--accent-cyan:#00d4ff;--accent-purple:#7c3aed;--accent-gold:#f59e0b;--accent-green:#10b981;--accent-red:#ef4444;--gradient-primary:linear-gradient(135deg,#00d4ff,#7c3aed);--radius-sm:10px;--radius-md:16px;--radius-pill:9999px;--transition:0.25s cubic-bezier(0.4,0,0.2,1)}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Inter",system-ui,sans-serif;background:var(--bg-deep);color:var(--text-primary);line-height:1.6;min-height:100vh}
body::before{content:"";position:fixed;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(ellipse at 30% 20%,rgba(0,212,255,0.04) 0%,transparent 50%),radial-gradient(ellipse at 70% 60%,rgba(124,58,237,0.04) 0%,transparent 50%);pointer-events:none;z-index:0}
.navbar{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(6,6,12,0.85);backdrop-filter:blur(20px);border-bottom:1px solid var(--border-subtle);padding:12px 0}
.navbar .container{max-width:1000px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between}
.nav-logo{display:flex;align-items:center;gap:10px;font-size:1.2rem;font-weight:800;color:var(--text-primary);text-decoration:none}
.nav-logo .icon{width:34px;height:34px;border-radius:var(--radius-sm);background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:.95rem}
.nav-back{color:var(--text-secondary);text-decoration:none;font-size:.85rem}
.container{max-width:900px;margin:0 auto;padding:100px 20px 40px;position:relative;z-index:1}
h1{font-size:1.5rem;margin-bottom:24px;font-weight:800}
.order-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:16px 20px;margin-bottom:10px;transition:var(--transition)}
.order-card:hover{background:var(--bg-card-hover)}
.order-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;flex-wrap:wrap;gap:8px}
.order-id{font-size:.78rem;color:var(--text-muted);font-family:monospace}
.order-game{font-weight:600;font-size:.95rem}
.order-detail{font-size:.82rem;color:var(--text-secondary)}
.order-footer{display:flex;justify-content:space-between;align-items:center;margin-top:8px;flex-wrap:wrap;gap:8px}
.order-price{font-weight:700;color:var(--accent-gold)}
.order-date{font-size:.72rem;color:var(--text-muted)}
.badge{padding:3px 10px;border-radius:var(--radius-pill);font-size:.7rem;font-weight:700;text-transform:uppercase}
.badge-success{background:rgba(16,185,129,0.15);color:var(--accent-green)}
.badge-pending{background:rgba(245,158,11,0.15);color:var(--accent-gold)}
.badge-failed{background:rgba(239,68,68,0.15);color:var(--accent-red)}
.badge-processing{background:rgba(0,212,255,0.15);color:var(--accent-cyan)}
.empty{text-align:center;padding:60px 20px;color:var(--text-muted)}
.empty .icon{font-size:3rem;margin-bottom:16px}
@media(max-width:640px){.container{padding:90px 14px 30px}}
</style>
</head>
<body>

<nav class="navbar">
  <div class="container">
    <a href="<?= BASE_URL() ?>" class="nav-logo"><span class="icon">🎮</span>GameTopup</a>
    <a href="<?= BASE_URL() ?>" class="nav-back">← Quay lại shop</a>
  </div>
</nav>

<div class="container">
  <h1>📋 Lịch sử nạp game</h1>

  <?php if(empty($user_orders)): ?>
  <div class="empty">
    <div class="icon">📭</div>
    <p>Chưa có đơn nạp nào</p>
    <a href="<?= BASE_URL() ?>" style="color:var(--accent-cyan);margin-top:12px;display:inline-block">Nạp game ngay →</a>
  </div>
  <?php else: ?>
  <?php foreach($user_orders as $o): 
    $note = json_decode($o['note'] ?? '{}', true);
    $statusLabels = ['pending'=>'⏳ Chờ XL','processing'=>'🔄 Đang XL','success'=>'✅ OK','failed'=>'❌ Lỗi','refunded'=>'↩ Hoàn'];
    $statusClass = ['pending'=>'badge-pending','processing'=>'badge-processing','success'=>'badge-success','failed'=>'badge-failed','refunded'=>'badge-failed'];
  ?>
  <div class="order-card">
    <div class="order-header">
      <span class="order-id">#<?= $o['trans_id'] ?></span>
      <span class="badge <?= $statusClass[$o['topup_status']] ?? '' ?>"><?= $statusLabels[$o['topup_status']] ?? $o['topup_status'] ?></span>
    </div>
    <div class="order-game"><?= htmlspecialchars($note['game_name'] ?? $o['product_name']) ?></div>
    <div class="order-detail">
      Gói: <?= htmlspecialchars($note['tier_label'] ?? '') ?> · 
      UID: <b><?= htmlspecialchars($o['game_uid'] ?? '') ?></b>
      <?php if(!empty($note['coupon_code'])): ?>· Coupon: <?= $note['coupon_code'] ?><?php endif; ?>
    </div>
    <div class="order-footer">
      <span class="order-price">💰 <?= number_format($o['pay']) ?>đ</span>
      <span class="order-date"><?= date('d/m/Y H:i', strtotime($o['create_gettime'])) ?></span>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

</body>
</html>
