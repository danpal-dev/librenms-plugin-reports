# Guía de Uso — Reportes

Genera reportes profesionales de **ancho de banda, paquetes, recursos y disponibilidad SLA** directamente desde LibreNMS, con exportación a CSV, Excel y PDF listos para facturación y Comité Técnico.

---

## 1. Flujo operativo NOC (4 pasos)

| Paso | Acción | Tecla / Botón |
|------|--------|---------------|
| 1 | Selecciona **Tipo de reporte**, **Dispositivo**, **Período** y (si aplica) **Interfaz** | Filtros superiores → `Buscar` |
| 2 | Revisa los **KPIs ejecutivos** en tarjetas de colores y la gráfica de tendencia | KPI + Canvas `#erxChart` |
| 3 | Valida el **cumplimiento de umbral** (BW contratado) y **SLA contractual** | Línea roja umbral · Chip Cumple/Fuera SLA |
| 4 | Exporta en el formato necesario para facturación / auditoría | CSV · Excel · PDF (botones derecha) |

---

## 2. Tipos de reporte

| Tipo | Objetivo | Métricas principales | Requiere puerto |
|------|----------|----------------------|:---------------:|
| **Ancho de Banda** (bandwidth) | Facturación de tráfico por enlace | Prom/Máx/Mín IN-OUT Mbps · Totales GB · Umbral contratado | ✅ |
| **Paquetes** (packets) | Diagnóstico de errores en capa 2 | Errores IN/OUT · Descartados IN/OUT · Día pico | ✅ |
| **Recursos** (resources) | Capacidad de CPU/Memoria por dispositivo | CPU % prom/máx · Mem % prom/máx · Días >80% | ❌ |
| **Disponibilidad** (availability) | Cumplimiento SLA y downtime | % Uptime · Horas down · Nº interrupciones · MTTR · Compliance | ❌ |

---

### 2.1 Selección de período

- **Daily**: Últimas 24h (día natural)
- **Weekly**: Últimos 7 días naturales
- **Monthly**: Últimos 30 días naturales
- **Annual**: Últimos 365 días
- **Custom**: Rango manual `date_from` → `date_to`. Activa el checkbox **Incluir día final completo** para incluir `date_to` hasta las 23:59:59.

> ⚠ El filtrado máximo recomendado sin agregar memoria PHP es **365 días** para BW/Packets y **730 días** para Availability.

---

## 3. Interpretación de KPIs y colores

### Ancho de banda
| KPI | Unidad | Color umbral |
|-----|--------|:------------:|
| Entrada Promedio / Salida Promedio | Mbps | Slate |
| Entrada Máximo / Salida Máximo    | Mbps | Ámbar (>80% contrato) · Rojo (>100% contrato) |
| Total Entrada GB / Total Salida GB | GB   | Verde |
| Umbral Contratado                 | Mbps | Línea roja fija en gráfica y tabla `rpt-row-warn/high` |

### Disponibilidad SLA
- **Cumple SLA** (chip verde) → downtime del período ≤ umbral contractual mensual × meses.
- **Fuera de SLA** (chip rojo)  → downtime supera el límite; abre incidencia en Comité.
- El SLA por defecto es **99.9%** (43.2 min/mes) si no has configurado un target por dispositivo en **Ajustes**.

---

## 4. Umbrales Contract Bandwidth y SLA Target

Se configuran desde el menú superior derecho **⚙ Ajustes** (sólo rol `admin`):

### 4.1 Velocidad contratada por puerto
1. Panel **Velocidades Contratadas** → selecciona Dispositivo.
2. El sistema carga dinámicamente sus puertos activos (`loadPortsForDevice`).
3. Ingresa Mbps (1 – 1.000.000) → **Agregar**.
4. El atributo se almacena en `devices_attribs` con clave `port_{PORT_ID}_contract_bandwidth`.
5. A partir del siguiente reporte, la gráfica dibuja la línea de umbral y las filas se pintan si la superan.

### 4.2 SLA por dispositivo
1. Panel **SLA Disponibilidad** → elige Dispositivo y % (98 – 100, step 0.01).
2. Usa los **presets rápidos**: 99.9% / 99.5% / 99% / 98%.
3. Al escribir, `slaPreview()` calcula en vivo los **minutos/mes permitidos**.
4. La clave almacenada es `device_sla_target` en `devices_attribs`.

---

## 5. Exportación y auditoría

### 5.1 Formatos exportados
| Formato | Encoding | Librería interna | Uso típico |
|---------|----------|:----------------:|------------|
| **CSV**  | UTF-8 BOM | nativo `fputcsv` | Abrir en Excel local / Google Sheets |
| **XLSX** | Office Open XML | `SimpleXlsxWriter` (sin PhpSpreadsheet) | Informe ejecutivo con hojas formateadas |
| **PDF**  | Binary    | `SimplePdfWriter` (GD+TTF, sin DomPDF) | Entregar a cliente / facturación |

El nombre de archivo sigue el patrón seguro:
```
reporte_{TIPO}_{HOSTNAME}_{DESDE}_{HASTA}.{ext}
```
donde `HOSTNAME` se sanitiza con regex `[^A-Za-z0-9._-]` para evitar ataques path traversal.

### 5.2 Registro de auditoría
Cada exportación graba una línea JSON en:
```
storage/logs/enlaces_report_audit.log
```
con campos: `created_at`, `username`, `role_name`, `action_type`, `report_type`, `device_name`, `period_name`, `date_from`, `date_to`, `rows_count`, `ip_address`, `user_agent`.

Desde **Ajustes → Auditoría** los admins pueden:
- Ver las últimas 20 descargas.
- Eliminar entradas individuales.
- Limpiar el log completo.

---

## 6. Workflow ejemplo: Facturación mensual de enlace

1. Entra a `/plugin/Reports`.
2. Tipo: `Ancho de Banda` · Dispositivo: `Router-Core-Sede` · Interfaz: `GigabitEthernet0/0 (Uplink proveedor)` · Período: `Monthly`.
3. Pulsa **Buscar**.
4. Valida KPIs: ¿Promedio < contrato? ¿Pico max < 110% contrato?
5. Revisa la línea roja de umbral en el gráfico.
6. Pulsa **PDF** → archivo se descarga listo para enviar al cliente.
7. Pulsa **CSV** → respaldo editable para tu propio ERP.
8. (Admin) Revisa la pestaña Auditoría para confirmar que la descarga quedó registrada.

---

## 7. Atajos de teclado / consejos UI

- Al cambiar **Tipo de reporte** el formulario se envía solo (`onchange="this.form.submit"`).
- Al cambiar **Período = Custom** se revelan los inputs `#date_from_input` y `#date_to_input`.
- Checkbox **Incluir día final**: desmarcado = `date_to` a las 00:00 (solo el inicio); marcado = 23:59:59 (día completo).
- Si el número de filas es > 500, recomendamos **Excel** en lugar de **PDF** para no alcanzar `memory_limit`.
