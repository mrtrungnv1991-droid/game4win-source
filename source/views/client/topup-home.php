<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$body = ['title' => 'Nạp Game Mobile — ' . $CMSNT->site('title'), 'desc' => 'Nạp game iOS & Android nhanh chóng, an toàn, giá rẻ. Hỗ trợ 121+ game.'];

// CSRF token cho login modal
$csrf_token = generate_csrf_token();

// Check login
$is_logged_in = false; $is_admin = false; $user_balance = 0; $user_name = ''; $user_token = '';
if (isset($_COOKIE['user_login'])) {
    $token = check_string($_COOKIE['user_login']);
    $u = $CMSNT->get_row_safe("SELECT * FROM `users` WHERE `token` = ? AND `banned` = 0", [$token]);
    if ($u) {
        $is_logged_in = true; $getUser = $u; $user_balance = $u['money'];
        $user_name = $u['username']; $user_token = $u['token']; $is_admin = ($u['admin'] == 1);
    }
}
?><!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $body['title'] ?></title>
<meta name="description" content="<?= $body['desc'] ?>">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--bg-deep:#06060c;--bg-surface:#0d0d18;--bg-card:#111122;--bg-card-hover:#161630;--border-subtle:rgba(255,255,255,0.06);--border-glow:rgba(0,212,255,0.15);--text-primary:#f0f0f5;--text-secondary:#9ca3af;--text-muted:#6b7280;--accent-cyan:#00d4ff;--accent-purple:#7c3aed;--accent-gold:#f59e0b;--accent-green:#10b981;--accent-red:#ef4444;--gradient-primary:linear-gradient(135deg,#00d4ff,#7c3aed);--radius-sm:10px;--radius-md:16px;--radius-lg:24px;--radius-pill:9999px;--shadow-glow:0 0 30px rgba(0,212,255,0.08);--shadow-modal:0 20px 60px rgba(0,0,0,0.5);--transition:0.25s cubic-bezier(0.4,0,0.2,1)}
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Inter",system-ui,sans-serif;background:var(--bg-deep);color:var(--text-primary);line-height:1.6;min-height:100vh;overflow-x:hidden}
body::before{content:"";position:fixed;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(ellipse at 30% 20%,rgba(0,212,255,0.04) 0%,transparent 50%),radial-gradient(ellipse at 70% 60%,rgba(124,58,237,0.04) 0%,transparent 50%);pointer-events:none;z-index:0}
.container{max-width:1300px;margin:0 auto;padding:0 24px;position:relative;z-index:1}

/* Navbar */
.navbar{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(6,6,12,0.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid var(--border-subtle);padding:10px 0}
.navbar .container{display:flex;align-items:center;justify-content:space-between;gap:12px}
.nav-logo{display:flex;align-items:center;gap:10px;font-size:1.2rem;font-weight:800;color:var(--text-primary);text-decoration:none;white-space:nowrap}
.nav-logo .icon{width:34px;height:34px;border-radius:var(--radius-sm);background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:.95rem}
.nav-right{display:flex;align-items:center;gap:14px}
.nav-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:var(--radius-pill);font-size:.82rem;font-weight:600;cursor:pointer;transition:var(--transition);border:none;font-family:inherit;text-decoration:none;white-space:nowrap}
.nav-btn-login{background:var(--gradient-primary);color:#fff}
.nav-btn-login:hover{transform:translateY(-2px);box-shadow:var(--shadow-glow)}
.nav-btn-outline{background:transparent;border:1.5px solid rgba(255,255,255,0.15);color:var(--text-primary)}
.nav-btn-icon{background:transparent;border:none;color:var(--text-secondary);font-size:1.2rem;cursor:pointer;position:relative;padding:6px}
.nav-btn-icon:hover{color:var(--text-primary)}
.cart-badge{position:absolute;top:-2px;right:-6px;background:var(--accent-red);color:#fff;font-size:.6rem;padding:1px 5px;border-radius:10px;font-weight:700;min-width:16px;text-align:center}
.balance-badge{display:inline-flex;align-items:center;gap:4px;padding:6px 14px;border-radius:var(--radius-pill);background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:var(--accent-green);font-size:.8rem;font-weight:600;cursor:pointer}
.user-badge{display:flex;align-items:center;gap:6px;cursor:pointer;position:relative;font-size:.85rem;font-weight:500}
.user-dropdown{display:none;position:absolute;top:100%;right:0;margin-top:8px;width:260px;background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:16px;z-index:150;box-shadow:0 8px 32px rgba(0,0,0,0.5)}
.user-dropdown.show{display:block}
.user-dd-header{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.user-dd-avatar{width:40px;height:40px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;font-size:1.2rem}
.user-dd-name{font-weight:600;font-size:.9rem}
.user-dd-email{font-size:.72rem;color:var(--text-muted)}
.user-dd-status{background:var(--bg-card);border-radius:var(--radius-sm);padding:12px;margin-bottom:12px}
.user-dd-status .status-row{display:flex;justify-content:space-between;align-items:center;font-size:.78rem;padding:3px 0;color:var(--text-secondary)}
.user-dd-status .count{font-weight:700;color:var(--text-primary)}
.user-dd-btn{display:block;width:100%;padding:8px;margin-top:4px;border-radius:var(--radius-sm);background:var(--bg-card);border:1px solid var(--border-subtle);color:var(--text-secondary);font-size:.8rem;cursor:pointer;text-align:left;font-family:inherit;text-decoration:none}
.user-dd-btn:hover{background:var(--bg-card-hover);color:var(--text-primary)}
.membership-badge{display:inline-block;padding:2px 8px;border-radius:var(--radius-pill);font-size:.65rem;font-weight:700;margin-top:4px}

/* Section title */
.section-title{font-size:1.5rem;font-weight:800;margin:40px 0 20px;text-align:center}
.section-sub{color:var(--text-muted);text-align:center;font-size:.9rem;margin-top:-16px;margin-bottom:30px}

/* Hero */
.hero{padding:120px 0 40px;text-align:center}
.hero h1{font-size:clamp(1.8rem,4vw,2.8rem);font-weight:900;line-height:1.2;margin-bottom:12px}
.hero h1 span{background:var(--gradient-primary);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.hero p{color:var(--text-secondary);font-size:1.05rem;max-width:600px;margin:0 auto 24px}
.hero-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap}
.btn{padding:12px 28px;border-radius:var(--radius-pill);font-weight:600;font-size:.9rem;cursor:pointer;border:none;font-family:inherit;transition:var(--transition);display:inline-flex;align-items:center;gap:8px;text-decoration:none}
.btn-primary{background:var(--gradient-primary);color:#fff}
.btn-primary:hover{transform:translateY(-2px);box-shadow:var(--shadow-glow)}
.btn-outline{background:transparent;border:1.5px solid rgba(255,255,255,0.15);color:var(--text-primary)}
.btn-outline:hover{border-color:var(--accent-cyan)}

/* Stats bar */
.stats-bar{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:20px}
.stat-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:14px 18px;flex:1;min-width:140px;text-align:center}
.stat-card .sv{font-size:1.4rem;font-weight:800}
.stat-card .sl{font-size:.72rem;color:var(--text-muted);margin-top:2px}

/* Search + filter */
.search-row{display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;align-items:center}
.search-box{position:relative;flex:1;min-width:200px}
.search-box input{width:100%;padding:10px 14px 10px 38px;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);background:var(--bg-surface);color:var(--text-primary);font-size:.85rem;outline:none;font-family:inherit}
.search-box input:focus{border-color:var(--accent-cyan)}
.search-box .si{position:absolute;left:13px;top:50%;transform:translateY(-50%)}
.sort-select{padding:8px 14px;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);background:var(--bg-surface);color:var(--text-primary);font-size:.8rem;font-family:inherit;cursor:pointer}
.currency-toggle{display:flex;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);overflow:hidden}
.currency-toggle button{padding:6px 14px;border:none;background:transparent;color:var(--text-secondary);font-size:.78rem;cursor:pointer;font-family:inherit;font-weight:500}
.currency-toggle button.active{background:var(--accent-gold);color:#000;font-weight:700}

/* Categories */
.cat-tabs{display:flex;gap:6px;flex-wrap:wrap;margin-bottom:16px}
.cat-tab{padding:5px 13px;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);background:transparent;color:var(--text-secondary);font-size:.76rem;cursor:pointer;font-weight:500;font-family:inherit;transition:var(--transition)}
.cat-tab:hover,.cat-tab.active{background:var(--gradient-primary);color:#fff;border-color:transparent}

/* Games grid */
.games-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(155px,1fr));gap:12px;padding-bottom:40px}
.game-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:16px 12px;cursor:pointer;transition:var(--transition);text-align:center;position:relative;overflow:hidden}
.game-card:hover{background:var(--bg-card-hover);border-color:var(--border-glow);transform:translateY(-2px);box-shadow:var(--shadow-glow)}
.game-card .gi{font-size:2rem;margin-bottom:8px}
.game-card .gn{font-size:.84rem;font-weight:600;color:var(--text-primary);margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.game-card .gc{font-size:.68rem;color:var(--text-muted)}
.game-card .gcr{font-size:.65rem;color:var(--accent-gold);margin-top:4px}
.deal-badge{position:absolute;top:8px;left:8px;padding:2px 8px;border-radius:var(--radius-pill);font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px}
.deal-badge.hot{background:var(--accent-red);color:#fff;animation:pulse 2s infinite}
.deal-badge.new{background:var(--accent-cyan);color:#000}
.deal-badge.sale{background:var(--accent-gold);color:#000}
.deal-badge.best{background:var(--gradient-primary);color:#fff}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.7}}

/* How it works */
.steps{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:40px}
.step-card{background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-md);padding:24px 20px;text-align:center}
.step-number{width:40px;height:40px;border-radius:50%;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-weight:800;font-size:1.1rem;color:#fff}
.step-card h4{font-size:.95rem;margin-bottom:6px}
.step-card p{font-size:.8rem;color:var(--text-secondary)}

/* FAQ */
.faq-section{max-width:800px;margin:0 auto 40px}
.faq-item{border:1px solid var(--border-subtle);border-radius:var(--radius-sm);margin-bottom:8px;overflow:hidden}
.faq-q{padding:14px 16px;background:var(--bg-card);cursor:pointer;display:flex;justify-content:space-between;align-items:center;font-weight:600;font-size:.9rem;transition:var(--transition)}
.faq-q:hover{background:var(--bg-card-hover)}
.faq-arrow{transition:transform .2s;font-size:.7rem}
.faq-q.open .faq-arrow{transform:rotate(180deg)}
.faq-a{display:none;padding:0 16px 14px;font-size:.85rem;color:var(--text-secondary);line-height:1.7}
.faq-q.open+.faq-a{display:block}

/* Footer */
.footer{background:var(--bg-surface);border-top:1px solid var(--border-subtle);padding:40px 0 20px;margin-top:60px}
.footer-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:30px}
.footer-col h4{font-size:.9rem;font-weight:700;margin-bottom:12px}
.footer-col a{display:block;color:var(--text-secondary);text-decoration:none;font-size:.82rem;margin-bottom:6px;transition:var(--transition)}
.footer-col a:hover{color:var(--text-primary)}
.footer-bottom{text-align:center;padding-top:20px;margin-top:30px;border-top:1px solid var(--border-subtle);color:var(--text-muted);font-size:.75rem}

/* Cart Drawer */
.cart-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:300}
.cart-overlay.show{display:block}
.cart-drawer{position:fixed;top:0;right:0;width:380px;max-width:100%;height:100vh;background:var(--bg-surface);z-index:301;transform:translateX(100%);transition:transform .3s ease;display:flex;flex-direction:column;box-shadow:var(--shadow-modal)}
.cart-drawer.show{transform:translateX(0)}
.cart-header{padding:16px 20px;border-bottom:1px solid var(--border-subtle);display:flex;justify-content:space-between;align-items:center}
.cart-close{background:none;border:none;color:var(--text-secondary);font-size:1.5rem;cursor:pointer}
.cart-body{flex:1;overflow-y:auto;padding:12px 20px}
.cart-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-subtle)}
.cart-item-icon{font-size:1.8rem}
.cart-item-info{flex:1}
.cart-item-name{font-size:.82rem;font-weight:600}
.cart-item-branch{font-size:.7rem;color:var(--text-muted)}
.cart-item-price{font-size:.82rem;font-weight:700;color:var(--accent-gold)}
.cart-qty{display:flex;align-items:center;gap:6px}
.cart-qty button{width:24px;height:24px;border-radius:50%;border:1px solid var(--border-subtle);background:transparent;color:var(--text-primary);cursor:pointer;font-size:.8rem}
.cart-remove{background:none;border:none;color:var(--accent-red);cursor:pointer;font-size:.9rem}
.cart-footer{padding:16px 20px;border-top:1px solid var(--border-subtle)}
.cart-total{display:flex;justify-content:space-between;font-weight:700;font-size:1rem;margin-bottom:12px}
.cart-empty{text-align:center;padding:60px 0;color:var(--text-muted)}

/* Modals */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.75);z-index:200;align-items:center;justify-content:center;padding:20px}
.modal-overlay.show{display:flex}
.modal-box{background:var(--bg-surface);border:1px solid var(--border-subtle);border-radius:var(--radius-lg);padding:24px;max-width:500px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:var(--shadow-modal);animation:modalIn .2s ease}
@keyframes modalIn{from{opacity:0;transform:scale(.95) translateY(10px)}to{opacity:1;transform:scale(1) translateY(0)}}
.modal-close{position:absolute;top:12px;right:16px;background:none;border:none;color:var(--text-muted);font-size:1.3rem;cursor:pointer}
.modal-box h2{font-size:1.2rem;margin-bottom:4px}
.modal-sub{font-size:.8rem;color:var(--text-muted);margin-bottom:16px}

