<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$contents=file_get_contents(dirname(__DIR__).'/public/index.php');$ok=!str_contains($contents,"->get('/admin/marcas/{id}/eliminar");echo($ok?'[OK] Las eliminaciones no usan GET'.PHP_EOL:'[FAIL] Acción destructiva GET'.PHP_EOL);exit($ok?0:1);
