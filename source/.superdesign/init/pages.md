# Admin Panel — Page Dependency Trees

Server-rendered PHP. Every page includes the same shell. Values shown are trusted render branches read from source.

## / (Dashboard) — the primary target
Entry: views/admin/home.php (1362 lines)

```
home.php
├── models/is_admin.php            (auth gate)
├── views/admin/header.php         (head, assets, body open, showMessage)
├── views/admin/sidebar.php        (switcher, loader, app-header, app-sidebar nav)
├── models/is_license.php          (license gate)
├── [content] .main-content.app-content > .container-fluid
│   ├── page-header-breadcrumb (empty on home)
│   ├── alert-secondary  (project bar: version, quick-fix btn, update log btn)
│   │   └── [conditional alerts] installer.php danger / SMTP warning / email queue / telegram queue / debug_auto_bank / PHP version
│   ├── KPI row (col-xxl-3 x4): 4 gradient stat widgets
│   │   └── each: label + 40px icon circle + 2x2 grid (Thành viên/Đơn hàng/Doanh thu/Lợi nhuận)
│   ├── col-xl-6: chart card "THỐNG KÊ ĐƠN HÀNG" (canvas#chartjs-line, Chart.js bar, 2 datasets)
│   ├── col-xl-6: chart card "THỐNG KÊ NẠP TIỀN" (canvas#chartjs-naptien, Chart.js bar, 1 dataset)
│   ├── col-xl-6: card "ĐƠN HÀNG GẦN ĐÂY" + ul.orders-timeline (500px scroll, AJAX 5s)
│   ├── col-xl-6: card "NẠP TIỀN GẦN ĐÂY" + ul.deposits-timeline (AJAX 5s)
│   └── floating buttons: Top Services (green) + Leaderboard (blue)
├── modals: #leaderboardModal (table top-50 spenders), #topServicesModal (table top-50 products)
└── views/admin/footer.php         (gtranslate, scrollToTop, update check)
```

AJAX endpoints (ajaxs/admin/view.php):
- show_thong_ke_dashboard (KPI numbers, refresh 5s)
- view_chart_thong_ke_don_hang (order chart, time_range param)
- view_chart_thong_ke_nap_tien (deposit chart, time_range param)
- view_don_hang_gan_day (orders timeline HTML)
- view_nap_tien_gan_day (deposits timeline HTML)

## Representative list page — views/admin/product-orders.php (3114 lines, biggest)
Same shell. Content pattern:
```
page-header-breadcrumb (title + breadcrumb)
top-filter bar (filters: search, status, date range; show/short selects)
card custom-card > table-responsive > table (striped hover sm) + thead sticky
pagination
```
This is the canonical dense-列表 layout for all list pages (products, users, logs, transactions, recharge-*).

## Detail/edit page — views/admin/user-edit.php (1890 lines)
```
page-header-breadcrumb
card custom-card > card-body > form (Bootstrap grid, col-md-6 fields, form-control/form-select)
action buttons (btn-primary save, btn-danger delete)
modals for confirmations
```
Canonical edit-page pattern.

## Settings — views/admin/settings.php + settings/*.php
Tabs (nav-tabs) + per-tab card sections. settings/addons.php=1728 lines, settings/connection.php=1227, settings/security.php=731.

## Priority note
For design work, home.php is the target (dashboard). Its visual content = alerts + 4 KPI gradient cards + 2 chart cards + 2 timeline cards + floating buttons + leaderboard/top-services modals.