/* Payment methods */
.payment-methods{display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap}
.pay-method{padding:8px 16px;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);background:transparent;color:var(--text-secondary);font-size:.8rem;cursor:pointer;font-family:inherit;transition:var(--transition)}
.pay-method:hover,.pay-method.selected{border-color:var(--accent-cyan);background:rgba(0,212,255,0.08);color:var(--text-primary)}

/* Inputs */
.input{width:100%;padding:10px 14px;border-radius:var(--radius-sm);border:1px solid var(--border-subtle);background:var(--bg-card);color:var(--text-primary);font-size:.88rem;font-family:inherit;outline:none;transition:var(--transition);margin-bottom:10px}
.input:focus{border-color:var(--accent-cyan)}
.input::placeholder{color:var(--text-muted)}

/* Coupon */
.coupon-row{display:flex;gap:8px;margin-bottom:10px}
.coupon-row .input{flex:1;margin-bottom:0}
.btn-apply-coupon{padding:8px 14px;border-radius:var(--radius-pill);background:var(--accent-purple);color:#fff;border:none;font-size:.8rem;cursor:pointer;font-family:inherit;font-weight:600;white-space:nowrap}
.btn-apply-coupon.applied{background:var(--accent-green)}
.strike{text-decoration:line-through;color:var(--text-muted)}

/* Toast */
.toast-container{position:fixed;bottom:30px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;align-items:center;gap:8px}
.toast{padding:12px 24px;border-radius:var(--radius-pill);background:var(--accent-green);color:#000;font-weight:700;font-size:.85rem;animation:toastIn .35s ease,toastOut .3s 2.5s ease forwards}
@keyframes toastIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
@keyframes toastOut{from{opacity:1;transform:translateY(0)}to{opacity:0;transform:translateY(-20px)}}

@media(max-width:768px){
  .games-grid{grid-template-columns:repeat(auto-fill,minmax(125px,1fr));gap:8px}
  .container{padding:0 14px}
  .hero{padding:100px 0 30px}
  .cart-drawer{width:100%}
}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="container">
    <a href="<?= BASE_URL() ?>" class="nav-logo"><span class="icon">🎮</span>GameTopup</a>
    <div class="nav-right">
      <a href="<?= base_url('client/group-buy') ?>" class="nav-btn nav-btn-outline" style="border-color:rgba(139,92,246,0.5);color:#a78bfa">👥 Group Buy</a>
      <button class="nav-btn-icon" onclick="toggleCart()" title="Giỏ hàng">🛒<span class="cart-badge" id="cartCount" style="display:none">0</span></button>
      <?php if($is_logged_in): ?>
      <span class="balance-badge" onclick="openWallet()">💰 <?= number_format($user_balance) ?>đ</span>
      <div class="user-badge" onclick="toggleUserMenu()">
        <span>👤 <?= htmlspecialchars($user_name) ?></span>
        <div class="user-dropdown" id="userDropdown" onclick="event.stopPropagation()">
          <div class="user-dd-header">
            <div class="user-dd-avatar">👤</div>
            <div>
              <div class="user-dd-name"><?= htmlspecialchars($user_name) ?></div>
              <div class="user-dd-email"><?= htmlspecialchars($is_admin ? 'Admin' : 'Member') ?></div>
            </div>
          </div>
          <div class="user-dd-status" id="orderStatusSummary">
            <div class="status-row"><span>✅ Hoàn thành</span><span class="count" id="countDone">0</span></div>
            <div class="status-row"><span>🔄 Đang xử lý</span><span class="count" id="countProcessing">0</span></div>
            <div class="status-row"><span>⏳ Chờ xử lý</span><span class="count" id="countPending">0</span></div>
          </div>
          <a href="<?= base_url('client/topup-history') ?>" class="user-dd-btn">📋 Lịch sử đơn hàng</a>
          <button class="user-dd-btn" onclick="openWallet();toggleUserMenu()">💰 Ví tiền</button>
          <?php if($is_admin): ?>
          <a href="<?= base_url('admin') ?>" class="user-dd-btn" style="color:var(--accent-gold)">⚙️ Admin Panel</a>
          <?php endif; ?>
          <a href="<?= base_url('client/logout') ?>" class="user-dd-btn" style="color:var(--accent-red)">🚪 Đăng xuất</a>
        </div>
      </div>
      <?php else: ?>
      <button class="nav-btn nav-btn-login" onclick="openLoginModal()">Đăng nhập</button>
      <button class="nav-btn nav-btn-outline" onclick="openRegisterModal()">Đăng ký</button>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- HERO -->
<div class="hero">
  <div class="container">
    <h1>Nạp Game <span>Nhanh Chóng</span><br>An Toàn & Giá Rẻ</h1>
    <p>Hỗ trợ 121+ game mobile iOS & Android. Nạp ngay — nhận quà liền tay!</p>
    <div class="hero-btns">
      <a href="#games" class="btn btn-primary">🎮 Nạp Game Ngay</a>
      <a href="#how" class="btn btn-outline">📖 Cách Thức</a>
    </div>
  </div>
</div>

<div class="container" id="games">
  <!-- STATS -->
  <div class="stats-bar">
    <div class="stat-card"><div class="sv" id="tp-total">...</div><div class="sl">Games hỗ trợ</div></div>
    <div class="stat-card"><div class="sv" id="tp-orders">0</div><div class="sl">Đơn hôm nay</div></div>
    <div class="stat-card"><div class="sv">99.8%</div><div class="sl">Tỉ lệ thành công</div></div>
  </div>

  <!-- SEARCH + FILTERS -->
  <div class="search-row">
    <div class="search-box"><span class="si">🔍</span><input type="text" id="tp-search" placeholder="Tìm game..." oninput="tpFilter()"></div>
    <div class="currency-toggle" id="currencyToggle">
      <button class="active" onclick="switchCurrency('VND',this)">🇻🇳 VND</button>
      <button onclick="switchCurrency('USDT',this)">💲 USDT</button>
    </div>
    <select id="tp-sort" onchange="tpFilter()" class="sort-select">
      <option value="">🕐 Mặc định</option>
      <option value="price-asc">💰 Giá thấp→cao</option>
      <option value="price-desc">💰 Giá cao→thấp</option>
      <option value="name">🔤 Tên A→Z</option>
    </select>
  </div>
  <div class="cat-tabs" id="tp-cats"><button class="cat-tab active" onclick="tpFilterCat('', this)">Tất cả</button></div>
  <div class="games-grid" id="tp-grid"><p style="color:#6b7280;padding:20px">Đang tải games...</p></div>
</div>

<!-- ==================== SẢN PHẨM SỐ (Account / Game Key / Gift Card) ==================== -->
<div class="container" id="shop">
  <div class="section-title">🛍️ Sản Phẩm Số</div>
  <div class="section-sub">Account • Game Key bản quyền • Gift Card — giao ngay tự động</div>

  <!-- Tabs phân nhánh -->
  <div class="tp-toolbar" style="justify-content:center">
    <div class="cat-tabs" id="shop-tabs">
      <button class="cat-tab active" onclick="shopSwitchTab('game_key', this)">🎮 Game Key</button>
      <button class="cat-tab" onclick="shopSwitchTab('gift_card', this)">💳 Gift Card</button>
      <button class="cat-tab" onclick="shopSwitchTab('account', this)">👤 Account</button>
      <button class="cat-tab" onclick="shopSwitchTab('software', this)">💻 Software</button>
      <button class="cat-tab" onclick="shopSwitchTab('subscription', this)">📺 Subscription</button>
    </div>
  </div>

  <div class="games-grid" id="shop-grid" style="margin-top:20px"><p style="color:#6b7280;padding:20px">Đang tải sản phẩm...</p></div>
</div>

<!-- Modal mua sản phẩm số -->
<div id="shopBuyOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.7);z-index:99998;align-items:center;justify-content:center" onclick="if(event.target===this)closeShopBuy()">
  <div style="background:#1a1a2e;border:1px solid rgba(139,92,246,.3);border-radius:16px;padding:28px;width:90%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.5)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <h3 style="margin:0;color:#fff;font-size:1.15rem" id="shopBuyName">Mua sản phẩm</h3>
      <button onclick="closeShopBuy()" style="background:none;border:none;color:#888;font-size:1.5rem;cursor:pointer">&times;</button>
    </div>
    <div id="shopBuyInfo" style="color:#aaa;font-size:.9rem;margin-bottom:14px"></div>
    <div style="margin-bottom:14px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Số lượng</label>
      <input type="number" id="shopBuyAmount" value="1" min="1" max="10"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box"
        oninput="updateShopBuyTotal()">
    </div>
    <div style="display:flex;justify-content:space-between;color:#fff;margin-bottom:16px;font-size:1.05rem">
      <span>Tổng cộng:</span><strong id="shopBuyTotal" style="color:var(--accent-gold)">0đ</strong>
    </div>
    <div id="shopBuyError" style="display:none;color:#ef4444;font-size:.85rem;margin-bottom:10px"></div>
    <div id="shopBuyKeys" style="display:none;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:12px;margin-bottom:12px;max-height:150px;overflow-y:auto"></div>
    <button id="shopBuyBtn" onclick="submitShopBuy()"
      style="width:100%;padding:12px;border:none;border-radius:8px;background:linear-gradient(135deg,#8B5CF6,#6D28D9);color:#fff;font-size:1rem;font-weight:600;cursor:pointer">
      💳 Mua ngay
    </button>
  </div>
</div>

<!-- HOW IT WORKS -->
<div class="container" id="how">
  <div class="section-title">🚀 Cách Thức Nạp Game</div>
  <div class="section-sub">Chỉ 3 bước đơn giản để nạp game yêu thích</div>
  <div class="steps">
    <div class="step-card"><div class="step-number">1</div><h4>Chọn Game & Gói</h4><p>Duyệt 121+ game, chọn gói nạp phù hợp với nhu cầu</p></div>
    <div class="step-card"><div class="step-number">2</div><h4>Nhập UID & Thanh Toán</h4><p>Nhập ID người chơi, chọn phương thức thanh toán</p></div>
    <div class="step-card"><div class="step-number">3</div><h4>Nhận Hàng Ngay</h4><p>Hệ thống xử lý tự động, nhận trong 30s-5 phút</p></div>
  </div>
</div>

<!-- FAQ -->
<div class="container">
  <div class="section-title">❓ Câu Hỏi Thường Gặp</div>
  <div class="faq-section">
    <div class="faq-item"><div class="faq-q" onclick="this.classList.toggle('open')"><span>Thời gian nạp game mất bao lâu?</span><span class="faq-arrow">▼</span></div><div class="faq-a">Thông thường từ 30 giây đến 5 phút. Với provider mock (test) thì gần như ngay lập tức.</div></div>
    <div class="faq-item"><div class="faq-q" onclick="this.classList.toggle('open')"><span>Hỗ trợ phương thức thanh toán nào?</span><span class="faq-arrow">▼</span></div><div class="faq-a">USDT, Chuyển khoản ngân hàng, Momo, Thẻ cào và nhiều cổng thanh toán khác.</div></div>
    <div class="faq-item"><div class="faq-q" onclick="this.classList.toggle('open')"><span>Nhập sai UID có được hoàn tiền không?</span><span class="faq-arrow">▼</span></div><div class="faq-a">Không. Vui lòng kiểm tra kỹ UID trước khi xác nhận. Hệ thống sẽ nạp vào đúng UID bạn nhập.</div></div>
    <div class="faq-item"><div class="faq-q" onclick="this.classList.toggle('open')"><span>Có tích điểm thành viên không?</span><span class="faq-arrow">▼</span></div><div class="faq-a">Có! Bạn sẽ nhận điểm loyalty cho mỗi đơn hàng. Tích lũy đủ điểm để đổi voucher giảm giá.</div></div>
  </div>
</div>

<!-- FOOTER -->
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <h4>🎮 GameTopup</h4>
        <p style="font-size:.82rem;color:var(--text-muted)">Nạp game mobile nhanh chóng, an toàn, giá rẻ. Hỗ trợ 121+ game.</p>
      </div>
      <div class="footer-col">
        <h4>Dịch vụ</h4>
        <a href="#games">Nạp game</a>
        <a href="#">Nạp thẻ</a>
        <a href="#">Mua tài khoản</a>
      </div>
      <div class="footer-col">
        <h4>Hỗ trợ</h4>
        <a href="#">Hướng dẫn</a>
        <a href="#">Liên hệ</a>
        <a href="#">Chính sách</a>
      </div>
      <div class="footer-col">
        <h4>Thanh toán</h4>
        <p style="font-size:.78rem;color:var(--text-muted)">💲 USDT · 🏦 Bank · 📱 Momo · 🎫 Thẻ cào</p>
      </div>
    </div>
    <div class="footer-bottom">© 2026 GameTopup. All rights reserved.</div>
  </div>
</footer>

<!-- CART DRAWER -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-header">
    <h3>🛒 Giỏ hàng (<span id="cartItemCount">0</span>)</h3>
    <button class="cart-close" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-body" id="cartItems"><div class="cart-empty">🛒 Giỏ hàng trống</div></div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-total"><span>Tổng cộng:</span><span id="cartTotal">0đ</span></div>
    <button class="btn btn-primary" style="width:100%" onclick="checkoutCart()">💳 Thanh toán tất cả</button>
  </div>
</div>

<!-- GAME DETAIL MODAL -->
<div class="modal-overlay" id="detailModal">
  <div class="modal-box" style="max-width:600px;position:relative">
    <button class="modal-close" onclick="closeDetail()" style="position:absolute;top:12px;right:16px;background:none;border:none;color:var(--text-muted);font-size:1.3rem;cursor:pointer">✕</button>
    <div style="text-align:center;font-size:4rem;margin-bottom:8px" id="detailIcon">🎮</div>
    <h2 id="detailName">Game Name</h2>
    <div class="modal-sub" id="detailMeta"></div>
    <div id="detailDesc" style="font-size:.85rem;color:var(--text-secondary);margin-bottom:16px"></div>
    <div id="detailTiers"></div>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal-overlay" id="checkoutModal">
  <div class="modal-box">
    <h2>💳 Thanh Toán</h2>
    <div class="modal-sub" id="chkItem"></div>
    <div class="payment-methods" id="payMethods">
      <button class="pay-method selected" onclick="selectPayMethod(this)">💲 USDT</button>
      <button class="pay-method" onclick="selectPayMethod(this)">🏦 Chuyển khoản</button>
      <button class="pay-method" onclick="selectPayMethod(this)">📱 Momo</button>
    </div>
    <input type="text" class="input" id="chkUID" placeholder="🎯 Nhập ID người chơi (Game UID)...">
    <div class="coupon-row">
      <input type="text" class="input" id="chkCoupon" placeholder="🏷️ Mã giảm giá (nếu có)">
      <button class="btn-apply-coupon" id="btnApplyCoupon" onclick="applyCoupon()">Áp dụng</button>
    </div>
    <div id="chkDiscount" style="display:none;margin-bottom:8px;font-size:.85rem;color:var(--accent-green)"></div>
    <div style="text-align:center;padding:12px;margin:10px 0;background:rgba(245,158,11,0.08);border-radius:12px;font-weight:700;font-size:1.1rem;color:var(--accent-gold)" id="chkTotal">0đ</div>
    <button class="btn btn-primary" style="width:100%" id="btnPay" onclick="processPayment()">💳 Thanh toán ngay</button>
    <button class="btn btn-outline" style="width:100%;margin-top:8px" onclick="closeCheckout()">Hủy</button>
  </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal-overlay" id="successModal">
  <div class="modal-box" style="text-align:center">
    <div style="font-size:4rem;margin-bottom:8px">✅</div>
    <h2>Thanh Toán Thành Công!</h2>
    <p style="color:var(--text-secondary);font-size:.9rem;margin:8px 0" id="successMsg"></p>
    <button class="btn btn-primary" style="width:100%;margin-top:12px" onclick="closeSuccess()">OK, đã hiểu</button>
  </div>
</div>

<!-- WALLET MODAL -->
<div class="modal-overlay" id="walletModal">
  <div class="modal-box">
    <h2>💰 Ví Tiền</h2>
    <div style="text-align:center;padding:20px;background:var(--bg-card);border-radius:var(--radius-md);margin:16px 0">
      <div style="font-size:2rem;font-weight:800;color:var(--accent-green)" id="walletBalance">0đ</div>
      <div style="font-size:.8rem;color:var(--text-muted)" id="walletUSDT"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px" id="walletMembership"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:12px">
      <button class="btn btn-primary" onclick="showTopup('vnd')" style="font-size:.82rem">💵 Nạp VND</button>
      <button class="btn btn-outline" onclick="showTopup('usdt')" style="font-size:.82rem">💲 Nạp USDT</button>
      <a href="<?= base_url('client/topup-history') ?>" class="btn btn-outline" style="font-size:.82rem;text-align:center">📋 Lịch sử GD</a>
      <button class="btn btn-outline" onclick="toggleTopupHistory()" style="font-size:.82rem">📜 LS Nạp tiền</button>
    </div>
    <div id="topupSection" style="display:none;margin-top:16px">
      <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px" id="topupChips"></div>
      <input type="text" class="input" id="topupNote" placeholder="Ghi chú (tuỳ chọn)">
      <button class="btn btn-primary" style="width:100%" id="btnTopup" onclick="processTopup()">Xác nhận nạp</button>
    </div>
    <div id="topupHistorySection" style="display:none;margin-top:12px;max-height:200px;overflow-y:auto"></div>
    <button class="btn btn-outline" style="width:100%;margin-top:12px" onclick="closeWallet()">Đóng</button>
  </div>
</div>

<!-- TOAST -->
<div class="toast-container" id="toastContainer"></div>

<!-- DYNAMIC GAME DATA FROM DB -->
<script>
// Fetch games from database API instead of static file
var GAMES = [];
var ALL_GAMES = [];
var ALL_CATEGORIES = [];

(async function loadGamesFromDB() {
  try {
    var resp = await fetch('/ajaxs/client/load_games.php');
    var data = await resp.json();
    ALL_CATEGORIES = data.categories || [];
    
    // Convert DB format to frontend format
    GAMES = data.games.map(function(g) {
      var game = {
        id: parseInt(g.id),
        name: g.name,
        cat: g.category || 'Other',
        icon: g.image || '🎮',
        currencyName: '',
        currencyUnit: '',
        description: g.full_name || '',
        gem: [],
        pack: [],
        allpack: []
      };
      
      // Group tiers by type
      g.tiers.forEach(function(t) {
        var tier = {
          vnd: t.price,
          label: t.label,
          tier_id: t.id,
          uid_pattern: t.uid_pattern || '',
          uid_placeholder: t.uid_placeholder || 'Enter UID'
        };
        if (t.original_price && t.original_price > t.price) {
          tier.original = t.original_price;
        }
        
        if (t.type === 'gem') game.gem.push(tier);
        else if (t.type === 'pack') game.pack.push(tier);
        else if (t.type === 'allpack') game.allpack.push(tier);
      });
      
      return game;
    });
    
    ALL_GAMES = GAMES;
    
    // Render now that data is loaded
    tpBuildCats();
    tpRender(GAMES);
    document.getElementById('tp-total').textContent = GAMES.length;
  } catch(e) {
    console.error('Failed to load games:', e);
    document.getElementById('tp-grid').innerHTML = '<div class="cart-empty">⚠️ Failed to load games. Please refresh.</div>';
  }
})();
</script>
<!-- END DYNAMIC GAME DATA -->
<script>
// ==================== DATA & STATE ====================
// GAMES is now loaded dynamically from DB API above
var tpState = {cat:"", q:""};
var catsBuilt = false;
var currentCurrency = 'VND';
var USDT_RATE = 25500;

// Cart state (localStorage)
var cart = JSON.parse(localStorage.getItem('gt_cart') || '[]');
var walletBalance = <?= $user_balance ?>;
var USER_TOKEN = '<?= $user_token ?>';
var IS_LOGGED_IN = <?= $is_logged_in ? 'true' : 'false' ?>;
var checkoutData = null;
var activeCoupon = null;
var topupType = 'vnd';
var topupAmount = 0;

// Coupon definitions
var COUPONS = {
  'WELCOME10': {discount:10, type:'percent', desc:'Giảm 10%'},
  'VIP50': {discount:50000, type:'fixed', desc:'Giảm 50,000đ'},
  'NEWUSER': {discount:25000, type:'fixed', desc:'Giảm 25,000đ'}
};

// ==================== TOAST ====================
function showToast(msg, isError){
  var t = document.createElement('div'); t.className = 'toast'; t.textContent = msg;
  if(isError) t.style.background = 'var(--accent-red)';
  document.getElementById('toastContainer').appendChild(t);
  setTimeout(function(){t.remove()},3000);
}

// ==================== CURRENCY ====================
function switchCurrency(curr, el){
  currentCurrency = curr;
  var btns = document.querySelectorAll('#currencyToggle button');
  for(var i=0;i<btns.length;i++) btns[i].classList.remove('active');
  el.classList.add('active');
  updatePrices();
}

function formatPrice(vnd){
  if(currentCurrency === 'USDT') return (vnd/USDT_RATE).toFixed(2) + ' $';
  return vnd.toLocaleString('vi-VN') + 'đ';
}

function updatePrices(){
  var els = document.querySelectorAll('[data-vnd]');
  for(var i=0;i<els.length;i++){
    var vnd = parseInt(els[i].getAttribute('data-vnd'));
    els[i].textContent = formatPrice(vnd);
  }
  updateCartUI();
  if(document.getElementById('detailModal').classList.contains('show')) renderDetailTiers(window._detailGame);
}

// ==================== GAME RENDERING ====================
function getDealBadge(g){
  if(g.id <= 3) return '<span class="deal-badge hot">HOT</span>';
  if(g.id <= 8 && g.id % 2 === 0) return '<span class="deal-badge new">NEW</span>';
  if(g.id <= 15 && g.id % 3 === 0) return '<span class="deal-badge sale">-15%</span>';
  if(g.id === 1 || g.id === 5) return '<span class="deal-badge best">BEST</span>';
  return '';
}

function tpRender(gs){
  document.getElementById('tp-grid').innerHTML = gs.map(function(g){
    return '<div class="game-card" onclick="openDetail(' + g.id + ')">' +
      getDealBadge(g) +
      '<div class="gi">' + (g.icon||'🎮') + '</div>' +
      '<div class="gn">' + g.name + '</div>' +
      '<div class="gc">' + (g.cat||'') + '</div>' +
      '<div class="gcr">' + (g.currencyName||'') + ' ' + (g.currencyUnit||'') + '</div></div>';
  }).join('');
  document.getElementById('tp-total').textContent = gs.length;
}

function tpBuildCats(){
  if(catsBuilt) return; catsBuilt = true;
  var cats = {};
  GAMES.forEach(function(g){var c = g.cat||'Other'; cats[c] = (cats[c]||0)+1});
  var sorted = Object.entries(cats).sort(function(a,b){return b[1]-a[1]});
  var html = '<button class="cat-tab active" onclick="tpFilterCat(\'\')">Tất cả</button>';
  sorted.forEach(function(p){
    html += '<button class="cat-tab" onclick="tpFilterCat(\'' + p[0].replace(/'/g,"\\'") + '\')">' + p[0] + ' (' + p[1] + ')</button>';
  });
  document.getElementById('tp-cats').innerHTML = html;
}

function tpFilterCat(c, el){
  tpState.cat = c;
  var tabs = document.querySelectorAll('.cat-tab');
  for(var i=0;i<tabs.length;i++) tabs[i].classList.remove('active');
  (el||event.target).classList.add('active');
  tpFilter();
}

function tpFilter(){
  var q = document.getElementById('tp-search').value.trim().toLowerCase();
  var gs = GAMES;
  if(tpState.cat) gs = gs.filter(function(g){return g.cat === tpState.cat});
  if(q.length >= 2) gs = gs.filter(function(g){return g.name.toLowerCase().indexOf(q) >= 0});
  var sort = document.getElementById('tp-sort').value;
  if(sort === 'price-asc') gs.sort(function(a,b){var ap=a.gem&&a.gem[0]?a.gem[0].vnd:0;var bp=b.gem&&b.gem[0]?b.gem[0].vnd:0;return ap-bp});
  else if(sort === 'price-desc') gs.sort(function(a,b){var ap=a.gem&&a.gem[0]?a.gem[0].vnd:0;var bp=b.gem&&b.gem[0]?b.gem[0].vnd:0;return bp-ap});
  else if(sort === 'name') gs.sort(function(a,b){return a.name.localeCompare(b.name)});
  tpRender(gs);
}

// ==================== CART ====================
function addToCart(gameId, gameName, gameIcon, branch, label, vnd){
  var key = gameId + '-' + branch + '-' + label.replace(/\s/g,'');
  var existing = cart.find(function(c){return c.key === key});
  if(existing){ existing.qty++; }
  else {
    cart.push({key:key, gameId:gameId, gameName:gameName, gameIcon:gameIcon, branch:branch, label:label, vnd:vnd, qty:1});
  }
  saveCart(); updateCartUI(); showToast('✅ Đã thêm vào giỏ: ' + label);
  document.getElementById('cartDrawer').classList.add('show');
  document.getElementById('cartOverlay').classList.add('show');
}

function removeFromCart(key){
  cart = cart.filter(function(c){return c.key !== key});
  saveCart(); updateCartUI();
}

function updateCartQty(key, delta){
  var item = cart.find(function(c){return c.key === key});
  if(!item) return;
  item.qty += delta;
  if(item.qty <= 0){ removeFromCart(key); return; }
  saveCart(); updateCartUI();
}

function saveCart(){ localStorage.setItem('gt_cart', JSON.stringify(cart)); }

function updateCartUI(){
  var totalItems = cart.reduce(function(s,c){return s + c.qty}, 0);
  var badge = document.getElementById('cartCount');
  if(totalItems > 0){ badge.style.display = 'block'; badge.textContent = totalItems; }
  else { badge.style.display = 'none'; }
  document.getElementById('cartItemCount').textContent = totalItems;

  var body = document.getElementById('cartItems');
  var footer = document.getElementById('cartFooter');
  var total = cart.reduce(function(s,c){return s + c.vnd * c.qty}, 0);

  if(cart.length === 0){
    body.innerHTML = '<div class="cart-empty">🛒 Giỏ hàng trống</div>';
    footer.style.display = 'none';
  } else {
    var html = '';
    for(var i=0;i<cart.length;i++){
      var c = cart[i];
      html += '<div class="cart-item">' +
        '<div class="cart-item-icon">' + (c.gameIcon||'🎮') + '</div>' +
        '<div class="cart-item-info"><div class="cart-item-name">' + c.gameName + '</div>' +
        '<div class="cart-item-branch">' + c.label + '</div></div>' +
        '<div class="cart-qty"><button onclick="updateCartQty(\'' + c.key + '\',-1)">−</button>' +
        '<span>' + c.qty + '</span>' +
        '<button onclick="updateCartQty(\'' + c.key + '\',1)">+</button></div>' +
        '<div class="cart-item-price">' + formatPrice(c.vnd * c.qty) + '</div>' +
        '<button class="cart-remove" onclick="removeFromCart(\'' + c.key + '\')">🗑️</button></div>';
    }
    body.innerHTML = html;
    footer.style.display = 'block';
    document.getElementById('cartTotal').textContent = formatPrice(total);
  }
}

function toggleCart(){
  var drawer = document.getElementById('cartDrawer');
  var overlay = document.getElementById('cartOverlay');
  if(drawer.classList.contains('show')){ closeCart(); }
  else { drawer.classList.add('show'); overlay.classList.add('show'); }
}

function closeCart(){
  document.getElementById('cartDrawer').classList.remove('show');
  document.getElementById('cartOverlay').classList.remove('show');
}

// ==================== USER DROPDOWN ====================
function toggleUserMenu(){
  document.getElementById('userDropdown').classList.toggle('show');
  if(document.getElementById('userDropdown').classList.contains('show')){
    updateUserDropdown();
  }
}

function updateUserDropdown(){
  // Fetch from localStorage order cache
  var orders = JSON.parse(localStorage.getItem('gt_topup_orders') || '[]');
  var pending=0, processing=0, done=0;
  for(var i=0;i<orders.length;i++){
    if(orders[i].status === 'pending') pending++;
    else if(orders[i].status === 'processing') processing++;
    else if(orders[i].status === 'done') done++;
  }
  document.getElementById('countPending').textContent = pending;
  document.getElementById('countProcessing').textContent = processing;
  document.getElementById('countDone').textContent = done;
}

// ==================== GAME DETAIL MODAL ====================
function openDetail(id){
  var g = GAMES.find(function(g){return g.id === id});
  if(!g) return;
  window._detailGame = g;
  document.getElementById('detailIcon').textContent = g.icon||'🎮';
  document.getElementById('detailName').textContent = g.name;
  document.getElementById('detailMeta').textContent = (g.cat||'') + ' · ' + (g.currencyName||'') + ' ' + (g.currencyUnit||'');
  document.getElementById('detailDesc').textContent = g.description || '';
  renderDetailTiers(g);
  document.getElementById('detailModal').classList.add('show');
}

function renderDetailTiers(g){
  var html = '';
  var sections = [
    {key:'gem', title:'💎 ' + (g.currencyName||'Currency'), items:g.gem||[]},
    {key:'pack', title:'📦 Gói & Battle Pass', items:g.pack||[]},
    {key:'allpack', title:'🎁 Combo Pack', items:g.allpack||[]}
  ];
  for(var s=0;s<sections.length;s++){
    var sec = sections[s];
    if(sec.items.length === 0) continue;
    html += '<div style="margin-bottom:16px"><h4 style="font-size:.82rem;color:var(--text-muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px">' + sec.title + '</h4>';
    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">';
    for(var j=0;j<sec.items.length;j++){
      var t = sec.items[j];
      html += '<div style="background:var(--bg-card);border:1px solid var(--border-subtle);border-radius:var(--radius-sm);padding:10px;text-align:center">' +
        '<div style="font-size:.8rem;font-weight:600;margin-bottom:4px">' + t.label + '</div>' +
        '<div style="font-size:.82rem;font-weight:700;color:var(--accent-gold)" data-vnd="' + t.vnd + '">' + formatPrice(t.vnd) + '</div>' +
        '<div style="display:flex;gap:4px;margin-top:6px">' +
        '<button onclick="buyNow(' + g.id + ',\'' + g.name.replace(/'/g,"\\'") + '\',\'' + (g.icon||'🎮') + '\',\'' + sec.key + '\',\'' + t.label.replace(/'/g,"\\'") + '\',' + t.vnd + ')" style="flex:1;padding:6px 8px;border-radius:var(--radius-pill);background:var(--gradient-primary);color:#fff;border:none;font-size:.7rem;font-weight:600;cursor:pointer;font-family:inherit">Mua</button>' +
        '<button onclick="event.stopPropagation();addToCart(' + g.id + ',\'' + g.name.replace(/'/g,"\\'") + '\',\'' + (g.icon||'🎮') + '\',\'' + sec.key + '\',\'' + t.label.replace(/'/g,"\\'") + '\',' + t.vnd + ')" style="padding:6px 10px;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);background:transparent;color:var(--text-primary);font-size:.7rem;cursor:pointer;font-family:inherit">🛒</button></div></div>';
    }
    html += '</div></div>';
  }
  document.getElementById('detailTiers').innerHTML = html;
}

function closeDetail(){
  document.getElementById('detailModal').classList.remove('show');
}

// ==================== CHECKOUT ====================
function buyNow(gameId, gameName, gameIcon, branch, label, vnd){
  checkoutData = {gameId:gameId, gameName:gameName, gameIcon:gameIcon, branch:branch, label:label, vnd:vnd, qty:1};
  openCheckout();
}

function checkoutCart(){
  if(cart.length === 0){ showToast('Giỏ hàng trống!', true); return; }
  var total = cart.reduce(function(s,c){return s + c.vnd * c.qty}, 0);
  checkoutData = {gameId:0, gameName:'Đơn hàng ' + cart.length + ' món', gameIcon:'🛒', branch:'cart', label:cart.length + ' gói', vnd:total, qty:1, cart:cart.slice()};
  openCheckout();
}

function openCheckout(){
  if(!checkoutData) return;
  document.getElementById('chkItem').textContent = checkoutData.gameName + ' — ' + checkoutData.label;
  document.getElementById('chkUID').value = '';
  document.getElementById('chkCoupon').value = '';
  document.getElementById('chkDiscount').style.display = 'none';
  activeCoupon = null;
  document.getElementById('btnApplyCoupon').textContent = 'Áp dụng';
  document.getElementById('btnApplyCoupon').classList.remove('applied');
  updateCheckoutTotal();
  document.getElementById('checkoutModal').classList.add('show');
  document.getElementById('detailModal').classList.remove('show');
  closeCart();
}

function closeCheckout(){
  document.getElementById('checkoutModal').classList.remove('show');
  checkoutData = null;
  activeCoupon = null;
}

function selectPayMethod(el){
  var methods = document.querySelectorAll('.pay-method');
  for(var i=0;i<methods.length;i++) methods[i].classList.remove('selected');
  el.classList.add('selected');
}

function applyCoupon(){
  var code = document.getElementById('chkCoupon').value.trim().toUpperCase();
  var coupon = COUPONS[code];
  if(!coupon){ showToast('Mã giảm giá không hợp lệ!', true); return; }
  activeCoupon = coupon;
  document.getElementById('btnApplyCoupon').textContent = '✓ Đã áp dụng';
  document.getElementById('btnApplyCoupon').classList.add('applied');
  updateCheckoutTotal();
  showToast(coupon.desc + ' đã được áp dụng!');
}

function updateCheckoutTotal(){
  var total = checkoutData ? checkoutData.vnd : 0;
  var discount = 0;
  if(activeCoupon){
    if(activeCoupon.type === 'percent') discount = total * activeCoupon.discount / 100;
    else discount = activeCoupon.discount;
  }
  var final = Math.max(0, total - discount);
  var html = formatPrice(final);
  if(discount > 0){
    html = '<span class="strike">' + formatPrice(total) + '</span> ' + formatPrice(final) + ' <span style="color:var(--accent-green);font-size:.8rem">(-' + formatPrice(discount) + ')</span>';
    document.getElementById('chkDiscount').style.display = 'block';
    document.getElementById('chkDiscount').textContent = 'Đã giảm: ' + formatPrice(discount);
  }
  document.getElementById('chkTotal').innerHTML = html;
}

function processPayment(){
  var uid = document.getElementById('chkUID').value.trim();
  if(uid.length < 3){ showToast('Vui lòng nhập UID người chơi!', true); return; }
  <?php if(!$is_logged_in): ?>
  showToast('Vui lòng đăng nhập để thanh toán!', true);
  openLoginModal();
  return;
  <?php endif; ?>

  var total = checkoutData.vnd;
  if(activeCoupon){
    if(activeCoupon.type === 'percent') total -= total * activeCoupon.discount / 100;
    else total -= activeCoupon.discount;
  }
  total = Math.max(0, total);

  if(walletBalance < total){ showToast('Số dư không đủ! Nạp thêm tiền vào ví.', true); return; }

  var btn = document.getElementById('btnPay');
  btn.disabled = true; btn.textContent = '⏳ Đang xử lý...';

  // Gọi API backend nếu là user thực, nếu không dùng localStorage
  <?php if($is_logged_in): ?>
  var fd = new FormData();
  fd.append('action','buyTopup');
  fd.append('token','<?= $user_token ?>');
  fd.append('game_id', checkoutData.gameId || 1);
  fd.append('tier_id', 1); // Cần lookup tier từ game data
  fd.append('game_uid', uid);
  if(activeCoupon) fd.append('coupon', document.getElementById('chkCoupon').value.trim());

  fetch('<?= BASE_URL('ajaxs/client/product.php') ?>', {method:'POST', body:fd})
    .then(function(r){return r.json()})
    .then(function(data){
      btn.disabled = false; btn.textContent = '💳 Thanh toán ngay';
      if(data.status === 'success'){
        walletBalance -= total;
        completeOrder(uid, total);
      } else {
        showToast(data.msg || 'Lỗi thanh toán!', true);
      }
    })
    .catch(function(){
      btn.disabled = false; btn.textContent = '💳 Thanh toán ngay';
      showToast('Lỗi kết nối!', true);
    });
  <?php else: ?>
  // Demo mode - localStorage
  walletBalance -= total;
  completeOrder(uid, total);
  btn.disabled = false; btn.textContent = '💳 Thanh toán ngay';
  <?php endif; ?>
}

function completeOrder(uid, total){
  closeCheckout();

  // Save to localStorage order history
  var orders = JSON.parse(localStorage.getItem('gt_topup_orders') || '[]');
  var orderId = 'GT' + Date.now().toString(36).toUpperCase();
  orders.unshift({
    id: orderId,
    gameName: checkoutData.gameName,
    label: checkoutData.label,
    uid: uid,
    vnd: total,
    status: 'done',
    date: new Date().toISOString(),
    payMethod: document.querySelector('.pay-method.selected') ? document.querySelector('.pay-method.selected').textContent.trim() : 'USDT'
  });
  if(orders.length > 50) orders = orders.slice(0,50);
  localStorage.setItem('gt_topup_orders', JSON.stringify(orders));

  // Clear cart
  cart = []; saveCart(); updateCartUI();

  // Update loyalty points
  var lp = parseInt(localStorage.getItem('gt_loyalty') || '0');
  lp += Math.floor(total / 1000);
  localStorage.setItem('gt_loyalty', lp.toString());

  // Update total spent
  var ts = parseInt(localStorage.getItem('gt_total_spent') || '0');
  ts += total;
  localStorage.setItem('gt_total_spent', ts.toString());

  // Show success
  document.getElementById('successMsg').textContent = checkoutData.gameName + ' — ' + checkoutData.label + ' | UID: ' + uid + ' | ' + formatPrice(total);
  document.getElementById('successModal').classList.add('show');
  checkoutData = null;
  activeCoupon = null;
  updateWalletUI();
}

function closeSuccess(){
  document.getElementById('successModal').classList.remove('show');
}

// ==================== WALLET ====================
function openWallet(){
  updateWalletUI();
  document.getElementById('walletModal').classList.add('show');
  document.getElementById('topupSection').style.display = 'none';
}

function closeWallet(){
  document.getElementById('walletModal').classList.remove('show');
}

function updateWalletUI(){
  document.getElementById('walletBalance').textContent = formatPrice(walletBalance);
  if(currentCurrency === 'USDT'){
    document.getElementById('walletUSDT').textContent = walletBalance.toLocaleString('vi-VN') + 'đ (gốc)';
  } else {
    document.getElementById('walletUSDT').textContent = '≈ ' + (walletBalance/USDT_RATE).toFixed(2) + ' USDT';
  }

  // Membership
  var ts = parseInt(localStorage.getItem('gt_total_spent') || '0');
  var tier = '🥈 Silver'; var nextTier = ''; var badgeStyle = 'linear-gradient(135deg,#9ca3af,#c0c0c0)';
  if(ts >= 20000000){ tier = '💎 Diamond'; badgeStyle = 'var(--gradient-primary)'; }
  else if(ts >= 5000000){ tier = '🥇 Gold'; badgeStyle = 'linear-gradient(135deg,#f59e0b,#fbbf24)'; nextTier = 'Cần thêm ' + formatPrice(20000000-ts) + ' để lên Diamond'; }
  else { nextTier = 'Cần thêm ' + formatPrice(5000000-ts) + ' để lên Gold'; }
  var lp = parseInt(localStorage.getItem('gt_loyalty') || '0');
  document.getElementById('walletMembership').innerHTML =
    '<div style="background:var(--bg-card);border-radius:var(--radius-sm);padding:12px;text-align:center"><span class="membership-badge" style="background:' + badgeStyle + ';color:#fff">' + tier + '</span><div style="font-size:.7rem;color:var(--text-muted);margin-top:4px">' + (nextTier||'Cấp cao nhất!') + '</div></div>' +
    '<div style="background:var(--bg-card);border-radius:var(--radius-sm);padding:12px;text-align:center"><div style="font-size:.9rem;font-weight:700;color:var(--accent-purple)">' + lp + ' điểm</div><div style="font-size:.7rem;color:var(--text-muted)">Loyalty Points</div></div>';
}

function showTopup(type){
  topupType = type;
  var section = document.getElementById('topupSection');
  section.style.display = 'block';
  var chips = document.getElementById('topupChips');
  var amounts = type === 'vnd' ? [50000,100000,200000,500000,1000000,5000000] : [5,10,20,50,100,500];
  var symbol = type === 'vnd' ? 'đ' : '$';
  chips.innerHTML = amounts.map(function(a){
    return '<button onclick="selectTopup(' + a + ',this)" style="padding:8px 14px;border-radius:var(--radius-pill);border:1px solid var(--border-subtle);background:transparent;color:var(--text-primary);font-size:.8rem;cursor:pointer;font-family:inherit;font-weight:600">' + a.toLocaleString() + symbol + '</button>';
  }).join('');
  topupAmount = 0;
  document.getElementById('btnTopup').disabled = true;
}

function selectTopup(amount, el){
  topupAmount = amount;
  var btns = document.querySelectorAll('#topupChips button');
  for(var i=0;i<btns.length;i++){ btns[i].style.borderColor = 'var(--border-subtle)'; btns[i].style.background = 'transparent'; }
  el.style.borderColor = 'var(--accent-cyan)'; el.style.background = 'rgba(0,212,255,0.1)';
  document.getElementById('btnTopup').disabled = false;
}

function processTopup(){
  if(topupAmount <= 0){ showToast('Vui lòng chọn số tiền!', true); return; }
  var amount = topupType === 'usdt' ? topupAmount * USDT_RATE : topupAmount;
  walletBalance += amount;
  showToast('✅ Đã nạp ' + formatPrice(amount) + ' vào ví!');

  // Save topup history
  var history = JSON.parse(localStorage.getItem('gt_topups') || '[]');
  history.unshift({id:'TP'+Date.now().toString(36), amount:amount, type:topupType, note:document.getElementById('topupNote').value, date:new Date().toISOString()});
  if(history.length > 100) history = history.slice(0,100);
  localStorage.setItem('gt_topups', JSON.stringify(history));

  document.getElementById('topupSection').style.display = 'none';
  updateWalletUI();
}

function toggleTopupHistory(){
  var section = document.getElementById('topupHistorySection');
  if(section.style.display === 'block'){ section.style.display = 'none'; return; }
  var history = JSON.parse(localStorage.getItem('gt_topups') || '[]');
  if(history.length === 0){ section.innerHTML = '<p style="color:var(--text-muted);text-align:center;padding:12px">Chưa có lịch sử nạp tiền</p>'; }
  else {
    section.innerHTML = history.slice(0,10).map(function(h){
      return '<div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--border-subtle);font-size:.78rem"><span>' + h.id + ' ' + (h.note||'') + '</span><span style="font-weight:700;color:var(--accent-green)">+' + formatPrice(h.amount) + '</span></div>';
    }).join('');
  }
  section.style.display = 'block';
}

// ==================== INIT ====================
document.addEventListener('click', function(e){
  if(!e.target.closest('.user-badge')) document.getElementById('userDropdown').classList.remove('show');
});

tpBuildCats();
tpRender(GAMES);
updateCartUI();
updateWalletUI();

// IIFE restore wallet from localStorage if user not logged in
(function(){
  if(!<?= $is_logged_in ? 'true' : 'false' ?>){
    var saved = parseInt(localStorage.getItem('gt_wallet_demo') || '0');
    if(saved > 0) walletBalance = saved;
  }
})();

// Save wallet to localStorage periodically for demo mode
setInterval(function(){
  if(!<?= $is_logged_in ? 'true' : 'false' ?>){
    localStorage.setItem('gt_wallet_demo', walletBalance.toString());
  }
}, 5000);

// ==================== SHOP SẢN PHẨM SỐ (Account/Key/GiftCard) ====================
var SHOP_DATA = {account:[],game_key:[],gift_card:[],software:[],subscription:[]};
var SHOP_TAB = 'game_key';
var SHOP_BUY_ITEM = null;
var SHOP_TYPE_ICON = {account:'👤',game_key:'🎮',gift_card:'💳',software:'💻',subscription:'📺'};

(function loadShopProducts(){
  fetch('/ajaxs/client/load_shop_products.php')
    .then(function(r){return r.json();})
    .then(function(d){
      if(d.status==='success'){
        SHOP_DATA = d.products;
        shopRender();
      } else {
        document.getElementById('shop-grid').innerHTML='<p style="color:#6b7280;padding:20px">Không tải được sản phẩm.</p>';
      }
    })
    .catch(function(){
      document.getElementById('shop-grid').innerHTML='<p style="color:#6b7280;padding:20px">⚠️ Lỗi kết nối.</p>';
    });
})();

function shopSwitchTab(tab, el){
  SHOP_TAB = tab;
  var tabs = document.querySelectorAll('#shop-tabs .cat-tab');
  for(var i=0;i<tabs.length;i++) tabs[i].classList.remove('active');
  if(el) el.classList.add('active');
  shopRender();
}

function shopRender(){
  var items = SHOP_DATA[SHOP_TAB] || [];
  var grid = document.getElementById('shop-grid');
  if(!items.length){
    grid.innerHTML = '<p style="color:#6b7280;padding:20px;grid-column:1/-1;text-align:center">Chưa có sản phẩm nào trong nhánh này.</p>';
    return;
  }
  grid.innerHTML = items.map(function(p){
    var icon = SHOP_TYPE_ICON[p.type]||'📦';
    var badge = '';
    if(p.discount>0) badge = '<div style="position:absolute;top:8px;right:8px;background:#ef4444;color:#fff;font-size:.65rem;padding:2px 6px;border-radius:6px;font-weight:700">-'+p.discount+'%</div>';
    var stockTxt = p.in_stock ? ('Còn '+p.stock) : 'Hết hàng';
    var stockColor = p.in_stock ? '#22c55e' : '#ef4444';
    var sub = '';
    if(p.type==='game_key'||p.type==='software'||p.type==='subscription'){
      sub = (p.platform||'') + (p.region?' • '+p.region:'');
    } else if(p.type==='gift_card'){
      sub = (p.platform||'') + (p.category?' • '+p.category:'');
    } else {
      sub = p.category||'';
    }
    var priceHtml = '<div class="gcr" style="font-size:.9rem">'+formatMoney(p.price)+'</div>';
    if(p.discount>0){
      priceHtml = '<div class="gcr" style="font-size:.9rem">'+formatMoney(p.price)+' <span style="text-decoration:line-through;color:#6b7280;font-size:.7rem">'+formatMoney(p.original_price)+'</span></div>';
    }
    return '<div class="game-card" style="'+(p.in_stock?'':'opacity:.5;cursor:not-allowed')+'" onclick="'+(p.in_stock?('openShopBuy('+p.id+',\''+p.type+'\')'):'')+'">' +
      badge +
      '<div class="gi">'+icon+'</div>' +
      '<div class="gn">'+p.name+'</div>' +
      '<div class="gc">'+sub+'</div>' +
      priceHtml +
      '<div style="font-size:.65rem;color:'+stockColor+';margin-top:4px">'+stockTxt+' • Đã bán '+p.sold+'</div>' +
      '</div>';
  }).join('');
}

function formatMoney(n){
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')+'đ';
}

function openShopBuy(id, type){
  if(!IS_LOGGED_IN){ showToast('Vui lòng đăng nhập để mua hàng!', true); openLoginModal(); return; }
  var items = SHOP_DATA[type]||[];
  var item = null;
  for(var i=0;i<items.length;i++){ if(items[i].id===id){item=items[i];break;} }
  if(!item) return;
  SHOP_BUY_ITEM = item;
  document.getElementById('shopBuyName').textContent = item.name;
  var info = 'Loại: '+(SHOP_TYPE_ICON[type]||'')+' '+type.replace('_',' ');
  if(item.platform) info += ' • '+item.platform;
  if(item.region) info += ' • '+item.region;
  info += '<br>Giá: <strong style="color:#fff">'+formatMoney(item.price)+'</strong> • Còn '+item.stock;
  document.getElementById('shopBuyInfo').innerHTML = info;
  document.getElementById('shopBuyAmount').value = 1;
  document.getElementById('shopBuyAmount').max = Math.min(10, item.stock);
  document.getElementById('shopBuyError').style.display='none';
  document.getElementById('shopBuyKeys').style.display='none';
  document.getElementById('shopBuyBtn').disabled=false;
  document.getElementById('shopBuyBtn').textContent='💳 Mua ngay';
  updateShopBuyTotal();
  document.getElementById('shopBuyOverlay').style.display='flex';
}

function closeShopBuy(){
  document.getElementById('shopBuyOverlay').style.display='none';
}

function updateShopBuyTotal(){
  if(!SHOP_BUY_ITEM) return;
  var amt = parseInt(document.getElementById('shopBuyAmount').value)||1;
  document.getElementById('shopBuyTotal').textContent = formatMoney(SHOP_BUY_ITEM.price*amt);
}

function submitShopBuy(){
  if(!SHOP_BUY_ITEM) return;
  var amt = parseInt(document.getElementById('shopBuyAmount').value)||1;
  var btn = document.getElementById('shopBuyBtn');
  var err = document.getElementById('shopBuyError');
  var keysBox = document.getElementById('shopBuyKeys');
  err.style.display='none'; keysBox.style.display='none';
  btn.disabled=true; btn.textContent='Đang xử lý...';

  var body = 'action=buyProduct&id='+SHOP_BUY_ITEM.id+'&amount='+amt+'&token='+USER_TOKEN;

  fetch('/ajaxs/client/product.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:body
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){
      // Reload stock data
      keysBox.style.display='block';
      keysBox.innerHTML='<div style="color:#22c55e;font-weight:600;margin-bottom:6px">✅ Mua thành công! Mã của bạn:</div>';
      if(d.accounts && d.accounts.length){
        d.accounts.forEach(function(a){
          keysBox.innerHTML += '<div style="color:#fff;font-family:monospace;background:rgba(255,255,255,.08);padding:6px 8px;border-radius:6px;margin-bottom:4px;word-break:break-all">'+a+'</div>';
        });
      } else {
        keysBox.innerHTML += '<div style="color:#aaa">Vào <a href="/client/topup-history" style="color:#8B5CF6">Lịch sử đơn hàng</a> để xem mã.</div>';
      }
      btn.textContent='✅ Đã mua';
      // Refresh shop data
      setTimeout(function(){
        fetch('/ajaxs/client/load_shop_products.php').then(function(r){return r.json();}).then(function(dd){
          if(dd.status==='success'){SHOP_DATA=dd.products;shopRender();}
        });
      },500);
    } else {
      err.textContent = d.msg||'Mua thất bại';
      err.style.display='block';
      btn.disabled=false; btn.textContent='💳 Mua ngay';
    }
  }).catch(function(){
    err.textContent='Lỗi kết nối';err.style.display='block';
    btn.disabled=false;btn.textContent='💳 Mua ngay';
  });
}

// ==================== LOGIN MODAL ====================
function openLoginModal(){
  document.getElementById('registerModalOverlay').style.display='none';
  document.getElementById('loginModalOverlay').style.display='flex';
}
function closeLoginModal(){
  document.getElementById('loginModalOverlay').style.display='none';
}
function submitLoginModal(){
  var u=document.getElementById('modal-login-username').value.trim();
  var p=document.getElementById('modal-login-password').value;
  var csrf=document.getElementById('modal-csrf-token').value;
  var btn=document.getElementById('modal-login-btn');
  var err=document.getElementById('modal-login-error');
  if(!u||!p){err.textContent='Vui lòng nhập đầy đủ thông tin';err.style.display='block';return;}
  btn.disabled=true;btn.textContent='Đang đăng nhập...';err.style.display='none';
  fetch('/ajaxs/client/auth.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=Login&username='+encodeURIComponent(u)+'&password='+encodeURIComponent(p)+'&csrf_token='+encodeURIComponent(csrf)
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){
      location.reload();
    }else{
      err.textContent=d.msg||'Đăng nhập thất bại';err.style.display='block';
      btn.disabled=false;btn.textContent='Đăng nhập';
    }
  }).catch(function(){
    err.textContent='Lỗi kết nối';err.style.display='block';
    btn.disabled=false;btn.textContent='Đăng nhập';
  });
}

// ==================== REGISTER MODAL ====================
function openRegisterModal(){
  document.getElementById('loginModalOverlay').style.display='none';
  document.getElementById('registerModalOverlay').style.display='flex';
}
function closeRegisterModal(){
  document.getElementById('registerModalOverlay').style.display='none';
}
function submitRegisterModal(){
  var u=document.getElementById('modal-reg-username').value.trim();
  var e=document.getElementById('modal-reg-email').value.trim();
  var p=document.getElementById('modal-reg-password').value;
  var p2=document.getElementById('modal-reg-password2').value;
  var csrf=document.getElementById('modal-csrf-token').value;
  var btn=document.getElementById('modal-reg-btn');
  var err=document.getElementById('modal-reg-error');
  var ok=document.getElementById('modal-reg-success');
  err.style.display='none';ok.style.display='none';
  if(!u||!e||!p||!p2){err.textContent='Vui lòng nhập đầy đủ thông tin';err.style.display='block';return;}
  if(p.length<6){err.textContent='Mật khẩu tối thiểu 6 ký tự';err.style.display='block';return;}
  if(p!==p2){err.textContent='Mật khẩu nhập lại không khớp';err.style.display='block';return;}
  btn.disabled=true;btn.textContent='Đang đăng ký...';
  fetch('/ajaxs/client/auth.php',{
    method:'POST',
    headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=Register&username='+encodeURIComponent(u)+'&email='+encodeURIComponent(e)+'&password='+encodeURIComponent(p)+'&repassword='+encodeURIComponent(p2)+'&csrf_token='+encodeURIComponent(csrf)
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){
      ok.textContent='✅ Đăng ký thành công! Đang đăng nhập...';ok.style.display='block';
      setTimeout(function(){location.reload();},1200);
    }else{
      err.textContent=d.msg||'Đăng ký thất bại';err.style.display='block';
      btn.disabled=false;btn.textContent='Đăng ký';
    }
  }).catch(function(){
    err.textContent='Lỗi kết nối';err.style.display='block';
    btn.disabled=false;btn.textContent='Đăng ký';
  });
}
</script>

