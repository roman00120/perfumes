<?php
declare(strict_types=1);
final class HealthService {
    public static function check(?PDO $db, bool $ready = false): array {
        $checks=['app'=>'ok','storage'=>is_writable(AppConfig::$data['storage'])?'ok':'fail','cache'=>is_writable(AppConfig::$data['storage'].'/cache')?'ok':'fail'];
        if ($ready) { try { $db?->query('SELECT 1'); $checks['database']=$db?'ok':'fail'; } catch(Throwable $e) { $checks['database']='fail'; } }
        $ok=!in_array('fail',$checks,true); return ['status'=>$ok?'ok':'fail','version'=>(string)Env::get('APP_VERSION','1.0.0'),'checks'=>$checks,'timestamp'=>gmdate('c')];
    }
}
