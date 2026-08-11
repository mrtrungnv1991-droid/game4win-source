# ADCP Module — Full Clone từ shopkey.cmsnt.net (SHOPKEY 2.1.9)

## Source: shopkey.cmsnt.net (extracted via browser console)
## Target: GameTopup

---

## 1. DASHBOARD
| Action | Route | Status |
|---|---|---|
| home | ?module=adcp&action=home | ✅ cloned |

## 2. LOGS (8 actions)
| Action | Route | Status |
|---|---|---|
| logs | ?module=adcp&action=logs | ✅ admin/logs |
| transactions | ?module=adcp&action=transactions | ✅ admin/transactions |
| bot-telegram-logs | ?module=adcp&action=bot-telegram-logs | ✅ admin/telegram-logs |
| telegram-shop-logs | ?module=adcp&action=telegram-shop-logs | 🔧 map |
| email-queue | ?module=adcp&action=email-queue | ✅ admin/email-queue |
| telegram-queue | ?module=adcp&action=telegram-queue | ✅ admin/telegram-queue |
| search-logs | ?module=adcp&action=search-logs | 🔧 NEW |
| ai-logs | ?module=adcp&action=ai-logs | 🔧 NEW |

## 3. TOOLS (4 actions)
| Action | Route | Status |
|---|---|---|
| automations | ?module=adcp&action=automations | ✅ admin (Tự động hóa) |
| addons | ?module=adcp&action=addons | ✅ admin/addons |
| block-ip | ?module=adcp&action=block-ip | ✅ admin/block-ip |
| media-library | ?module=adcp&action=media-library | 🔧 NEW |

## 4. PRODUCTS (7 actions)
| Action | Route | Status |
|---|---|---|
| categories | ?module=adcp&action=categories | ✅ admin/categories |
| products | ?module=adcp&action=products | ✅ admin/products |
| product-plans-all | ?module=adcp&action=product-plans-all | ❌ (account plans) |
| product-stock-all | ?module=adcp&action=product-stock-all | ❌ (account stock) |
| product-orders | ?module=adcp&action=product-orders | ✅ admin/topup-orders |
| product-api | ?module=adcp&action=product-api | ✅ admin/provider-manager |
| **product-reviews** | ?module=adcp&action=product-reviews | 🔧 **CLONE NGAY** |

## 5. MANAGEMENT (6 actions)
| Action | Route | Status |
|---|---|---|
| users | ?module=adcp&action=users | ✅ admin/users |
| coupons | ?module=adcp&action=coupons | ✅ admin/coupons |
| **flash-sales** | ?module=adcp&action=flash-sales | 🔧 **CLONE NGAY** |
| **tickets** | ?module=adcp&action=tickets | 🔧 rename ticket-list→tickets |
| **messages** | ?module=adcp&action=messages | ✅ cloned |
| roles | ?module=adcp&action=roles | ✅ admin/roles |

## 6. RECHARGE (16 actions)
| Action | Route | Status |
|---|---|---|
| recharge-bank | ?module=adcp&action=recharge-bank | ✅ |
| recharge-card | ?module=adcp&action=recharge-card | ✅ |
| recharge-crypto | ?module=adcp&action=recharge-crypto | ✅ |
| recharge-paypal | ?module=adcp&action=recharge-paypal | ✅ |
| recharge-manual | ?module=adcp&action=recharge-manual | ✅ |
| +11 cổng khác | xipay, korapay, tmweasyapi, openpix, bakong, tripay, thesieure, moneymotion, payssion, squadco, pocketfi | ❌ (SEA payment gateways) |

## 7. AFFILIATE (3 actions)
| Action | Route | Status |
|---|---|---|
| affiliate-history | ?module=adcp&action=affiliate-history | ✅ |
| affiliate-withdraw | ?module=adcp&action=affiliate-withdraw | ✅ |
| affiliate-config | ?module=adcp&action=affiliate-config | ✅ settings |

## 8. MARKETING (4 actions)
| Action | Route | Status |
|---|---|---|
| email-campaigns | ?module=adcp&action=email-campaigns | ✅ |
| blog-add | ?module=adcp&action=blog-add | ✅ |
| blogs | ?module=adcp&action=blogs | ✅ |
| blog-category | ?module=adcp&action=blog-category | 🔧 NEW |

## 9. API (2 actions)
| Action | Route | Status |
|---|---|---|
| api-keys | ?module=adcp&action=api-keys | 🔧 NEW |
| api-logs | ?module=adcp&action=api-logs | 🔧 NEW |

## 10. SYSTEM (3 actions)
| Action | Route | Status |
|---|---|---|
| language-list | ?module=adcp&action=language-list | ✅ |
| currency-list | ?module=adcp&action=currency-list | ✅ |
| settings | ?module=adcp&action=settings | ✅ |

---

## TỔNG KẾT
- ✅ Đã clone: 30/45
- ❌ Không cần: 10 (account stock/plans, SEA gateways)
- 🔧 Cần clone gấp: flash-sales, product-reviews, tickets (rename)
- 🔧 NEW (optional): search-logs, ai-logs, media-library, blog-category, api-keys, api-logs

## PRIORITY CLONE:
1. **flash-sales** — Flash Sale CRUD
2. **product-reviews** — Quản lý reviews  
3. **tickets** — Đổi tên từ ticket-list

## PHASE 2 (optional):
4. media-library, blog-category, api-keys, api-logs
