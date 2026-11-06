# FAQ — Reportes (Solución de errores frecuentes)

Diagnóstico paso a paso para los 9 problemas más comunes al generar reportes. Cada entrada incluye **causa raíz**, **solución** y **ruta al log** para validar.

---

## ❓ 1. "El dispositivo aparece pero tiene **0 puertos activos**"

### Causa
Los puertos están marcados como `deleted=1`, `disabled=1` o `ignore=1` en la tabla `ports` de LibreNMS, o bien el poller aún no ha descubierto interfaces después de agregar el dispositivo.

### Solución
1. **Descubrimiento manual**:
   ```bash
   sudo -u librenms /opt/librenms/discovery.php -h ID_DISPOSITIVO
   sudo -u librenms /opt/librenms/poller.php -h ID_DISPOSITIVO
   ```
2. Revisa en LibreNMS → Dispositivo → Puertos: ¿aparecen las interfaces? Si no, habilita SNMP en el equipo.
3. Valida SQL:
   ```sql
   SELECT port_id, ifName, deleted, disabled, `ignore`, ifOperStatus
   FROM ports WHERE device_id = X ORDER BY ifIndex;
   ```
4. Si `deleted=1` por accidente, edita el dispositivo → Puertos → "Desmarcar eliminados" y vuelve a correr discovery.

---

## ❓ 2. "Reporte sale **vacío** — sin KPIs ni filas"

### Causa (más frecuente)
El archivo RRD del puerto/dispositivo no existe o el poller nunca ha corrido con éxito. Muy común en dispositivos recién agregados (< 5 minutos).

### Solución
1. Espera **2 ciclos de poller** (~10 min) y reintenta.
2. Verifica existencia del RRD:
   ```bash
   ls -la /opt/librenms/rrd/<HOSTNAME>/port-id<PORT_ID>.rrd
   ```
   Si no existe → `poller.php -h X` y revisa `/opt/librenms/logs/librenms.log`.
3. Para **Resources** (CPU/Mem): deben existir `processor-*.rrd` y `mempool-*.rrd`. Si no, el MIB de CPU/Mem no está soportado para ese OS; ejecuta `discovery.php -h X -m processors,mempools`.
4. Para **Availability**: requiere filas en `device_outages`. Si el dispositivo siempre ha estado UP, el reporte muestra 0 interrupciones y 100% (correcto).

---

## ❓ 3. "SLA muestra 99.9% pero la tabla sale **sin dato**"

### Causa
El cálculo de disponibilidad necesita al menos **1 evento de outage** registrado por el poller `services-wrapper` o `availability-wrapper`. Si el dispositivo no tiene módulo Availability habilitado, `device_outages` permanece vacío.

### Solución
1. Habilita el módulo **Availability** en LibreNMS → Dispositivo → Ajustes → Módulos.
2. (Admin) Valida la cola:
   ```sql
   SELECT * FROM device_outages WHERE device_id = X ORDER BY going_down DESC LIMIT 5;
   ```
3. Si el dispositivo estuvo down antes de habilitar el módulo, esos cortes no se recuperan (normal). A partir de ahora sí se registran.

---

## ❓ 4. "Umbral `contract_bandwidth` duplicado — no me deja agregar"

### Causa
Por diseño el sistema evita duplicados de la clave `port_{ID}_contract_bandwidth` en `devices_attribs`. Si alguien insertó manualmente 2 filas con el mismo `(device_id, attrib_type)`, el mensaje "ya tiene un umbral configurado" aparece aunque no se vea en la UI.

### Solución
1. Elimina duplicados desde SQL:
   ```sql
   SELECT attrib_id, device_id, attrib_type, attrib_value
   FROM devices_attribs
   WHERE attrib_type LIKE 'port_%_contract_bandwidth' AND device_id = X;

   -- deja sólo el attrib_id más alto por clave
   DELETE da1 FROM devices_attribs da1
   JOIN devices_attribs da2
     ON da1.device_id = da2.device_id AND da1.attrib_type = da2.attrib_type AND da1.attrib_id < da2.attrib_id;
   ```
2. Ahora ya puedes agregar/actualizar desde **Ajustes** con normalidad.

---

## ❓ 5. "Exportar a PDF produce **blank page** o `Allowed memory size exhausted`"

### Causa
`SimplePdfWriter` renderiza tablas completas en memoria; períodos >180 días BW con step horario = ~4320 filas × 10 columnas → >256 MB.

### Solución
1. **Aumenta `memory_limit` SOLO para la ruta Reports** (no global): en `Settings.php` data() inicial agrega temporalmente:
   ```php
   ini_set('memory_limit', '512M');
   ```
   O permanentemente en `/etc/php/8.4/fpm/conf.d/99-reports.ini`:
   ```ini
   memory_limit = 512M
   max_execution_time = 120
   ```
2. Acorta el período: si necesitas Annual, exporta a **XLSX** (consume ~60% menos RAM que PDF).
3. Valida log error: `/opt/librenms/logs/librenms.log` buscar `PHP Fatal error: Allowed memory`.

