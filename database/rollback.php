<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
require_once dirname(__DIR__).'/app/Services/MigrationService.php';
try { $count=(new MigrationService(Database::connection()))->rollback(); echo "Migraciones revertidas: {$count}".PHP_EOL; } catch(Throwable $e) { fwrite(STDERR,"Error controlado: ".$e->getMessage().PHP_EOL); exit(1); }
