<?php
/**
 * CompetitorResearch — Crawl cấu trúc sản phẩm & giá từ thị trường
 * Digital Commerce OS
 * 
 * Nguồn: CheapShark API (free, aggregate G2A, Steam, GreenManGaming, Fanatical...)
 * Docs: https://www.cheapshark.com/api
 * 
 * Flow: Crawl deals → lưu competitor_products → so sánh với products của mình → import thành sản phẩm mới
 */

class CompetitorResearch {
    private $db;
    private $api_base = 'https://www.cheapshark.com/api/1.0';

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Crawl deals từ CheapShark (mô phỏng cấu trúc G2A/Eneba/Kinguin)
     */
    public function crawlDeals($limit = 30, $min_discount = 20) {
        $url = $this->api_base . '/deals?limit=' . intval($limit) . '&sortBy=Savings&minDiscount=' . intval($min_discount);
        $data = $this->httpGet($url);
        if (!$data) return ['status' => 'error', 'msg' => 'Cannot reach CheapShark API'];

        $imported = 0;
        $updated = 0;

        foreach ($data as $deal) {
            $external_id = 'CS-' . $deal['dealID'];
            $name = $deal['title'];
            $price = (float)$deal['salePrice'];
            $retail = (float)$deal['normalPrice'];
            $discount = (float)$deal['savings'];

            // Convert USD → VND (approx rate)
            $price_vnd = $price * 25500;
            $retail_vnd = $retail * 25500;

            $existing = $this->db->get_row_safe(
                "SELECT id FROM competitor_products WHERE source = 'cheapshark' AND external_id = ?", [$external_id]);

            if ($existing) {
                $this->db->update('competitor_products', [
                    'price' => $price_vnd,
                    'retail_price' => $retail_vnd,
                    'scraped_at' => date('Y-m-d H:i:s'),
                ], " `id` = " . intval($existing['id']));
                $updated++;
            } else {
                $this->db->insert('competitor_products', [
                    'source' => 'cheapshark',
                    'external_id' => $external_id,
                    'name' => $name,
                    'slug' => $this->slugify($name),
                    'platform' => 'Steam',
                    'region' => 'GLOBAL',
                    'product_type' => 'game_key',
                    'price' => $price_vnd,
                    'retail_price' => $retail_vnd,
                    'offers_count' => 1,
                    'url' => 'https://www.cheapshark.com/redirect?dealID=' . $deal['dealID'],
                    'image_url' => $deal['thumb'] ?? '',
                ]);
                $imported++;
            }
        }

        return ['status' => 'success', 'imported' => $imported, 'updated' => $updated, 'total_crawled' => count($data)];
    }

    /**
     * Search sản phẩm cụ thể (VD: "Elden Ring")
     */
    public function searchGames($keyword, $limit = 10) {
        $url = $this->api_base . '/games?title=' . urlencode($keyword) . '&limit=' . intval($limit);
        $data = $this->httpGet($url);
        if (!$data) return [];

        $results = [];
        foreach ($data as $game) {
            $results[] = [
                'game_id' => $game['gameID'],
                'name' => $game['external'],
                'cheapest' => (float)$game['cheapest'] * 25500,
                'steam_app_id' => $game['steamAppID'] ?? null,
            ];
        }
        return $results;
    }

