<?php
/**
 * DCOS Layout — Tailwind-based admin shell for Digital Commerce OS modules.
 * Sidebar giữ NGUYÊN từ sidebar.php gốc (YNEX theme) để không thiếu module nào.
 * 
 * Usage: require_once(__DIR__ . '/dcos-layout.php'); at top of module content
 *        require_once(__DIR__ . '/dcos-layout-close.php'); at end
 */
if (!defined('IN_SITE')) die('The Request Not Found');
require_once(__DIR__ . '/sidebar-render.php');

// Determine active module for sidebar highlighting
$dcos_active = $dcos_active ?? basename($_SERVER['SCRIPT_NAME'], '.php');

$admin_url = base_url_admin();
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $body['title'] ?? 'Digital Commerce OS' ?></title>
    <!-- Tailwind self-hosted (Punch #4): build từ tailwind.dcos.config.js, không dùng CDN JIT runtime -->
    <link rel="stylesheet" href="<?= base_url('public/theme/assets/css/dcos-tailwind.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Boxicons local bundle (Punch #5): thay unpkg CDN -->
    <link href="<?= base_url('public/theme/assets/icon-fonts/boxicons/css/boxicons.min.css') ?>" rel="stylesheet">
    <style>
        :root {
            --page-bg: rgb(240, 241, 247);
            --primary: #845adf;
            --secondary: rgb(35, 183, 229);
            --success: rgb(38, 191, 148);
            --warning: rgb(245, 184, 73);
            --danger: rgb(230, 83, 60);
            --text-main: #333335;
            --border-color: #f3f3f3;
            --sidebar-width: 260px;
        }
        body { font-family: 'Roboto', sans-serif; background-color: var(--page-bg); color: var(--text-main); margin: 0; }
        
        /* Sidebar - giữ nguyên style YNEX.
           FIX: height:100vh (không dùng min-height) để sidebar KHÔNG phình theo nội dung;
           overflow-y:auto cuộn menu bên trong, các mục cuối luôn với tới được. */
        .sidebar { width: var(--sidebar-width); background: #111c43; height: 100vh; position: fixed; left: 0; top: 0; z-index: 50; overflow-y: auto; overflow-x: hidden; }
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        .sidebar { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar { height: 64px; background: #fff; border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 40; }
        
        /* Sidebar menu styles - copy từ YNEX */
        .main-menu { list-style: none; padding: 0; margin: 0; }
        .slide__category { padding: 1.5rem 1.5rem 0.5rem; font-size: 11px; font-weight: 700; color: #536485; text-transform: uppercase; letter-spacing: 0.05rem; }
        .side-menu__item { display: flex; align-items: center; padding: 0.75rem 1.5rem; color: #8c9097; text-decoration: none; transition: all 0.2s; font-size: 14px; }
        .side-menu__item:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .side-menu__item.active { background: rgba(255,255,255,0.08); color: #fff; border-left: 4px solid var(--primary); }
        .side-menu__icon { font-size: 1.125rem; margin-right: 0.75rem; }
        .side-menu__angle { margin-left: auto; font-size: 0.75rem; transition: transform 0.2s; }
        .slide-menu { list-style: none; padding: 0; margin: 0; display: none; background: rgba(0,0,0,0.1); }
        .slide-menu .side-menu__item { padding-left: 3rem; font-size: 13px; }
        .slide-menu .slide-menu .side-menu__item { padding-left: 4rem; font-size: 12px; }
        .has-sub.open > .slide-menu { display: block; }
        .has-sub.open > .side-menu__item > .side-menu__angle { transform: rotate(90deg); }
        
        /* Content area styles */
        .custom-card { background: #fff; border-radius: 8px; border: 1px solid var(--border-color); box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.05); }
        .btn-primary { background-color: var(--primary); color: white; border-radius: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary:hover { opacity: 0.9; transform: scale(1.02); }
        .btn-outline { border: 1px solid #e2e8f0; color: #64748b; border-radius: 8px; font-weight: 600; transition: all 0.2s; background: white; cursor: pointer; }
        .btn-outline:hover { background: #f8fafc; border-color: #cbd5e1; }
        .stat-card { padding: 1.5rem; border-left: 4px solid var(--primary); }
        .progress-bar { background-color: #f0f1f7; border-radius: 10px; height: 8px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 10px; background: var(--primary); transition: width 0.5s ease; }
        .badge { padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; display: inline-block; }
        table th { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        table td { font-size: 13px; }
        .modal-overlay { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="min-h-screen flex">

<!-- Sidebar - GIỮ NGUYÊN từ sidebar.php -->
<aside class="sidebar">
    <div class="h-16 flex items-center px-6 border-b border-slate-700/50">
        <a href="<?= $admin_url ?>" class="flex items-center">
            <div class="w-8 h-8 rounded bg-[var(--primary)] flex items-center justify-center text-white font-bold">C</div>
            <span class="ml-3 text-white font-bold tracking-tight text-lg">SHOPCLONE7</span>
        </a>
    </div>
    <nav class="py-4">
        <ul class="main-menu">
            <?= sidebar_render_menu('b', $CMSNT) ?>
        </ul>
    </nav>
</aside>

<!-- Main Content -->
<main class="main-content flex-1 flex flex-col">

<!-- Topbar -->
<header class="topbar flex items-center justify-between px-8">
    <div class="flex items-center gap-4">
        <button class="text-slate-500 text-xl lg:hidden" onclick="document.querySelector('.sidebar').classList.toggle('open')">
            <i class="bx bx-menu"></i>
        </button>
        <div class="relative hidden sm:block">
            <input type="text" placeholder="Tìm kiếm..." 
                   class="bg-slate-100 border-none rounded-full px-4 py-1.5 text-sm w-64 focus:ring-2 focus:ring-[var(--primary)]/20 outline-none">
            <i class="bx bx-search absolute right-3 top-2 text-slate-400"></i>
        </div>
    </div>
    <div class="flex items-center gap-6">
        <a href="<?= base_url() ?>" class="text-slate-500 text-xl hover:text-slate-700" title="Về shop">
            <i class="bx bx-store-alt"></i>
        </a>
        <button class="text-slate-500 text-xl" title="Fullscreen" onclick="document.documentElement.requestFullscreen()">
            <i class="bx bx-fullscreen"></i>
        </button>
        <div class="h-8 w-px bg-slate-200"></div>
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xs">
                <?= strtoupper(substr($getUser['username'] ?? 'A', 0, 1)) ?>
            </div>
            <span class="text-sm font-medium hidden sm:inline"><?= htmlspecialchars($getUser['username'] ?? 'Administrator') ?></span>
        </div>
    </div>
</header>

<!-- Content Area -->
<div class="p-8"><?php
// Module content starts here