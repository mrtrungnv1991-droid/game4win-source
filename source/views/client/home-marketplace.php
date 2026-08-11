<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }

$body = ['title' => $CMSNT->site('title'), 'desc' => 'Marketplace sản phẩm số — Game Key, Gift Card, Account, Top Up. Giao ngay tự động.'];
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

// Load all products
$products = $CMSNT->get_list_safe(
    "SELECT p.id, p.code, p.name, p.slug, p.price, p.discount, p.product_type, p.platform, p.region,
            p.short_desc, p.sold, p.images, p.rating, p.rating_count, p.supplier_id, p.api_stock, c.name as cat_name
     FROM products p LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.status = 1 AND p.hide_in_shop = 0
     ORDER BY p.sold DESC, p.id DESC LIMIT 200", []);

// Load group buy deals
$gb_deals = [];
$gb_by_product = [];
try {
    $gb_deals = $CMSNT->get_list_safe(
        "SELECT d.*, p.name as p_name FROM group_buy_deals d
         LEFT JOIN products p ON d.product_id = p.id
         WHERE d.status = 'active' ORDER BY d.id DESC LIMIT 20", []);
    foreach ($gb_deals as $d) {
        if ($d['product_id'] > 0) $gb_by_product[$d['product_id']] = $d;
    }
} catch(Exception $e) {}

// Topup games count
$games_count = $CMSNT->num_rows("SELECT id FROM games WHERE status = 1");

// Build product data with GB info
function buildProductData($p, $gb_by_product) {
    $gb = $gb_by_product[$p['id']] ?? null;
    return [
        'id'=>(int)$p['id'],'code'=>$p['code'],'name'=>$p['name'],
        'price'=>(int)($p['price']-$p['price']*$p['discount']/100),
        'old_price'=>(int)$p['price'],'discount'=>(float)$p['discount'],
        'type'=>$p['product_type']??'account','platform'=>$p['platform']?:'',
        'region'=>$p['region']?:'','sold'=>(int)$p['sold'],
                'stock'=>(!empty($p['supplier_id'])) ? (int)$p['api_stock'] : getStock($p['code']),
        'rating'=>(float)($p['rating']??0),
        'rating_count'=>(int)($p['rating_count']??0),
        'gb_id'=>$gb?(int)$gb['id']:0,
        'gb_price'=>$gb?(int)$gb['group_price']:0,
        'gb_cur'=>$gb?(int)$gb['current_participants']:0,
        'gb_max'=>$gb?(int)$gb['max_participants']:0,
        'gb_end'=>$gb&&$gb['end_date']?(int)(strtotime($gb['end_date'])*1000):0
    ];
}
$products_data = array_map(function($p) use ($gb_by_product){ return buildProductData($p, $gb_by_product); }, $products);

// Sections
$bestsellers = array_slice(array_filter($products_data, function($p){ return $p['sold'] > 0 || $p['stock'] > 0; }), 0, 30);
$top_rated = $products_data;
usort($top_rated, function($a,$b){ return $b['rating'] <=> $a['rating']; });
$top_rated = array_slice($top_rated, 0, 30);
$top_deals = array_filter($products_data, function($p){ return $p['discount'] > 0 || $p['gb_id'] > 0; });
usort($top_deals, function($a,$b){ return $b['discount'] <=> $a['discount']; });
$top_deals = array_slice($top_deals, 0, 30);
?><!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($body['title']) ?></title>
<meta name="description" content="<?= htmlspecialchars($body['desc']) ?>">
<style>
:root{--bg:#0f0f1a;--bg-card:#1a1a2e;--bg-hover:#22223a;--border:#2a2a4a;--text:#e2e8f0;--muted:#8892a8;--accent:#8B5CF6;--accent2:#6D28D9;--gold:#F59E0B;--green:#22c55e;--red:#ef4444;--radius:12px}
*{margin:0;padding:0;box-sizing:border-box}
body{background:var(--bg);color:var(--text);font-family:'Segoe UI',system-ui,-apple-system,sans-serif;min-height:100vh}
a{color:var(--accent);text-decoration:none}
.container{max-width:1360px;margin:0 auto;padding:0 16px}

/* HEADER */
.header{background:rgba(15,15,26,.97);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;backdrop-filter:blur(10px)}
.header-inner{display:flex;align-items:center;gap:16px;padding:12px 0}
.logo{font-size:1.3rem;font-weight:800;color:#fff;white-space:nowrap}
.logo span{color:var(--accent)}
.search-box{flex:1;max-width:560px;position:relative}
.search-box input{width:100%;padding:10px 16px 10px 40px;border-radius:24px;border:1px solid var(--border);background:var(--bg-card);color:var(--text);font-size:.9rem;outline:none}
.search-box input:focus{border-color:var(--accent)}
.search-box .icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted)}
.header-actions{display:flex;align-items:center;gap:10px;margin-left:auto}
.btn{padding:8px 18px;border-radius:8px;border:none;cursor:pointer;font-size:.85rem;font-weight:600;transition:.2s}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff}
.btn-primary:hover{opacity:.9;transform:translateY(-1px)}
.btn-outline{background:transparent;border:1px solid var(--border);color:var(--text)}
.btn-outline:hover{border-color:var(--accent);color:var(--accent)}
.balance-badge{background:var(--bg-card);border:1px solid var(--border);padding:6px 14px;border-radius:20px;font-size:.85rem;cursor:pointer;color:var(--gold)}

/* CATEGORY NAV */
.cat-nav{background:var(--bg-card);border-bottom:1px solid var(--border);overflow-x:auto}
.cat-nav-inner{display:flex;gap:0;padding:0}
.cat-nav-item{padding:12px 20px;font-size:.85rem;font-weight:600;color:var(--muted);cursor:pointer;white-space:nowrap;border-bottom:2px solid transparent;transition:.2s}
.cat-nav-item:hover{color:var(--text)}
.cat-nav-item.active{color:var(--accent);border-bottom-color:var(--accent)}

/* HERO */
.hero{background:linear-gradient(135deg,#1a1a2e 0%,#2d1b69 50%,#1a1a2e 100%);padding:36px 0;text-align:center;border-bottom:1px solid var(--border)}
.hero h1{font-size:2rem;font-weight:800;margin-bottom:8px}
.hero h1 span{color:var(--accent)}
.hero p{color:var(--muted);font-size:.95rem}
.hero-stats{display:flex;justify-content:center;gap:40px;margin-top:22px}
.hero-stat{text-align:center}
.hero-stat .num{font-size:1.5rem;font-weight:800;color:var(--gold)}
.hero-stat .lbl{font-size:.75rem;color:var(--muted)}

/* SECTION (G2A-style rows) */
.section{margin:32px 0}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.section-title{font-size:1.35rem;font-weight:800;display:flex;align-items:center;gap:10px}
.section-link{font-size:.85rem;color:var(--accent);cursor:pointer}
.section-link:hover{text-decoration:underline}

/* PRODUCT GRID — 6 per row (G2A style) */
.grid{display:grid;grid-template-columns:repeat(6,1fr);gap:14px}
.p-card{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius);padding:14px;cursor:pointer;transition:.2s;position:relative;overflow:hidden;display:flex;flex-direction:column}
.p-card:hover{border-color:var(--accent);transform:translateY(-3px);box-shadow:0 8px 24px rgba(139,92,246,.15)}
.p-card .badge-disc{position:absolute;top:8px;right:8px;background:var(--red);color:#fff;font-size:.68rem;font-weight:700;padding:3px 7px;border-radius:6px;z-index:2}
.p-card .badge-gb{position:absolute;top:8px;left:8px;background:var(--accent);color:#fff;font-size:.6rem;font-weight:700;padding:3px 7px;border-radius:6px;z-index:2}
.p-card .card-img{height:90px;display:flex;align-items:center;justify-content:center;font-size:2.8rem;background:rgba(255,255,255,.03);border-radius:8px;margin-bottom:10px}
.p-card .platform-badge{display:inline-flex;align-items:center;gap:4px;font-size:.65rem;color:var(--muted);background:rgba(255,255,255,.06);padding:2px 8px;border-radius:4px;margin-bottom:6px;width:fit-content}
.p-card .name{font-size:.82rem;font-weight:700;margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;min-height:2.2em}
.p-card .rating{display:flex;align-items:center;gap:4px;font-size:.7rem;color:var(--gold);margin-bottom:6px}
.p-card .rating .count{color:var(--muted)}
.p-card .price-row{display:flex;align-items:center;gap:6px;margin-top:auto}
.p-card .price{font-size:.95rem;font-weight:800;color:var(--gold)}
.p-card .old-price{font-size:.7rem;color:var(--muted);text-decoration:line-through}
.p-card .stock{font-size:.65rem;margin-top:5px}
.stock-in{color:var(--green)}
.stock-out{color:var(--red)}

/* SHOW MORE */
.show-more-wrap{text-align:center;margin-top:20px}
.btn-show-more{padding:12px 32px;border-radius:24px;border:1px solid var(--accent);background:transparent;color:var(--accent);font-size:.9rem;font-weight:700;cursor:pointer;transition:.2s}
.btn-show-more:hover{background:var(--accent);color:#fff}

/* PAGINATION */
.pagination{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:18px;flex-wrap:wrap}
.pg-btn{min-width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:1px solid var(--border);background:var(--bg-card);color:var(--text);font-size:.85rem;font-weight:600;cursor:pointer;transition:.2s;padding:0 10px}
.pg-btn:hover{border-color:var(--accent);color:var(--accent)}
.pg-btn.active{background:var(--accent);border-color:var(--accent);color:#fff}
.pg-btn.disabled{opacity:.35;cursor:not-allowed;pointer-events:none}
.pg-info{font-size:.78rem;color:var(--muted);margin-left:8px}

/* GROUP BUY STRIP */
.gb-strip{display:flex;gap:12px;overflow-x:auto;padding:8px 0 16px}
.gb-card{min-width:270px;background:var(--bg-card);border:1px solid var(--accent);border-radius:var(--radius);padding:14px;flex-shrink:0;cursor:pointer;transition:.2s}
.gb-card:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(139,92,246,.2)}
.gb-card .gb-title{font-size:.85rem;font-weight:700;margin-bottom:6px}
.gb-card .gb-progress{height:6px;background:var(--border);border-radius:3px;margin:8px 0;overflow:hidden}
.gb-card .gb-progress-fill{height:100%;background:linear-gradient(90deg,var(--accent),var(--gold));border-radius:3px}
.gb-card .gb-info{font-size:.72rem;color:var(--muted);display:flex;justify-content:space-between}
.gb-countdown{font-size:.75rem;color:var(--red);font-weight:700;margin:6px 0}

/* CAROUSEL (cuộn xoay với mũi tên ‹ ›) */
.carousel-wrap{position:relative;display:flex;align-items:center;gap:8px}
.carousel-track{display:flex;gap:14px;overflow-x:auto;scroll-behavior:smooth;padding:8px 4px 16px;flex:1;scrollbar-width:none;-ms-overflow-style:none}
.carousel-track::-webkit-scrollbar{display:none}
.carousel-track .p-card{min-width:200px;width:200px;flex-shrink:0}
.carousel-track .gb-card{min-width:270px;flex-shrink:0}
.carousel-arrow{width:44px;height:44px;border-radius:50%;border:1px solid var(--border);background:var(--bg-card);color:var(--text);font-size:1.6rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;flex-shrink:0;z-index:5;line-height:1;padding-bottom:4px}
.carousel-arrow:hover{background:var(--accent);border-color:var(--accent);color:#fff;transform:scale(1.1)}
.carousel-arrow:active{transform:scale(.95)}
@media(max-width:768px){
  .carousel-arrow{width:36px;height:36px;font-size:1.3rem}
  .carousel-track .p-card{min-width:160px;width:160px}
}

/* MODAL */
.modal-overlay{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.75);z-index:999;align-items:center;justify-content:center}
.modal-box{background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:28px;width:92%;max-width:440px;max-height:90vh;overflow-y:auto}
.modal-box h3{font-size:1.15rem;margin-bottom:16px;color:#fff}
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:.82rem;color:var(--muted);margin-bottom:4px}
.form-group input,.form-group select{width:100%;padding:10px 14px;border-radius:8px;border:1px solid var(--border);background:rgba(255,255,255,.05);color:var(--text);font-size:.9rem}
.form-group input:focus,.form-group select:focus{outline:none;border-color:var(--accent)}
.error-msg{color:var(--red);font-size:.82rem;margin-bottom:10px;display:none}
.success-msg{color:var(--green);font-size:.82rem;margin-bottom:10px;display:none}
.key-result{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);border-radius:8px;padding:12px;margin:10px 0;max-height:150px;overflow-y:auto}
.key-result .key-item{color:#fff;font-family:monospace;background:rgba(255,255,255,.08);padding:6px 8px;border-radius:6px;margin-bottom:4px;word-break:break-all;font-size:.85rem}

/* TOPUP */
.topup-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px}
.topup-card{background:var(--bg-card);border:1px solid var(--border);border-radius:10px;padding:14px 10px;text-align:center;cursor:pointer;transition:.2s}
.topup-card:hover{border-color:var(--accent);transform:translateY(-2px)}
.topup-card .gi{font-size:1.8rem;margin-bottom:6px}
.topup-card .gn{font-size:.75rem;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.topup-card .gc{font-size:.62rem;color:var(--muted)}

/* FOOTER */
.footer{border-top:1px solid var(--border);padding:24px 0;margin-top:40px;text-align:center;color:var(--muted);font-size:.8rem}

@media(max-width:1100px){.grid,.topup-grid{grid-template-columns:repeat(4,1fr)}}
@media(max-width:768px){
  .header-inner{flex-wrap:wrap}
  .search-box{order:3;max-width:100%;flex-basis:100%}
  .grid,.topup-grid{grid-template-columns:repeat(2,1fr)}
  .hero h1{font-size:1.4rem}
  .hero-stats{gap:20px}
}
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
<div class="container header-inner">
  <a href="/" class="logo">🎮 <span>Digital</span>Commerce</a>
  <div class="search-box">
    <span class="icon">🔍</span>
    <input type="text" id="searchInput" placeholder="Tìm kiếm sản phẩm..." oninput="filterProducts()">
  </div>
  <div class="header-actions">
    <?php if($is_logged_in): ?>
    <span class="balance-badge" onclick="openWallet()">💰 <?= number_format($user_balance) ?>đ</span>
    <?php if($is_admin): ?><a href="<?= base_url('admin') ?>" class="btn btn-outline">⚙️ Admin</a><?php endif; ?>
    <button class="btn btn-outline" onclick="logout()">Đăng xuất</button>
    <?php else: ?>
    <button class="btn btn-primary" onclick="openModal('loginModal')">Đăng nhập</button>
    <button class="btn btn-outline" onclick="openModal('registerModal')">Đăng ký</button>
    <?php endif; ?>
  </div>
</div>
</header>

<!-- CATEGORY NAV -->
<nav class="cat-nav">
<div class="container cat-nav-inner">
  <div class="cat-nav-item active" onclick="switchCat('all',this)">🏪 Tất cả</div>
  <div class="cat-nav-item" onclick="switchCat('game_key',this)">🎮 Game Key</div>
  <div class="cat-nav-item" onclick="switchCat('gift_card',this)">💳 Gift Card</div>
  <div class="cat-nav-item" onclick="switchCat('account',this)">👤 Account</div>
  <div class="cat-nav-item" onclick="switchCat('software',this)">💻 Software</div>
  <div class="cat-nav-item" onclick="switchCat('subscription',this)">📺 Subscription</div>
  <div class="cat-nav-item" onclick="switchCat('topup',this)">📱 Top Up (<?= $games_count ?>)</div>
</div>
</nav>

<!-- HERO -->
<div class="hero">
<div class="container">
  <h1>Marketplace <span>Sản Phẩm Số</span></h1>
  <p>Game Key • Gift Card • Account • Top Up — Giao ngay tự động 24/7</p>
  <div class="hero-stats">
    <div class="hero-stat"><div class="num"><?= count($products) ?></div><div class="lbl">Sản phẩm</div></div>
    <div class="hero-stat"><div class="num"><?= $games_count ?></div><div class="lbl">Game Top Up</div></div>
    <div class="hero-stat"><div class="num">99.8%</div><div class="lbl">Thành công</div></div>
    <div class="hero-stat"><div class="num">24/7</div><div class="lbl">Tự động</div></div>
  </div>
</div>
</div>

<div class="container">

<!-- GROUP BUY CAROUSEL -->
<?php if(!empty($gb_deals)): ?>
<div class="section" id="section-groupbuy">
<div class="section-header">
  <div class="section-title">👥 Group Buy — Mua chung giá rẻ</div>
</div>
<div class="carousel-wrap">
  <button class="carousel-arrow left" onclick="scrollCarousel('carousel-groupbuy',-1)">‹</button>
  <div class="carousel-track" id="carousel-groupbuy">
    <?php foreach($gb_deals as $d):
      $pct = $d['max_participants'] > 0 ? min(100, round($d['current_participants']/$d['max_participants']*100)) : 0;
      $gb_name = $d['p_name'] ?: ($d['product_name'] ?: 'Deal #'.$d['id']);
      $end_ts = $d['end_date'] ? strtotime($d['end_date'])*1000 : 0;
      $has_people = $d['current_participants'] > 0;
    ?>
    <div class="gb-card" onclick="openGroupBuy(<?= $d['id'] ?>)">
      <div class="gb-title">🔥 <?= htmlspecialchars($gb_name) ?></div>
      <div style="font-size:.8rem;color:var(--gold)"><?= number_format($d['group_price']) ?>đ <span style="text-decoration:line-through;color:var(--muted)"><?= number_format($d['original_price']) ?>đ</span></div>
      <?php if($has_people && $end_ts > 0): ?>
      <div class="gb-countdown" data-end="<?= $end_ts ?>">⏳ --:--:--</div>
      <?php endif; ?>
      <div class="gb-progress"><div class="gb-progress-fill" style="width:<?= $pct ?>%"></div></div>
      <div class="gb-info"><span><?= $d['current_participants'] ?>/<?= $d['max_participants'] ?> người</span><span>Còn <?= max(0, $d['max_participants'] - $d['current_participants']) ?> chỗ</span></div>
    </div>
    <?php endforeach; ?>
  </div>
  <button class="carousel-arrow right" onclick="scrollCarousel('carousel-groupbuy',1)">›</button>
</div>
</div>
<?php endif; ?>

<!-- MAIN CONTENT (product sections) -->
<div id="mainContent">

<!-- BESTSELLERS CAROUSEL -->
<div class="section" id="section-bestsellers">
<div class="section-header">
  <div class="section-title">🔥 Bestsellers</div>
</div>
<div class="carousel-wrap">
  <button class="carousel-arrow left" onclick="scrollCarousel('carousel-bestsellers',-1)">‹</button>
  <div class="carousel-track" id="carousel-bestsellers"></div>
  <button class="carousel-arrow right" onclick="scrollCarousel('carousel-bestsellers',1)">›</button>
</div>
</div>

<!-- TOP RATED CAROUSEL -->
<div class="section" id="section-toprated">
<div class="section-header">
  <div class="section-title">⭐ Đánh giá cao</div>
</div>
<div class="carousel-wrap">
  <button class="carousel-arrow left" onclick="scrollCarousel('carousel-toprated',-1)">‹</button>
  <div class="carousel-track" id="carousel-toprated"></div>
  <button class="carousel-arrow right" onclick="scrollCarousel('carousel-toprated',1)">›</button>
</div>
</div>

<!-- TOP DEALS CAROUSEL -->
<div class="section" id="section-deals">
<div class="section-header">
  <div class="section-title">💰 Top Deals</div>
</div>
<div class="carousel-wrap">
  <button class="carousel-arrow left" onclick="scrollCarousel('carousel-deals',-1)">‹</button>
  <div class="carousel-track" id="carousel-deals"></div>
  <button class="carousel-arrow right" onclick="scrollCarousel('carousel-deals',1)">›</button>
</div>
</div>

<!-- RANDOM FAVORITES — 6/row, 2 rows then Show More + Pagination -->
<div class="section" id="section-all">
<div class="section-header">
  <div class="section-title">💖 Những sản phẩm ngẫu nhiên được khách hàng yêu thích nhất</div>
</div>
<div class="grid" id="grid-all"></div>
<div id="footer-all"></div>
</div>

</div>

<!-- TOPUP GRID -->
<div id="section-topup" style="display:none">
<div class="section">
<div class="section-header">
  <div class="section-title">📱 Top Up Game (<?= $games_count ?> games)</div>
</div>
<div class="topup-grid" id="topupGrid"><p style="color:var(--muted)">Đang tải...</p></div>
</div>
</div>

</div>

<!-- FOOTER -->
<footer class="footer">
<div class="container">
  <p>© <?= date('Y') ?> <?= htmlspecialchars($CMSNT->site('title')) ?> — Marketplace sản phẩm số. Giao ngay tự động.</p>
</div>
</footer>

<!-- BUY MODAL -->
<div class="modal-overlay" id="buyModal" onclick="if(event.target===this)closeModal('buyModal')">
<div class="modal-box">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3 id="buyTitle" style="margin:0">Mua sản phẩm</h3>
    <button onclick="closeModal('buyModal')" style="background:none;border:none;color:var(--muted);font-size:1.4rem;cursor:pointer">&times;</button>
  </div>
  <div id="buyInfo" style="color:var(--muted);font-size:.88rem;margin-bottom:14px"></div>
  <div class="form-group">
    <label>Số lượng</label>
    <input type="number" id="buyAmount" value="1" min="1" max="10" oninput="updateBuyTotal()">
  </div>
  <div style="display:flex;justify-content:space-between;margin-bottom:14px;font-size:1rem">
    <span>Tổng cộng:</span><strong id="buyTotal" style="color:var(--gold)">0đ</strong>
  </div>
  <div class="error-msg" id="buyError"></div>
  <div class="key-result" id="buyKeys" style="display:none"></div>
  <button class="btn btn-primary" id="buyBtn" style="width:100%;padding:12px" onclick="submitBuy()">💳 Mua ngay</button>
  <div id="gbSection" style="display:none;margin-top:14px;border-top:1px solid var(--border);padding-top:14px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
      <span style="font-size:.9rem;font-weight:700;color:var(--accent)">👥 Group Buy</span>
      <span id="gbPriceTag" style="color:var(--gold);font-weight:800"></span>
    </div>
    <div style="font-size:.78rem;color:var(--muted);margin-bottom:10px" id="gbProgressText"></div>
    <button class="btn btn-outline" id="gbJoinBtn" style="width:100%;padding:12px;border-color:var(--accent);color:var(--accent)" onclick="joinGroupBuy()">👥 Tham gia Group Buy — giá rẻ hơn</button>
  </div>
</div>
</div>

<!-- LOGIN MODAL -->
<div class="modal-overlay" id="loginModal" onclick="if(event.target===this)closeModal('loginModal')">
<div class="modal-box">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3 style="margin:0">🔐 Đăng nhập</h3>
    <button onclick="closeModal('loginModal')" style="background:none;border:none;color:var(--muted);font-size:1.4rem;cursor:pointer">&times;</button>
  </div>
  <div class="form-group"><label>Tên đăng nhập</label><input type="text" id="loginUser" autocomplete="username"></div>
  <div class="form-group"><label>Mật khẩu</label><input type="password" id="loginPass" autocomplete="current-password" onkeydown="if(event.key==='Enter')submitLogin()"></div>
  <div class="error-msg" id="loginError"></div>
  <button class="btn btn-primary" style="width:100%;padding:12px" onclick="submitLogin()">Đăng nhập</button>
  <div style="text-align:center;margin-top:12px;font-size:.85rem">
    <a href="javascript:void(0)" onclick="closeModal('loginModal');openModal('registerModal')">Đăng ký</a>
  </div>
</div>
</div>

<!-- REGISTER MODAL -->
<div class="modal-overlay" id="registerModal" onclick="if(event.target===this)closeModal('registerModal')">
<div class="modal-box">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
    <h3 style="margin:0">📝 Đăng ký</h3>
    <button onclick="closeModal('registerModal')" style="background:none;border:none;color:var(--muted);font-size:1.4rem;cursor:pointer">&times;</button>
  </div>
  <div class="form-group"><label>Tên đăng nhập</label><input type="text" id="regUser" autocomplete="username"></div>
  <div class="form-group"><label>Email</label><input type="email" id="regEmail" autocomplete="email"></div>
  <div class="form-group"><label>Mật khẩu</label><input type="password" id="regPass" autocomplete="new-password"></div>
  <div class="form-group"><label>Nhập lại mật khẩu</label><input type="password" id="regPass2" autocomplete="new-password" onkeydown="if(event.key==='Enter')submitRegister()"></div>
  <div class="error-msg" id="regError"></div>
  <div class="success-msg" id="regSuccess"></div>
  <button class="btn btn-primary" style="width:100%;padding:12px" onclick="submitRegister()">Đăng ký</button>
  <div style="text-align:center;margin-top:12px;font-size:.85rem">
    <a href="javascript:void(0)" onclick="closeModal('registerModal');openModal('loginModal')">Đã có tài khoản? Đăng nhập</a>
  </div>
</div>
</div>

<script>
var USER_TOKEN = '<?= $user_token ?>';
var IS_LOGGED_IN = <?= $is_logged_in ? 'true' : 'false' ?>;
var CSRF = '<?= $csrf_token ?>';
var PRODUCTS = <?= json_encode($products_data, JSON_UNESCAPED_UNICODE) ?>;
var BESTSELLERS = <?= json_encode(array_values($bestsellers), JSON_UNESCAPED_UNICODE) ?>;
var TOP_RATED = <?= json_encode(array_values($top_rated), JSON_UNESCAPED_UNICODE) ?>;
var TOP_DEALS = <?= json_encode(array_values($top_deals), JSON_UNESCAPED_UNICODE) ?>;
var TYPE_ICON = {account:'👤',game_key:'🎮',gift_card:'💳',software:'💻',subscription:'📺',topup:'📱'};
var PLATFORM_ICON = {'Steam':'🎮','Epic Games':'🎯','GOG':'📦','EA App':'⚽','Ubisoft':'🗡️','Xbox':'❎','PlayStation':'🎮','Google Play':'▶️','App Store':'🍎','Roblox':'🧱','Netflix':'🎬','Other':'📦'};
var currentCat = 'all';
var buyItem = null;
var ITEMS_PER_ROW = 6;
var VISIBLE_ROWS = 2;

function formatMoney(n){return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')+'đ';}

function starRating(r){
  var full = Math.floor(r);
  var half = (r - full) >= 0.5 ? 1 : 0;
  var s = '';
  for(var i=0;i<full;i++) s+='★';
  if(half) s+='⯨';
  for(var i=full+half;i<5;i++) s+='☆';
  return s;
}

function renderCard(p){
  var icon = TYPE_ICON[p.type]||'📦';
  var pfIcon = PLATFORM_ICON[p.platform]||'';
  var disc = p.discount>0 ? '<div class="badge-disc">-'+p.discount+'%</div>' : '';
  var gbBadge = p.gb_id>0 ? '<div class="badge-gb">👥 GROUP BUY</div>' : '';
  var platformHtml = p.platform ? '<div class="platform-badge">'+pfIcon+' '+p.platform+(p.region?' • '+p.region:'')+'</div>' : '';
  var ratingHtml = p.rating>0 ? '<div class="rating">'+starRating(p.rating)+' '+p.rating.toFixed(1)+' <span class="count">('+p.rating_count+')</span></div>' : '';
  var stockHtml = p.stock>0 ? '<div class="stock stock-in">Còn '+p.stock+'</div>' : '<div class="stock stock-out">Hết hàng</div>';
  var priceHtml = '<span class="price">'+formatMoney(p.price)+'</span>';
  if(p.discount>0) priceHtml += '<span class="old-price">'+formatMoney(p.old_price)+'</span>';
  var click = p.stock>0 ? 'onclick="openBuy('+p.id+')"' : '';
  return '<div class="p-card" '+click+' style="'+(p.stock>0?'':'opacity:.5;cursor:not-allowed')+'">'+disc+gbBadge+
    '<div class="card-img">'+icon+'</div>'+
    platformHtml+
    '<div class="name">'+p.name+'</div>'+
    ratingHtml+
    '<div class="price-row">'+priceHtml+'</div>'+stockHtml+'</div>';
}

// === CAROUSEL: cuộn xoay bằng mũi tên ‹ › ===
function scrollCarousel(trackId, dir){
  var track = document.getElementById(trackId);
  if(!track) return;
  var card = track.querySelector('.p-card, .gb-card');
  var step = card ? (card.offsetWidth + 14) : 220;
  track.scrollBy({left: dir * step * 2, behavior:'smooth'});
}

// Render 3 carousel sections: ALL items vào track (mũi tên cuộn để xem thêm)
function renderCarousels(){
  var map = {bestsellers:'carousel-bestsellers', toprated:'carousel-toprated', deals:'carousel-deals'};
  for(var key in map){
    var track = document.getElementById(map[key]);
    if(!track) continue;
    var items = getSectionItems(key);
    track.innerHTML = items.length ? items.map(renderCard).join('') : '<p style="color:var(--muted);padding:20px">Không có sản phẩm nào.</p>';
  }
}

// === SECTION "ALL": Hiển thị thêm + Phân trang ===
var PAGE_SIZE = ITEMS_PER_ROW * VISIBLE_ROWS; // 12 items per page
var sectionState = { all: {expanded:false, page:1} };

function getSectionItems(key){
  if(key==='bestsellers') return BESTSELLERS;
  if(key==='toprated') return TOP_RATED;
  if(key==='deals') return TOP_DEALS;
  // all: filter by cat + search
  var q = document.getElementById('searchInput').value.trim().toLowerCase();
  return PRODUCTS.filter(function(p){
    if(currentCat!=='all' && p.type!==currentCat) return false;
    if(q && p.name.toLowerCase().indexOf(q)<0) return false;
    return true;
  });
}

function renderSection(key){
  var grid = document.getElementById('grid-all');
  var footer = document.getElementById('footer-all');
  if(!grid) return;
  var items = getSectionItems(key);
  var st = sectionState[key];
  var totalPages = Math.max(1, Math.ceil(items.length / PAGE_SIZE));

  if(!items.length){
    grid.innerHTML = '<p style="color:var(--muted);grid-column:1/-1;text-align:center;padding:20px">Không có sản phẩm nào.</p>';
    if(footer) footer.innerHTML = '';
    return;
  }

  if(!st.expanded){
    var visible = items.slice(0, PAGE_SIZE);
    grid.innerHTML = visible.map(renderCard).join('');
    if(footer){
      if(items.length > PAGE_SIZE){
        footer.innerHTML = '<div class="show-more-wrap"><button class="btn-show-more" onclick="expandSection(\''+key+'\')">Hiển thị thêm ('+(items.length-PAGE_SIZE)+' sản phẩm)</button></div>';
      } else {
        footer.innerHTML = '';
      }
    }
  } else {
    if(st.page > totalPages) st.page = totalPages;
    if(st.page < 1) st.page = 1;
    var start = (st.page-1)*PAGE_SIZE;
    var visible = items.slice(start, start+PAGE_SIZE);
    grid.innerHTML = visible.map(renderCard).join('');
    if(footer){
      footer.innerHTML = buildPagination(key, st.page, totalPages, items.length);
    }
  }
}

function buildPagination(key, page, totalPages, totalItems){
  var html = '<div class="pagination">';
  html += '<button class="pg-btn'+(page<=1?' disabled':'')+'" onclick="goPage(\''+key+'\','+(page-1)+')">‹</button>';
  for(var i=1;i<=totalPages;i++){
    html += '<button class="pg-btn'+(i===page?' active':'')+'" onclick="goPage(\''+key+'\','+i+')">'+i+'</button>';
  }
  html += '<button class="pg-btn'+(page>=totalPages?' disabled':'')+'" onclick="goPage(\''+key+'\','+(page+1)+')">›</button>';
  html += '<span class="pg-info">Trang '+page+'/'+totalPages+' • '+totalItems+' SP</span>';
  html += '<button class="pg-btn" style="margin-left:8px" onclick="collapseSection(\''+key+'\')">Hiển thị ít hơn</button>';
  html += '</div>';
  return html;
}

function expandSection(key){
  sectionState[key].expanded = true;
  sectionState[key].page = 1;
  renderSection(key);
}
function collapseSection(key){
  sectionState[key].expanded = false;
  sectionState[key].page = 1;
  renderSection(key);
  document.getElementById('section-all').scrollIntoView({behavior:'smooth'});
}
function goPage(key, page){
  var items = getSectionItems(key);
  var totalPages = Math.max(1, Math.ceil(items.length / PAGE_SIZE));
  if(page<1) page=1;
  if(page>totalPages) page=totalPages;
  sectionState[key].page = page;
  renderSection(key);
  document.getElementById('section-all').scrollIntoView({behavior:'smooth',block:'start'});
}

function renderSections(){
  renderCarousels();
  renderSection('all');
}

function renderAllGrid(){
  // Reset all-section pagination when filter/search changes
  sectionState.all.page = 1;
  renderSection('all');
}

function switchCat(cat,el){
  currentCat = cat;
  // Reset pagination khi đổi category
  sectionState.all = {expanded:false, page:1};
  var items = document.querySelectorAll('.cat-nav-item');
  for(var i=0;i<items.length;i++) items[i].classList.remove('active');
  if(el) el.classList.add('active');
  var isTopup = (cat==='topup');
  document.getElementById('mainContent').style.display = isTopup?'none':'';
  document.getElementById('section-topup').style.display = isTopup?'':'none';
  if(isTopup){ loadTopupGames(); return; }
  // Filter sections by cat
  var showSections = (cat==='all');
  document.getElementById('section-bestsellers').style.display = showSections?'':'none';
  document.getElementById('section-toprated').style.display = showSections?'':'none';
  document.getElementById('section-deals').style.display = showSections?'':'none';
  renderAllGrid();
}

function filterProducts(){
  renderAllGrid();
}

function loadTopupGames(){
  var grid = document.getElementById('topupGrid');
  fetch('/ajaxs/client/load_games.php').then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'&&d.games){
      grid.innerHTML = d.games.map(function(g){
        return '<div class="topup-card" onclick="window.location.href=\'/?module=client&action=topup-home\'">'+
          '<div class="gi">'+(g.icon||'🎮')+'</div><div class="gn">'+g.name+'</div><div class="gc">'+(g.cat||'')+'</div></div>';
      }).join('');
    }
  }).catch(function(){grid.innerHTML='<p style="color:var(--muted)">Lỗi tải games.</p>';});
}

function openBuy(id){
  if(!IS_LOGGED_IN){showToast('Vui lòng đăng nhập để mua hàng!');openModal('loginModal');return;}
  var item=null;
  for(var i=0;i<PRODUCTS.length;i++){if(PRODUCTS[i].id===id){item=PRODUCTS[i];break;}}
  if(!item)return;
  buyItem=item;
  document.getElementById('buyTitle').textContent=item.name;
  var info='Loại: '+(TYPE_ICON[item.type]||'')+' '+item.type.replace('_',' ');
  if(item.platform)info+=' • '+item.platform;
  if(item.region)info+=' • '+item.region;
  if(item.rating>0)info+='<br>⭐ '+item.rating.toFixed(1)+' ('+item.rating_count+' đánh giá)';
  info+='<br>Giá: <strong style="color:#fff">'+formatMoney(item.price)+'</strong> • Còn '+item.stock;
  document.getElementById('buyInfo').innerHTML=info;
  document.getElementById('buyAmount').value=1;
  document.getElementById('buyAmount').max=Math.min(10,item.stock);
  document.getElementById('buyError').style.display='none';
  document.getElementById('buyKeys').style.display='none';
  document.getElementById('buyBtn').disabled=false;
  document.getElementById('buyBtn').textContent='💳 Mua ngay';
  var gbSec=document.getElementById('gbSection');
  if(item.gb_id>0){
    gbSec.style.display='block';
    document.getElementById('gbPriceTag').textContent=formatMoney(item.gb_price);
    var progTxt='Đã có '+item.gb_cur+'/'+item.gb_max+' người tham gia — đủ người sẽ giao hàng ngay!';
    if(item.gb_cur>0 && item.gb_end>0){
      progTxt+=' <span class="gb-countdown" data-end="'+item.gb_end+'" style="color:var(--red);font-weight:700">⏳ --:--:--</span>';
    }
    document.getElementById('gbProgressText').innerHTML=progTxt;
    updateCountdowns();
    document.getElementById('gbJoinBtn').disabled=false;
    document.getElementById('gbJoinBtn').textContent='👥 Tham gia Group Buy — giá rẻ hơn';
  }else{
    gbSec.style.display='none';
  }
  updateBuyTotal();
  openModal('buyModal');
}

function joinGroupBuy(){
  if(!buyItem||!buyItem.gb_id)return;
  var btn=document.getElementById('gbJoinBtn');
  var err=document.getElementById('buyError');
  err.style.display='none';
  btn.disabled=true;btn.textContent='Đang tham gia...';
  fetch('/ajaxs/client/group-buy.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=join&deal_id='+buyItem.gb_id+'&token='+USER_TOKEN
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){
      btn.textContent='✅ Đã tham gia Group Buy!';
      showToast('🎉 Tham gia Group Buy thành công!');
      setTimeout(function(){
        fetch('/ajaxs/client/load_shop_products.php').then(function(r){return r.json();}).then(function(dd){
          if(dd.status==='success'){
            var all=[];for(var t in dd.products){all=all.concat(dd.products[t]);}
            PRODUCTS=all;renderSections();
          }
        });
      },500);
    }else{
      err.textContent=d.msg||'Tham gia thất bại';err.style.display='block';
      btn.disabled=false;btn.textContent='👥 Tham gia Group Buy — giá rẻ hơn';
    }
  }).catch(function(){err.textContent='Lỗi kết nối';err.style.display='block';btn.disabled=false;btn.textContent='👥 Tham gia Group Buy';});
}

function updateBuyTotal(){
  if(!buyItem)return;
  var amt=parseInt(document.getElementById('buyAmount').value)||1;
  document.getElementById('buyTotal').textContent=formatMoney(buyItem.price*amt);
}

function submitBuy(){
  if(!buyItem)return;
  var amt=parseInt(document.getElementById('buyAmount').value)||1;
  var btn=document.getElementById('buyBtn');
  var err=document.getElementById('buyError');
  var keys=document.getElementById('buyKeys');
  err.style.display='none';keys.style.display='none';
  btn.disabled=true;btn.textContent='Đang xử lý...';
  fetch('/ajaxs/client/product.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=buyProduct&id='+buyItem.id+'&amount='+amt+'&token='+USER_TOKEN
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){
      keys.style.display='block';
      keys.innerHTML='<div style="color:var(--green);font-weight:600;margin-bottom:6px">✅ Mua thành công! Mã của bạn:</div>';
      if(d.accounts&&d.accounts.length){
        d.accounts.forEach(function(a){keys.innerHTML+='<div class="key-item">'+a+'</div>';});
      }else{
        keys.innerHTML+='<div style="color:var(--muted)">Vào Lịch sử đơn hàng để xem mã.</div>';
      }
      btn.textContent='✅ Đã mua';
      setTimeout(function(){
        fetch('/ajaxs/client/load_shop_products.php').then(function(r){return r.json();}).then(function(dd){
          if(dd.status==='success'){
            var all=[];for(var t in dd.products){all=all.concat(dd.products[t]);}
            PRODUCTS=all;renderSections();
          }
        });
      },500);
    }else{
      err.textContent=d.msg||'Mua thất bại';err.style.display='block';
      btn.disabled=false;btn.textContent='💳 Mua ngay';
    }
  }).catch(function(){err.textContent='Lỗi kết nối';err.style.display='block';btn.disabled=false;btn.textContent='💳 Mua ngay';});
}

