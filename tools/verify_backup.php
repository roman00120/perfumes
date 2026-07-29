<?php
declare(strict_types=1);
$file=$argv[1]??'';
if($file===''||!is_file($file))exit("Archivo no encontrado.".PHP_EOL);
echo hash_file('sha256',$file).PHP_EOL;
