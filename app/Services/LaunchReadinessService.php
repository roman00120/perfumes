<?php
declare(strict_types=1);
final class LaunchReadinessService {
    public static function evaluate(?PDO $db=null): array {
        $checks=['php_8_2'=>version_compare(PHP_VERSION,'8.2.0','>='),'debug_off'=>!Env::bool('APP_DEBUG',false),'https_url'=>str_starts_with((string)Env::get('APP_URL',''),'https://'),'admin_hash'=>password_get_info((string)Env::get('ADMIN_PASSWORD_HASH',''))['algo']!==0,'pepper'=>strlen((string)Env::get('SECURITY_PEPPER',''))>=32,'storage'=>is_writable(AppConfig::$data['storage']),'uploads'=>is_writable(AppConfig::$data['uploads']),'database'=>(bool)$db,'indexing_controlled'=>AppConfig::$data['env']==='production' ? Env::bool('SEARCH_ENGINE_INDEXING',false) : !Env::bool('SEARCH_ENGINE_INDEXING',true)];
        return ['status'=>in_array(false,$checks,true)?'bloqueado':'listo_para_produccion','checks'=>$checks,'version'=>(string)Env::get('APP_VERSION','1.0.0'),'generated_at'=>gmdate('c')];
    }
}
