<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$checks=[];$check=function(string $name,bool $value)use(&$checks):void{$checks[$name]=$value;echo($value?'[OK] ':'[FALLO] ').$name.PHP_EOL;};
$check('robots bloquea fuera de producción',str_contains(RobotsService::text(),'Disallow: /'));
$check('redirección relativa segura',RedirectService::validTarget('/catalogo'));
$check('redirección protocol-relative bloqueada',!RedirectService::validTarget('//evil.example'));
$check('redirección javascript bloqueada',!RedirectService::validTarget('javascript:alert(1)'));
$health=HealthService::check(null,false);$check('health no expone secretos',!str_contains(json_encode($health),'DB_PASSWORD'));
$check('sitemap XML base',(new SitemapService(null))->xml('pages') !== '');
exit(in_array(false,$checks,true)?1:0);
