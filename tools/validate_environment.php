<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$required=['pdo_mysql','mbstring','fileinfo','json','openssl','session','filter','hash','dom','zip'];$modules=array_map('strtolower',get_loaded_extensions()) ?: [];$missing=array_values(array_diff($required,$modules));
$checks=['php_version'=>PHP_VERSION,'php_ok'=>version_compare(PHP_VERSION,'8.2.0','>='),'extensions_missing'=>$missing,'storage_writable'=>is_writable(AppConfig::$data['storage']),'uploads_writable'=>is_writable(AppConfig::$data['uploads']),'database_available'=>Database::available(),'env'=>Env::get('APP_ENV','local'),'debug'=>Env::bool('APP_DEBUG',false),'app_url'=>Env::get('APP_URL',''),'admin_hash_configured'=>Env::get('ADMIN_PASSWORD_HASH','')!=='','security_pepper_configured'=>Env::get('SECURITY_PEPPER','')!==''];
$blockers=[];if(!$checks['php_ok'])$blockers[]='php_version';if($missing)$blockers[]='extensions_missing';if(!$checks['database_available'])$blockers[]='database_available';if($checks['env']==='production'&&$checks['debug'])$blockers[]='production_debug';if($checks['env']==='production'&&str_contains((string)$checks['app_url'],'DOMINIO_CONFIRMADO'))$blockers[]='app_url_placeholder';$checks['blockers']=$blockers;
echo json_encode($checks,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).PHP_EOL;exit($checks['blockers']?1:0);
