# Admin Panel — Shared UI Components

Framework: PHP (CMSNT ShopClone7) + Bootstrap 5 + jQuery. No React/Vue. YNEX (Sash-style) admin theme.
Icons: Boxicons (bx/fe), FontAwesome (fas/fa), Remix (ri), LineAwesome (las). Chart.js for charts.

## Card (custom-card) — the core container
```html
<div class="card custom-card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h6 class="card-title mb-0">TITLE</h6>
    <div class="ms-auto">[actions: select/dropdown/icon]</div>
  </div>
  <div class="card-body">...</div>
</div>
```
- Header: title left, actions right (form-select-sm dropdown for time range, gif-live.gif 60px)
- Used for: charts, tables, stat panels. Border #f3f3f3, radius default bootstrap.

## Stat widget (gradient card) — dashboard KPI cards
```html
<div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
  <div class="card custom-card overflow-hidden" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-bold mb-0 text-white">LABEL</h6>
        <span style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.25);border-radius:50%;">
          <i class="fa-solid fa-infinity" style="font-size:18px;color:#fff;"></i>
        </span>
      </div>
      <div class="row g-2">
        <div class="col-6"><div class="p-2 rounded" style="background:rgba(255,255,255,0.15);">
          <p class="mb-1 text-white text-opacity-75 fs-11">Thành viên</p>
          <h5 class="fw-bold mb-0 text-white" id="...">VALUE</h5>
        </div></div>
        <!-- 4 sub-metrics: Thành viên / Đơn hàng / Doanh thu / Lợi nhuận -->
      </div>
    </div>
  </div>
</div>
```
4 gradient variants: blue #3b82f6→#1d4ed8 (TOÀN THỜI GIAN), purple #8b5cf6→#6d28d9 (THÁNG), green #10b981→#059669 (TUẦN NÀY), orange #f59e0b→#d97706 (HÔM NAY). Icons: fa-infinity, fa-calendar-days, fa-calendar-week, fa-sun.

## Alerts (system status)
- alert-secondary (custom-alert-icon shadow-sm): project/version bar with title + action buttons
- alert-warning: SMTP not configured / important notices
- alert-danger: installer.php exists, cron not running, debug_auto_bank on, PHP version mismatch
- Style: `svg` icon block (svg-danger/svg-warning), bold title, dismissible, Custom Alert (cute-alert)

## Tables
```html
<div class="table-responsive">
  <table class="table table-striped table-hover table-sm">
    <thead class="table-primary sticky-top"><tr><th>...</th></tr></thead>
    <tbody>...</tbody>
  </table>
</div>
```
- .table-wrapper (max-height 700px, overflow-y auto), thead/tfoot sticky
- .table-responsive .dropdown-menu absolute (fix cut-off)

## Timeline list (recent orders/deposits)
```html
<ul class="timeline list-unstyled orders-timeline" style="height:500px;overflow-x:hidden;overflow-y:auto;"></ul>
```
- Populated by AJAX every 5s. Lives in col-xl-6 card with title + gif-live.gif.

## Chart card
```html
<canvas id="chartjs-line" class="chartjs-chart" style="height:300px;"></canvas>
```
- Chart.js bar charts. Order chart: profit #49b6f5 + revenue #845adf (2 datasets). Deposit chart: #1d4ed8 (1 dataset).
- Header: card-title + select#chart-time-range (today/week/month/last_month/year).
- Loader: spinner-border + "Đang tải dữ liệu biểu đồ..." overlays canvas.

## Buttons
- btn-primary, btn-secondary, btn-info, btn-success, btn-warning, btn-danger, btn-light
- btn-sm, btn-icon rounded-circle shadow-lg (floating action buttons)
- Form: form-select-sm, form-check-input (grey #bdbdbd bg), form-label

## Modals
- .modal fade + modal-dialog modal-lg/modal-xl, header with icon + title, body max-height 600px overflow-y auto, footer with Đóng/Làm mới buttons.

## Floating action buttons (dashboard)
```html
<div class="position-fixed" style="bottom:80px;right:20px;z-index:1000;">
  <div class="mb-3"><button class="btn btn-success btn-icon rounded-circle shadow-lg" onclick="showTopServices()"><i class="fas fa-chart-bar fs-18"></i></button></div>
  <div><button class="btn btn-primary btn-icon rounded-circle shadow-lg" onclick="showLeaderboard()"><i class="fas fa-trophy fs-18"></i></button></div>
</div>
```

## Filter bar (list pages)
```html
<div class="top-filter d-flex align-items-center justify-content-between" style="margin-bottom:25px;">
  [filters left] [filter-show 150px] [filter-short 225px]
</div>
```
- .filter-label uppercase 14px 500 weight, .filter-select height 40px transparent bg