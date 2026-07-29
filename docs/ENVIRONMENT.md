# Variables de entorno

Copiar `.env.example` a `.env` y completar únicamente valores reales del servidor. Nunca subir `.env` al repositorio ni al paquete público.

## Producción mínima

`APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://DOMINIO_REAL`, `SEARCH_ENGINE_INDEXING=false` hasta validar el dominio, `FORCE_HTTPS=true`, `SESSION_COOKIE_SECURE=true`, `ADMIN_PASSWORD_HASH` generado con `tools/generate_admin_password.php` y un `SECURITY_PEPPER` aleatorio.

La base de datos requiere `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` y `DB_PASSWORD`. `MAIL_ENABLED`, GA4, Meta Pixel y alertas permanecen desactivados si no existe configuración confirmada.

## Compatibilidad detectada localmente

El entorno de desarrollo tiene PHP 8.0.30, PDO MySQL, mbstring, fileinfo, DOM, GD no disponible e Imagick no disponible; ZipArchive y gzip sí están disponibles. El requisito de producción es PHP 8.2 o superior. Las imágenes se procesan con fallback sin Imagick.
