<?php
/**
 * GroupBuy — Crowd-buying engine
 * Digital Commerce OS
 * 
 * Flow: Deals created → Users join & pay → Target reached → Auto-purchase → Distribute keys
 */

class GroupBuy {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Create a new group buy deal
     */
    public function createDeal($data) {
        $required = ['title', 'product_name', 'original_price', 'group_price', 'min_participants'];
        foreach ($required as $f) {
            if (empty($data[$f])) {
                return ['status' => 'error', 'msg' => "Missing field: $f"];
            }
        }

        $discount = round((1 - $data['group_price'] / $data['original_price']) * 100);

        $insert = [
            'title' => $data['title'],
            'product_name' => $data['product_name'],
            'product_description' => $data['product_description'] ?? '',
            'image_url' => $data['image_url'] ?? '',
            'original_price' => $data['original_price'],
            'group_price' => $data['group_price'],
            'min_participants' => $data['min_participants'],
            'max_participants' => $data['max_participants'] ?? 50,
            'discount_percent' => $discount,
            'category' => $data['category'] ?? 'game_key',
            'product_type' => $data['product_type'] ?? 'game_key',
            'status' => $data['status'] ?? 'draft',
            'start_date' => $data['start_date'] ?? date('Y-m-d H:i:s'),
            'end_date' => $data['end_date'] ?? date('Y-m-d H:i:s', strtotime('+7 days')),
            'supplier_id' => $data['supplier_id'] ?? 0,
            'supplier_sku' => $data['supplier_sku'] ?? '',
            'auto_fulfill' => $data['auto_fulfill'] ?? 1,
        ];

        $insert_id = $this->db->insert('group_buy_deals', $insert);
        return ['status' => 'success', 'msg' => 'Deal created', 'deal_id' => $insert_id];
    }

    /**
     * Get active deals for client display
     */
    public function getActiveDeals($category = '', $limit = 20) {
        $sql = "SELECT * FROM group_buy_deals WHERE status IN ('active','filled')";
        $params = [];
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        $sql .= " ORDER BY discount_percent DESC, current_participants DESC LIMIT " . intval($limit);
        return $this->db->get_list_safe($sql, $params);
    }

    /**
     * Get single deal with participant count
     */
    public function getDeal($deal_id) {
        $deal = $this->db->get_row_safe(
            "SELECT * FROM group_buy_deals WHERE id = ?", [$deal_id]
        );
        if (!$deal) return null;

        $deal['participants'] = $this->db->get_list_safe(
            "SELECT gbp.*, u.username 
             FROM group_buy_participants gbp 
             LEFT JOIN users u ON gbp.user_id = u.id 
             WHERE gbp.deal_id = ? 
             ORDER BY gbp.joined_at DESC", [$deal_id]
        );
        $deal['spots_left'] = max(0, $deal['min_participants'] - $deal['current_participants']);
        $deal['progress_percent'] = $deal['min_participants'] > 0 
            ? round(($deal['current_participants'] / $deal['min_participants']) * 100) 
            : 0;

        return $deal;
    }

    /**
     * User joins a group buy deal
     */
    public function joinDeal($deal_id, $user_id, $quantity = 1) {
        $deal = $this->db->get_row_safe(
            "SELECT * FROM group_buy_deals WHERE id = ?", [$deal_id]
        );
        if (!$deal) {
            return ['status' => 'error', 'msg' => 'Deal not found'];
        }
        if (!in_array($deal['status'], ['active', 'filled'])) {
            return ['status' => 'error', 'msg' => 'Deal is not active'];
        }

        // Check if user already joined
        $existing = $this->db->get_row_safe(
            "SELECT id FROM group_buy_participants WHERE deal_id = ? AND user_id = ? AND payment_status != 'refunded'",
            [$deal_id, $user_id]
        );
        if ($existing) {
            return ['status' => 'error', 'msg' => 'You already joined this deal'];
        }

        // Check max participants (không nhận quá slot)
        if ($deal['max_participants'] > 0 && $deal['current_participants'] >= $deal['max_participants']) {
            return ['status' => 'error', 'msg' => 'Deal đã đủ người tham gia'];
        }

        // Check user balance
        $user = $this->db->get_row_safe("SELECT money FROM users WHERE id = ?", [$user_id]);
        $total_price = $deal['group_price'] * $quantity;

        if ($user['money'] < $total_price) {
            return ['status' => 'error', 'msg' => 'Insufficient balance. Need ' . number_format($total_price) . 'đ'];
        }

        // Deduct balance
        $this->db->update('users', [
            'money' => $user['money'] - $total_price
        ], "id = " . intval($user_id));

        // Record transaction
        $this->db->insert('dongtien', [
            'user_id' => $user_id,
            'sotientruoc' => $user['money'],
            'sotienthaydoi' => -$total_price,
            'sotiensau' => $user['money'] - $total_price,
            'thoigian' => date('Y-m-d H:i:s'),
            'noidung' => 'Join group buy: ' . $deal['title'],
        ]);

        // Add participant
        $participant_id = $this->db->insert('group_buy_participants', [
            'deal_id' => $deal_id,
            'user_id' => $user_id,
            'quantity' => $quantity,
            'unit_price' => $deal['group_price'],
            'total_price' => $total_price,
            'payment_status' => 'paid',
        ]);

        // Update deal participant count
        $new_count = $deal['current_participants'] + 1;
        $new_status = ($new_count >= $deal['min_participants']) ? 'filled' : 'active';
        $this->db->update('group_buy_deals', [
            'current_participants' => $new_count,
            'status' => $new_status,
        ], "id = " . intval($deal_id));

        return [
            'status' => 'success',
            'msg' => 'Successfully joined! Deal is ' . round(($new_count / $deal['min_participants']) * 100) . '% full',
            'filled' => ($new_status === 'filled'),
            'participant_id' => $participant_id,
        ];
    }

