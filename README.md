# Game4Win — Digital Commerce OS (ShopClone7 v6.3.8)

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-10.3%2B-003545?logo=mariadb&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)

**Game4Win** là nền tảng **marketplace sản phẩm số** (Digital Commerce) xây dựng trên **ShopClone7 v6.3.8** — hệ thống bán Game Key, Gift Card, Account, Top Up với giao hàng tự động 24/7.

> **Clean clone**: bản này đã được làm sạch — không chứa user data, transactions, products, orders của bản gốc (game4win.net / g2up.net).

---

## ⚡ Quick Start

```bash
# 1. Import database
mysql -u root -p < database/schema_clean.sql

# 2. Chạy local
cd source
php -S 127.0.0.1:8080 router.php

# 3. Mở trình duyệt
# http://127.0.0.1:8080
```

---

## ✨ Tính năng chính

| Nhóm | Tính năng |
|------|-----------|
| 🛒 **Marketplace** | Game Key, Gift Card, Account, Software, Subscription, Top Up |
| 👥 **Group Buy** | Mua chung giá rẻ — đếm ngược thời gian, tiến độ tham gia |
| ⚡ **Top Up Game** | Nạp game tự động, UID lookup, nhiều tier giá |
| 💰 **Ví tiền** | Nạp/rút qua MOMO, Zalo Pay, THESIEURE, crypto |
| 🎫 **Coupon / Flash Sale** | Mã giảm giá, flash sale countdown, giảm giá theo % |
| 🤖 **API Suppliers** | Kết nối nguồn hàng API, tự động đồng bộ stock |
| 📊 **Admin Panel** | Dashboard Chart.js, quản lý game/tier/order/provider, tickets, messages |
| 📱 **Responsive** | Dark theme hiện đại, mobile-first |
| 🔐 **Bảo mật** | bcrypt password, CSRF token, 2FA, rate-limit login |

---

## 🗂️ Cấu trúc thư mục

```
game4win-clone/
├── source/                  # PHP source (ShopClone7 v6.3.8)
│   ├── index.php            # Router chính
│   ├── router.php           # Router cho PHP built-in server
│   ├── config.php           # Cấu hình hệ thống
│   ├── views/               # Views: client, admin, adcp, ctv
│   │   ├── client/          # Trang khách (home-marketplace, topup-home...)
│   │   ├── admin/           # Admin panel
│   │   └── adcp/            # Admin control panel (dashboard, tickets...)
│   ├── ajaxs/               # AJAX handlers (client, admin, ctv)
│   ├── api/                 # Public API
│   ├── libs/                # Core library (DB, helper, TopupProvider...)
│   ├── models/              # Models
│   ├── cron/                # Cron jobs (checklive, suppliers, topup)
│   └── public/              # Assets: css, js, images, theme
├── database/
│   └── schema_clean.sql     # Schema sạch: 64 tables + config data
└── README.md
```

---

## 🛠️ Tech Stack

- **PHP** 8.0+ (đã test với PHP 8.5)
- **MySQL** 5.7+ / **MariaDB** 10.3+
- **Composer** (dependencies)
- **mod_rewrite** (Apache) / **router.php** (PHP built-in server)
- **Chart.js** (dashboard analytics)
- **jQuery** 3.6, Bootstrap, AdminLTE theme
- **SweetAlert2, CuteAlert, Simple Notify** (UI)

---

## 🚀 Cài đặt & Deploy

### Yêu cầu
- PHP 8.0+, MySQL 5.7+/MariaDB 10.3+, Composer, mod_rewrite

### 1. Import database
```bash
mysql -u root -p < database/schema_clean.sql
```

### 2. Chạy local (PHP built-in server)
```bash
cd source
php -S 127.0.0.1:8080 router.php
```

> 💡 **router.php là bắt buộc** khi dùng PHP built-in server — nó map friendly URLs (`/login`, `/admin`) sang `index.php?module=X&action=Y`. Không có nó, các route trả 404.

### 3. Deploy production
- Upload `source/` lên hosting (Apache + mod_rewrite)
- Import `database/schema_clean.sql`
- Cấu hình DB credentials trong `.env` (đã chặn truy cập trực tiếp qua `.htaccess`)
- Đổi `LICENSE_KEY` trong `config.php`
- Setup cron: `cron/checklive/` + `cron/suppliers/`
- **SSL certificate** bắt buộc cho payment gateways

---

## 🗄️ Database (64 tables)

`schema_clean.sql` chứa:
- ✅ Toàn bộ 64 table structures + indexes + AUTO_INCREMENT
- ✅ System config: admin_role, currencies, languages, settings, menu, automations
- ✅ Bảng topup: `games`, `topup_tiers`, `game_servers`, `topup_providers`, `topup_api_logs`
- ✅ Bảng marketing: `flash_sales`, `flash_sale_products`, `product_reviews`
- ✅ Bảng hỗ trợ: `tickets`, `ticket_replies`, `messages`
- ❌ **Đã strip**: users, products, orders, transactions, payment logs, sold accounts (~500+ rows dữ liệu nhạy cảm)

---

## 🔐 Bảo mật

- Mật khẩu user: **bcrypt hash**
- `.env` chứa DB credentials — chặn truy cập qua `.htaccess`
- CSRF token cho mọi form
- Rate-limit đăng nhập, block IP
- 2FA secrets, API keys, bank tokens đã được strip khỏi bản clone

---

## 📄 License

MIT — bản clone clean, không kèm dữ liệu người dùng thật.