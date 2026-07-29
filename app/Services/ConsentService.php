<?php
declare(strict_types=1);
final class ConsentService {
    public static function enabled(): bool { return Env::bool('CONSENT_ENABLED', true); }
    public static function allowsAnalytics(): bool { return !self::enabled() || ($_COOKIE['les_sens_consent_analytics'] ?? '') === '1'; }
    public static function allowsMarketing(): bool { return !self::enabled() || ($_COOKIE['les_sens_consent_marketing'] ?? '') === '1'; }
    public static function version(): string { return (string)Env::get('CONSENT_VERSION','1'); }
}
