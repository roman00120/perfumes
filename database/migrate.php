<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require_once dirname(__DIR__).'/app/Services/MigrationService.php';
try { $count=(new MigrationService(Database::connection()))->migrate(); echo "Migraciones ejecutadas: {$count}".PHP_EOL; } catch(Throwable $e) { fwrite(STDERR,"Error controlado: ".$e->getMessage().PHP_EOL); exit(1); }
