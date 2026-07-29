# Les Sens Perfumería

Base técnica de la web institucional de Les Sens. Esta fase no incluye tienda, carrito, pagos ni sistema de usuarios; el acceso administrativo es únicamente una pantalla provisional.

## Requisitos

PHP 8.2+, Apache con mod_rewrite, MySQL/MariaDB y PDO MySQL habilitado. No requiere Composer.

## Instalación local

1. Copia .env.example como .env y configura la conexión.
2. Sirve public/ como document root, por ejemplo php -S 127.0.0.1:8000 -t public.
3. Visita / y /admin/login.

## cPanel

**A. Todo en public_html:** sube el proyecto completo y apunta el dominio a public_html/public si el hosting permite cambiar document root. Si no, usa el index.php raíz y conserva las reglas de public/.htaccess en el document root.

**B. Núcleo fuera de public_html (recomendado):** coloca app, config, database, routes, storage, views, .env y bootstrap.php fuera de public_html; coloca el contenido de public dentro de public_html y ajusta en public/index.php la ruta a bootstrap.php según la ubicación real. Asegura permisos de escritura en storage/* y public/uploads/*.

## Configuración requerida

En .env: APP_URL, credenciales DB_*, ADMIN_USER, ADMIN_PASSWORD_HASH (se utilizará en la fase de autenticación), WHATSAPP_NUMBER, PHONE_NUMBER, STORE_EMAIL y STORE_ADDRESS. No publiques .env ni contraseñas.

## Base de datos y Fase 1

La Fase 1 añade `database/schema.sql`, `database/seed.sql`, una migración inicial, modelos ligeros, servicios de catálogo/configuración/auditoría, validadores y un seed PHP con 166 perfumes iniciales. Todos quedan en `borrador`, con disponibilidad `pendiente`, sin precio y sin publicación. No se afirma que estén disponibles.

Configura `DB_*` en `.env` y crea primero la base de datos vacía `les_sens` desde cPanel/phpMyAdmin. Después ejecuta desde la raíz:

```text
php database/migrate.php
php database/status.php
php database/seed.php
php tests/database_test.php
```

Para revertir el último lote: `php database/rollback.php`. Para reconstruir únicamente en local, usa `php database/fresh.php`, confirma escribiendo `FRESH` y deja que el script ejecute migración y seed. No existe una ruta web para estos scripts.

Alternativamente, importa `database/schema.sql` y luego `database/seed.sql` desde phpMyAdmin. El seed PHP es el recomendado porque además relaciona marcas, categorías, aliases y categorías de perfumes.

## Acceso administrativo

Rutas disponibles: `/admin/login`, `/admin/dashboard`, `/admin/logout` mediante POST y módulos provisionales protegidos bajo `/admin/*`. No existe tabla de usuarios, roles ni permisos.

Variables relevantes: `ADMIN_USER`, `ADMIN_PASSWORD_HASH`, `ADMIN_SESSION_TIMEOUT`, `ADMIN_MAX_LOGIN_ATTEMPTS`, `ADMIN_LOCKOUT_MINUTES`, `ADMIN_SESSION_NAME`, `SECURITY_PEPPER`, `CSRF_TOKEN_LIFETIME` y las variables `SESSION_COOKIE_*`. Las credenciales se validan exclusivamente desde `.env`; nunca se guardan en MySQL.

Para generar un hash nuevo, ejecuta desde CLI:

```text
php tools/generate_admin_password.php
```

Después copia únicamente la línea `ADMIN_PASSWORD_HASH=...` en `.env`. La contraseña recomendada debe tener al menos 12 caracteres. En producción configura un `SECURITY_PEPPER` aleatorio, HTTPS y `SESSION_COOKIE_SECURE=true`.

El login usa `password_verify()`, CSRF con `random_bytes()`/`hash_equals()`, sesiones HttpOnly/SameSite=Lax, expiración por inactividad y bloqueo temporal por intentos. Los intentos almacenan solo hashes HMAC de identificador e IP.

Prueba administrativa:

```text
php tests/admin_auth_test.php
```

## Diseño público

La portada usa `HomeController` y `PublicHomeService` para consultar configuraciones públicas, categorías, perfumes publicados destacados, marcas, promociones, testimonios verificados, horarios y redes activas. Los borradores nunca aparecen.

La interfaz pública se organiza en `public.css`, `home.css`, `pages.css` y `public.js`, con layout y partials reutilizables. Incluye hero, beneficios, categorías, selección de fragancias, asesoría por WhatsApp, marcas, contacto, ubicación, footer, SEO básico y JSON-LD `LocalBusiness`.

Para activar perfumes destacados, un registro debe tener simultáneamente `status='publicado'`, `is_published=1`, `is_featured=1` y `deleted_at IS NULL`; además, su marca y categoría deben estar activas. No se cargan precios ni testimonios ficticios.

Las imágenes se pueden sustituir desde `main_image`, `categories.image`, `brands.logo` y los campos de banners, usando rutas relativas dentro de `public/uploads` o assets locales. El número de WhatsApp se cambia en `WHATSAPP_NUMBER` y la dirección en `STORE_ADDRESS` o en la tabla pública `settings`.

Prueba pública:

```text
php tests/public_home_test.php
```

Las tablas principales son `settings`, `brands`, `brand_aliases`, `categories`, `olfactory_families`, `notes`, `perfumes`, sus tablas pivote y presentaciones, `promotions`, `banners`, contenido institucional, medios, auditoría y control de intentos administrativos. No existe tabla `users`.

## Fase 5 — Administración de perfumes

El módulo protegido está disponible en `/admin/perfumes`. Permite listar y filtrar por texto, estado y disponibilidad; crear, editar, duplicar, previsualizar, publicar, devolver a borrador, archivar, restaurar y eliminar lógicamente. Las acciones destructivas son POST con CSRF y quedan registradas en `audit_logs`.

El formulario gestiona información básica, marca, categoría principal, familia, descripciones sanitizadas, disponibilidad, precio opcional, características, imagen y SEO. La publicación exige nombre, slug único, marca, categoría, descripción, disponibilidad no pendiente y registro no eliminado. No publica automáticamente los perfumes existentes.

Las imágenes aceptan JPG, PNG y WebP; se validan con MIME real, tamaño y dimensiones. Se almacenan bajo `public/uploads/perfumes/{id}` con bloqueo de ejecución PHP. Presentaciones y notas se gestionan mediante endpoints protegidos y preparados para ampliarse desde la ficha administrativa.

Configuración adicional: `ADMIN_PERFUMES_PER_PAGE`, `ADMIN_PERFUMES_MAX_PER_PAGE`, `UPLOAD_MAX_IMAGE_MB`, `UPLOAD_MAX_PERFUME_IMAGES`, `UPLOAD_IMAGE_MIN_WIDTH`, `UPLOAD_IMAGE_MIN_HEIGHT`, `UPLOAD_IMAGE_MAX_WIDTH`, `UPLOAD_IMAGE_MAX_HEIGHT`, `UPLOAD_IMAGE_QUALITY`, `UPLOAD_GENERATE_WEBP`, `UPLOAD_GENERATE_THUMBNAILS`, `MEDIA_STORAGE_PATH`, `MEDIA_PUBLIC_PATH` y `MEDIA_CLEANUP_DRY_RUN`.

Migración ejecutada: `004_perfume_admin_seo.php`, que agrega `published_at`, `og_title`, `og_description`, `og_image` y `robots`.

Pruebas específicas:

```text
php tests/admin_perfume_crud_test.php
php tests/admin_auth_test.php
php tools/cleanup_orphan_media.php
```

## Fase 6 — Catálogos auxiliares

Rutas administrativas protegidas: /admin/marcas, /admin/categorias, /admin/familias-olfativas, /admin/notas y /admin/etiquetas. Cada módulo permite listado, búsqueda, ordenamiento, paginación, creación, edición, activación, desactivación, soft delete, restauración, eliminación definitiva controlada y duplicación. Las acciones modificadoras usan POST, CSRF y auditoría.

Las marcas incluyen aliases y dependencias de perfumes. Las categorías conservan jerarquía y validan padre propio, ciclos y profundidad máxima mediante CATEGORY_MAX_DEPTH. Las fusiones reasignan relaciones dentro de una transacción y bloquean fusionar un registro consigo mismo. No se eliminan ni fusionan registros automáticamente.

Variables añadidas: ADMIN_TAXONOMIES_PER_PAGE, ADMIN_TAXONOMIES_MAX_PER_PAGE, CATEGORY_MAX_DEPTH, límites de imágenes de taxonomías, ADMIN_ASYNC_SEARCH_LIMIT, ADMIN_DUPLICATE_DETECTION_ENABLED y ADMIN_DUPLICATE_SIMILARITY_THRESHOLD.

Migración: 006_taxonomy_soft_delete.php, que agrega deleted_at e índices a familias, notas y etiquetas para conservar el comportamiento de soft delete.

Pruebas: php tests/admin_taxonomy_test.php y php tests/admin_taxonomy_security_test.php.

## Fase 8 — Importación, exportación y calidad

Se añadieron las tablas de importaciones, filas de importación, exportaciones y revisiones de calidad mediante `008_import_export_quality.php`. El flujo CSV/TSV valida MIME, extensión, tamaño, columnas y filas; genera filas de simulación y obliga a confirmar escribiendo IMPORTAR. Los nuevos perfumes se normalizan a borrador y no se publican automáticamente.

Rutas administrativas: `/admin/importaciones`, `/admin/importaciones/nueva`, `/admin/importaciones/plantilla/{tipo}`, `/admin/exportaciones` y sus acciones protegidas. Las exportaciones disponibles son CSV y JSON para perfumes y catálogos auxiliares; las celdas potencialmente peligrosas se protegen contra CSV injection y los archivos expiran.

Herramientas CLI: `tools/catalog_quality_scan.php`, `tools/create_backup.php`, `tools/verify_backup.php` y `tools/cleanup_import_export_files.php`. Los respaldos requieren configurar `MYSQLDUMP_PATH`; la restauración web permanece desactivada.

Variables nuevas: `IMPORT_*`, `EXPORT_*`, `BACKUP_*`, `MAINTENANCE_*`, `MYSQLDUMP_PATH` y límites de contacto/contenido.

## Estado de Fase 0

## Catálogo público — Fase 4

Rutas: `/catalogo`, `/catalogo/pagina/{n}`, `/buscar`, `/perfume/{slug}`, `/marcas`, `/marca/{slug}`, `/categorias` y `/categoria/{slug}`. Admite búsqueda, marca, categoría, género, familia, concentración, disponibilidad, temporada, ocasión, intensidad, etiquetas y filtros de destacadas/nuevas/promoción; ordena por relevancia, nombre o fecha.

Solo se consultan perfumes con `status='publicado'`, `is_published=1`, `deleted_at IS NULL` y relaciones activas. No se muestran borradores, privados, eliminados ni taxonomías inactivas. La ficha muestra información, notas, etiquetas, presentaciones y disponibilidad; no inventa precios y solo muestra uno con `show_price=1`. No incluye carrito, checkout ni usuarios públicos.

Configuración: `CATALOG_PER_PAGE`, `CATALOG_MAX_PER_PAGE`, `CATALOG_CACHE_ENABLED`, `CATALOG_CACHE_TTL`, `CATALOG_ENABLE_VIEWS`, `CATALOG_VIEW_SESSION_TTL`, `CATALOG_SHOW_RESULT_COUNTS` y `CATALOG_INDEX_FILTERED_PAGES`. Incluye responsive, filtros móviles, estados vacíos, canonical, Open Graph y JSON-LD Product sin `offers` cuando no existe un precio autorizado.

Pruebas:

```text
php tests/catalog_test.php
php tests/public_taxonomy_test.php
```

Incluye router GET/POST, helpers, CSRF, sesiones seguras, PDO preparado, páginas provisionales, errores 404/500, estilos responsive, protección Apache y estructura de uploads/logs/caché. No se han creado tablas definitivas ni usuarios, roles, permisos o autenticación completa.
