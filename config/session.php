<?php
declare(strict_types=1);
$secure = Env::bool('SESSION_COOKIE_SECURE', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
$sessionPath = AppConfig::$data['storage'].'/sessions';
if (!is_dir($sessionPath)) @mkdir($sessionPath, 0750, true);
if (is_dir($sessionPath)) session_save_path($sessionPath);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', Env::bool('SESSION_COOKIE_HTTPONLY', true) ? '1' : '0');
ini_set('session.cookie_samesite', (string)Env::get('SESSION_COOKIE_SAMESITE', 'Lax'));
session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$secure,'httponly'=>Env::bool('SESSION_COOKIE_HTTPONLY',true),'samesite'=>(string)Env::get('SESSION_COOKIE_SAMESITE','Lax')]);
session_name((string)Env::get('ADMIN_SESSION_NAME', 'les_sens_admin'));
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
