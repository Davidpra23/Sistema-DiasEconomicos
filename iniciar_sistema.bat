@echo off
title Sistema de Gestion de Dias Economicos - Servidor Local
color 0A

echo =====================================================================
echo    SISTEMA DE GESTION DE DIAS ECONOMICOS - SERVIDOR LOCAL
echo =====================================================================
echo.
echo Buscando un puerto disponible en el sistema...

rem Buscar puerto libre a partir del 8000 hasta el 8100
powershell -Command "for ($port = 8000; $port -le 8100; $port++) { $listener = New-Object System.Net.Sockets.TcpListener([System.Net.IPAddress]::Loopback, $port); try { $listener.Start(); $listener.Stop(); $port; break; } catch {} }" > "%temp%\temp_economicos_port.txt"

set /p PORT=<"%temp%\temp_economicos_port.txt"
del "%temp%\temp_economicos_port.txt"

if "%PORT%"=="" (
    color 0C
    echo [ERROR] No se pudo encontrar ningun puerto libre entre 8000 y 8100.
    echo Por favor, cierra aplicaciones que puedan estar usando estos puertos e intenta de nuevo.
    echo.
    pause
    exit /b
)

echo.
echo [+] ¡Puerto libre encontrado!: %PORT%
echo [+] Iniciando el servidor local en: http://127.0.0.1:%PORT%
echo [+] Abriendo el navegador web predeterminado...
echo.
echo =====================================================================
echo    ¡IMPORTANTE!: NO CIERRES ESTA VENTANA mientras uses el sistema.
echo    Al cerrar esta ventana, el servidor se detendra.
echo =====================================================================
echo.

start "" "http://127.0.0.1:%PORT%"
".\php\php.exe" -S 127.0.0.1:%PORT%
