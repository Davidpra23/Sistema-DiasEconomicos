-- Base de datos para Sistema de Días Económicos
CREATE DATABASE IF NOT EXISTS sistema_dias;
USE sistema_dias;

-- Tabla de departamentos
CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

-- Tabla de empleados
CREATE TABLE IF NOT EXISTS employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  department_id INT,
  initial_days_month INT DEFAULT 0 COMMENT 'Días económicos iniciales del mes actual',
  initial_days_year INT DEFAULT 0 COMMENT 'Días económicos iniciales del año actual',
  FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Tabla de solicitudes
CREATE TABLE IF NOT EXISTS requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT NOT NULL,
  date_requested DATE NOT NULL,
  days INT NOT NULL,
  status VARCHAR(20) DEFAULT 'pendiente',
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- Datos de ejemplo: Departamentos
INSERT INTO departments (name) VALUES
('Ventas'),
('Recursos Humanos'),
('Logística'),
('Contabilidad'),
('TI');

-- Datos de ejemplo: Empleados
INSERT INTO employees (name, department_id) VALUES
('Carlos Rodríguez Hernández', 1),
('María González López', 2),
('Juan Pérez Martínez', 3),
('Ana Sánchez Ramírez', 4),
('Roberto Díaz Cruz', 5);

-- Datos de ejemplo: Solicitudes
INSERT INTO requests (employee_id, date_requested, days, status, notes) VALUES
(1, '2025-09-19', 1, 'aprobada', 'Permiso económico'),
(2, '2025-08-15', 2, 'rechazada', 'Excedió límite de días'),
(1, '2025-07-05', 1, 'aprobada', 'Permiso personal');

-- ---------------------------------------------------------
-- Sincronización de contadores (Lógica de migración)
-- ---------------------------------------------------------

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
