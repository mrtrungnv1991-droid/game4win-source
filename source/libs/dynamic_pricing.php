<?php
/**
 * DynamicPricing — Tự động điều chỉnh giá theo competitor
 * Digital Commerce OS
 * 
 * Strategies:
 *  - undercut: bán rẻ hơn competitor X%
 *  - match: bán bằng giá competitor
 *  - margin_floor: giữ biên lợi nhuận tối thiểu X%
 * 
 * Flow: Đọc pricing_rules → so giá products vs competitor_products → cập nhật giá → log price_history
 */

class DynamicPricing {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Chạy engine: điều chỉnh giá tất cả products có linked competitor
     */
    public function runEngine($dry_run = false) {
        $rules = $this->db->get_list_safe("SELECT * FROM pricing_rules WHERE status = 1", []);
        if (empty($rules)) {
            return ['status' => 'error', 'msg' => 'No active pricing rules'];
        }
        $rule = $rules[0]; // Dùng rule đầu tiên (default)

        // Lấy tất cả products đã import từ competitor
        $linked = $this->db->get_list_safe(
            "SELECT p.id, p.name, p.price, p.cost, cp.price as competitor_price, cp.name as competitor_name
             FROM products p
             JOIN competitor_products cp ON cp.imported_product_id = p.id
             WHERE p.status = 1", []);

        $adjusted = 0;
        $skipped = 0;
        $changes = [];

        foreach ($linked as $item) {
            $new_price = $this->calculatePrice(
                (float)$item['price'],
                (float)$item['cost'],
                (float)$item['competitor_price'],
                $rule
            );

            if ($new_price === null || abs($new_price - $item['price']) < 1000) {
                $skipped++;
                continue;
            }

            // Giới hạn biến động giá (max_price_change_percent)
            $max_change = $item['price'] * $rule['max_price_change_percent'] / 100;
            if (abs($new_price - $item['price']) > $max_change) {
                $new_price = $item['price'] + ($new_price > $item['price'] ? $max_change : -$max_change);
            }

            $new_price = round($new_price / 1000) * 1000; // Làm tròn nghìn

            if (!$dry_run) {
                $this->db->update('products', [
                    'price' => $new_price,
                    'update_gettime' => date('Y-m-d H:i:s'),
                ], " `id` = " . intval($item['id']));

                $this->db->insert('price_history', [
                    'product_id' => $item['id'],
                    'old_price' => $item['price'],
                    'new_price' => $new_price,
                    'competitor_price' => $item['competitor_price'],
                    'reason' => $rule['strategy'] . ' vs ' . $item['competitor_name'],
                    'rule_id' => $rule['id'],
                ]);
            }

            $changes[] = [
                'product' => $item['name'],
                'old_price' => (int)$item['price'],
                'new_price' => (int)$new_price,
                'competitor_price' => (int)$item['competitor_price'],
            ];
            $adjusted++;
        }

        return [
            'status' => 'success',
            'dry_run' => $dry_run,
            'adjusted' => $adjusted,
            'skipped' => $skipped,
            'changes' => $changes,
        ];
    }

    /**
     * Tính giá mới theo strategy
     */
    private function calculatePrice($current_price, $cost, $competitor_price, $rule) {
        if ($competitor_price <= 0) return null;

        $min_price = $cost * (1 + $rule['min_margin_percent'] / 100); // Giá sàn: cost + margin tối thiểu

        switch ($rule['strategy']) {
            case 'undercut':
                // Bán rẻ hơn competitor X%
                $target = $competitor_price * (1 - $rule['undercut_percent'] / 100);
                break;
            case 'match':
                // Bán bằng giá competitor
                $target = $competitor_price;
                break;
            case 'margin_floor':
                // Giữ nguyên giá nếu đã trên sàn, nếu không kéo lên sàn
                $target = max($current_price, $min_price);
                break;
            default:
                return null;
        }

        // Không bao giờ bán dưới giá sàn
        return max($target, $min_price);
    }

    /**
     * Lịch sử thay đổi giá
     */
    public function getPriceHistory($limit = 30) {
        return $this->db->get_list_safe(
            "SELECT ph.*, p.name as product_name FROM price_history ph
             LEFT JOIN products p ON ph.product_id = p.id
             ORDER BY ph.id DESC LIMIT " . intval($limit), []);
    }

    /**
     * Stats cho dashboard
     */
    public function getStats() {
        return [
            'active_rules' => $this->db->num_rows("SELECT id FROM pricing_rules WHERE status = 1"),
            'linked_products' => $this->db->num_rows(
                "SELECT p.id FROM products p JOIN competitor_products cp ON cp.imported_product_id = p.id"),
            'total_adjustments' => $this->db->num_rows("SELECT id FROM price_history"),
            'recent_history' => $this->getPriceHistory(10),
            'rules' => $this->db->get_list_safe("SELECT * FROM pricing_rules", []),
        ];
    }
}