function openGroupBuy(id){
  var item=null;
  for(var i=0;i<PRODUCTS.length;i++){if(PRODUCTS[i].gb_id===id){item=PRODUCTS[i];break;}}
  if(item){openBuy(item.id);}
  else{window.location.href='/?module=client&action=group-buy-detail&id='+id;}
}

function openModal(id){document.getElementById(id).style.display='flex';}
function closeModal(id){document.getElementById(id).style.display='none';}

function submitLogin(){
  var u=document.getElementById('loginUser').value.trim();
  var p=document.getElementById('loginPass').value;
  var err=document.getElementById('loginError');
  err.style.display='none';
  if(!u||!p){err.textContent='Vui lòng nhập đầy đủ';err.style.display='block';return;}
  fetch('/ajaxs/client/auth.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=Login&username='+encodeURIComponent(u)+'&password='+encodeURIComponent(p)+'&csrf_token='+encodeURIComponent(CSRF)
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){location.reload();}
    else{err.textContent=d.msg||'Đăng nhập thất bại';err.style.display='block';}
  }).catch(function(){err.textContent='Lỗi kết nối';err.style.display='block';});
}

function submitRegister(){
  var u=document.getElementById('regUser').value.trim();
  var e=document.getElementById('regEmail').value.trim();
  var p=document.getElementById('regPass').value;
  var p2=document.getElementById('regPass2').value;
  var err=document.getElementById('regError');
  var ok=document.getElementById('regSuccess');
  err.style.display='none';ok.style.display='none';
  if(!u||!e||!p||!p2){err.textContent='Vui lòng nhập đầy đủ';err.style.display='block';return;}
  if(p.length<6){err.textContent='Mật khẩu tối thiểu 6 ký tự';err.style.display='block';return;}
  if(p!==p2){err.textContent='Mật khẩu không khớp';err.style.display='block';return;}
  fetch('/ajaxs/client/auth.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
    body:'action=Register&username='+encodeURIComponent(u)+'&email='+encodeURIComponent(e)+'&password='+encodeURIComponent(p)+'&repassword='+encodeURIComponent(p2)+'&csrf_token='+encodeURIComponent(CSRF)
  }).then(function(r){return r.json();}).then(function(d){
    if(d.status==='success'){ok.textContent='✅ Đăng ký thành công! Đang đăng nhập...';ok.style.display='block';setTimeout(function(){location.reload();},1200);}
    else{err.textContent=d.msg||'Đăng ký thất bại';err.style.display='block';}
  }).catch(function(){err.textContent='Lỗi kết nối';err.style.display='block';});
}

