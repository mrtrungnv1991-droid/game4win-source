# Admin Panel — Extractable Components

Catalog of components that can be extracted as reusable Superdesign DraftComponents for consistent design across pages.

## Layout Components (appear on most pages — extract first)

### AppShell
- Source: `views/admin/sidebar.php` (header + sidebar) + `views/admin/header.php`
- Category: layout
- Description: Full admin shell — light top bar + dark vertical sidebar + content area
- Extractable props: activeItem (string, default "home"), sidebarToggled (boolean, default false)
- Hardcoded: logo, nav structure, all category labels, icons, CSS

### SideNav
- Source: `views/admin/sidebar.php:603-1128`
- Category: layout
- Description: Dark vertical nav with 6 categories (Main, Bảo mật, Digital Commerce OS, Dịch vụ, Quản lý, Cài đặt hệ thống), 2-level submenus, per-item colored icons, emoji labels
- Extractable props: activeItem (string, default "home"), openSubmenu (string, default "")
- Hardcoded: all menu items/labels/emoji/icons/colors

### TopBar
- Source: `views/admin/sidebar.php:492-588`
- Category: layout
- Description: Light top bar — logo, hamburger toggle, user-circle storefront link, theme toggle, fullscreen, settings switcher
- Extractable props: none major (all static)
- Hardcoded: icons, links

## Basic Components (used across pages)

### StatWidget (gradient KPI card)
- Source: `views/admin/home.php:316-515`
- Category: basic
- Description: Gradient card with label + circular icon + 2x2 grid of 4 metrics (Thành viên/Đơn hàng/Doanh thu/Lợi nhuận). 4 color variants: blue/purple/green/orange
- Extractable props: title (string, default "TOÀN THỜI GIAN"), iconClass (string, default "fa-solid fa-infinity"), gradient (string, default "linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%)"), metricLabels (array of 4 strings)
- Hardcoded: metric cell styles, value ids

### ChartCard
- Source: `views/admin/home.php:578-888`
- Category: basic
- Description: Card with title + time-range select + 300px Chart.js bar canvas
- Extractable props: title (string, default "THỐNG KÊ ĐƠN HÀNG"), chartId (string, default "chartjs-line")
- Hardcoded: canvas, loader, select options

### TimelinePanel
- Source: `views/admin/home.php:894-921`
- Category: basic
- Description: Card with title + live gif + 500px scrollable timeline list (AJAX-fed)
- Extractable props: title (string, default "ĐƠN HÀNG GẦN ĐÂY"), listClass (string, default "orders-timeline")
- Hardcoded: timeline container, refresh interval

### StatusAlert
- Source: `views/admin/home.php:26-315`
- Category: basic
- Description: System status alert — secondary (project bar), warning (SMTP), danger (installer/cron/PHP). SVG icon block + bold title + dismiss + action buttons
- Extractable props: variant (string, default "warning"), title (string, default ""), dismissible (boolean, default true)
- Hardcoded: svg icons, button labels

### FloatingActions
- Source: `views/admin/home.php:966-985`
- Category: basic
- Description: Fixed bottom-right circular action buttons (Top Services green, Leaderboard blue)
- Extractable props: showLeaderboard (boolean, default true), showTopServices (boolean, default true)
- Hardcoded: icons, positions, tooltips

### FilterBar
- Source: header.php inline CSS + list pages
- Category: basic
- Description: .top-filter bar — flex space-between, filter selects (150px show / 225px short), uppercase 14px labels
- Extractable props: filterCount (number, default 3)
- Hardcoded: filter field markup

## Extraction guidance
- Extract AppShell + SideNav + TopBar first (appear on all 95+ pages)
- StatWidget/ChartCard/TimelinePanel are dashboard-specific but reusable across future dashboards
- Skip Button/Input/Card primitives (Bootstrap provides them)