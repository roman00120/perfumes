<?php
declare(strict_types=1);
final class Database {
    private static ?PDO $pdo = null;
    public static function connection(): PDO {
        if (self::$pdo) return self::$pdo;
        $c = require dirname(__DIR__, 2).'/config/database.php';
        $dsn = "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4";
        self::$pdo = new PDO($dsn, (string)$c['user'], (string)$c['password'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
        return self::$pdo;
    }
    public static function available(): bool { try { self::connection(); return true; } catch (Throwable $e) { error_log('Database unavailable: '.$e->getMessage()); return false; } }
}

