<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
foreach([dirname(__DIR__).'/storage/imports',dirname(__DIR__).'/storage/exports'] as $dir)if(is_dir($dir))foreach(glob($dir.'/*')?:[] as $file)if(is_file($file)&&filemtime($file)<time()-Env::int('IMPORT_TEMP_RETENTION_HOURS',24)*3600)@unlink($file);
echo "Limpieza completada en modo seguro.".PHP_EOL;
