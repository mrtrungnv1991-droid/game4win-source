<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$game_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($game_id <= 0) { redirect(base_url()); }

$game = $CMSNT->get_row_safe("SELECT * FROM `games` WHERE `id` = ? AND `status` = 1", [$game_id]);
if (!$game) { redirect(base_url()); }

$tiers = $CMSNT->get_list_safe("SELECT * FROM `topup_tiers` WHERE `game_id` = ? AND `status` = 1 ORDER BY FIELD(`type`,'gem','pack','allpack'), `sort_order` ASC", [$game_id]);

$grouped = ['gem' => [], 'pack' => [], 'allpack' => []];
foreach ($tiers as $t) { $grouped[$t['type']][] = $t; }

// Check login — standalone page
$is_logged_in = false;
$user_token = '';
if (isset($_COOKIE['user_login'])) {
    $token = check_string($_COOKIE['user_login']);
    $u = $CMSNT->get_row_safe("SELECT * FROM `users` WHERE `token` = ? AND `banned` = 0", [$token]);
    if ($u) {
        $is_logged_in = true;
        $getUser = $u;
        $user_token = $u['token'];
    }
}

$body = ['title' => "Nạp {$game['name']} — " . $CMSNT->site('title')];
?><!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $body['title'] ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--bg-deep:#06060c;--bg-surface:#0d0d18;--bg-card:#111122;--bg-card-hover:#161630;--border-subtle:rgba(255,255,255,0.06);--text-primary:#f0f0f5;--text-secondary:#9ca3af;--text-muted:#6b7280;--accent-cyan:#00d4ff;--accent-purple:#7c3aed;--accent-gold:#f59e0b;--accent-green:#10b981;--gradient-primary:linear-gradient(135deg,#00d4ff,#7c3aed);--radius-sm:10px;--radius-md:16px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Inter",system-ui,sans-serif;background:var(--bg-deep);color:var(--text-primary);line-height:1.6;min-height:100vh}
body::before{content:"";position:fixed;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(ellipse at 30% 20%,rgba(0,212,255,0.04) 0%,transparent 50%),radial-gradient(ellipse at 70% 60%,rgba(124,58,237,0.04) 0%,transparent 50%);pointer-events:none;z-index:0}

/* Nav */
.navbar{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(6,6,12,0.85);backdrop-filter:blur(20px);border-bottom:1px solid var(--border-subtle);padding:12px 0}
.navbar .container{max-width:800px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between}
.nav-logo{display:flex;align-items:center;gap:10px;font-size:1.2rem;font-weight:800;color:var(--text-primary);text-decoration:none}
.nav-logo .icon{width:34px;height:34px;border-radius:var(--radius-sm);background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:.95rem}
.nav-back{color:var(--text-secondary);text-decoration:none;font-size:.85rem}
.nav-back:hover{color:var(--text-primary)}

/* Game detail */
.game-detail{max-width:600px;margin:0 auto;padding:100px 20px 40px;position:relative;z-index:1}
.game-header{display:flex;align-items:center;gap:16px;margin-bottom:24px;background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:20px}
.game-icon{font-size:3rem}
.game-info h1{font-size:1.4rem;font-weight:700;margin-bottom:4px}
.game-meta{font-size:.82rem;color:var(--text-secondary)}

/* Tiers */
.tier-group{margin-bottom:20px}
.tier-group h3{font-size:.85rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px}
.tier-item{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:12px;border:1px solid var(--border-subtle);margin-bottom:6px;background:var(--bg-surface);cursor:pointer;transition:all .2s}
.tier-item:hover,.tier-item.selected{border-color:var(--accent-cyan);background:rgba(0,212,255,0.05)}
.tier-label{font-weight:500;font-size:.9rem}
.tier-price{font-weight:700;font-size:.95rem;color:var(--accent-gold)}

