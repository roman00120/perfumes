# Runbook operativo

- Revisar `/health/readiness` después de cada cambio.
- Revisar logs privados y el panel SEO diariamente durante el lanzamiento.
- Crear y verificar respaldos antes de migraciones o limpiezas.
- Mantener `APP_DEBUG=false` en producción.
- Si aparece un 500, conservar el request ID/log, revisar la última release y revertir archivos sólo con respaldo verificado.
- No restaurar una base de producción destructivamente durante QA.
