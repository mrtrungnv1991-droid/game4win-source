<?php if (!defined('IN_SITE')) { die('The Request Not Found'); }
$body = ['title' => 'Quản lý Games — Admin'];
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
require_once(__DIR__ . '/nav.php');
?>

<div class="main-content app-content">
  <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb"><h1>🎮 Quản lý Games (Topup)</h1>
    <a href="<?= BASE_URL() ?>" class="btn btn-sm btn-default" style="margin-left:12px">← Về shop</a></div>
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <input type="text" id="gsearch" placeholder="Tìm game..." style="padding:8px 14px;border-radius:6px;border:1px solid #ddd;width:300px" oninput="gRender()">
        <select id="gcat" onchange="gRender()" style="padding:8px;border-radius:6px;border:1px solid #ddd;margin-left:10px">
          <option value="">Tất cả thể loại</option>
        </select>
        <span style="margin-left:10px;color:#666">Hiển thị <b id="gcount">121</b> games</span>
      </div>
      <div class="card-body">
        <table class="table table-striped" id="gtable">
          <thead><tr><th>ID</th><th>Icon</th><th>Tên</th><th>Thể loại</th><th>Currency</th><th>Gem</th><th>Pack</th><th>Combo</th><th>Giá thấp nhất</th><th></th></tr></thead>
          <tbody id="gtbody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="<?= BASE_URL('public/client/js/topup-games.js') ?>"></script>
<script>
var G=TOPUP_GAMES;
function gRender(){
  var q=document.getElementById("gsearch").value.trim().toLowerCase();
  var cat=document.getElementById("gcat").value;
  var gs=G;
  if(cat)gs=gs.filter(function(g){return g.cat===cat});
  if(q)gs=gs.filter(function(g){return g.name.toLowerCase().indexOf(q)>=0});
  document.getElementById("gtbody").innerHTML=gs.map(function(g){
    var min=Math.min.apply(null,(g.gem||[]).concat(g.pack||[],g.allpack||[]).map(function(t){return t.vnd}));
    return "<tr><td>"+g.id+"</td><td style=\"font-size:1.5rem\">"+(g.icon||"🎮")+"</td><td><b>"+g.name+"</b></td><td>"+(g.cat||"")+"</td><td>"+(g.currencyName||"")+"</td><td>"+(g.gem||[]).length+"</td><td>"+(g.pack||[]).length+"</td><td>"+(g.allpack||[]).length+"</td><td style=\"font-weight:600;color:#f59e0b\">"+(min||0).toLocaleString()+"đ</td><td><a href=\"?module=admin&action=game-edit&id="+g.id+"\" style=\"color:#00d4ff\">✏️</a></td></tr>";
  }).join("");
  document.getElementById("gcount").textContent=gs.length;
}
(function(){
  var cats={};G.forEach(function(g){var c=g.cat||"Other";cats[c]=(cats[c]||0)+1});
  var sel=document.getElementById("gcat");
  Object.entries(cats).sort(function(a,b){return b[1]-a[1]}).forEach(function(p){
    sel.innerHTML+="<option value=\""+p[0]+"\">"+p[0]+" ("+p[1]+")</option>";
  });
  gRender();
})();
</script>

<?php require_once(__DIR__ . '/footer.php'); ?>
