<?php
declare(strict_types=1);
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = ''): string { return AppConfig::$data['url'].'/'.ltrim($path,'/'); }
function asset(string $path): string { return url('assets/'.ltrim($path,'/')); }
function media_url(?string $path, string $fallback='images/placeholders/perfume.svg'): string { if(!$path)return asset($fallback);if(str_starts_with($path,'http://')||str_starts_with($path,'https://')||str_starts_with($path,'/'))return $path;return url(ltrim($path,'/')); }
function redirect(string $path): never { header('Location: '.(str_starts_with($path,'http')?$path:url($path))); exit; }
function csrf_token(): string { $lifetime=max(300,Env::int('CSRF_TOKEN_LIFETIME',7200));if(empty($_SESSION['_csrf'])||time()-(int)($_SESSION['_csrf_created_at']??0)>$lifetime){$_SESSION['_csrf']=bin2hex(random_bytes(32));$_SESSION['_csrf_created_at']=time();}return $_SESSION['_csrf']; }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="'.e(csrf_token()).'">'; }
function verify_csrf(): bool { return isset($_POST['_csrf'],$_SESSION['_csrf']) && hash_equals((string)$_SESSION['_csrf'],(string)$_POST['_csrf']) && time()-(int)($_SESSION['_csrf_created_at']??0)<=max(300,Env::int('CSRF_TOKEN_LIFETIME',7200)); }
function rotate_csrf(): string { $_SESSION['_csrf']=bin2hex(random_bytes(32));$_SESSION['_csrf_created_at']=time();return $_SESSION['_csrf']; }
function flash(string $key, ?string $value = null): mixed { if($value!==null){$_SESSION['_flash'][$key]=$value;return null;}$v=$_SESSION['_flash'][$key]??null;unset($_SESSION['_flash'][$key]);return $v; }
function admin_security_headers(): void { header('X-Content-Type-Options: nosniff');header('X-Frame-Options: SAMEORIGIN');header('Referrer-Policy: strict-origin-when-cross-origin');header('Permissions-Policy: camera=(), microphone=(), geolocation=()');header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self'; img-src 'self' data:; form-action 'self'; frame-ancestors 'self'");header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');header('Pragma: no-cache'); }
function view(string $name, array $data = []): string { extract($data); ob_start(); require AppConfig::$data['root'].'/views/'.$name.'.php'; return (string)ob_get_clean(); }
function render(string $name,array $data=[]): never { echo view($name,$data); exit; }
function old(string $key,string $default=''): string { return e($_SESSION['_old'][$key]??$default); }
function phone_href(): string { return 'tel:'.preg_replace('/\D+/','',(string)Env::get('PHONE_NUMBER','')); }
function whatsapp_href(): string { return 'https://wa.me/'.preg_replace('/\D+/','',(string)Env::get('WHATSAPP_NUMBER','')).'?text='.rawurlencode('Hola, vi la página de Les Sens Perfumería y me gustaría recibir información.'); }
