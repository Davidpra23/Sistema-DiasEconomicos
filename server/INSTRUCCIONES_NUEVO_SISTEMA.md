# Instrucciones: Nuevo Sistema de Días (Solo Tabla Employees)

## ¿Qué cambió?

Ahora el sistema **solo toma en cuenta la tabla `employees`** para los días usados. Las solicitudes aprobadas se reflejan automáticamente en `initial_days_month` e `initial_days_year`.

## Flujo del sistema

1. **Las solicitudes se guardan** en la tabla `requests` (con estado 'pendiente')
2. **Cuando se aprueba una solicitud**, automáticamente se actualizan los días en `employees`
3. **La página solo lee de `employees`** para mostrar días usados

## Pasos para migrar

### Paso 1: Ejecutar la migración de días iniciales (si no lo has hecho)

```sql
ALTER TABLE employees 
ADD COLUMN initial_days_month INT DEFAULT 0,
ADD COLUMN initial_days_year INT DEFAULT 0;
```

### Paso 2: Migrar solicitudes aprobadas existentes

Ejecuta el archivo: `server/migrate_requests_to_initial_days.sql`

O ejecuta manualmente:

```sql
-- Actualizar días del mes actual
UPDATE employees e
SET initial_days_month = (
    SELECT COALESCE(SUM(r.days), 0)
    FROM requests r
    WHERE r.employee_id = e.id
    AND r.status = 'aprobada'
    AND YEAR(r.date_requested) = YEAR(CURDATE())
    AND MONTH(r.date_requested) = MONTH(CURDATE())
);

-- Actualizar días del año actual
UPDATE employees e
SET initial_days_year = (
    SELECT COALESCE(SUM(r.days), 0)
    FROM requests r
    WHERE r.employee_id = e.id
    AND r.status = 'aprobada'
    AND YEAR(r.date_requested) = YEAR(CURDATE())
);
```

### Paso 3: Verificar

```sql
SELECT e.id, e.name, e.initial_days_month, e.initial_days_year 
FROM employees e;
```

## Cómo funciona ahora

### Cuando se crea una solicitud:
- Se guarda en `requests` con estado 'pendiente'
- NO se actualizan los días en `employees` todavía

### Cuando se aprueba una solicitud:
- Se actualiza `requests.status = 'aprobada'`
- **Automáticamente** se suma a `employees.initial_days_month` (si es del mes actual)
- **Automáticamente** se suma a `employees.initial_days_year` (si es del año actual)

### Cuando se muestra información:
- La página solo lee `employees.initial_days_month` e `initial_days_year`
- NO consulta la tabla `requests` para contar días

## Ventajas

1. **Más rápido**: Solo una consulta a `employees` en lugar de JOIN con `requests`
2. **Más simple**: Los días están directamente en la tabla del empleado
3. **Automático**: Cuando se aprueba una solicitud, se actualiza automáticamente
4. **Consistente**: Los días siempre reflejan las solicitudes aprobadas

## Notas importantes

- **Solo se actualizan días del mes/año actual**: Si se aprueba una solicitud de un mes pasado, no afecta los días del mes actual
- **Las solicitudes pendientes no cuentan**: Solo las aprobadas actualizan los días
- **Si rechazas una solicitud**: No se actualizan los días (permanece como estaba)

## Resumen

- ✅ Migrar solicitudes aprobadas existentes → `migrate_requests_to_initial_days.sql`
- ✅ El sistema ahora actualiza automáticamente `employees` cuando se aprueba una solicitud
- ✅ La página solo lee de `employees` para mostrar días usados

¡Listo! El sistema ahora funciona solo con la tabla `employees`.

