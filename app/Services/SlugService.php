<?php
declare(strict_types=1);
final class SlugService {
    public static function make(string $value): string {
        $value=trim($value); $value=class_exists('Transliterator') ? transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9]+', $value) : iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$value);
        $value=strtolower((string)$value); $value=preg_replace('/[^a-z0-9]+/','-',(string)$value) ?? ''; return trim($value,'-');
    }
    public static function normalized(string $value): string { return self::make($value); }
}
