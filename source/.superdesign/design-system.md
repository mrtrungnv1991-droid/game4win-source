# Design System — Admin Panel (Digital Commerce OS / ShopClone7)

## Product context

**What**: Admin panel for a game top-up / account-selling e-commerce platform (CMSNT ShopClone7 base + custom "Digital Commerce OS" modules: Smart Routing, Group Buy, API Keys, Competitor Research, Trend Detection, Dynamic Pricing).

**Who**: Store owner + staff (CTV sellers, affiliate managers). High-frequency operations: checking KPIs, managing products/stock, processing orders, monitoring deposits, handling 20+ payment gateways.

**Core jobs-to-be-done**:
1. At-a-glance business health (revenue, profit, orders, members — today/week/month/all-time)
2. Fast order & deposit monitoring (live timelines)
3. Product/stock/warehouse management
4. Payment gateway management (many deposit methods)
5. System configuration

**Key pages**: Dashboard (home), Products, Orders, Users, Recharge (20+ methods), Settings.

## Design direction

- **Theme**: LIGHT ONLY (user explicitly rejected dark theme as "xấu quá"). YNEX (Sash-style) admin aesthetic: light grey-blue page bg, white cards, light top bar, dark sidebar menu.
- **Mood**: clean, professional, data-dense but scannable. Trustworthy, not flashy.
- **Layout target**: the current dashboard stacks many alerts + 4 KPI gradient cards + 2 charts + 2 timelines + floating buttons. Redesign should improve hierarchy, reduce visual noise, group related info, and make the dense sidebar navigation easier to scan.

## Design tokens (from actual CSS — DO NOT deviate)

### Colors
| Token | Value | Usage |
|---|---|---|
| Page background | `rgb(240,241,247)` | body |
| Primary | `rgb(132,90,223)` #845adf | buttons, active states, links |
| Secondary | `rgb(35,183,229)` #23b7e5 | secondary elements |
| Info | `rgb(73,182,245)` #49b6f5 | chart profit series |
| Success | `rgb(38,191,148)` #26bf94 | success states |
| Warning | `rgb(245,184,73)` #f5b849 | warnings |
| Danger | `rgb(230,83,60)` #e6533c | errors/destructive |
| Text | `#333335` | body text |
| Border | `#f3f3f3` | card/table borders |
| White | `#fff` | cards, top bar |
| Sidebar text (dark menu) | `#536485` | inactive nav items |

### KPI gradient cards (dashboard)
- All-time: `linear-gradient(135deg, #3b82f6, #1d4ed8)` — blue
- Month: `linear-gradient(135deg, #8b5cf6, #6d28d9)` — purple
- Week: `linear-gradient(135deg, #10b981, #059669)` — green
- Today: `linear-gradient(135deg, #f59e0b, #d97706)` — orange

### Charts (Chart.js)
- Order chart: profit `rgb(73,182,245)`, revenue `rgb(132,90,223)`
- Deposit chart: `rgb(29,78,216)`
- Grid: `rgba(142,156,173,0.1)`, labels `#8c9097`

### Typography
- Font: **Roboto** (body), weights 400/500/700
- Card titles: bold, uppercase style
- Metric labels: fs-11 uppercase-ish, muted (white 75% on gradients)
- KPI values: h5/h6 fw-bold

### Spacing & structure
- Container: container-fluid; content in `.main-content.app-content`
- Grid: KPI row `col-xxl-3 col-xl-3 col-lg-6 col-md-6`, charts/timelines `col-xl-6`
- Card: white, border #f3f3f3, card-header (title + actions) + card-body
- KPI card internals: 40px circular icon `rgba(255,255,255,0.25)` top-right; 2×2 metric grid, cells `p-2 rounded rgba(255,255,255,0.15)`
- Timeline: 500px scrollable
- Radius: bootstrap default; shadows: shadow-sm on alerts

## Component patterns
- **Card (custom-card)**: title left + action (select/dropdown) right; body = chart/table/list
- **KPI StatWidget**: gradient hero card, 4 metrics each (Thành viên/Đơn hàng/Doanh thu/Lợi nhuận)
- **ChartCard**: title + time-range select (today/week/month/last_month/year) + bar chart 300px
- **TimelinePanel**: live-updating list (AJAX 5s), title + live gif
- **StatusAlert**: system health warnings (dismissible, icon + bold title + action)
- **SideNav**: dark sidebar, categories: Main / Bảo mật / Digital Commerce OS / Dịch vụ / Quản lý / Cài đặt hệ thống; per-item colored icons; 2-level submenus
- **TopBar**: light, logo + hamburger + user/theme/fullscreen/settings icons
- **FilterBar**: `.top-filter` flex, uppercase labels, show/short selects
- **FloatingActions**: bottom-right circular buttons (Top Services green, Leaderboard blue)

## Motion
- Live data refresh: 5s intervals (KPI + timelines) — subtle, no flashy animation
- Chart transitions: Chart.js defaults
- Alerts: fade dismiss

## Constraints
- LIGHT theme only. Do NOT introduce dark mode.
- Use ONLY the colors above (RGB triples / hexes) — no new palette inventions.
- Keep Roboto typography.
- Admin = 95+ PHP pages sharing one shell; any redesign must stay feasible to apply to the shared header/sidebar/footer + dashboard without touching every page.