# ADCP Module — Mapping Document

## Nguồn: shopkey.cmsnt.net (ShopClone7 gốc)
## Target: GameTopup (game4win-clone)

---

## Legend
- ✅ Đã có trong GameTopup
- 🔧 Cần migrate / tạo mới
- ❌ Không cần cho GameTopup (bán account)
- 🔀 Map sang module khác

---

## 1. DASHBOARD (1 action)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 1 | dashboard | ?module=adcp&action=home | ✅ | Đã có adcp/home.php |

## 2. PRODUCT MANAGEMENT (12 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 2 | category | ?module=admin&action=categories | ✅ | Đã có |
| 3 | product | ?module=admin&action=products | ✅ | Đã có (account products) |
| 4 | product-add | ?module=admin&action=product-add | ✅ | Đã có |
| 5 | product-edit | ?module=admin&action=product-edit&id= | ✅ | Đã có |
| 6 | product-orders | ?module=admin&action=topup-orders | ✅ | Đã tích hợp topup |
| 7 | product-orders-detail | ?module=admin&action=product-orders | ✅ | Đã có |
| 8 | product-api | ?module=admin&action=product-api | ✅ | Đã có (suppliers API) |
| 9 | product-api-add | ?module=admin&action=product-api-add | ✅ | Đã có |
| 10 | product-api-edit | ?module=admin&action=product-api-edit&id= | ✅ | Đã có |
| 11 | stock | - | ❌ | Không cần (kho account) |
| 12 | stock-add | - | ❌ | Không cần |
| 13 | account-sold | - | ❌ | Không cần |

## 3. GAME TOPUP (3 actions) — TỰ THÊM

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 14 | game-manager | ?module=admin&action=game-manager | ✅ | 121 games listing |
| 15 | game-edit | ?module=admin&action=game-edit&id= | ✅ | Edit game + tiers |
| 16 | provider-manager | ?module=admin&action=provider-manager | ✅ | Topup providers |

## 4. ORDERS (2 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 17 | topup-orders | ?module=admin&action=topup-orders | ✅ | Đơn nạp game |
| 18 | topup-orders-detail | ?module=admin&action=product-orders | 🔀 | Map sang product-orders |

## 5. TICKETS (2 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 19 | tickets | ?module=adcp&action=ticket-list | ✅ | Đã tạo |
| 20 | ticket-detail | ?module=adcp&action=ticket-detail&id= | ✅ | Đã tạo |

## 6. USERS (7 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 21 | users | ?module=admin&action=users | ✅ | Đã có |
| 22 | user-add | ?module=admin&action=user-edit | ✅ | Đã có |
| 23 | user-edit | ?module=admin&action=user-edit&id= | ✅ | Đã có |
| 24 | login-user | ?module=admin&action=login-user&id= | ✅ | Đã có |
| 25 | block-ip | ?module=admin&action=block-ip | ✅ | Đã có |
| 26 | logs | ?module=admin&action=logs | ✅ | Đã có |
| 27 | transactions | ?module=admin&action=transactions | ✅ | Đã có |

## 7. DEPOSIT / RECHARGE (3 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 28 | deposit | ?module=admin&action=recharge-manual | ✅ | Nạp tiền thủ công |
| 29 | deposit-card | ?module=admin&action=recharge-card | ✅ | Nạp thẻ cào |
| 30 | deposit-bank | ?module=admin&action=recharge-bank | ✅ | Nạp bank |

## 8. MARKETING (3 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 31 | coupons | ?module=admin&action=coupons | ✅ | Đã có |
| 32 | blogs | ?module=admin&action=blogs | ✅ | Đã có |
| 33 | blog-add | ?module=admin&action=blog-add | ✅ | Đã có |

## 9. SETTINGS (5 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 34 | general | ?module=admin&action=settings | ✅ | Đã có |
| 35 | language | ?module=admin&action=language-list | ✅ | Đã có |
| 36 | currency | ?module=admin&action=currency-list | ✅ | Đã có |
| 37 | theme | ?module=admin&action=theme | ✅ | Đã có |
| 38 | menu | ?module=admin&action=menu-list | ✅ | Đã có |

## 10. SYSTEM (3 actions)

| # | Action | Route | Status | Note |
|---|---|---|---|---|
| 39 | about | - | 🔧 | Chưa có (trang about hệ thống) |
| 40 | recycle-bin | - | 🔧 | Chưa có (thùng rác orders) |
| 41 | notifications | ?module=admin&action=settings | 🔀 | Map sang settings |

## 11. KHÔNG CẦN (5 actions)

| # | Action | Lý do |
|---|---|---|
| 42 | proxy | Không cần proxy cho topup |
| 43 | automation | Tự động hóa mua account |
| 44 | ctv | CTV bán account |
| 45 | account-api-logs | Log API mua account |
| 46 | account-sold | TK đã bán |

---

## SUMMARY

- ✅ Đã có: 35/41 actions
- 🔧 Cần làm thêm: 2 (about, recycle-bin)
- ❌ Không cần: 5
- 🔀 Map sang module khác: 4

## GAP cần điền:

1. **adcp/about** — Trang giới thiệu hệ thống (version, stats, license)
2. **adcp/recycle-bin** — Thùng rác (orders đã xóa, có thể khôi phục)
