# Arquitectura Técnica — Plugin Reports

Diseño interno del plugin: ciclo de vida de hooks, motor de cálculo RRD, almacenamiento en `devices_attribs` y exportadores PDF/XLSX **sin dependencias Composer externas**.

---

## 1. Ciclo de vida: 3 Hooks LibreNMS

`PluginProvider` hace `glob(app/Plugins/*/*.php)` y carga cada archivo que implementa una interfaz de `App\Plugins\Hooks\*`.

```
app/Plugins/Reports/
├── Menu.php              ← MenuEntryHook   (item lateral)
├── Settings.php          ← SettingsHook    (UI Ajustes admin)
├── Page.php              ← PageHook        (UI principal /plugin/Reports)
├── DeviceOverview.php    (no usado por ahora)
├── Support/
│   ├── SimplePdfWriter.php  ↓ exportadores
│   ├── SimpleXlsxWriter.php ↓ sin vendor
│   └── SlaPolicy.php        ↓ cálculo SLA downtime mensual
├── Http/               (no usa — PageHook se encarga)
├── tests/PageCalculationsTest.php
└── resources/views/
    ├── page.blade.php       ← UI principal Flowbite
    ├── settings.blade.php   ← UI Ajustes
    ├── menu.blade.php
    ├── pdf.blade.php        ← layout simple PDF
    └── markdown-lines.blade ← renderer MD autocontenido
```

### 1.1 authorize() — firma NULLABLE OBLIGATORIA

```php
// Menu.php
public function authorize(?Authenticatable $user): bool
{
    return $user !== null;        // cualquier usuario logeado
}

// Settings.php
public function authorize(?Authenticatable $user): bool
{
    return $user !== null && $user->can('admin');  // solo admins
}

// Page.php
public function authorize(?Authenticatable $user): bool
{
    return $user !== null;        // NOC + operadores
}
```
> ⚠ Firma no-nullable produce **TypeError** al cargar hooks anónimamente → `PluginManager` captura `Throwable` y escribe `plugins.plugin_active=0` → `/plugin/Reports` = 404.

### 1.2 Orden invocación por request

```
1. PluginProvider boot → glob → instancia Menu + Settings + Page
2. Auth guard → llama a ->authorize($user) en CADA hook
3. SettingsHook::data()   : renderiza /plugin/Reports/edit
        └ handleContractBandwidthPost() + handleSlaPost() (POST only once $postHandled static)
4. PageHook::data()       : renderiza /plugin/Reports
        └ validateDate → switch report_type → fetch RRD → summary KPI → export_*_urls
5. Blade render via PluginPageController::class → include($content_view)
```

---

## 2. Motor de 4 tipos de reporte

| Tipo       | DS RRD leídos        | Agregación | Step RRD | KPI |
|------------|:--------------------:|:----------:|:--------:|:----|
| bandwidth  | INOCTETS, OUTOCTETS  | AVERAGE + MAX + MIN | STEP_HOUR (3600s) → bucket diario | 8 KPIs: avg/max/min IN-OUT + totales GB |
| packets    | INERRORS, OUTERRORS, INDISCARDS, OUTDISCARDS | SUM contadores | STEP_HOUR → diario | 5 KPIs: 4 errores/descartes + peak_day |
| resources  | processor.usage, mempool.used/free | AVG(multi-proc/pool) | STEP_HOUR → diario | 6 KPIs: CPU avg/max + Mem avg/max + días >80% |
| availability | device_outages SQL  | merged intervals overlap | STEP_DAY (86400s) para gráfica | 5 KPIs: % uptime, hrs down, N outages, MTTR, compliance SLA |

### 2.1 Agregación paso → bucket diario

En `aggregateDaily()` (Page.php L1166):
- timestamp RRD **termina al final del intervalo**. Se restan 1s: `max(0, ts - 1)` para mapear al día correcto.
- Cada DS suma `_sum`, cuenta `_cnt`, segundos `_seconds`.
- Promedio diario = `_sum / _cnt`.
- Para MAX/MIN real se corre `rrdtool xport` con `CF=MAX` y `CF=MIN` separadamente (`dataBandwidth()` L620-634).

