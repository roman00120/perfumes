# Despliegue y operación

## Producción

1. Usar PHP 8.2 o superior, extensiones PDO MySQL, mbstring, fileinfo, GD/Imagick y DOM.
2. Apuntar el document root exclusivamente a `public/` y bloquear `.env`, `storage/`, `database/`, `tools/` y `tests/`.
3. Crear `.env` desde `.env.example`, usar un `APP_URL` HTTPS y definir `APP_ENV=production`, `APP_DEBUG=false`, `SEARCH_ENGINE_INDEXING=true` únicamente cuando el sitio esté listo.
4. Configurar `ADMIN_PASSWORD_HASH` y `SECURITY_PEPPER` fuera del repositorio. Activar `SESSION_COOKIE_SECURE=true` bajo HTTPS.
5. Ejecutar `php database/migrate.php` durante el despliegue y comprobar `/health/readiness`.

## SEO y privacidad

`/sitemap.xml` genera el índice de sitemaps públicos y `/robots.txt` bloquea todo el sitio fuera de producción. La analítica propia requiere consentimiento; GA4 y Meta Pixel permanecen desactivados por defecto.

## Mantenimiento

Programar limpieza de logs, importaciones/exportaciones temporales y respaldos. Revisar periódicamente `/admin/seo`, los códigos 4xx/5xx del servidor y los avisos de `/health/readiness`. No exponer los endpoints de salud con información de conexión o secretos.
