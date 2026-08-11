<?php
/**
 * SmartRouter — Intelligent supplier selection engine
 * Digital Commerce OS
 * 
 * Flow: Đơn hàng vào → chấm điểm tất cả suppliers → chọn điểm cao nhất → xuất lệnh mua
 * Score = Price(30%) + Stock(10%) + Speed(15%) + ErrorRate(20%) + Refund(15%) + Stability(10%)
 */

class SmartRouter {
    private $db;
    private $rule;
    private $weights;
    private $log = [];

    public function __construct($db, $rule_id = 1) {
        $this->db = $db;
        $this->loadRule($rule_id);
    }

    /**
     * Load routing rule from DB
     */
    private function loadRule($rule_id) {
        $this->rule = $this->db->get_row_safe(
            "SELECT * FROM smart_routing_rules WHERE id = ? AND status = 1", [$rule_id]
        );
        if (!$this->rule) {
            // Default weights
            $this->weights = [
                'price'    => 0.30,
                'stock'    => 0.10,
                'speed'    => 0.15,
                'error'    => 0.20,
                'refund'   => 0.15,
                'stability'=> 0.10,
            ];
        } else {
            $this->weights = [
                'price'    => (float)$this->rule['price_weight'],
                'stock'    => (float)$this->rule['stock_weight'],
                'speed'    => (float)$this->rule['speed_weight'],
                'error'    => (float)$this->rule['error_weight'],
                'refund'   => (float)$this->rule['refund_weight'],
                'stability'=> (float)$this->rule['stability_weight'],
            ];
        }
    }

    /**
     * Get all active suppliers that can fulfill this product
     */
    public function getAvailableSuppliers($product_id = 0, $category_id = 0) {
        $sql = "SELECT * FROM suppliers WHERE status = 1";
        $params = [];
        // Ưu tiên suppliers đã từng route thành công (nếu có lịch sử)
        if ($product_id > 0) {
            $sql .= " ORDER BY (id IN (SELECT DISTINCT supplier_id FROM routing_logs WHERE success = 1)) DESC, rate ASC";
        } else {
            $sql .= " ORDER BY rate ASC";
        }
        return $this->db->get_list_safe($sql, $params);
    }

    /**
     * Score a single supplier based on performance data
     * Returns score 0-100
     */
    public function scoreSupplier($supplier_id, $expected_price = 0) {
        $perf = $this->db->get_row_safe(
            "SELECT * FROM supplier_performance WHERE supplier_id = ?", [$supplier_id]
        );

        if (!$perf) {
            // New supplier — give neutral score
            $perf = [
                'total_orders' => 0, 'success_orders' => 0, 'failed_orders' => 0,
                'avg_response_ms' => 5000, 'avg_price_deviation' => 0, 'score' => 50
            ];
        }

        $scores = [];

        // 1. Price score (higher is cheaper)
        if ($expected_price > 0 && $perf['avg_price_deviation'] != 0) {
            $price_score = max(0, 100 - abs($perf['avg_price_deviation']) * 5);
        } else {
            $price_score = 50; // Unknown
        }
        $scores['price'] = $price_score;

        // 2. Stock availability (if never out of stock)
        $total_supplier_orders = $this->db->num_rows_safe(
            "SELECT id FROM product_order WHERE supplier_id = ?", [$supplier_id]
        );
        $stock_score = $total_supplier_orders > 0 ? 80 : 50;
        $scores['stock'] = $stock_score;

        // 3. Speed score (lower response time = higher score)
        $avg_ms = (int)($perf['avg_response_ms'] ?? 5000);
        if ($avg_ms <= 1000) $speed_score = 100;
        elseif ($avg_ms <= 3000) $speed_score = 80;
        elseif ($avg_ms <= 5000) $speed_score = 60;
        elseif ($avg_ms <= 10000) $speed_score = 40;
        else $speed_score = 20;
        $scores['speed'] = $speed_score;

        // 4. Error rate score (lower is better)
        $total = (int)($perf['total_orders'] ?? 1);
        $failed = (int)($perf['failed_orders'] ?? 0);
        $error_rate = $total > 0 ? ($failed / $total) : 0.5;
        $scores['error'] = max(0, 100 - ($error_rate * 100));

        // 5. Refund/retry score
        $success_rate = $total > 0 ? ($perf['success_orders'] / $total) : 0.5;
        $scores['refund'] = $success_rate * 100;

        // 6. Stability score (based on recent activity)
        $last_success = $perf['last_success'] ?? null;
        if ($last_success) {
            $hours_since = (time() - strtotime($last_success)) / 3600;
            $scores['stability'] = max(0, 100 - ($hours_since * 5));
        } else {
            $scores['stability'] = 30;
        }

        // Weighted total
        $total_score = 0;
        foreach ($this->weights as $key => $weight) {
            $total_score += ($scores[$key] ?? 50) * $weight;
        }

        // Update performance score
        $this->db->update('supplier_performance', [
            'score' => round($total_score, 2)
        ], "supplier_id = " . intval($supplier_id));

        return [
            'supplier_id' => $supplier_id,
            'total_score' => round($total_score, 2),
            'breakdown' => $scores,
            'weights' => $this->weights,
        ];
    }