    /**
     * Auto-fulfill: when deal is filled, purchase from supplier and distribute keys
     */
    public function fulfillDeal($deal_id) {
        $deal = $this->db->get_row_safe(
            "SELECT * FROM group_buy_deals WHERE id = ? AND status = 'filled'", [$deal_id]
        );
        if (!$deal) {
            return ['status' => 'error', 'msg' => 'Deal not ready for fulfillment'];
        }

        $participants = $this->db->get_list_safe(
            "SELECT * FROM group_buy_participants WHERE deal_id = ? AND payment_status = 'paid' AND key_delivered = 0",
            [$deal_id]
        );

        if (empty($participants)) {
            return ['status' => 'error', 'msg' => 'No pending participants'];
        }

        $fulfilled = 0;
        $errors = [];

        // Use SmartRouter if available
        if (class_exists('SmartRouter') && $deal['supplier_id'] == 0) {
            $router = new SmartRouter($this->db);
            $best = $router->findBestSupplier(0, $deal['group_price']);
            if ($best) {
                $deal['supplier_id'] = $best['supplier_id'];
            }
        }

        foreach ($participants as $p) {
            try {
                // Generate key (mock for demo, real would call supplier API)
                $key = $this->generateKey($deal);
                
                // Update participant
                $this->db->update('group_buy_participants', [
                    'key_delivered' => 1,
                    'delivered_at' => date('Y-m-d H:i:s'),
                ], "id = " . intval($p['id']));

                // Log key delivery
                $this->db->insert('logs', [
                    'user_id' => $p['user_id'],
                    'ip' => '127.0.0.1',
                    'device' => 'GroupBuy System',
                    'createdate' => date('Y-m-d H:i:s'),
                    'action' => "🎁 Group Buy Key Delivered: {$deal['title']} — Key: $key",
                ]);

                $fulfilled++;
            } catch (Exception $e) {
                $errors[] = "Participant #{$p['id']}: " . $e->getMessage();
            }
        }

        // Mark deal as completed
        if ($fulfilled == count($participants)) {
            $this->db->update('group_buy_deals', ['status' => 'completed'], "id = " . intval($deal_id));
        }

        return [
            'status' => 'success',
            'fulfilled' => $fulfilled,
            'total' => count($participants),
            'errors' => $errors,
        ];
    }

    /**
     * Generate a mock key (replace with real supplier API call)
     */
    private function generateKey($deal) {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $deal['product_name']), 0, 4));
        return $prefix . '-' . strtoupper(substr(md5(uniqid() . time()), 0, 16));
    }

    /**
     * Get group buy stats for dashboard
     */
    public function getStats() {
        return [
            'active_deals' => $this->db->num_rows(
                "SELECT id FROM group_buy_deals WHERE status IN ('active','filled')"
            ),
            'total_participants' => $this->db->num_rows(
                "SELECT id FROM group_buy_participants WHERE payment_status = 'paid'"
            ),
            'completed_deals' => $this->db->num_rows(
                "SELECT id FROM group_buy_deals WHERE status = 'completed'"
            ),
            'recent_deals' => $this->db->get_list_safe(
                "SELECT * FROM group_buy_deals ORDER BY created_at DESC LIMIT 10", []
            ),
            'pending_delivery' => $this->db->num_rows(
                "SELECT id FROM group_buy_deals WHERE status = 'filled' AND auto_fulfill = 1"
            ),
        ];
    }

    /**
     * Refund participant
     */
    public function refundParticipant($participant_id) {
        $p = $this->db->get_row_safe(
            "SELECT * FROM group_buy_participants WHERE id = ?", [$participant_id]
        );
        if (!$p) return ['status' => 'error', 'msg' => 'Participant not found'];

        // Refund balance
        $user = $this->db->get_row_safe("SELECT money FROM users WHERE id = ?", [$p['user_id']]);
        $this->db->update('users', [
            'money' => $user['money'] + $p['total_price']
        ], "id = " . intval($p['user_id']));

        $this->db->update('group_buy_participants', [
            'payment_status' => 'refunded'
        ], "id = " . intval($participant_id));

        return ['status' => 'success', 'msg' => 'Refunded ' . number_format($p['total_price']) . 'đ'];
    }
}
