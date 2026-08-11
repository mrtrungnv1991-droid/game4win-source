# Admin Panel — Theme Tokens

Source: YNEX (Sash-style) admin theme, `public/theme/assets/css/styles.min.css`.

## Part 1 — Compact token summary

### Colors (CSS variables, light mode)
| Token | Value | Usage |
|---|---|---|
| --body-bg-rgb | 240,241,247 | page background (light grey-blue) |
| --primary-rgb | 132,90,223 | primary purple (#845adf) |
| --secondary-rgb | 35,183,229 | secondary cyan (#23b7e5) |
| --warning-rgb | 245,184,73 | amber |
| --info-rgb | 73,182,245 | light blue (#49b6f5) |
| --success-rgb | 38,191,148 | green (#26bf94) |
| --danger-rgb | 230,83,60 | red (#e6533c) |
| --default-text-color | #333335 | body text |
| --default-border | #f3f3f3 | card/table borders |
| --default-background | #f7f8f9 | alternate bg |
| --menu-bg | #fff | sidebar bg (dark via data-menu-styles) |
| --menu-prime-color | #536485 | sidebar text (light variant) |
| --header-bg | #fff | top bar bg |
| --header-prime-color | #536485 | top bar icons |

### Dashboard KPI gradients (inline styles)
- All-time: `linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)` (blue)
- Month: `linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%)` (purple)
- Week: `linear-gradient(135deg, #10b981 0%, #059669 100%)` (green)
- Today: `linear-gradient(135deg, #f59e0b 0%, #d97706 100%)` (orange)

### Sidebar nav icon colors (per-item inline)
Dashboard #3b82f6 · Lịch sử #8b5cf6 · Tự động hóa #14b8a6 · Block IP #ef4444 · Smart Routing #2563EB · Group Buy #8B5CF6 · API Keys #F59E0B · Competitor Research #10B981 · Trend Detection #EF4444 · Dynamic Pricing #06B6D4 · Sản phẩm #f97316 · CTV #ec4899 · Thành viên #06b6d4 · Admin Role #eab308 · Nạp tiền #22c55e · Affiliate #a855f7 · Email Campaigns #3b82f6 · Mã giảm giá #f97316 · Khuyến mãi #ec4899 · Bài viết #14b8a6 · Ngôn ngữ #6366f1 · Tiền tệ #10b981 · Giao diện #a855f7 · Cài đặt #64748b

### Chart colors
- Order chart: profit `rgb(73,182,245)`, revenue `rgb(132, 90, 223)` (bar, 2 datasets)
- Deposit chart: `rgb(29, 78, 216)` (bar, 1 dataset)
- Chart defaults: borderColor `rgba(142,156,173,0.1)`, color `#8c9097`

### Typography
- Body: Roboto (header.php inline override), fallback sans-serif
- Font weights: 400 default, 500 labels, 700 bold (fw-bold)
- Sizes: fs-11 (metric labels), fs-13, fs-18 (floating icons), h5/h6 card titles
- card-title: bold, uppercase heading style

### Spacing / structure
- Container: `.container-fluid` inside `.main-content.app-content`
- Cards in Bootstrap grid: `col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12` (KPI row), `col-xl-6` (charts/timelines)
- Card body padding: bootstrap default; KPI sub-metrics use `p-2 rounded` cells with `row g-2`
- Timeline: fixed height 500px, scroll-y auto
- Page header breadcrumb: `page-header-breadcrumb` (mostly empty on home)
- Floating buttons: fixed bottom 80px, right 20px, z-index 1000

### Radius / shadow
- Cards: default bootstrap radius (var(--default-border) #f3f3f3)
- KPI icon circle: 40px, border-radius 50%, bg rgba(255,255,255,0.25)
- KPI sub-cells: `rounded`, bg rgba(255,255,255,0.15)
- Alerts: shadow-sm, custom-alert-icon

### Breakpoints
- KPI cards: xxl=3up, xl=3up, lg=2up, md=2up, sm=1up
- Charts/timelines: col-xl-6 (2-up on xl, stacked below)

## Part 2 — Raw source dumps

### header.php key assets
```html
<link href="{base}public/theme/assets/libs/bootstrap/css/bootstrap.min.css">
<link href="{base}public/theme/assets/css/styles.min.css">
<link href="{base}public/theme/assets/css/icons.css">
<link href="{base}public/theme/assets/css/styles.css">
<link href="{base}public/fontawesome/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100..900&family=Lora:ital,wght@0,400..700&display=swap">
<script src="{base}public/theme/assets/js/main.js">
<script src="{base}public/js/jquery-3.6.0.js">
<script src="https://cdn.jsdelivr.net/npm/chart.js">
```

### Root CSS variables (styles.min.css, light)
```css
:root {
  --body-bg-rgb:240,241,247; --primary-rgb:132,90,223; --secondary-rgb:35,183,229;
  --warning-rgb:245,184,73; --info-rgb:73,182,245; --success-rgb:38,191,148;
  --danger-rgb:230,83,60; --light-rgb:243,246,248; --dark-rgb:35,35,35;
  --orange-rgb:255,165,5; --pink-rgb:231,145,188; --teal-rgb:18,194,194; --purple-rgb:137,32,173;
  --default-body-bg-color:rgb(var(--body-bg-rgb));
  --primary-color:rgb(var(--primary-rgb));
  --default-font-family:"Inter",sans-serif; --default-font-weight:400;
  --default-text-color:#333335; --default-border:#f3f3f3; --default-background:#f7f8f9;
  --menu-bg:#fff; --menu-prime-color:#536485; --menu-border-color:#f3f3f3;
  --header-bg:#fff; --header-prime-color:#536485; --header-border-color:#f3f3f3;
  --custom-white:#fff; --custom-black:#000; --bootstrap-card-border:#f3f3f3;
}
```

### HTML shell attributes (header.php)
```html
<html lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="light" data-menu-styles="dark" data-toggled="close">
```
- Vertical nav, light theme, light header, DARK sidebar menu, sidebar toggled close (collapsed by default? data-toggled="close")

### Custom CSS (header.php inline)
- body font-family: Roboto
- scrollbar: 15px, thumb #c3c3c3
- .top-filter flex space-between mb-25px; .filter-show 150px; .filter-short 225px; .filter-label 14px/500/uppercase; .filter-select height 40px transparent
- .form-check-input bg #bdbdbd
- .table-responsive overflow-x auto, overflow-y visible; .table-responsive .dropdown static→absolute z-1050
- .table-wrapper max-height 700px overflow-y auto; thead/tfoot sticky
- th,td padding 8px