function logout(){window.location.href='/?module=client&action=logout';}
function openWallet(){window.location.href='/?module=client&action=topup-history';}

function showToast(msg){
  var t=document.createElement('div');
  t.style.cssText='position:fixed;bottom:20px;right:20px;background:var(--accent);color:#fff;padding:12px 20px;border-radius:8px;z-index:9999;font-size:.9rem;box-shadow:0 4px 12px rgba(0,0,0,.3)';
  t.textContent=msg;document.body.appendChild(t);
  setTimeout(function(){t.remove();},3000);
}

// Init
renderSections();

// === ĐỒNG HỒ ĐẾM NGƯỢC GROUP BUY ===
function updateCountdowns(){
  var els = document.querySelectorAll('.gb-countdown');
  var now = Date.now();
  for(var i=0;i<els.length;i++){
    var end = parseInt(els[i].getAttribute('data-end'))||0;
    var diff = end - now;
    if(diff <= 0){
      els[i].textContent = '⏰ Đã hết thời gian!';
      els[i].style.color = 'var(--muted)';
      continue;
    }
    var h = Math.floor(diff/3600000);
    var m = Math.floor((diff%3600000)/60000);
    var s = Math.floor((diff%60000)/1000);
    els[i].textContent = '⏳ ' + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
    if(diff < 3600000) els[i].style.color = 'var(--red)';
    else if(diff < 21600000) els[i].style.color = 'var(--gold)';
  }
}
updateCountdowns();
setInterval(updateCountdowns, 1000);
</script>
</body>
</html>