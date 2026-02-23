# Sistema de Gestión de Días Económicos

Una aplicación web diseñada para administrar y controlar los días económicos (permisos) de los empleados, permitiendo gestionar solicitudes, departamentos y llevar un registro histórico, todo funcionando de manera local y portable.

## Tecnologías y lenguajes utilizados

*   **HTML5** (Estructura)
*   **CSS3** (Estilos y diseño responsivo)
*   **JavaScript (ES6)** (Lógica del lado del cliente)
*   **PHP** (Backend y API REST)
*   **SQLite** (Base de datos local y portable, sin necesidad de instalación de servidores SQL externos)
*   **Python** (Scripts de utilidad para mantenimiento de base de datos)
*   **jsPDF & html2canvas** (Generación de reportes PDF)

## Componentes principales

*   **Frontend Principal**: `index.html` (Panel de administración y calendario), `Frontend/css/Estilosindex.css`.
*   **Gestión**: `Backend/GestionEmpleados.html` (CRUD de empleados y departamentos), `Frontend/css/Estilosgestion.css`.
*   **Historial**: `Backend/Historial.html` (Registro de solicitudes pasadas), `Frontend/css/Estiloshistorial.css`.
*   **Backend API (PHP)**:
    *   `server/php-api/employees.php`: Gestión de empleados.
    *   `server/php-api/requests.php`: Gestión de solicitudes.
    *   `server/php-api/departments.php`: Gestión de departamentos.
    *   `server/php-api/db.php`: Conexión a la base de datos SQLite.
*   **Base de Datos**: `server/database.db` (Archivo único de base de datos).

## Cómo correr la app

Este sistema está diseñado para ser **portable** y **autocontenido**. No requieres instalar XAMPP ni configurar servidores complejos si utilizas el script incluido.

### Prerrequisitos
*   Sistema Operativo Windows (probado en Windows 10/11).
*   No se requiere instalación previa de PHP ni bases de datos (los binarios de PHP están incluidos en la carpeta `php/`).

### Pasos para iniciar

1.  **Ejecuta el iniciador automático**:
    Haz doble clic en el archivo `iniciar_sistema.bat` ubicado en la raíz del proyecto.

    Este script se encargará de:
    *   Iniciar el servidor PHP local incluido en el puerto 8000.
    *   Configurar automáticamente las extensiones necesarias (SQLite, MBString).
    *   Abrir tu navegador predeterminado en la aplicación.

2.  **Uso de la aplicación**:
    *   El navegador se abrirá en: `http://localhost:8000`
    *   Desde ahí podrás gestionar empleados, crear nuevas solicitudes y visualizar el historial.

### (Opcional) Ejecución manual o reinicio de base de datos

Si deseas reiniciar la base de datos a su estado de fábrica (borrando todos los datos nuevos):
1.  Abre una terminal en la carpeta del proyecto.
2.  Ejecuta el script de reinicio (requiere Python instalado):
    ```bash
    python server/php-api/init_db.py
    ```

## Nota
El sistema utiliza una base de datos **SQLite** (`server/database.db`). Este archivo contiene toda la información del sistema. Si deseas hacer una copia de seguridad, simplemente copia este archivo en otra ubicación.
