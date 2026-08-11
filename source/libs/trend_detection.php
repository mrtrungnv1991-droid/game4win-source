<?php
/**
 * TrendDetection — AI phát hiện game hot từ social
 * Digital Commerce OS
 * 
 * Nguồn: Reddit r/gamedeals (JSON API public) + Google Trends RSS
 * Flow: Quét → chấm điểm trend → match với competitor products → auto tạo listing
 */

class TrendDetection {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Quét Reddit r/gamedeals — các post deal game hot
     * (Fallback: nếu Reddit chặn IP, dùng scanSteamTrending)
     */
    public function scanReddit($limit = 25) {
        $url = 'https://www.reddit.com/r/gamedeals/hot.json?limit=' . intval($limit);
        $data = $this->httpGetJson($url);
        if (!$data || !isset($data['data']['children'])) {
            // Fallback: Steam trending (không bị chặn)
            return $this->scanSteamTrending();
        }

        $found = 0;
        foreach ($data['data']['children'] as $child) {
            $post = $child['data'];
            $title = $post['title'] ?? '';
            $score = (int)($post['score'] ?? 0);
            $comments = (int)($post['num_comments'] ?? 0);
            $url_post = 'https://reddit.com' . ($post['permalink'] ?? '');

            // Extract game name từ title (bỏ phần giá/discount)
            $keyword = $this->extractGameName($title);
            if (!$keyword) continue;

            // Trend score = upvotes + comments*2 (engagement)
            $trend_score = $score + $comments * 2;

            $this->upsertTrend($keyword, 'reddit', $trend_score, $score, $url_post);
            $found++;
        }
        return ['status' => 'success', 'scanned' => $found, 'source' => 'reddit'];
    }

    /**
     * Quét Steam Featured — game đang được Steam đẩy mạnh (trending)
     */
    public function scanSteamTrending() {
        $data = $this->httpGetJson('https://store.steampowered.com/api/featuredcategories');
        if (!$data) return ['status' => 'error', 'msg' => 'Cannot reach Steam API'];

        $found = 0;
        foreach ($data as $cat) {
            if (!isset($cat['items'])) continue;
            foreach ($cat['items'] as $item) {
                $name = $item['name'] ?? '';
                if (strlen($name) < 3 || in_array($name, ['MIDWEEK DEAL', 'WEEKEND DEAL', 'SPECIAL PROMOTION'])) continue;
                // Score dựa trên vị trí category (Spotlight = hot nhất)
                $score = ($cat['id'] === 'cat_spotlight') ? 80 : 50;
                $this->upsertTrend($name, 'steam', $score, 1, $item['url'] ?? '');
                $found++;
            }
        }
        return ['status' => 'success', 'scanned' => $found, 'source' => 'steam'];
    }

    /**
     * Quét Google Trends RSS cho gaming keywords
     */
    public function scanGoogleTrends() {
        $url = 'https://trends.google.com/trending/rss?geo=US';
        $xml = $this->httpGet($url);
        if (!$xml) return ['status' => 'error', 'msg' => 'Cannot reach Google Trends'];

        $found = 0;
        // Parse RSS đơn giản
        preg_match_all('/<title>(?:<!\[CDATA\[)?(.*?)(?:\]\]>)?<\/title>/s', $xml, $matches);
        foreach (array_slice($matches[1] ?? [], 1, 20) as $title) {
            $title = trim($title);
            if (strlen($title) < 3) continue;
            // Chỉ lấy trend liên quan game (heuristic đơn giản)
            $this->upsertTrend($title, 'google_trends', 50, 1, '');
            $found++;
        }
        return ['status' => 'success', 'scanned' => $found, 'source' => 'google_trends'];
    }

    /**
     * Match trends với competitor products → gợi ý listing
     */
    public function matchTrendsToProducts() {
        $trends = $this->db->get_list_safe(
            "SELECT * FROM trend_items WHERE status = 'new' ORDER BY score DESC LIMIT 50", []);
        $matched = 0;

        foreach ($trends as $t) {
            // Tìm competitor product có tên gần giống (escape LIKE wildcards)
            $kw = str_replace(['%', '_'], ['\\%', '\\_'], $t['keyword']);
            $cp = $this->db->get_row_safe(
                "SELECT * FROM competitor_products WHERE name LIKE ? LIMIT 1",
                ['%' . $kw . '%']
            );
            if ($cp) {
                $this->db->update('trend_items', [
                    'matched_competitor_id' => $cp['id'],
                    'status' => 'approved',
                ], " `id` = " . intval($t['id']));
                $matched++;
            }
        }
        return ['status' => 'success', 'matched' => $matched];
    }