    /**
     * Import competitor product → products của mình (tạo listing mới)
     */
    public function importToShop($competitor_id, $markup_percent = 15) {
        $cp = $this->db->get_row_safe("SELECT * FROM competitor_products WHERE id = ?", [$competitor_id]);
        if (!$cp) return ['status' => 'error', 'msg' => 'Competitor product not found'];
        if ($cp['imported_product_id'] > 0) return ['status' => 'error', 'msg' => 'Already imported'];

        // Giá bán = giá competitor + markup
        $sell_price = $cp['price'] * (1 + $markup_percent / 100);
        $cost = $cp['price']; // giá vốn = giá competitor

        // Category: Game Key
        $cat = $this->db->get_row_safe("SELECT id FROM categories WHERE slug = 'game-key'", []);
        $cat_id = $cat ? $cat['id'] : 0;

        $code = 'IMP-' . strtoupper(substr(md5($cp['name']), 0, 6));
        $product_id = $this->db->insert('products', [
            'stt' => 1,
            'code' => $code,
            'user_id' => 1,
            'name' => $cp['name'] . ' (Global Key)',
            'slug' => $cp['slug'] . '-imported',
            'short_desc' => 'Key bản quyền toàn cầu — nhập từ ' . $cp['source'] . ', giao ngay',
            'price' => $sell_price,
            'cost' => $cost,
            'discount' => 0,
            'min' => 1, 'max' => 10,
            'sold' => 0,
            'category_id' => $cat_id,
            'status' => 1,
            'create_gettime' => date('Y-m-d H:i:s'),
            'update_gettime' => date('Y-m-d H:i:s'),
            'supplier_id' => 0,
            'api_stock' => 0,
            'order_by' => 1,
            'allow_api' => 1,
            'hide_in_shop' => 0,
            'preview_uid' => 0,
            'pending' => 0,
        ]);

        // Đánh dấu đã import
        $this->db->update('competitor_products', ['imported_product_id' => $product_id], " `id` = " . intval($competitor_id));

        return ['status' => 'success', 'msg' => 'Imported as product #' . $product_id, 'product_id' => $product_id, 'sell_price' => $sell_price];
    }

    /**
     * Xóa deal competitor (chỉ khi chưa import vào shop)
     */
    public function deleteDeal($competitor_id) {
        $cp = $this->db->get_row_safe("SELECT * FROM competitor_products WHERE id = ?", [$competitor_id]);
        if (!$cp) return ['status' => 'error', 'msg' => 'Deal không tồn tại'];
        if ($cp['imported_product_id'] > 0) {
            return ['status' => 'error', 'msg' => 'Deal đã import vào shop (SP #' . $cp['imported_product_id'] . ') — xóa sản phẩm trong mục Sản phẩm trước'];
        }
        $this->db->remove('competitor_products', "id = " . intval($competitor_id));
        return ['status' => 'success', 'msg' => 'Đã xóa deal "' . $cp['name'] . '"'];
    }

    /**
     * So sánh giá products của mình vs competitor
     */
    public function comparePrices() {
        $my_products = $this->db->get_list_safe(
            "SELECT id, name, price, cost FROM products WHERE status = 1 AND code LIKE 'IMP-%'", []);
        $comparisons = [];

        foreach ($my_products as $p) {
            // Tìm competitor product tương ứng (qua imported link)
            $cp = $this->db->get_row_safe(
                "SELECT * FROM competitor_products WHERE imported_product_id = ?", [$p['id']]);
            if (!$cp) continue;

            $diff_percent = $cp['price'] > 0 ? (($p['price'] - $cp['price']) / $cp['price']) * 100 : 0;
            $comparisons[] = [
                'product_id' => $p['id'],
                'name' => $p['name'],
                'my_price' => (int)$p['price'],
                'competitor_price' => (int)$cp['price'],
                'diff_percent' => round($diff_percent, 1),
                'competitive' => $diff_percent <= 5,
            ];
        }
        return $comparisons;
    }

    /**
     * Stats cho dashboard
     */
    public function getStats() {
        return [
            'total_competitor_products' => $this->db->num_rows("SELECT id FROM competitor_products"),
            'imported_count' => $this->db->num_rows("SELECT id FROM competitor_products WHERE imported_product_id > 0"),
            'sources' => $this->db->get_list_safe(
                "SELECT source, COUNT(*) as cnt FROM competitor_products GROUP BY source", []),
            'recent' => $this->db->get_list_safe(
                "SELECT * FROM competitor_products ORDER BY scraped_at DESC LIMIT 20", []),
        ];
    }

    private function httpGet($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'DigitalCommerceOS-ResearchBot/1.0 (contact@digitalcommerce.local)',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        if (!$resp) return null;
        return json_decode($resp, true);
    }

    private function slugify($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
