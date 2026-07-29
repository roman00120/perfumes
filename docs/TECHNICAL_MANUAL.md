# Manual técnico

El front controller es `public/index.php`; el autoload carga clases desde `app/`. Las migraciones se ejecutan con `php database/migrate.php`. Los archivos sensibles permanecen fuera del document root y `.htaccess` bloquea rutas internas.

Las comprobaciones principales son `tools/validate_environment.php`, `tools/production_check.php` y `tools/smoke_test.php`. Los reportes QA se guardan en `storage/reports/qa` sin secretos.
