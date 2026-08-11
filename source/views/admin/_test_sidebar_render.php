<?php
// CLI harness verify sidebar-render + sidebar-menu-data (Punch #3)
define('IN_SITE', true);

// Mocks
$GLOBALS['getUser'] = ['admin' => 99999];
function checkPermission($admin_id, $role) { return true; }
function base_url_admin($url = '') { return 'http://localhost:8080/?module=admin&action=' . $url; }
function active_sidebar($action) {
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) return 'active';
    }
    return '';
}
function show_sidebar($action) {
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) return 'active open';
    }
    return '';
}
class FakeCMSNT {
    public function get_row($q) { return ['COUNT(id)' => 3]; }
}
$CMSNT = new FakeCMSNT();

require __DIR__ . '/sidebar-render.php';

$_GET['action'] = 'api-keys'; // simulate active page

foreach (['a', 'b'] as $shell) {
    $html = sidebar_render_menu($shell, $CMSNT);
    $links = substr_count($html, '<li class="slide">');
    $subs = substr_count($html, 'has-sub');
    $cats = substr_count($html, 'slide__category');
    $active = substr_count($html, 'side-menu__item active');
    echo "Shell $shell: links=$links submenus=$subs categories=$cats active_items=$active len=" . strlen($html) . "\n";
    // spot checks
    foreach (['Dashboard', 'Smart Routing', 'Group Buy', 'API Keys', 'Competitor Research', 'Trend Detection', 'Dynamic Pricing', 'Nạp tiền', 'Manual Payment', 'Bakong Wallet Cambodia', 'Cài đặt'] as $needle) {
        if (strpos($html, $needle) === false) echo "  MISSING: $needle\n";
    }
    if (strpos($html, 'badge bg-warning-transparent') === false) echo "  MISSING: affiliate badge\n";
}
echo "DONE\n";
