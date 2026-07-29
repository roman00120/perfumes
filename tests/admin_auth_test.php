<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$checks=[];$ok=function(string $name,bool $result)use(&$checks):void{$checks[$name]=$result;};
$ok('Hash administrativo configurado',Env::get('ADMIN_PASSWORD_HASH','')!=='' && password_get_info((string)Env::get('ADMIN_PASSWORD_HASH'))['algo']!==0);
$ok('Usuario administrativo configurado',trim((string)Env::get('ADMIN_USER',''))!=='');
$ok('Pepper configurado',strlen((string)Env::get('SECURITY_PEPPER',''))>=32);
$ok('Timeout positivo',Env::int('ADMIN_SESSION_TIMEOUT',0)>0);
$ok('Límite de intentos positivo',Env::int('ADMIN_MAX_LOGIN_ATTEMPTS',0)>0);
try{$db=Database::connection();$cols=$db->query('SHOW COLUMNS FROM admin_login_attempts')->fetchAll(PDO::FETCH_COLUMN);$ok('Columnas de seguridad',$cols && count(array_intersect(['identifier_hash','ip_hash','expires_at'],$cols))===3);$tables=$db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);$ok('Sin users',!in_array('users',$tables,true));$auth=new AdminAuthService($db);$result=$auth->attempt('usuario-de-prueba-no-real','contraseña-inválida');$ok('Login incorrecto controlado',$result['ok']===false && in_array($result['reason'],['invalid','blocked'],true));$probe='probe-'.bin2hex(random_bytes(4));for($i=0;$i<Env::int('ADMIN_MAX_LOGIN_ATTEMPTS',5);$i++)$auth->attempt($probe,'contraseña-inválida');$ok('Bloqueo temporal',$auth->isBlocked($probe));$ok('Intento hash almacenado',(int)$db->query("SELECT COUNT(*) FROM admin_login_attempts WHERE identifier_hash IS NOT NULL AND ip_hash IS NOT NULL")->fetchColumn()>0);$db->prepare('DELETE FROM admin_login_attempts WHERE identifier_hash=?')->execute([hash_hmac('sha256',strtolower($probe),Env::get('SECURITY_PEPPER'))]);}catch(Throwable $e){$ok('Conexión y servicio',false);fwrite(STDERR,$e->getMessage().PHP_EOL);}
foreach($checks as $name=>$result)echo($result?'[OK] ':'[FALLO] ').$name.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
