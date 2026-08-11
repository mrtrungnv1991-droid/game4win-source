# Game4Win ShopClone7 — Clean Clone

**Version**: ShopClone7 v6.3.8  
**Original**: game4win.net → g2up.net  
**Cleaned**: No user data, no transactions, no products, no orders

## 📦 Package Contents

```
game4win-clone/
├── source/              # PHP source code (18K files)
├── database/
│   └── schema_clean.sql # Clean schema + config data (190KB)
└── README.md
```

## 🗄️ Database

`schema_clean.sql` contains:
- ✅ All 64 table structures (CREATE TABLE)
- ✅ All indexes, AUTO_INCREMENTS
- ✅ System config: admin_role, currencies, languages, settings, menu, automations
- ❌ Stripped: users, products, orders, transactions, payment logs, sold accounts

Import:
```sql
mysql -u root -p < database/schema_clean.sql
```

## 🚀 Deployment

### 1. Upload source to your web server
```
Copy everything in source/ to your public_html (or equivalent)
```

### 2. Configure `.env`
```bash
cp .env.example .env
# Edit .env with your database credentials
```

### 3. Install Composer dependencies
```bash
cd source/
composer install
```

### 4. Import database
```bash
mysql -u root -p < database/schema_clean.sql
```

### 5. Set permissions
```bash
chmod -R 755 .
chmod 777 tmp/
chmod 777 assets/storage/
```

## 🔧 Post-deployment

1. Log in to admin: `/admin` with default credentials
2. Update site settings: title, logo, timezone
3. Add products and categories
4. Configure payment gateways
5. Set up cron jobs: `cron/checklive/` and `cron/suppliers/`

## ⚠️ What was stripped

| Stripped | Rows |
|----------|------|
| users | 69 |
| product_order | 63 |
| product_sold | 756 |
| product_stock | 76 |
| dongtien | 263 |
| payment_bank | 304 |
| payment_crypto | 236 |
| All other payment records | ~500+ |
| logs, order_log, log_bank_auto, etc. | 100+ |

## 🔐 Security notes

- `.env` contains DB credentials — blocked from direct access via `.htaccess`
- All user passwords were bcrypt hashed
- 2FA secrets, API keys, bank tokens were stripped with data
- Change LICENSE_KEY in config for production

## 📋 Requirements

- PHP 8.0+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- mod_rewrite enabled
- SSL certificate (required for payment gateways)
