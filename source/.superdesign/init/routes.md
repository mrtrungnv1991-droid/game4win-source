# Admin Panel — Routes

All routes under `/admin/?module=<name>` (base_url_admin). PHP view files in `views/admin/`. No React router — server-rendered pages.

## Main
| Route | File | Summary |
|---|---|---|
| home / (dashboard) | home.php | KPI widgets (all-time/month/week/today), order+deposit charts, recent orders/deposits timelines, leaderboard & top-services modals, floating action buttons |
| logs | logs.php | Activity logs (filterable, paginated) |
| transactions | transactions.php | Balance transactions |
| log-auto-bank | log-auto-bank.php | Bank auto-detect history |
| telegram-logs | telegram-logs.php | Telegram send logs |
| email-queue | email-queue.php | Email queue |
| telegram-queue | telegram-queue.php | Telegram queue |
| automations | automations.php | Automation rules (edit: automation-edit.php) |

## Security
| block-ip | block-ip.php | Blocked IPs |

## Digital Commerce OS (custom modules)
| smart-routing | smart-routing.php | Smart routing rules |
| group-buy-admin | group-buy-admin.php | Group buy management (detail: group-buy-detail.php) |
| api-keys | api-keys.php | API keys |
| competitor-research | competitor-research.php | Competitor research |
| trend-detection | trend-detection.php | Trend detection |
| dynamic-pricing | dynamic-pricing.php | Dynamic pricing |

## Products (Dịch vụ)
| categories | categories.php | Categories (add/edit) |
| products | products.php | All products (add/edit) |
| product-warehouse | product-warehouse.php | Account warehouse |
| product-stock | product-stock.php | Stock by type |
| key-inventory | key-inventory.php | Game key inventory |
| giftcard-inventory | giftcard-inventory.php | Gift card inventory |
| game-manager | game-manager.php | Top-up games (121) (edit: game-edit.php) |
| provider-manager | provider-manager.php | Providers |
| product-api | product-api.php | API connections (manager/add/edit) |
| product-orders | product-orders.php | Orders (3114 lines, biggest page) |
| product-sold | product-sold.php | Sold items |

## Management (Quản lý)
| ctv-statistics / ctv-withdraw / ctv-pending-products / ctv-config | CTV panel |
| users / user-edit | users.php | Members |
| roles / role-edit | roles.php | Admin roles |
| recharge-* (22 routes) | recharge-*.php | Deposit methods: bank, card, crypto, momo, thesieure, paypal, perfectmoney, toyyibpay, squadco, flutterwave, xipay, lempay, tripay, zinipay, korapay, pocketfi, paymentpoint, dsociopay, tmweasyapi, openpix, bakong, manual |
| affiliate-links / affiliate-history / affiliate-withdraw / affiliate-config | Affiliate program |
| email-campaigns (+add/edit/sending-view) | Email campaigns |
| coupons (+edit) | coupons.php | Discount codes |
| promotions | promotions.php | Deposit promotions |
| blogs / blog-add / blog-edit / blog-category | Blog |

## Settings (Cài đặt hệ thống)
| language-list / language-edit / translate-list | Languages |
| currency-list / currency-edit | Currencies |
| theme | theme.php | Theme |
| settings | settings.php | Settings (sub: settings/addons.php, settings/connection.php, settings/security.php) |

## Layout files (shared by every page)
- header.php — <head>, CSS/JS assets, body open, showMessage() helper
- sidebar.php — switcher offcanvas, loader, app-header, app-sidebar (nav)
- footer.php — gtranslate footer, scrollToTop, update check
- nav.php — empty stub (2 lines, unused)