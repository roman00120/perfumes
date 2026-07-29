# Estado de entrega para cPanel

## Artefactos preparados

- Paquete: `storage/releases/les-sens-1.0.0.zip`
- Hash: `storage/releases/les-sens-1.0.0.zip.sha256`
- Configuración base: `.env.production.example`
- Guía: `docs/CPANEL_DEPLOYMENT.md`
- Matriz QA: `docs/QA_ACCEPTANCE_MATRIX.md`

El paquete excluye `.env`, sesiones, logs, caché, respaldos, documentación y tests. Incluye la aplicación, `public/`, migraciones, herramientas operativas y `.env.example`.

## Pasos externos inevitables

1. Crear la base de datos y usuario en cPanel.
2. Configurar PHP 8.2+ y el document root hacia `public/`.
3. Subir y extraer el ZIP.
4. Crear `.env` desde `.env.production.example` y completar dominio, DB, hash y pepper reales.
5. Ejecutar `php database/migrate.php`.
6. Ejecutar `php tools/production_check.php` y `php tools/smoke_test.php https://DOMINIO_REAL` desde el servidor.
7. Activar `SEARCH_ENGINE_INDEXING=true` sólo después de verificar el dominio.
