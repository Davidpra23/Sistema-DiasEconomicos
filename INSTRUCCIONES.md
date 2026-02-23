# Sistema de Días Económicos - Guía de Uso

## 📋 Novedades - Selección Múltiple de Fechas

### ¿Cómo funciona?

Cuando solicitas **2 o 3 días**, ahora puedes seleccionar múltiples fechas en el calendario haciendo clic en cada día que desees solicitar.

### Características:

✅ **Selección Inteligente**: Haz clic en los días que deseas solicitar
✅ **Validación Mensual**: El sistema respeta el límite de 3 días por mes
✅ **Feedback Visual**: 
   - **Todas las fechas seleccionadas**: Color vino (#611232)
   - **Primera fecha que seleccionaste**: Borde dorado distintivo (⭐) - Esta es la fecha principal
   - Días disponibles: Verde claro
   - Días bloqueados: Rojo (límite alcanzado)
   - Hover: Efecto de zoom y cambio de color

✅ **Información en Tiempo Real**: Muestra cuántos días has seleccionado
✅ **Fecha Principal**: La primera fecha que hagas clic será la fecha principal de la solicitud
✅ **Observaciones Automáticas**: Al seleccionar 2 o 3 días, las fechas adicionales se escriben automáticamente en observaciones
   - 2 días: "También se solicitó la fecha dd/mm/aaaa"
   - 3 días: "También se solicitaron las fechas dd/mm/aaaa y dd/mm/aaaa"
✅ **Botón Limpiar**: Aparece cuando hay fechas seleccionadas para empezar de nuevo con un clic

### Ejemplo de Uso:

1. **Selecciona un empleado** de la lista
2. **Elige el número de días** (1, 2 o 3)
3. **Haz clic en los días** del calendario que deseas solicitar
   - Si eliges 3 días y ya se usaron 2 en enero, solo podrás seleccionar 1 día de enero
   - Los días restantes puedes seleccionarlos de otros meses
4. **Verifica la selección**: El mensaje debajo del selector mostrará el progreso
5. **Previsualiza**: Una vez seleccionados todos los días, haz clic en "Previsualizar"

### Validaciones Automáticas:

🔒 **Límite Mensual**: Máximo 3 días por mes (incluyendo días ya usados)
🔒 **Límite de Selección**: No puedes seleccionar más días de los solicitados
🔒 **Días Bloqueados**: No puedes seleccionar días con solicitudes existentes
🔒 **Validación de Envío**: El sistema verifica que hayas seleccionado todos los días antes de generar la solicitud

### Cambio de Número de Días:

Si cambias el número de días a solicitar, la selección se limpia automáticamente para que puedas empezar de nuevo.

---

## 🚀 Instalación y Uso General

### Requisitos:
- Windows 7 SP1 o superior
- .NET Framework 4.0+ (incluido en Windows 7+)
- Navegador web moderno

### Instrucciones:

1. **Copiar** la carpeta completa "Sistema-de-Dias-Economicos"
2. **Pegar** en cualquier ubicación (USB, Escritorio, Documentos, etc.)
3. **Doble clic** en `Sistema.exe`
4. ¡El sistema se abre automáticamente!

### Características del Sistema:

- ✅ 100% Portable (no requiere instalación)
- ✅ Base de datos SQLite local
- ✅ Generación de PDF automática
- ✅ Gestión de empleados
- ✅ Historial de solicitudes
- ✅ Validaciones inteligentes

---

**Sistema de Gestión de Días Económicos © 2026**
**CIIDIR Guasave, Sinaloa - IPN**
