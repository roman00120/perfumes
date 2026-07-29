<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$db=Database::connection();$repo=new PerfumeRepository($db);$ok=true;
$check=function(bool $condition,string $label)use(&$ok){echo ($condition?'[OK] ':'[FAIL] ').$label.PHP_EOL;$ok=$ok&&$condition;};
$check((int)$db->query("SELECT COUNT(*) FROM perfumes WHERE status='publicado' AND is_published=1 AND deleted_at IS NULL")->fetchColumn()===0,'No hay borradores expuestos como públicos');
$r=$repo->paginate(catalog_filters(['q'=>str_repeat('dior ',100),'sort'=>'invalid','featured'=>'x']),1,12);
$check($r['total']===0,'La consulta pública excluye perfumes no publicados');$check(strlen(catalog_filters(['q'=>str_repeat('x',300)])['q'])===100,'La búsqueda limita longitud');$check(str_contains(catalog_whatsapp('Dior Sauvage','Dior'),'wa.me/'),'WhatsApp dinámico');
exit($ok?0:1);