    /**
     * Find best supplier for an order
     * Returns the best supplier or null if none available
     */
    public function findBestSupplier($product_id = 0, $expected_price = 0, $preferred_supplier_id = 0) {
        $suppliers = $this->db->get_list_safe(
            "SELECT * FROM suppliers WHERE status = 1 ORDER BY rate ASC", []
        );

        if (empty($suppliers)) {
            $this->log[] = "No active suppliers found";
            return null;
        }

        $results = [];
        foreach ($suppliers as $sup) {
            $score = $this->scoreSupplier($sup['id'], $expected_price);
            $score['supplier_name'] = $sup['domain'] ?? 'Supplier #' . $sup['id'];
            $score['supplier_rate'] = (float)($sup['rate'] ?? 1);
            
            // Apply supplier's rate multiplier to price score
            $score['total_score'] = $score['total_score'] / max(0.5, $score['supplier_rate']);
            
            $results[] = $score;
        }

        // Sort by total score descending
        usort($results, function($a, $b) {
            return $b['total_score'] <=> $a['total_score'];
        });

        // If preferred supplier is specified and its score is acceptable, use it
        if ($preferred_supplier_id > 0) {
            foreach ($results as $r) {
                if ($r['supplier_id'] == $preferred_supplier_id && $r['total_score'] >= 40) {
                    $this->log[] = "Using preferred supplier #{$preferred_supplier_id} (score: {$r['total_score']})";
                    $r['choice_reason'] = 'preferred_supplier';
                    return $r;
                }
            }
        }

        $best = $results[0];
        $this->log[] = "Selected supplier #{$best['supplier_id']} ({$best['supplier_name']}) with score {$best['total_score']}";
        $best['choice_reason'] = 'highest_score';
        $best['all_scores'] = $results;
        return $best;
    }

    /**
     * Log routing decision
     */
    public function logRoute($order_id, $supplier_id, $choice_reason, $scores, $success = 1, $response_ms = 0, $error_msg = '') {
        $this->db->insert('routing_logs', [
            'order_id' => $order_id,
            'supplier_id' => $supplier_id,
            'choice_reason' => $choice_reason,
            'scores_json' => json_encode($scores),
            'response_ms' => $response_ms,
            'success' => $success,
            'error_msg' => $error_msg,
        ]);
    }

    /**
     * Update supplier performance after an order
     */
    public function updatePerformance($supplier_id, $success, $response_ms = 0) {
        $perf = $this->db->get_row_safe(
            "SELECT * FROM supplier_performance WHERE supplier_id = ?", [$supplier_id]
        );

        if (!$perf) {
            $this->db->insert('supplier_performance', [
                'supplier_id' => $supplier_id,
                'total_orders' => 1,
                'success_orders' => $success ? 1 : 0,
                'failed_orders' => $success ? 0 : 1,
                'avg_response_ms' => $response_ms,
                'last_success' => $success ? date('Y-m-d H:i:s') : null,
                'last_failure' => $success ? null : date('Y-m-d H:i:s'),
                'score' => $success ? 60 : 40,
            ]);
        } else {
            $new_total = $perf['total_orders'] + 1;
            $new_success = $perf['success_orders'] + ($success ? 1 : 0);
            $new_failed = $perf['failed_orders'] + ($success ? 0 : 1);
            
            // Rolling average for response time
            $old_avg = (int)($perf['avg_response_ms'] ?? 5000);
            $new_avg = $response_ms > 0 ? (int)(($old_avg * ($new_total - 1) + $response_ms) / $new_total) : $old_avg;

            $this->db->update('supplier_performance', [
                'total_orders' => $new_total,
                'success_orders' => $new_success,
                'failed_orders' => $new_failed,
                'avg_response_ms' => $new_avg,
                'last_success' => $success ? date('Y-m-d H:i:s') : $perf['last_success'],
                'last_failure' => $success ? $perf['last_failure'] : date('Y-m-d H:i:s'),
            ], "supplier_id = " . intval($supplier_id));
        }
    }

    /**
     * Get routing statistics for dashboard
     */
    public function getStats() {
        return [
            'total_suppliers' => $this->db->num_rows("SELECT id FROM suppliers WHERE status = 1"),
            'active_rules' => $this->db->num_rows("SELECT id FROM smart_routing_rules WHERE status = 1"),
            'routes_today' => $this->db->num_rows("SELECT id FROM routing_logs WHERE DATE(created_at) = CURDATE()"),
            'success_rate' => $this->getSuccessRate(),
            'supplier_performance' => $this->db->get_list_safe(
                "SELECT s.id, s.domain, sp.score, sp.total_orders, sp.success_orders, sp.avg_response_ms
                 FROM supplier_performance sp
                 JOIN suppliers s ON sp.supplier_id = s.id
                 ORDER BY sp.score DESC", []
            ),
        ];
    }

    private function getSuccessRate() {
        $total = $this->db->num_rows("SELECT id FROM routing_logs");
        if ($total == 0) return 100;
        $success = $this->db->num_rows("SELECT id FROM routing_logs WHERE success = 1");
        return round(($success / $total) * 100, 1);
    }

    public function getLog() {
        return $this->log;
    }
}

/**
 * Helper toàn cục — ghi nhận hiệu suất supplier sau mỗi đơn hàng.
 * Gọi sau khi đơn thành công / thất bại để Smart Router học (feedback loop).
 * An toàn: không ném exception, không phá flow mua hàng.
 */
function recordSupplierPerformance($db, $supplier_id, $success, $response_ms = 0) {
    try {
        if (!$db || $supplier_id <= 0) return;
        if (!class_exists('SmartRouter')) {
            require_once __DIR__ . '/smart_router.php';
        }
        $router = new SmartRouter($db);
        $router->updatePerformance($supplier_id, $success ? 1 : 0, $response_ms);
        // Log routing decision (success/fail) để dashboard có dữ liệu
        $router->logRoute(0, $supplier_id, $success ? 'order_success' : 'order_failed', [], $success ? 1 : 0, $response_ms);
    } catch (\Throwable $e) {
        // Không bao giờ phá flow mua hàng
        error_log('[SmartRouter] recordSupplierPerformance: ' . $e->getMessage());
    }
}
