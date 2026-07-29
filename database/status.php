<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require_once dirname(__DIR__).'/app/Services/MigrationService.php';
try { $s=new MigrationService(Database::connection());$done=array_column($s->applied(),'migration');foreach($s->files() as $f){$n=basename($f);echo (in_array($n,$done,true)?'[OK] ':'[PENDIENTE] ').$n.PHP_EOL;} } catch(Throwable $e) { fwrite(STDERR,"Error controlado: ".$e->getMessage().PHP_EOL); exit(1); }
