<?php
declare(strict_types=1);
final class AppConfig {
    public static array $data = [];
    public static function init(): void {
        date_default_timezone_set((string) Env::get('APP_TIMEZONE', 'America/Mexico_City'));
        self::$data = ['name'=>Env::get('APP_NAME','Les Sens Perfumería'),'url'=>rtrim((string)Env::get('APP_URL','http://localhost'),'/'),'env'=>Env::get('APP_ENV','local'),'debug'=>Env::bool('APP_DEBUG',false),'timezone'=>date_default_timezone_get(),'locale'=>'es_MX','root'=>dirname(__DIR__),'public'=>dirname(__DIR__).'/public','storage'=>dirname(__DIR__).'/storage','uploads'=>dirname(__DIR__).'/public/uploads'];
        ini_set('default_charset', 'UTF-8');
        ini_set('display_errors', self::$data['debug'] ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', self::$data['storage'].'/logs/php-errors.log');
    }
}

