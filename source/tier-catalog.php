<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GameTopup — Catalog Tiers</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#0a0a14;color:#e0e0e0;min-height:100vh}
.header{position:sticky;top:0;z-index:100;background:linear-gradient(135deg,#1a1a2e,#16213e);border-bottom:1px solid rgba(255,255,255,.08);padding:16px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.header h1{font-size:1.3rem;background:linear-gradient(135deg,#00d4ff,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.header .stats{font-size:.8rem;color:#9ca3af}
.header input{padding:8px 14px;border-radius:20px;border:1px solid rgba(255,255,255,.1);background:#1a1a2e;color:#e0e0e0;font-size:.85rem;width:240px;outline:none}
.header input:focus{border-color:#7c3aed}
.game-card{margin:8px 12px;border-radius:12px;overflow:hidden;background:#111122;border:1px solid rgba(255,255,255,.05)}
.game-header{cursor:pointer;padding:14px 16px;display:flex;align-items:center;gap:10px;transition:background .2s}
.game-header:hover{background:#161630}
.game-header .icon{font-size:1.5rem}
.game-header .name{font-weight:700;font-size:.95rem;flex:1}
.game-header .meta{font-size:.75rem;color:#9ca3af}
.game-header .arrow{color:#6b7280;transition:transform .3s;font-size:1.2rem}
.game-card.open .arrow{transform:rotate(180deg)}
.game-body{display:none;padding:0 16px 16px}
.game-card.open .game-body{display:block}
.tier-table{width:100%;border-collapse:collapse;font-size:.82rem}
.tier-table th{text-align:left;padding:8px 10px;color:#9ca3af;font-weight:600;font-size:.75rem;text-transform:uppercase;border-bottom:1px solid rgba(255,255,255,.06)}
.tier-table td{padding:8px 10px;border-bottom:1px solid rgba(255,255,255,.03)}
.tier-table tr:hover td{background:rgba(124,58,237,.05)}
.badge{display:inline-block;padding:2px 8px;border-radius:10px;font-size:.7rem;font-weight:700;text-transform:uppercase}
.badge-gem{background:rgba(0,212,255,.15);color:#00d4ff}
.badge-pack{background:rgba(245,158,11,.15);color:#f59e0b}
.badge-allpack{background:rgba(16,185,129,.15);color:#10b981}
.price{font-weight:700;color:#f59e0b}
.amount{font-weight:600;color:#e0e0e0}
.cost{color:#6b7280;font-size:.75rem}
.no-tiers{padding:12px 0;color:#6b7280;font-size:.8rem}
.empty{text-align:center;padding:40px;color:#6b7280}
.footer{padding:20px;text-align:center;color:#6b7280;font-size:.75rem}
</style>
</head>
<body>
<div class="header">
  <div>
    <h1>📦 GameTopup — Tiers Catalog</h1>
    <div class="stats" id="stats"></div>
  </div>
  <input type="text" placeholder="🔍 Tìm game..." oninput="filter(this.value)">
</div>
<div id="games"></div>
<div class="footer">Generated <span id="time"></span></div>

<script>
const DATA = __DATA_PLACEHOLDER__;

function render() {
  const container = document.getElementById('games');
  const totalTiers = DATA.reduce((s,g)=>s+g.tiers.length,0);
  document.getElementById('stats').textContent = DATA.length + ' games · ' + totalTiers + ' tiers';
  document.getElementById('time').textContent = new Date().toLocaleString('vi-VN');

  container.innerHTML = DATA.map((g,i) => {
    const gemCount = g.tiers.filter(t=>t.type==='gem').length;
    const packCount = g.tiers.filter(t=>t.type==='pack').length;
    const allpackCount = g.tiers.filter(t=>t.type==='allpack').length;
    const parts = [];
    if(gemCount) parts.push(gemCount+' gem');
    if(packCount) parts.push(packCount+' pack');
    if(allpackCount) parts.push(allpackCount+' allpack');
    const tiersHtml = g.tiers.length === 0
      ? '<div class="no-tiers">⛔ Chưa có tiers</div>'
      : '<table class="tier-table"><thead><tr><th>Loại</th><th>Tên gói</th><th>Amount</th><th>Giá</th><th>Cost</th></tr></thead><tbody>'
        + g.tiers.map(t => {
          const typeBadge = '<span class="badge badge-'+t.type+'">'+t.type+'</span>';
          const priceStr = t.price ? '<span class="price">'+Number(t.price).toLocaleString('vi-VN')+'đ</span>' : '<span class="cost">-</span>';
          const costStr = t.cost ? '<span class="cost">'+Number(t.cost).toLocaleString('vi-VN')+'đ</span>' : '<span class="cost">-</span>';
          const amountStr = t.amount ? '<span class="amount">'+Number(t.amount).toLocaleString()+'</span>' : '-';
          return '<tr><td>'+typeBadge+'</td><td>'+esc(t.label)+'</td><td>'+amountStr+'</td><td>'+priceStr+'</td><td>'+costStr+'</td></tr>';
        }).join('')
        + '</tbody></table>';
    return '<div class="game-card'+(i===0?' open':'')+'" data-name="'+esc(g.name.toLowerCase())+'">'
      +'<div class="game-header" onclick="this.parentElement.classList.toggle(\'open\')">'
      +'<span class="icon">'+esc(g.icon||'🎮')+'</span>'
      +'<span class="name">'+esc(g.name)+'</span>'
      +'<span class="meta">'+g.tiers.length+' tiers · '+parts.join(' + ')+'</span>'
      +'<span class="arrow">▼</span>'
      +'</div><div class="game-body">'+tiersHtml+'</div></div>';
  }).join('');
}

function esc(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function filter(q) {
  const cards = document.querySelectorAll('.game-card');
  const s = q.toLowerCase();
  cards.forEach(c => { c.style.display = s === '' || c.dataset.name.includes(s) ? '' : 'none'; });
}

render();
</script>
</body>
</html>
