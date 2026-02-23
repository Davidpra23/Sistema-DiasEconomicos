# Instrucciones: Días Económicos Iniciales

## ¿Qué es esto?

Esta funcionalidad permite registrar empleados que ya tienen días económicos gastados sin necesidad de crear solicitudes históricas individuales. Solo necesitas saber cuántos días han usado en el mes actual y en el año actual.

## Pasos para usar

### 1. Ejecutar la migración (si la base de datos ya existe)

Si ya tienes una base de datos con empleados, ejecuta este SQL:

```sql
ALTER TABLE employees 
ADD COLUMN initial_days_month INT DEFAULT 0 COMMENT 'Días económicos iniciales del mes actual',
ADD COLUMN initial_days_year INT DEFAULT 0 COMMENT 'Días económicos iniciales del año actual';
```

O ejecuta el archivo: `server/migration_add_initial_days.sql`

### 2. Agregar un nuevo empleado con días iniciales

**Ejemplo: Empleado con 2 días gastados en el mes y 8 en el año**

```sql
INSERT INTO employees (name, department_id, initial_days_month, initial_days_year) 
VALUES ('Pedro García López', 1, 2, 8);
```

**Parámetros:**
- `name`: Nombre completo del empleado (obligatorio)
- `department_id`: ID del departamento (opcional, puede ser NULL)
- `initial_days_month`: Días económicos ya gastados en el mes actual (por defecto 0)
- `initial_days_year`: Días económicos ya gastados en el año actual (por defecto 0)

### 3. Actualizar días iniciales de un empleado existente

Si necesitas actualizar los días iniciales de un empleado que ya existe:

```sql
UPDATE employees 
SET initial_days_month = 2, 
    initial_days_year = 8 
WHERE id = 6;
```

### 4. Cómo funciona

- Los días iniciales **solo se aplican al mes actual y año actual**
- Se suman a las solicitudes aprobadas del mes/año actual
- Las validaciones consideran los días iniciales + solicitudes existentes
- Si el empleado tiene 2 días iniciales del mes y ya solicitó 1 día, el sistema mostrará 3 días usados del mes
- Si el empleado intenta solicitar más días y ya tiene 2 iniciales + 1 solicitud = 3 días, no podrá solicitar más en ese mes

### 5. Ejemplos

**Ejemplo 1: Empleado nuevo sin días iniciales**
```sql
INSERT INTO employees (name, department_id) 
VALUES ('Juan Pérez', 1);
-- initial_days_month = 0, initial_days_year = 0 (por defecto)
```

**Ejemplo 2: Empleado con días iniciales**
```sql
INSERT INTO employees (name, department_id, initial_days_month, initial_days_year) 
VALUES ('María López', 2, 2, 8);
-- 2 días del mes, 8 días del año ya gastados
```

**Ejemplo 3: Solo días del año (sin días del mes)**
```sql
INSERT INTO employees (name, department_id, initial_days_month, initial_days_year) 
VALUES ('Carlos Ramírez', 3, 0, 5);
-- 0 días del mes, 5 días del año ya gastados
```

### 6. Notas importantes

- Los días iniciales **solo afectan al mes/año actual** cuando se registra al empleado
- Si cambias de mes o año, los días iniciales seguirán ahí pero solo afectarán al mes/año en el que fueron registrados
- Si necesitas resetear los días iniciales al cambiar de mes/año, puedes ejecutar:
  ```sql
  -- Resetear días iniciales del mes (al comenzar nuevo mes)
  UPDATE employees SET initial_days_month = 0;
  
  -- Resetear días iniciales del año (al comenzar nuevo año)
  UPDATE employees SET initial_days_year = 0;
  ```

### 7. Visualización

El sistema mostrará automáticamente:
- **En la interfaz**: "2/3 (mes) · 8/12 (año)" si el empleado tiene 2 días iniciales del mes y 8 del año
- **En las validaciones**: Considerará los días iniciales al validar si puede solicitar más días

## Resumen rápido

Para agregar un empleado con días ya gastados:
```sql
INSERT INTO employees (name, department_id, initial_days_month, initial_days_year) 
VALUES ('Nombre Empleado', ID_DEPARTAMENTO, DIAS_MES, DIAS_AÑO);
```

¡Listo! El sistema manejará automáticamente el conteo y las validaciones.

