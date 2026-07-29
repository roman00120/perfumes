<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
if (Env::get('APP_ENV') !== 'local' || PHP_SAPI !== 'cli') { fwrite(STDERR,"fresh.php solo está permitido por CLI y con APP_ENV=local.".PHP_EOL); exit(1); }
echo "Base de datos: ".Env::get('DB_NAME').PHP_EOL."Escribe FRESH para confirmar: ";
if (trim((string)fgets(STDIN)) !== 'FRESH') { echo "Operación cancelada.".PHP_EOL; exit(0); }
try {
 $db=Database::connection();$db->exec("SET FOREIGN_KEY_CHECKS=0");
 foreach(['admin_login_attempts','audit_logs','media','social_links','store_hours','contact_messages','faqs','pages','testimonials','banners','promotion_perfumes','promotions','perfume_aliases','perfume_tags','tags','perfume_presentations','perfume_notes','perfume_images','perfume_categories','perfumes','notes','olfactory_families','categories','brand_aliases','brands','settings','migrations'] as $t)$db->exec("DROP TABLE IF EXISTS {$t}");
 $db->exec("SET FOREIGN_KEY_CHECKS=1");passthru(PHP_BINARY.' '.__DIR__.'/migrate.php');passthru(PHP_BINARY.' '.__DIR__.'/seed.php');
} catch(Throwable $e){fwrite(STDERR,"Error controlado: ".$e->getMessage().PHP_EOL);exit(1);}