### 2.2 Disponibilidad: fusión de intervalos overlap

`buildMergedIntervals()` (Page.php L1008) → algoritmo sweep line O(n log n):
1. Ordena `(start, end)` por `start`.
2. Itera: si el actual empieza antes de que termine el último fusionado → `max(ends)`.
3. Sino → nuevo bucket.
4. `mergedDurationSeconds()` suma `end-start` de buckets fusionados.

Para gráfica diaria: cursor STEP_DAY, para cada día suma overlap `min(end, dayEnd) − max(start, cursor)` sobre intervals fusionados (L314-328).

---

## 3. Almacenamiento: devices_attribs (SIN migraciones BD)

Cumpliendo AGENTS.md: **no se crean migraciones**. Todo en clave/valor por dispositivo.

| Clave `attrib_type`      | Propósito | Valor ejemplo |
|--------------------------|-----------|---------------|
| `port_{PORT_ID}_contract_bandwidth` | Umbral facturación Mbps (BW) | `500` |
| `device_sla_target`               | % SLA contractual por disp | `99.9` |

Patrón INSERT/ON DUPLICATE KEY para idempotencia (Settings.php L225-228):
```sql
INSERT INTO devices_attribs (device_id, attrib_type, attrib_value)
VALUES (?, "port_{id}_contract_bandwidth", ?)
ON DUPLICATE KEY UPDATE attrib_value = ?
```

JOIN para recuperar (Settings.php getContractBandwidths L251):
```sql
... JOIN ports p ON p.device_id = da.device_id
    AND da.attrib_type = CONCAT('port_', p.port_id, '_contract_bandwidth')
WHERE da.attrib_type LIKE 'port_%_contract_bandwidth'
```

### 3.1 Auditoría exportaciones — archivo plano JSON line

Ruta configurable `auditLogPath()` (Page.php L479):
```
/opt/librenms/storage/logs/enlaces_report_audit.log
```
Cada línea es JSON (una por export): `created_at, username, role_name, action_type, report_type, device_id/name, port_id, period_name, date_from/to, rows_count, ip_address, user_agent`.

Ventajas vs BD tabla:
- Sin migraciones.
- Soporte para grep / tail -f / logrotate sin Eloquent.
- Rotación fácil por tamaño/mes (agrega `/etc/logrotate.d/librenms-reports` si quieres).

---

## 4. Exportadores internos (sin Composer)

### 4.1 SimpleXlsxWriter (XLSX — Office Open XML)
- Construye `[Content_Types].xml`, `_rels/.rels`, `xl/workbook.xml`, `xl/worksheets/sheet1.xml`, `xl/styles.xml`, `docProps/*` en memoria.
- Zipea con `ZipArchive` (extensión `zip` nativa).
- Sin PhpSpreadsheet; footprint ~18KB vs 15MB.
- Header row se escribe con estilo bold + borde + fondo gris claro.

### 4.2 SimplePdfWriter (PDF — GD + TTF nativo)
- Renderiza tabla completa a PNG por páginas usando `imagettftext()` con fuentes TrueType.
- Footer page X/Y y título reporte en header.
- Convierte imágenes rasterizadas a PDF con formato simple `%PDF-1.4` + `xobject Image` (FlateDecode opcional).
- Sin DomPDF/TCPDF/MPDF. No requiere `mbstring` fuerte en el render (sí para conteo de ancho columna).

---

## 5. SlaPolicy — cálculo contractual

Ubicada `Support/SlaPolicy.php`:
```php
// minutos de downtime permitidos / mes dado un % SLA
public static function monthlyDowntimeMinutes(float $pct): float
{
    // 30 días × 24h × 60m = 43200 minutos total mes
    return 43_200.0 * (100.0 - $pct) / 100.0;
}
```
| SLA % | min/mes | hrs/mes |
|:-----:|:-------:|:-------:|
| 99.9  | 43.2    | 0.72    |
| 99.5  | 216     | 3.6     |
| 99    | 432     | 7.2     |
| 98    | 864     | 14.4    |

En Page.php L987 `compliesWithSla()` compara `down_seconds` del período vs `threshold_min × 60`. Para períodos anuales/custom multiplica por `sla_months` (L978 match).