/* Form */
.tp-form{margin-top:20px}
.tp-form input{width:100%;padding:12px 16px;border-radius:12px;border:1px solid var(--border-subtle);background:var(--bg-card);color:var(--text-primary);font-size:.9rem;margin-bottom:10px;font-family:inherit}
.tp-form input:focus{border-color:var(--accent-cyan);outline:none}
.tp-btn{width:100%;padding:14px;border-radius:12px;background:var(--gradient-primary);color:#fff;border:none;font-size:1rem;font-weight:600;cursor:pointer;font-family:inherit}
.tp-btn:disabled{opacity:0.5;cursor:not-allowed}
.price-summary{text-align:center;padding:12px;margin:12px 0;background:rgba(245,158,11,0.08);border-radius:12px;font-weight:700;font-size:1.1rem;color:var(--accent-gold);display:none}

/* Toast */
.toast-container{position:fixed;bottom:30px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;align-items:center;gap:8px}
.toast{padding:12px 24px;border-radius:9999px;background:var(--accent-green);color:#000;font-weight:700;font-size:.85rem;animation:toastIn .35s ease,toastOut .3s 2.5s ease forwards}
@keyframes toastIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes toastOut{from{opacity:1;transform:translateY(0)}to{opacity:0;transform:translateY(-20px)}}
.login-hint{text-align:center;margin-top:16px;color:var(--text-secondary);font-size:.85rem}
.login-hint a{color:var(--accent-cyan)}
</style>
</head>
<body>

<nav class="navbar">
  <div class="container">
    <a href="<?= BASE_URL() ?>" class="nav-logo"><span class="icon">🎮</span>GameTopup</a>
    <a href="<?= BASE_URL() ?>" class="nav-back">← Quay lại</a>
  </div>
</nav>

<div class="game-detail">
  <div class="game-header">
    <div class="game-icon"><?= htmlspecialchars($game['icon'] ?: '🎮') ?></div>
    <div class="game-info">
      <h1><?= htmlspecialchars($game['name']) ?></h1>
      <div class="game-meta">
        <?= htmlspecialchars($game['category']) ?> · 
        <?= htmlspecialchars($game['currency_name']) ?> <?= htmlspecialchars($game['currency_unit']) ?>
      </div>
    </div>
  </div>

  <form id="topupForm" onsubmit="return submitTopup(event)">
    <input type="hidden" id="tier_id" value="">
    <input type="hidden" id="user_token" value="<?= htmlspecialchars($user_token) ?>">

    <?php 
    $titles = ['gem'=>'💎 In-game Currency', 'pack'=>'🎫 Battle Pass & Gói Tháng', 'allpack'=>'🎁 Combo Pack'];
    foreach(['gem','pack','allpack'] as $type): 
      if(empty($grouped[$type])) continue;
    ?>
    <div class="tier-group">
      <h3><?= $titles[$type] ?></h3>
      <?php foreach($grouped[$type] as $t): ?>
      <div class="tier-item" onclick="selectTier(<?= $t['id'] ?>, <?= $t['price'] ?>, '<?= htmlspecialchars(addslashes($t['label'])) ?>', this)">
        <span class="tier-label"><?= htmlspecialchars($t['label']) ?></span>
        <span class="tier-price"><?= number_format($t['price']) ?>đ</span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

    <div class="price-summary" id="price-display">
      💰 <span id="selected-price">0</span>đ — <span id="selected-label"></span>
    </div>

    <div class="tp-form">
      <input type="text" id="game_uid" placeholder="🎯 Nhập ID người chơi (Game UID)..." required minlength="3">
      <input type="text" id="coupon_code" placeholder="🏷️ Mã giảm giá (nếu có)">
      <button type="submit" class="tp-btn" id="btn-buy" disabled>Xác nhận nạp</button>
    </div>
  </form>

  <?php if(!$is_logged_in): ?>
  <p class="login-hint">Vui lòng <a href="<?= base_url('client/login') ?>">đăng nhập</a> để nạp game</p>
  <?php endif; ?>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
function showToast(msg, isError){
  var t=document.createElement("div");t.className="toast";t.textContent=msg;
  if(isError) t.style.background="#ef4444";
  document.getElementById("toastContainer").appendChild(t);
  setTimeout(function(){t.remove()},3000);
}

function selectTier(id, price, label, el){
  document.querySelectorAll('.tier-item').forEach(function(i){i.classList.remove('selected')});
  el.classList.add('selected');
  document.getElementById('tier_id').value=id;
  document.getElementById('selected-price').textContent=price.toLocaleString();
  document.getElementById('selected-label').textContent=label;
  document.getElementById('price-display').style.display='block';
  document.getElementById('btn-buy').disabled=false;
}

function submitTopup(e){
  e.preventDefault();
  var uid=document.getElementById('game_uid').value.trim();
  var tid=document.getElementById('tier_id').value;
  var token=document.getElementById('user_token').value;
  var coupon=document.getElementById('coupon_code').value.trim();

  if(!tid){showToast('Vui lòng chọn gói nạp!',true);return false}
  if(uid.length<3){showToast('ID người chơi phải ít nhất 3 ký tự!',true);return false}
  if(!token){showToast('Vui lòng đăng nhập!',true);return false}

  var btn=document.getElementById('btn-buy');
  btn.disabled=true;btn.textContent='⏳ Đang xử lý...';

  var fd=new FormData();
  fd.append('action','buyTopup');
  fd.append('token',token);
  fd.append('game_id',<?= $game_id ?>);
  fd.append('tier_id',tid);
  fd.append('game_uid',uid);
  if(coupon) fd.append('coupon',coupon);

  fetch('<?= BASE_URL('ajaxs/client/product.php') ?>',{method:'POST',body:fd})
    .then(function(r){return r.json()})
    .then(function(data){
      if(data.status==='success'){
        showToast('✅ Nạp thành công! Mã ĐH: '+data.trans_id);
        setTimeout(function(){window.location.href='<?= BASE_URL() ?>'},1500);
      }else{
        showToast(data.msg||'Lỗi!',true);
        btn.disabled=false;btn.textContent='Xác nhận nạp';
      }
    })
    .catch(function(err){
      showToast('Lỗi kết nối!',true);
      btn.disabled=false;btn.textContent='Xác nhận nạp';
    });

  return false;
}
</script>

</body>
</html>