<!-- LOGIN MODAL (đăng nhập ngay trên homepage, không cần trang riêng) -->
<div id="loginModalOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.7);z-index:99999;align-items:center;justify-content:center" onclick="if(event.target===this)closeLoginModal()">
  <div style="background:#1a1a2e;border:1px solid rgba(139,92,246,.3);border-radius:16px;padding:32px;width:90%;max-width:400px;box-shadow:0 20px 60px rgba(0,0,0,.5)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="margin:0;color:#fff;font-size:1.3rem">🔐 Đăng nhập</h3>
      <button onclick="closeLoginModal()" style="background:none;border:none;color:#888;font-size:1.5rem;cursor:pointer">&times;</button>
    </div>
    <input type="hidden" id="modal-csrf-token" value="<?= $csrf_token ?? '' ?>">
    <div style="margin-bottom:14px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Tên đăng nhập</label>
      <input type="text" id="modal-login-username" placeholder="Nhập username" autocomplete="username"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box">
    </div>
    <div style="margin-bottom:14px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Mật khẩu</label>
      <input type="password" id="modal-login-password" placeholder="Nhập mật khẩu" autocomplete="current-password"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box"
        onkeydown="if(event.key==='Enter')submitLoginModal()">
    </div>
    <div id="modal-login-error" style="display:none;color:#ef4444;font-size:.85rem;margin-bottom:10px"></div>
    <button id="modal-login-btn" onclick="submitLoginModal()"
      style="width:100%;padding:12px;border:none;border-radius:8px;background:linear-gradient(135deg,#8B5CF6,#6D28D9);color:#fff;font-size:1rem;font-weight:600;cursor:pointer">
      Đăng nhập
    </button>
    <div style="text-align:center;margin-top:14px;font-size:.85rem">
      <a href="<?= base_url('client/forgot-password') ?>" style="color:#8B5CF6">Quên mật khẩu?</a>
      <span style="color:#555"> • </span>
      <a href="javascript:void(0)" onclick="openRegisterModal()" style="color:#8B5CF6">Đăng ký</a>
    </div>
  </div>
