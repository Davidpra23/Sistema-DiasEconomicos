# API PHP para Sistema de Días Económicos

Archivos:

- `config.php` - configuración de conexión (host, bd, usuario, pass). Edita con tus credenciales.
- `db.php` - helper que crea la conexión PDO y la devuelve.
- `employees.php` - endpoint GET para listar empleados; también `?departments=1` para listar departamentos.
- `requests.php` - endpoint REST para solicitudes (GET, POST, PUT).

Cómo ejecutar (opción rápida con PHP integrado):

1. Asegúrate de tener MySQL corriendo y la base `sistema_dias` creada (ejecuta `schema.sql` en tu servidor MySQL).
2. Coloca esta carpeta `php-api` dentro de la carpeta pública de tu servidor (ej. `htdocs` en XAMPP) o usa el servidor integrado de PHP:

```powershell
cd .\server\php-api
php -S 127.0.0.1:8000
```

3. Probar endpoints:

- Listar empleados:
  http://127.0.0.1:8000/employees.php

- Listar departamentos:
  http://127.0.0.1:8000/employees.php?departments=1

- Listar solicitudes:
  http://127.0.0.1:8000/requests.php

- Crear solicitud (POST JSON):
  POST http://127.0.0.1:8000/requests.php
  Body JSON: { "employee_id": 1, "date_requested": "2025-10-25", "days": 1, "notes": "..." }

- Actualizar estado (PUT):
  PUT http://127.0.0.1:8000/requests.php?id=1
  Body JSON: { "status": "aprobada" }
