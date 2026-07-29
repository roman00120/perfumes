<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
if(!Env::bool('BACKUP_ENABLED',true))exit("Respaldo deshabilitado.".PHP_EOL);
echo (new BackupService())->create().PHP_EOL;
