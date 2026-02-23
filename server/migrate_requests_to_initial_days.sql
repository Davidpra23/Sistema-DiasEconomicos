-- Migración: Convertir solicitudes aprobadas a días iniciales en la tabla employees
-- Este script toma todas las solicitudes aprobadas y las suma a initial_days_month/initial_days_year

-- Actualizar días del mes actual basado en solicitudes aprobadas del mes actual
UPDATE employees e
SET initial_days_month = (
    SELECT COALESCE(SUM(r.days), 0)
    FROM requests r
    WHERE r.employee_id = e.id
    AND r.status = 'aprobada'
    AND YEAR(r.date_requested) = YEAR(CURDATE())
    AND MONTH(r.date_requested) = MONTH(CURDATE())
);

-- Actualizar días del año actual basado en solicitudes aprobadas del año actual
UPDATE employees e
SET initial_days_year = (
    SELECT COALESCE(SUM(r.days), 0)
    FROM requests r
    WHERE r.employee_id = e.id
    AND r.status = 'aprobada'
    AND YEAR(r.date_requested) = YEAR(CURDATE())
);

-- Verificar resultados (opcional)
-- SELECT e.id, e.name, e.initial_days_month, e.initial_days_year 
-- FROM employees e;

