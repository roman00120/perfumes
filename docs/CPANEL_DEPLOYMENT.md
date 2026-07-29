# Despliegue en cPanel

1. Crear una base MySQL/MariaDB y un usuario con privilegios sobre ella.
2. Configurar el dominio o subdominio con document root apuntando a `public/`. Si cPanel exige `public_html`, subir el contenido de `public/` allí y dejar el resto fuera de `public_html`; ajustar `APP_URL`.
3. Subir el paquete de release sin `.env`, `storage/sessions`, logs ni respaldos. Crear `.env` en la raíz privada.
4. Elegir PHP 8.2+ y activar PDO MySQL, mbstring, fileinfo, json, openssl, session, filter, hash, ZipArchive y DOM.
5. Aplicar permisos 644 a archivos, 755 a directorios y hacer escribibles solamente `storage`, `storage/logs`, `storage/cache`, `storage/backups`, `storage/reports` y `public/uploads` según el usuario de PHP. No usar 777.
6. Activar AutoSSL/HTTPS, `SESSION_COOKIE_SECURE=true` y verificar `/health/readiness`.
7. Ejecutar `php database/migrate.php` desde Terminal/cron de cPanel. No ejecutar seeds de demostración en producción.
8. Ejecutar `php tools/production_check.php` y `php tools/smoke_test.php https://DOMINIO_REAL` antes de activar indexación.

El despliegue no se realiza automáticamente desde el navegador. Para revertir, conservar la release anterior, detener tráfico si es necesario, restaurar archivos y ejecutar únicamente migraciones reversibles aprobadas; restaurar la base desde un respaldo verificado en un entorno aislado antes de tocar producción.