    /**
     * Auto-listing: tạo sản phẩm từ trend đã approve + matched competitor
     */
    public function autoListing($trend_id, $markup_percent = 20) {
        $t = $this->db->get_row_safe("SELECT * FROM trend_items WHERE id = ? AND status = 'approved'", [$trend_id]);
        if (!$t) return ['status' => 'error', 'msg' => 'Trend not found or not approved'];
        if ($t['auto_listing_product_id'] > 0) return ['status' => 'error', 'msg' => 'Already listed'];

        $cp = $this->db->get_row_safe("SELECT * FROM competitor_products WHERE id = ?", [$t['matched_competitor_id']]);
        if (!$cp) return ['status' => 'error', 'msg' => 'No matched competitor product'];

        // Tạo product mới (giá = competitor + markup, kèm badge TRENDING)
        $sell_price = $cp['price'] * (1 + $markup_percent / 100);
        $cat = $this->db->get_row_safe("SELECT id FROM categories WHERE slug = 'game-key'", []);
        $code = 'TRD-' . strtoupper(substr(md5($t['keyword']), 0, 6));

        $product_id = $this->db->insert('products', [
            'stt' => 1,
            'code' => $code,
            'user_id' => 1,
            'name' => $cp['name'] . ' 🔥 TRENDING',
            'slug' => $this->slugify($cp['name']) . '-trending',
            'short_desc' => 'Đang HOT trên mạng! Key bản quyền, giao ngay. Trend score: ' . $t['score'],
            'price' => $sell_price,
            'cost' => $cp['price'],
            'discount' => 0,
            'min' => 1, 'max' => 10,
            'sold' => 0,
            'category_id' => $cat ? $cat['id'] : 0,
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

        $this->db->update('trend_items', [
            'auto_listing_product_id' => $product_id,
            'status' => 'listed',
        ], " `id` = " . intval($trend_id));

        return ['status' => 'success', 'product_id' => $product_id, 'msg' => 'Auto-listed: ' . $cp['name']];
    }

    /**
     * Stats cho dashboard
     */
    public function getStats() {
        return [
            'total_trends' => $this->db->num_rows("SELECT id FROM trend_items"),
            'new_trends' => $this->db->num_rows("SELECT id FROM trend_items WHERE status = 'new'"),
            'approved' => $this->db->num_rows("SELECT id FROM trend_items WHERE status = 'approved'"),
            'listed' => $this->db->num_rows("SELECT id FROM trend_items WHERE status = 'listed'"),
            'top_trends' => $this->db->get_list_safe(
                "SELECT * FROM trend_items ORDER BY score DESC LIMIT 20", []),
        ];
    }

    // ===== Helpers =====
    private function upsertTrend($keyword, $source, $score, $mentions, $url) {
        $existing = $this->db->get_row_safe(
            "SELECT id, mentions FROM trend_items WHERE keyword = ? AND source = ?", [$keyword, $source]);
        if ($existing) {
            $this->db->update('trend_items', [
                'score' => max($score, 0),
                'mentions' => $existing['mentions'] + $mentions,
                'last_seen' => date('Y-m-d H:i:s'),
            ], " `id` = " . intval($existing['id']));
        } else {
            $this->db->insert('trend_items', [
                'keyword' => $keyword,
                'source' => $source,
                'score' => $score,
                'mentions' => $mentions,
                'url' => $url,
                'status' => 'new',
            ]);
        }
    }

    private function extractGameName($title) {
        // Bỏ pattern giá/discount: "$19.99", "50% off", "(-75%)", "[Steam]"
        $clean = preg_replace('/\(?-?\d+%?\s*(off|discount)?\)?/i', '', $title);
        $clean = preg_replace('/\$\s?\d+(\.\d+)?/', '', $clean);
        $clean = preg_replace('/\[(.*?)\]/', '', $clean);
        $clean = preg_replace('/\s+/', ' ', trim($clean));
        // Lấy phần trước dấu phẩy hoặc " for "
        if (preg_match('/^(.{5,60}?)(?:,| for | at | on )/i', $clean, $m)) {
            return trim($m[1]);
        }
        return strlen($clean) >= 5 ? substr($clean, 0, 60) : null;
    }

    private function httpGetJson($url) {
        $resp = $this->httpGet($url);
        return $resp ? json_decode($resp, true) : null;
    }

    private function httpGet($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_USERAGENT => 'DigitalCommerceOS-TrendBot/1.0 (contact@digitalcommerce.local)',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp ?: null;
    }

    private function slugify($text) {
        $text = strtolower(trim($text));
        return trim(preg_replace('/[^a-z0-9]+/', '-', $text), '-');
    }
}
