import sqlite3
import os
from datetime import datetime

db_path = os.path.join(os.path.dirname(__file__), '..', 'database.db')

# Connect to database (creates it if not exists)
conn = sqlite3.connect(db_path)
cursor = conn.cursor()

# Enable foreign keys
cursor.execute("PRAGMA foreign_keys = ON")

# 1. Create Tables

# Departments
cursor.execute("""
CREATE TABLE IF NOT EXISTS departments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL
)
""")

# Employees
cursor.execute("""
CREATE TABLE IF NOT EXISTS employees (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    department_id INTEGER,
    initial_days_month INTEGER DEFAULT 0,
    initial_days_year INTEGER DEFAULT 0,
    FOREIGN KEY (department_id) REFERENCES departments(id)
)
""")

# Requests
cursor.execute("""
CREATE TABLE IF NOT EXISTS requests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employee_id INTEGER NOT NULL,
    date_requested TEXT NOT NULL,
    days INTEGER NOT NULL,
    status TEXT DEFAULT 'pendiente',
    notes TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
)
""")

# 2. Insert Data

# Check if data already exists to avoid duplicates
cursor.execute("SELECT COUNT(*) FROM departments")
if cursor.fetchone()[0] == 0:
    print("Inserting departments...")
    cursor.execute("INSERT INTO departments (name) VALUES ('Ventas')")
    cursor.execute("INSERT INTO departments (name) VALUES ('Recursos Humanos')")
    cursor.execute("INSERT INTO departments (name) VALUES ('Logística')")
    cursor.execute("INSERT INTO departments (name) VALUES ('Contabilidad')")
    cursor.execute("INSERT INTO departments (name) VALUES ('TI')")

cursor.execute("SELECT COUNT(*) FROM employees")
if cursor.fetchone()[0] == 0:
    print("Inserting employees...")
    cursor.execute("INSERT INTO employees (name, department_id) VALUES ('Carlos Rodríguez Hernández', 1)")
    cursor.execute("INSERT INTO employees (name, department_id) VALUES ('María González López', 2)")
    cursor.execute("INSERT INTO employees (name, department_id) VALUES ('Juan Pérez Martínez', 3)")
    cursor.execute("INSERT INTO employees (name, department_id) VALUES ('Ana Sánchez Ramírez', 4)")
    cursor.execute("INSERT INTO employees (name, department_id) VALUES ('Roberto Díaz Cruz', 5)")

cursor.execute("SELECT COUNT(*) FROM requests")
if cursor.fetchone()[0] == 0:
    print("Inserting requests...")
    # Using specific dates from schema.sql
    cursor.execute("INSERT INTO requests (employee_id, date_requested, days, status, notes) VALUES (1, '2025-09-19', 1, 'aprobada', 'Permiso económico')")
    cursor.execute("INSERT INTO requests (employee_id, date_requested, days, status, notes) VALUES (2, '2025-08-15', 2, 'rechazada', 'Excedió límite de días')")
    cursor.execute("INSERT INTO requests (employee_id, date_requested, days, status, notes) VALUES (1, '2025-07-05', 1, 'aprobada', 'Permiso personal')")

conn.commit()

# 3. Migration Logic (Update initial days based on requests)
# SQLite equivalent of the UPDATE queries in schema.sql

print("Updating initial days based on requests...")

# Update initial_days_month
# The logic uses strftime inside subquery
cursor.execute("""
UPDATE employees
SET initial_days_month = (
    SELECT COALESCE(SUM(r.days), 0)
    FROM requests r
    WHERE r.employee_id = employees.id
    AND r.status = 'aprobada'
    AND strftime('%Y', r.date_requested) = strftime('%Y', 'now')
    AND strftime('%m', r.date_requested) = strftime('%m', 'now')
)
""")

# Update initial_days_year
cursor.execute("""
UPDATE employees
SET initial_days_year = (
    SELECT COALESCE(SUM(r.days), 0)
    FROM requests r
    WHERE r.employee_id = employees.id
    AND r.status = 'aprobada'
    AND strftime('%Y', r.date_requested) = strftime('%Y', 'now')
)
""")

conn.commit()
conn.close()

print(f"Database initialized successfully at {db_path}")
