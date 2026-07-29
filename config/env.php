<?php
declare(strict_types=1);
final class Env {
    private static array $values = [];
    public static function load(string $file): void {
        if (!is_readable($file)) return;
        foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
            [$key, $value] = explode('=', $line, 2);
            $value = trim($value);
            if (strlen($value) >= 2 && (($value[0] === '"' && $value[-1] === '"') || ($value[0] === "'" && $value[-1] === "'"))) $value = substr($value, 1, -1);
            self::$values[trim($key)] = $value;
        }
    }
    public static function get(string $key, mixed $default = null): mixed { return self::$values[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default; }
    public static function bool(string $key, bool $default = false): bool { return filter_var(self::get($key, $default), FILTER_VALIDATE_BOOLEAN); }
    public static function int(string $key, int $default): int { return (int) self::get($key, $default); }
}

