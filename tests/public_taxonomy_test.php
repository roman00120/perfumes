<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$db=Database::connection();$repo=new PerfumeRepository($db);$ok=true;
$check=function(bool $condition,string $label)use(&$ok){echo ($condition?'[OK] ':'[FAIL] ').$label.PHP_EOL;$ok=$ok&&$condition;};
$check($repo->publicBrands()===[],'No se muestran marcas sin perfumes públicos');$check($repo->publicBrand('dior')===null,'El detalle de marca vacía devuelve 404 lógico');$check($repo->publicCategory('perfumes-para-mujer')===null,'El detalle de categoría vacía devuelve 404 lógico');
exit($ok?0:1);