</div>

<!-- REGISTER MODAL (đăng ký ngay trên homepage, không cần trang riêng) -->
<div id="registerModalOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.7);z-index:99999;align-items:center;justify-content:center" onclick="if(event.target===this)closeRegisterModal()">
  <div style="background:#1a1a2e;border:1px solid rgba(139,92,246,.3);border-radius:16px;padding:32px;width:90%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.5)">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
      <h3 style="margin:0;color:#fff;font-size:1.3rem">📝 Đăng ký tài khoản</h3>
      <button onclick="closeRegisterModal()" style="background:none;border:none;color:#888;font-size:1.5rem;cursor:pointer">&times;</button>
    </div>
    <div style="margin-bottom:12px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Tên đăng nhập</label>
      <input type="text" id="modal-reg-username" placeholder="Username (3-50 ký tự)" autocomplete="username" minlength="3" maxlength="50"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box">
    </div>
    <div style="margin-bottom:12px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Email</label>
      <input type="email" id="modal-reg-email" placeholder="email@example.com" autocomplete="email" maxlength="100"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box">
    </div>
    <div style="margin-bottom:12px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Mật khẩu</label>
      <input type="password" id="modal-reg-password" placeholder="Tối thiểu 6 ký tự" autocomplete="new-password" minlength="6" maxlength="50"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box">
    </div>
    <div style="margin-bottom:12px">
      <label style="color:#aaa;font-size:.85rem;display:block;margin-bottom:4px">Nhập lại mật khẩu</label>
      <input type="password" id="modal-reg-password2" placeholder="Nhập lại mật khẩu" autocomplete="new-password" minlength="6" maxlength="50"
        style="width:100%;padding:10px 14px;border-radius:8px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.05);color:#fff;font-size:.95rem;box-sizing:border-box"
        onkeydown="if(event.key==='Enter')submitRegisterModal()">
    </div>
    <div id="modal-reg-error" style="display:none;color:#ef4444;font-size:.85rem;margin-bottom:10px"></div>
    <div id="modal-reg-success" style="display:none;color:#22c55e;font-size:.85rem;margin-bottom:10px"></div>
    <button id="modal-reg-btn" onclick="submitRegisterModal()"
      style="width:100%;padding:12px;border:none;border-radius:8px;background:linear-gradient(135deg,#8B5CF6,#6D28D9);color:#fff;font-size:1rem;font-weight:600;cursor:pointer">
      Đăng ký
    </button>
    <div style="text-align:center;margin-top:14px;font-size:.85rem">
      <span style="color:#aaa">Đã có tài khoản?</span>
      <a href="javascript:void(0)" onclick="openLoginModal()" style="color:#8B5CF6">Đăng nhập</a>
    </div>
  </div>
</div>

</body>
</html>
