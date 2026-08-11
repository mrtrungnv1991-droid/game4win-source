# GameTopup — AI Agent Context

## Project Overview
Game top-up platform built on ShopClone7 (PHP). Converts account-selling shop into game top-up marketplace.

## Tech Stack
- **Backend**: PHP 8.5, MariaDB 11.4
- **Frontend**: AdminLTE 3 (Bootstrap 4), JavaScript, Chart.js
- **Database**: `gamewinn_topup` (121 games, 1702 tiers)
- **Server**: PHP Built-in (`php -S 127.0.0.1:8080 router.php`)

## Key Files
| File | Purpose |
|---|---|
| `source/index.php` | Main router (modules: adcp, admin, client, ctv) |
| `source/router.php` | PHP dev server rewrite rules |
| `source/libs/db.php` | Database class (CMSNT) |
| `source/libs/helper.php` | base_url, check_string, auth helpers |
| `source/libs/topup_provider.php` | TopupProvider class (Mock/REST/Webhook) |
| `source/views/adcp/home.php` | Admin dashboard |
| `source/views/client/topup-home.php` | Client marketplace (dark theme) |
| `source/ajaxs/client/product.php` | buyTopup handler |
| `source/cron/process_topup_orders.php` | Cron: retry + expire orders |
| `database/schema_clean.sql` | Database schema |

## Module Structure
```
adcp/    → Admin Control Panel (dashboard, tickets, messages, flash-sales, reviews, products, categories, plans, stock, orders, api)
admin/   → Legacy admin (game-manager, topup-orders, provider-manager, settings, users)
client/  → Client views (topup-home, support, ticket-detail, topup-history)
```

## Database (MariaDB)
- **Host**: 127.0.0.1, **User**: root, **Pass**: root123, **DB**: gamewinn_topup
- **Admin**: admin/admin123 (balance: 0đ)
- **Test user**: testuser/test123 (balance: 475kđ)

## Key Business Logic
- **Order flow**: pending → processing → success/failed → (admin refunds manually)
- **Provider**: Mock/REST/Webhook via TopupProvider class
- **No auto-refund**: Admin manually refunds failed/pending orders
- **Membership**: Silver/Gold/Diamond based on total spent
- **Loyalty points**: 1 point per 1,000đ spent

## Conventions
- Vietnamese language throughout
- BOOLEAN: admin=1, status=1, banned=0
- Column naming: snake_case (currency_name, uid_pattern)
- Table prefix: none (bare names)
- Auth: cookie-based (`user_login` cookie with MD5 token)

## Pitfalls
- `get_list()` does NOT support prepared statement params — use `get_list_safe()` or inline query
- `execute_code` + `write_file` corrupts files (prefix "N|") — never use together
- PHP built-in server must run foreground (background exits immediately on Windows)
- Never use template literals in write_file — they get escaped incorrectly
