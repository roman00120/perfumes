# Presupuesto de rendimiento

- No cargar scripts de terceros sin consentimiento.
- Mantener JavaScript propio con `defer`.
- Usar imágenes con dimensiones, lazy loading y WebP cuando el procesamiento esté disponible.
- Mantener caché HTTP de assets y gzip activo.
- Investigar consultas que superen `DB_SLOW_QUERY_THRESHOLD_MS`.
- Medir Core Web Vitals en el dominio real antes del lanzamiento.
