<?php
declare(strict_types=1);
final class Validator {
    public static function required(mixed $value): bool { return is_string($value)?trim($value)!=='':$value!==null; }
    public static function length(string $value,int $min=0,?int $max=null): bool { $n=mb_strlen($value);return $n >= $min && ($max===null || $n <= $max); }
    public static function slug(string $value): bool { return (bool)preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$value); }
    public static function email(string $value): bool { return filter_var($value,FILTER_VALIDATE_EMAIL)!==false; }
    public static function url(string $value): bool { return filter_var($value,FILTER_VALIDATE_URL)!==false; }
    public static function phone(string $value): bool { return (bool)preg_match('/^[0-9+() .-]{7,25}$/',$value); }
    public static function nonNegative(mixed $value): bool { return is_numeric($value) && (float)$value>=0; }
    public static function allowed(mixed $value,array $allowed): bool { return in_array($value,$allowed,true); }
}
