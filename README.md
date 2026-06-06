# Reports — Plugin para LibreNMS

Plugin que añade un módulo de **reportes profesionales** de rendimiento y disponibilidad de dispositivos directamente en LibreNMS. Permite visualizar métricas históricas de ancho de banda, paquetes, recursos del sistema y disponibilidad, con soporte de exportación a CSV, Excel y PDF. **No modifica ningún archivo del núcleo de LibreNMS.**

---

## Características

- **Tipos de reporte**: ancho de banda, paquetes, recursos del sistema (CPU/memoria) y disponibilidad.
- **Períodos configurables**: personalizado, diario, semanal, mensual y anual.
- **Exportación** a CSV, Excel y PDF desde la propia interfaz.
- **Bitácora de auditoría** de reportes generados, con opción de eliminar entradas (solo admin).
- Etiqueta e icono del menú configurables desde el panel de administración.
- Accesible para todos los usuarios autenticados.

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

```bash
cd /opt/librenms/app/Plugins/Reports
git pull
```

### Desinstalar

1. Desactiva el plugin desde **Configuración → Plugins**.
2. Elimina la carpeta:

```bash
rm -rf /opt/librenms/app/Plugins/Reports
```

---

## Configuración

Los administradores pueden personalizar el plugin en:

**Plugins → Reports → Settings**

| Opción | Descripción |
|---|---|
| Menu label | Texto que aparece en el menú de navegación |
| Menu icon | Icono FontAwesome del menú (ej. `fa-line-chart`) |
| Page title | Título de la página de reportes |
| Page subtitle | Subtítulo descriptivo de la página |

---

## Requisitos

- LibreNMS con soporte de plugins (sistema de hooks `app/Plugins`).
- PHP 8.1+

---

## Autor

**danpal-dev**
- GitHub: [@danpal-dev](https://github.com/danpal-dev)

---

## Licencia

MIT
