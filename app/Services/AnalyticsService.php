<?php
declare(strict_types=1);
final class AnalyticsService {
    public function __construct(private PDO $db) {}
    public function record(string $event, array $data = [], ?string $path = null, ?string $entityType = null, ?int $entityId = null): bool {
        if (!Env::bool('ANALYTICS_INTERNAL_ENABLED', true) || !ConsentService::allowsAnalytics() || !preg_match('/^[a-z][a-z0-9_.-]{1,79}$/', $event)) return false;
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        $ip = Env::bool('ANALYTICS_IP_ANONYMIZATION', true) ? hash_hmac('sha256', $ip, (string)Env::get('SECURITY_PEPPER','les-sens-analytics')) : substr($ip,0,64);
        $ua = substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500);
        $token = hash('sha256', session_id().':'.gmdate('Y-m-d-H', time() / max(1, Env::int('ANALYTICS_SESSION_ROTATION_HOURS',24))));
        $q = $this->db->prepare('INSERT INTO analytics_events (event_name,event_data_json,page_path,entity_type,entity_id,session_token,anonymized_ip,user_agent) VALUES (?,?,?,?,?,?,?,?)');
        return $q->execute([$event,json_encode($data,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR),substr($path ?: parse_url($_SERVER['REQUEST_URI'] ?? '/',PHP_URL_PATH) ?: '/',0,500),$entityType,$entityId,$token,$ip,$ua]);
    }
}
