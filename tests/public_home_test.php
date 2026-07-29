<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$checks=[];$ok=function(string $name,bool $result)use(&$checks):void{$checks[$name]=$result;};
try{$html=(new HomeController(Database::connection()))->index();$ok('Ruta de inicio renderiza',strlen($html)>5000);$ok('Un solo h1',preg_match_all('/<h1\b/i',$html,$m)===1);$ok('Idioma es-MX',str_contains($html,'lang="es-MX"'));$ok('WhatsApp configurado',str_contains($html,'wa.me/523331979793'));$ok('LocalBusiness JSON-LD',str_contains($html,'"@type":"LocalBusiness"'));$ok('No se muestra perfume borrador',!str_contains($html,'Dior Sauvage')&&!str_contains($html,'Chanel Coco Mademoiselle'));$ok('No carrito',!preg_match('/carrito|checkout|mi cuenta/i',$html));$ok('Categorías reales',(int)Database::connection()->query("SELECT COUNT(*) FROM categories WHERE is_active=1 AND deleted_at IS NULL AND is_featured=1")->fetchColumn()>=1);}catch(Throwable $e){$ok('Inicio sin errores',false);fwrite(STDERR,$e->getMessage().PHP_EOL);}
foreach($checks as $name=>$result)echo($result?'[OK] ':'[FALLO] ').$name.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
