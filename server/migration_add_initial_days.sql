-- Migración: Agregar campos para días económicos iniciales
-- Este script agrega campos a la tabla employees para almacenar
-- los días económicos ya gastados antes de registrar al empleado

ALTER TABLE employees 
ADD COLUMN initial_days_month INT DEFAULT 0 COMMENT 'Días económicos iniciales del mes actual',
ADD COLUMN initial_days_year INT DEFAULT 0 COMMENT 'Días económicos iniciales del año actual';

-- Ejemplo de uso: Actualizar un empleado existente con días iniciales
-- UPDATE employees SET initial_days_month = 2, initial_days_year = 8 WHERE id = 1;

