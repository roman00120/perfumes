<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require_once dirname(__DIR__).'/app/Services/MigrationService.php';
$checks=[];$ok=function(string $name,bool $result)use(&$checks):void{$checks[$name]=$result;};
$ok('Slug con acentos',SlugService::make('L’Eau d’Issey / Ámbar') === 'l-eau-d-issey-ambar');
try {
 $db=Database::connection();$ok('Conexión PDO',true);
 $tables=['migrations','settings','brands','brand_aliases','categories','olfactory_families','notes','perfumes','perfume_categories','perfume_images','perfume_notes','perfume_presentations','tags','perfume_tags','perfume_aliases','promotions','promotion_perfumes','banners','testimonials','pages','faqs','contact_messages','store_hours','social_links','media','audit_logs','admin_login_attempts'];
 $existing=$db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);$ok('Tablas esperadas',count(array_diff($tables,$existing))===0);$ok('Ausencia tabla users',!in_array('users',$existing,true));
 $published=(int)$db->query("SELECT COUNT(*) FROM perfumes WHERE status='publicado' OR is_published=1")->fetchColumn();$total=(int)$db->query('SELECT COUNT(*) FROM perfumes')->fetchColumn();$ok('Sin perfumes publicados',$published===0);$ok('Perfumes >= 150',$total>=150);$ok('Sin precios iniciales',(int)$db->query('SELECT COUNT(*) FROM perfumes WHERE price_from IS NOT NULL OR show_price=1')->fetchColumn()===0);$ok('Disponibilidad pendiente',(int)$db->query("SELECT COUNT(*) FROM perfumes WHERE availability_status<>'pendiente'")->fetchColumn()===0);$ok('Slugs únicos',$total=== (int)$db->query('SELECT COUNT(DISTINCT slug) FROM perfumes')->fetchColumn());$ok('UTF-8',str_contains((string)$db->query("SELECT @@character_set_database")->fetchColumn(),'utf8'));
 $catalog=(new CatalogService($db))->published(1,12);$ok('Paginación de catálogo',$catalog['page']===1 && $catalog['per_page']===12 && $catalog['total']===0);
} catch(Throwable $e) { $checks['Conexión/motor MySQL']=false;fwrite(STDERR,"No se pudieron ejecutar pruebas de base de datos: ".$e->getMessage().PHP_EOL); }
foreach($checks as $name=>$result) echo ($result?'[OK] ':'[FALLO] ').$name.PHP_EOL;
exit(in_array(false,$checks,true)?1:0);
