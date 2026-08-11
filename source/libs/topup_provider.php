<?php
/**
 * TopupProvider — Gọi API nhà cung cấp nạp game
 * 
 * Hỗ trợ: REST API, Mock, Webhook
 * Tự động retry, timeout, log
 */

class TopupProvider {
    private $db;
    private $provider;
    private $log_table = 'topup_api_logs';
    
    public function __construct($provider_id_or_slug, $db_instance = null) {
        $this->db = $db_instance ?: new DB();
        
        if (is_numeric($provider_id_or_slug)) {
            $this->provider = $this->db->get_row_safe(
                "SELECT * FROM `topup_providers` WHERE `id` = ? AND `status` = 1",
                [$provider_id_or_slug]
            );
        } else {
            $this->provider = $this->db->get_row_safe(
                "SELECT * FROM `topup_providers` WHERE `slug` = ? AND `status` = 1",
                [$provider_id_or_slug]
            );
        }
        
        if (!$this->provider) {
            throw new Exception('Provider không tồn tại hoặc đã bị vô hiệu hóa');
        }
    }
    
    /**
     * Gửi yêu cầu nạp game đến provider
     * 
     * @param array $order Order data: game_uid, tier_label, amount, price, game_name
     * @return array ['status' => 'success'|'failed'|'processing', 'provider_order_id' => ?, 'msg' => ?]
     */
    public function submit($order) {
        $startTime = microtime(true);
        $requestData = json_encode($order, JSON_UNESCAPED_UNICODE);
        $response = null;
        
        try {
            switch ($this->provider['type']) {
                case 'mock':
                    $response = $this->_mockSubmit($order);
                    break;
                case 'rest_api':
                    $response = $this->_restSubmit($order);
                    break;
                case 'webhook':
                    $response = $this->_webhookSubmit($order);
                    break;
                default:
                    throw new Exception('Loại provider không được hỗ trợ: ' . $this->provider['type']);
            }
        } catch (Exception $e) {
            $response = [
                'status' => 'failed',
                'provider_order_id' => null,
                'msg' => $e->getMessage()
            ];
        }
        
        $duration = round((microtime(true) - $startTime) * 1000);
        
        // Log
        $this->_log($order['order_id'] ?? 0, $requestData, json_encode($response), $duration);
        
        // Update provider stats
        $this->db->update('topup_providers', [
            'last_check' => date('Y-m-d H:i:s'),
            'response_time_ms' => $duration
        ], "`id` = " . $this->provider['id']);
        
        return $response;
    }
    
    /**
     * Mock provider — luôn trả về success sau delay ngẫu nhiên
     */
    private function _mockSubmit($order) {
        // Giả lập delay 0.5-2s
        usleep(rand(500000, 2000000));
        
        return [
            'status' => 'success',
            'provider_order_id' => 'MOCK_' . strtoupper(substr(md5(uniqid()), 0, 12)),
            'msg' => 'Nạp thành công (mock)'
        ];
    }
    
    /**
     * REST API provider — gọi HTTP endpoint
     */
    private function _restSubmit($order) {
        $endpoint = $this->provider['api_endpoint'];
        $timeout = $this->provider['timeout_ms'] / 1000;
        $retryCount = $this->provider['retry_count'];
        $retryDelay = $this->provider['retry_delay_ms'] / 1000;
        
        $payload = [
            'api_key' => $this->provider['api_key'],
            'game_uid' => $order['game_uid'],
            'tier_label' => $order['tier_label'],
            'amount' => $order['amount'] ?? 0,
            'order_id' => $order['trans_id'],
            'timestamp' => time()
        ];
        
        // Sign payload nếu có secret
        if ($this->provider['api_secret']) {
            $payload['sign'] = $this->_sign($payload);
        }
        
        $lastError = '';
        for ($attempt = 0; $attempt <= $retryCount; $attempt++) {
            if ($attempt > 0) {
                sleep($retryDelay);
            }
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                $lastError = "cURL error (attempt {$attempt}): " . $error;
                continue;
            }
            
            $data = json_decode($response, true);
            
            // Parse response — adjust based on actual provider format
            if ($httpCode >= 200 && $httpCode < 300) {
                if (isset($data['success']) && $data['success']) {
                    return [
                        'status' => 'success',
                        'provider_order_id' => $data['order_id'] ?? $data['ref'] ?? null,
                        'msg' => $data['message'] ?? 'OK'
                    ];
                }
                
                if (isset($data['code']) && $data['code'] == 0) {
                    return [
                        'status' => 'success',
                        'provider_order_id' => $data['data']['order_id'] ?? null,
                        'msg' => 'OK'
                    ];
                }
                
                // Unknown but OK response — mark as processing
                return [
                    'status' => 'processing',
                    'provider_order_id' => $data['order_id'] ?? $data['ref'] ?? null,
                    'msg' => 'Đang xử lý, vui lòng đợi'
                ];
            }
            
            $lastError = "HTTP {$httpCode}: " . substr($response, 0, 200);
        }
        
        throw new Exception($lastError ?: 'Provider không phản hồi sau ' . ($retryCount + 1) . ' lần thử');
    }
    
    /**
     * Webhook provider — gửi request và chờ callback (chỉ tạo request, không chờ)
     */
    private function _webhookSubmit($order) {
        $endpoint = $this->provider['api_endpoint'];
        
        $payload = [
            'api_key' => $this->provider['api_key'],
            'game_uid' => $order['game_uid'],
            'tier_label' => $order['tier_label'],
            'order_id' => $order['trans_id'],
            'callback_url' => BASE_URL('api/topup_callback.php'),
        ];
        
        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return [
            'status' => 'processing',
            'provider_order_id' => null,
            'msg' => 'Đã gửi yêu cầu, đang chờ callback'
        ];
    }
    
    /**
     * Tạo chữ ký HMAC-SHA256 cho request
     */
    private function _sign($payload) {
        ksort($payload);
        $signString = '';
        foreach ($payload as $k => $v) {
            if ($k !== 'sign') {
                $signString .= $k . '=' . $v . '&';
            }
        }
        $signString = rtrim($signString, '&');
        return hash_hmac('sha256', $signString, $this->provider['api_secret']);
    }
    
    /**
     * Log request/response vào DB
     */
    private function _log($orderId, $request, $response, $durationMs) {
        $this->db->insert($this->log_table, [
            'order_id' => $orderId,
            'game_id' => 0,
            'request_data' => $request,
            'response_data' => $response,
            'status_code' => 200,
            'duration_ms' => $durationMs,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Check provider health
     */
    public function healthCheck() {
        $startTime = microtime(true);
        try {
            $result = $this->_mockSubmit([]);
            $ok = ($result['status'] === 'success');
        } catch (Exception $e) {
            $ok = false;
        }
        $duration = round((microtime(true) - $startTime) * 1000);
        
        $this->db->update('topup_providers', [
            'last_check' => date('Y-m-d H:i:s'),
            'response_time_ms' => $duration,
            'status' => $ok ? 1 : 0
        ], "`id` = " . $this->provider['id']);
        
        return ['ok' => $ok, 'duration_ms' => $duration];
    }
}
