# Reports — Plugin para LibreNMS

![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)
![LibreNMS](https://img.shields.io/badge/LibreNMS-compatible-green)
![License](https://img.shields.io/badge/license-MIT-lightgrey)

Plugin que añade un módulo de **reportes profesionales** de rendimiento y disponibilidad de dispositivos directamente en LibreNMS. Permite visualizar métricas históricas de ancho de banda, paquetes, recursos del sistema y disponibilidad, con soporte de exportación a CSV, Excel y PDF. **No modifica ningún archivo del núcleo de LibreNMS.**

## Screenshots

> Agrega tus capturas de pantalla aquí.
> Puedes subir imágenes a la carpeta `screenshots/` del repositorio y referenciarlas así:
> `![Reportes](screenshots/reports.png)`

---

## Características

### Reportes
- **Tipos de reporte**: ancho de banda, paquetes, recursos del sistema (CPU/memoria) y disponibilidad.
- **Períodos configurables**: personalizado, diario, semanal, mensual y anual.
- **Checkbox "Incluir último día completo"**: en rangos personalizados, extiende el período hasta las 23:59:59 del día final.
- **Exportación** a CSV, Excel y PDF desde la propia interfaz.
- **Bitácora de auditoría** de reportes generados, con opción de eliminar entradas (solo admin).

### Gráficas
- **Tipo de gráfica configurable**: línea suave (área rellena), línea simple, barras verticales u horizontales.
- **Paleta de colores armónica**: azul, teal, violeta, ámbar y naranja; diferenciados del umbral (rojo).
- **Umbral de velocidad contratada**: se muestra siempre como línea discontinua roja independientemente del tipo de gráfica seleccionado.

### Disponibilidad y SLA
- **Disponibilidad con detalle de eventos**: fecha/hora de inicio, recuperación y duración de cada caída.
- **Gráfica de disponibilidad diaria**: evolución día a día del porcentaje de uptime con línea de referencia SLA.
- **SLA por dispositivo**: objetivo de disponibilidad configurable individualmente (p. ej. 99.9 %). Se almacena en `devices_attribs` y se muestra como línea discontinua en la gráfica.
- **SLA por defecto**: 43 minutos de indisponibilidad por mes cuando no hay SLA específico configurado.
- Los intervalos de caída solapados se consolidan antes de calcular la disponibilidad.

### Configuración de umbrales
- **Velocidad contratada por puerto**: agrega un umbral de Mbps por interfaz; se valida unicidad dispositivo-puerto.
- **SLA por dispositivo**: presets rápidos (99.9 % / 99.5 % / 99.0 % / 98.0 %), preview de downtime en tiempo real y tabla con badges de color por nivel de SLA.

### General
- Dropdown de dispositivos muestra el campo `display` cuando está disponible.
- Etiqueta, icono, título y subtítulo del menú configurables desde el panel de administración.
- Accesible para todos los usuarios autenticados; acciones de administración protegidas por política de autorización.
- Las exportaciones CSV neutralizan valores que una hoja de cálculo podría interpretar como fórmulas.

---

## Instalación

### 1. Clonar el repositorio

```bash
cd /opt/librenms/app/Plugins
git clone https://github.com/danpal-dev/librenms-plugin-reports.git Reports
```

### 2. Corregir permisos

```bash
chown -R librenms:librenms /opt/librenms/app/Plugins/Reports
```

### 3. Activar el plugin en LibreNMS

1. Inicia sesión en LibreNMS como administrador.
2. Ve a **Configuración → Plugins** (o accede a `/plugins`).
3. Busca **Reports** en la lista y haz clic en **Enable**.

### Actualizar

> Repositorio: https://github.com/danpal-dev/librenms-plugin-reports

```bash
cd /opt/librenms/app/Plugins/Reports
git pull
chown -R librenms:librenms .
sudo -u librenms php artisan view:cache
sudo -u librenms php artisan cache:clear
```

### Desinstalar

1. Desactiva el plugin desde **Configuración → Plugins**.
2. Elimina la carpeta:

```bash
rm -rf /opt/librenms/app/Plugins/Reports
```

---

## Configuración

Los administradores pueden personalizar el plugin en **Plugins → Reports → Settings**.

### Apariencia

| Opción | Descripción |
|---|---|
| Etiqueta de menú | Texto que aparece en el menú de navegación |
| Icono FontAwesome | Icono del menú (ej. `fa-line-chart`) |
| Título principal | Encabezado del banner de la página |
| Subtítulo | Descripción breve bajo el título |
| Tipo de gráfica | `line` · `line_clean` · `bar` · `bar_h` |

### Umbrales de velocidad contratada

Agrega por dispositivo y puerto la velocidad contratada en Mbps. Se renderiza como línea discontinua roja en los reportes de ancho de banda.

### SLA por dispositivo

Define el porcentaje de disponibilidad objetivo para cada equipo (90 – 100 %). Se almacena en la tabla `devices_attribs` con `attrib_type = 'device_sla_target'` y se usa como línea de referencia en la gráfica de disponibilidad.

---

## Requisitos

- LibreNMS con soporte de plugins (sistema de hooks `app/Plugins`).
- PHP 8.1+

## Base de datos

El plugin **no crea tablas nuevas**. Usa tablas estándar de LibreNMS que ya existen en cualquier instalación:

| Tabla | Uso | Columnas escritas |
|---|---|---|
| `devices_attribs` | SLA por dispositivo | `attrib_type = 'device_sla_target'`, `attrib_value` |
| `devices_attribs` | Velocidad contratada por puerto | `attrib_type = 'port_<port_id>_contract_bandwidth'`, `attrib_value` |
| `plugins` | Ajustes del plugin (label, icon, chart_type…) | `settings` (JSON) |

Para eliminar todos los datos del plugin de la BD al desinstalar:

```sql
DELETE FROM devices_attribs WHERE attrib_type = 'device_sla_target' OR attrib_type LIKE 'port_%_contract_bandwidth';
```

---

## Autor

**danpal-dev**
- GitHub: [@danpal-dev](https://github.com/danpal-dev)

---

## Licencia

MIT