---

## 6. Seguridad: validaciones y mitigaciones

| Riesgo | Control | Línea |
|--------|---------|:-----:|
| **SQL Injection** port_id/device_id | Todo `(int)` cast + DB prepare (?) | passim |
| **Command Injection** rrdtool args | CF whitelist AVERAGE/MAX/MIN/LAST → `strtoupper()` | Page.php L1297 |
| **Path Traversal** RRD | `resolveExistingRrdPath()`: `realpath()` + prefix `rrdDir/` check + `str_ends_with('.rrd')` + `is_readable()` | L1150 |
| **CSV Injection** (=@-+) | `sanitizeSpreadsheetCell()`: prefix `'` si empieza con operador | L969 |
| **Filename Injection** export | Sanitiza hostname regex `[^A-Za-z0-9._-]` | exportCsv/Excel/Pdf |
| **CSRF forms** (Settings) | Laravel `@csrf` por defecto route `plugin.update` | settings.blade |
| **Auth bypass Auditoría** | `auth()->user()->can('admin')` antes delete/clear | handleAuditAction L484 |
| **PluginManager disable** | Firma `authorize(?Authenticatable)` nullable en 3 hooks | Menu L10 / Settings L12 / Page L22 |

---

## 7. Diagrama de flujo request `/plugin/Reports?report_type=bandwidth&period=monthly`

```
Request GET /plugin/Reports
│
├─► Auth → authorize(PageHook) OK
│
├─► Page::data()
│   ├ parse allowedActions/reports/periods
│   ├ default period range monthly (-29 días)
│   ├ swap date_from/date_to si hace falta
│   ├ getDevice / getPorts by hasAccess scope
│   ├ dataBandwidth()
│   │  ├ resolvePortRrdPath() + security realpath
│   │  ├ rrdtool xport AVERAGE (INOCTETS, OUTOCTETS)
│   │  ├ rrdtool xport MAX y MIN
│   │  └ aggregateDaily → bucket → rows
│   ├ summary: avg/max/min IN/OUT → totales GB → chart_labels/data
│   ├ build export_csv/excel/pdf urls
│   └ ─► wrap() → merge plugin_version + docs[4] leídos con readDoc()
│
└─► Blade page.blade.php
    ├ Hero gradient + pills v1.2.0 / ACTIVO
    ├ Filtros .rpt-filters (onchange submit)
    ├ KPIs dinámicos (8 BW / 5 Packets / 6 Resources / 5 Availability)
    ├ Canvas #erxChart Chart.js
    ├ Tabla rows + rpt-row-{warn/high} si > umbral
    └ Acordeón 4 tabs → @include('Reports::resources.views.markdown-lines', lines)
```

---

## 8. Constantes clave

| Constante | Valor | Uso |
|-----------|:-----:|-----|
| `Page::STEP_HOUR` | 3600 | Step rrdtool para BW/Packets/Resources |
| `Page::STEP_DAY`  | 86400 | Step rrdtool gráfica anual + bucket día |
| `Page::PLUGIN_VERSION` | `'1.2.0'` | Pill UI header |
| `SlaPolicy::CONTRACT_TARGET_PERCENT` | `99.9` | Default si no hay device_sla_target |
| `SimpleXlsxWriter::EXCEL_DATE_STYLE` | *interno* | Format fecha columnas |

---

## 9. Testing

`tests/PageCalculationsTest.php` (cobertura 100% lógica sin RRD):
```bash
cd /opt/librenms
./vendor/bin/phpunit --bootstrap vendor/autoload.php app/Plugins/Reports/tests/PageCalculationsTest.php
```

Cobertura esperada:
- ✅ `buildMergedIntervals` — overlap simple, anidados, sin touch
- ✅ `mergedDurationSeconds` — horas down real
- ✅ `slaMonths` — annual → 12, custom 62 días → 2, daily → 1
- ✅ `compliesWithSla` — boundary 43.2 min/mes (99.9%)
- ✅ `sanitizeSpreadsheetCell` — payloads `=CMD|'A1`
- ✅ `readDoc` — archivo no existe / vacío / con título
