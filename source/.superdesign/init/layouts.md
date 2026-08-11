# Admin Panel — Layouts

Global shell: `<html data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">`
Font: Roboto (body override). Theme: YNEX Sash-style, Bootstrap 5.

## Page skeleton (header.php → sidebar.php → content → footer.php)
```
<body>
  <div class="offcanvas offcanvas-end" id="switcher-canvas">   <!-- theme switcher: light/dark, LTR/RTL, vertical/horizontal, colors -->
  <div id="loader"><img callback loader.svg></div>
  <div class="page">
    <header class="app-header">...</header>                     <!-- top bar -->
    <aside class="app-sidebar sticky" id="sidebar">...</aside>  <!-- left dark sidebar -->
    <div class="main-content app-content">
      <div class="container-fluid">...page content...</div>
    </div>
    <footer class="footer mt-auto py-3 bg-white text-center">gtranslate</footer>
  </div>
  <div class="scrollToTop">
  <div id="responsive-overlay">
</body>
```

## Header (app-header) — views/admin/sidebar.php:492-588
```
<header class="app-header">
  <div class="main-header-container container-fluid">
    <div class="header-content-left">
      <div class="header-element"><div class="horizontal-logo"><a class="header-logo"></a></div></div>
      <div class="header-element">
        <a class="sidemenu-toggle header-link animated-arrow hor-toggle" data-bs-toggle="sidebar"><span></span></a>
      </div>
    </div>
    <div class="header-content-right">
      <div class="header-element header-search"><a href={base_url} class="header-link"><i class="bx bx-user-circle"></i></a></div>
      <div class="header-element header-theme-mode"><a class="header-link layout-setting">moon/sun icons</a></div>
      <div class="header-element header-fullscreen"><a onclick="openFullscreen()"><i class="bx bx-fullscreen"></i></a></div>
      <div class="header-element"><a class="header-link switcher-icon" data-bs-toggle="offcanvas" data-bs-target="#switcher-canvas"><i class="bx bx-cog"></i></a></div>
    </div>
  </div>
</header>
```
Light header (#fff), icons #536485. Left: logo + hamburger. Right: user-circle (links to storefront), theme toggle, fullscreen, settings cog.

## Sidebar (app-sidebar sticky, dark) — views/admin/sidebar.php:590-1135
```
<aside class="app-sidebar sticky" id="sidebar">
  <div class="main-sidebar-header"><a class="header-logo"></a></div>
  <div class="main-sidebar" id="sidebar-scroll">
    <nav class="main-menu-container nav nav-pills flex-column sub-open">
      <ul class="main-menu">
        <li class="slide__category"><span class="category-name">Main</span></li>
        <li class="slide"><a class="side-menu__item"><i class="bx bxs-dashboard side-menu__icon" style="color:#3b82f6;"></i><span class="side-menu__label">Dashboard</span></a></li>
        <li class="slide has-sub">... Lịch sử (submenu: Nhật ký hoạt động, Biến động số dư, Lịch sử ngân hàng, Telegram Logs, Email Queue, Telegram Queue)
        <li class="slide">... Tự động hóa
        <li class="slide__category">Bảo mật</li>
        <li class="slide">... Block IP
        <li class="slide__category">Digital Commerce OS</li>   <!-- custom modules -->
        <li class="slide">... 🧠 Smart Routing, 👥 Group Buy, 🔑 API Keys, 🕵️ Competitor Research, 📈 Trend Detection, 💲 Dynamic Pricing
        <li class="slide__category">Dịch vụ</li>
        <li class="slide has-sub">... Sản phẩm (2-level submenu: Chuyên mục, Tất cả sản phẩm, 🗄️ Kho hàng theo loại → child2: Kho Account/Kho Game Key/Kho Gift Card/Top Up 121 games/Providers, 🔗 Kết nối API, 🛒 Đơn hàng, ✅ Đã bán)
        <li class="slide__category">Quản lý</li>
        <li class="slide has-sub">... CTV Panel (Thống kê, Đơn rút tiền, Sản phẩm chờ duyệt, Cấu hình)
        <li class="slide">... Thành viên, Admin Role
        <li class="slide has-sub">... Nạp tiền (20+ submenu: Ngân hàng, Nạp thẻ cào, Crypto USDT, Ví MOMO, THESIEURE, Paypal, Perfect Money, Toyyibpay, Squadco, Flutterwave, XiPay, LemPay, TriPay, ZiniPay, Korapay, PocketFi, PaymentPoint, DSocioPay, Tmweasyapi, OpenPix, Bakong, Manual Payment)
        <li class="slide has-sub">... Affiliate Program (Danh sách liên kết, Nhật ký hoa hồng, Rút tiền + badge count, Cấu hình)
        <li class="slide">... Email Campaigns, Mã giảm giá, Khuyến mãi nạp tiền
        <li class="slide has-sub">... Bài viết (Viết bài mới, Tất cả bài viết, Chuyên mục)
        <li class="slide__category">Cài đặt hệ thống</li>
        <li class="slide">... Ngôn ngữ, Tiền tệ, Giao diện, Cài đặt (mb-5)
      </ul>
    </nav>
  </div>
</aside>
```
- Dark menu (data-menu-styles="dark"), white text, icons with per-item inline colors (blue/purple/teal/red/green/amber/cyan/orange/pink/yellow)
- Categories: Main / Bảo mật / Digital Commerce OS / Dịch vụ / Quản lý / Cài đặt hệ thống
- Emoji mixed into labels (🧠👥🔑🕵️📈💲📂📦🗄️👤🎮💳📱🔌🔗🛒✅)
- has-sub → slide-menu child1 → child2 (2-level nesting for Kho hàng)
- Active: side-menu__item active; submenu open: show_sidebar() → has-sub class

## Footer
```
<footer class="footer mt-auto py-3 bg-white text-center"><div class="container"><div class="gtranslate_wrapper"></div></div></footer>
```
Plus scrollToTop button, responsive-overlay, update-check AJAX to update.php.