---

## ❓ 6. "`date_from` > `date_to` — el sistema no reporta error"

### Causa
Es comportamiento **diseñado**: la función `validateDate()` y el swap en `Page.php` L70-74 **automáticamente invierten** las fechas si el usuario las ingresa al revés.

### Cómo confirmarlo
En los parámetros ocultos del formulario, `date_from` siempre queda ≤ `date_to` después de pulsar Buscar. Puedes ver los valores actuales en las cajas de fecha.

> Si prefieres comportamiento estricto (error), en `Page.php` reemplaza el bloque swap por:
> ```php
> if ($dateFrom > $dateTo) {
>     $errorMessage = 'La fecha DESDE debe ser menor o igual que HASTA.';
> }
> ```

---

## ❓ 7. "Error: `El puerto no pertenece al dispositivo seleccionado`"

### Causa
Race condition entre la carga del selector `#contractPortSelect` (JS `loadPortsForDevice`) y el submit del formulario. O bien alguien modificó el HTML vía DevTools para enviar un `port_id` ajeno.

### Solución
1. No hagas bookmark directos de URLs antiguas con `?device_id=A&port_id=B`; re-selecciona siempre el dispositivo primero para repoblar el `<select>` de puertos.
2. (Debug) Comprueba coherencia:
   ```sql
   SELECT port_id, device_id, ifName FROM ports WHERE port_id = X;
   ```
3. Esta validación es **intencional** y no debe quitarse (evita contaminar `devices_attribs` con claves malas).

---

## ❓ 8. "Disponibilidad muestra **0%** pero el dispositivo está UP"

### Causa
El poller de Availability registró mal el `came_back` (o `up_again`) en `device_outages`. Ocurre cuando el proceso `availability-wrapper` fue interrumpido durante un corte.

### Solución
1. Busca outages sin `came_back`:
   ```sql
   SELECT outage_id, device_id, FROM_UNIXTIME(going_down),
          came_back IS NULL AS 'sigue_down'
   FROM device_outages
   WHERE device_id = X AND came_back IS NULL ORDER BY going_down DESC LIMIT 3;
   ```
2. Si es un corte antiguo ya resuelto, márcalo manualmente:
   ```sql
   UPDATE device_outages SET came_back = UNIX_TIMESTAMP()
   WHERE outage_id = Y AND came_back IS NULL;
   ```
3. Fuerza un re-cálculo del módulo Availability desde LibreNMS (menu Dispositivo → Disponibilidad → Re-calcular).

---

## ❓ 9. "CSV abre en Excel con **caracteres raros** (Ã©, Ã±) — encoding"

### Causa
Excel para Windows no autodetecta UTF-8 sin BOM. Aunque el plugin ya escribe `\xEF\xBB\xBF` (BOM UTF-8), algunas versiones antiguas de Excel + locale ES ignoran el BOM si el archivo se abre por doble click.

### Solución
**Opción A (recomendada)**: usa **Excel → Datos → From Text/CSV** → elige "65001: Unicode (UTF-8)" como origen.

**Opción B**: entrega **XLSX** en vez de CSV. El XLSX interno siempre es UTF-16 y se abre perfecto en cualquier Excel.

**Opción C (Windows 7/8 antiguos)**: Convertir archivo descargado con `iconv -f UTF-8 -t CP1252`. No recomendamos modificar el plugin a ISO-8859-1 ya que rompería PDFs y otros campos Unicode.

---

## 🗂 Rutas de logs diagnóstico

| Log | Ubicación | Cuándo revisar |
|-----|-----------|:--------------:|
| LibreNMS general | `/opt/librenms/logs/librenms.log` | Errores PHP, fallo poller/discovery |
| Auditoría Reports | `storage/logs/enlaces_report_audit.log` | Qué usuario descargó qué reporte |
| PHP-FPM slowlog | `/var/log/php8.4-fpm/slow.log` | Exportaciones PDF lentas > 5s |
| PHP-FPM error | `/var/log/php8.4-fpm/error.log` | memory_limit / segfault |
| RRD base dir  | `/opt/librenms/rrd/<HOSTNAME>/` | Archivos .rrd faltantes |

---

## 🔧 Comandos de diagnóstico rápido

```bash
# 1. Ver estado plugin Reports
cd /opt/librenms && php artisan tinker --execute="
echo DB::selectOne(\"SELECT plugin_name,plugin_active FROM plugins WHERE plugin_name='Reports'\")->plugin_active;
"

# 2. Últimos errores PHP
tail -n 50 /opt/librenms/logs/librenms.log | grep -i "reports\|report\|plugin"

# 3. Verificar extensiones PHP requeridas
php -m | grep -E "gd|mbstring|zip|pdo_mysql"

# 4. Tamaño RRD por dispositivo
du -sh /opt/librenms/rrd/MI-HOSTNAME/
```
