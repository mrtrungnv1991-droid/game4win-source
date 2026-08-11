<?php
/**
 * sidebar-render.php (Punch list #3 — Master Prompt v3 §4.1)
 * Render admin sidebar từ mảng sidebar-menu-data.php cho cả 2 shell.
 * Shell A = Bootstrap/YNEX (sidebar.php). Shell B = DCOS/Tailwind (dcos-layout.php).
 * Chỉ khác lớp CSS + icon angle; cấu trúc menu giống hệt nhau.
 */
if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

/**
 * Render badge động (VD: số đơn rút tiền affiliate đang chờ).
 */
function sidebar_render_badge($badge, $CMSNT)
{
    if (!is_array($badge)) return '';
    if (($badge['type'] ?? '') === 'affiliate_withdraw_pending') {
        $n = $CMSNT->get_row(" SELECT COUNT(id) FROM `aff_withdraw` WHERE `status` = 'pending' ")['COUNT(id)'];
        if ($n > 0) {
            return '<span class="badge bg-warning-transparent ms-2">' . $n . '</span>';
        }
    }
    return '';
}

/**
 * Render một node link.
 */
function sidebar_render_link($item, $shell, $CMSNT)
{
    if (!empty($item['perm']) && checkPermission($GLOBALS['getUser']['admin'], $item['perm']) != true) {
        return '';
    }
    $href = base_url_admin($item['href']);
    $active = active_sidebar($item['active'] ?? []);
    $extra = isset($item['class']) ? ' ' . $item['class'] : '';
    $badge = isset($item['badge']) ? sidebar_render_badge($item['badge'], $CMSNT) : '';
    $icon = '';
    if (!empty($item['icon'])) {
        $color = isset($item['color']) ? ' style="color: ' . $item['color'] . ';"' : '';
        $icon = '<i class="' . $item['icon'] . ' side-menu__icon"' . $color . '></i>';
    }
    $label = isset($item['label_html']) ? $item['label_html'] : htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');
    return '<li class="slide">'
        . '<a href="' . $href . '" class="side-menu__item ' . $active . $extra . '">'
        . $icon
        . '<span class="side-menu__label">' . $label . $badge . '</span>'
        . '</a>'
        . '</li>';
}

/**
 * Render một node submenu (đệ quy cho submenu lồng nhau).
 */
function sidebar_render_submenu($item, $shell, $depth, $CMSNT)
{
    if (!empty($item['perm']) && checkPermission($GLOBALS['getUser']['admin'], $item['perm']) != true) {
        return '';
    }
    $show = show_sidebar($item['show'] ?? []);
    $angle = ($shell === 'a') ? 'fe fe-chevron-right' : 'bx bx-chevron-right';
    $menu_class = ($shell === 'a') ? 'slide-menu child' . $depth : 'slide-menu';

    $icon = '';
    if (!empty($item['icon'])) {
        $color = isset($item['color']) ? ' style="color: ' . $item['color'] . ';"' : '';
        $icon = '<i class="' . $item['icon'] . ' side-menu__icon"' . $color . '></i>';
    }
    $label = htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8');

    $html = '<li class="slide has-sub ' . $show . '">'
        . '<a href="javascript:void(0);" class="side-menu__item ' . $show . '">'
        . $icon
        . '<span class="side-menu__label">' . $label . '</span>'
        . '<i class="' . $angle . ' side-menu__angle"></i>'
        . '</a>'
        . '<ul class="' . $menu_class . '">';

    foreach ($item['children'] ?? [] as $child) {
        if ($child['type'] === 'link') {
            $html .= sidebar_render_link($child, $shell, $CMSNT);
        } elseif ($child['type'] === 'submenu') {
            $html .= sidebar_render_submenu($child, $shell, $depth + 1, $CMSNT);
        }
    }
    $html .= '</ul></li>';
    return $html;
}

/**
 * Render toàn bộ menu từ data array.
 * $shell: 'a' (Bootstrap/YNEX) hoặc 'b' (DCOS/Tailwind).
 */
function sidebar_render_menu($shell, $CMSNT)
{
    $menu = require __DIR__ . '/sidebar-menu-data.php';
    $html = '';
    foreach ($menu as $node) {
        switch ($node['type']) {
            case 'category':
                $html .= '<li class="slide__category"><span class="category-name">'
                    . htmlspecialchars($node['label'], ENT_QUOTES, 'UTF-8') . '</span></li>';
                break;
            case 'link':
                $html .= sidebar_render_link($node, $shell, $CMSNT);
                break;
            case 'submenu':
                $html .= sidebar_render_submenu($node, $shell, 1, $CMSNT);
                break;
        }
    }
    return $html;
}
