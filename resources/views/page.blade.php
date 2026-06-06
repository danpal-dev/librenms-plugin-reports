@php
    /* ── Leer accent del FlowbiteTheme (fallback azul) ── */
    $fbSettings  = \App\Plugins\FlowbiteTheme\Settings::getSettings();
    $fbAccentMap = [
        'blue'    => ['500'=>'#3b82f6','600'=>'#2563eb','700'=>'#1d4ed8','bg'=>'rgba(37,99,235,.08)'],
        'indigo'  => ['500'=>'#6366f1','600'=>'#4f46e5','700'=>'#4338ca','bg'=>'rgba(79,70,229,.08)'],
        'violet'  => ['500'=>'#8b5cf6','600'=>'#7c3aed','700'=>'#6d28d9','bg'=>'rgba(124,58,237,.08)'],
        'emerald' => ['500'=>'#10b981','600'=>'#059669','700'=>'#047857','bg'=>'rgba(5,150,105,.08)'],
        'teal'    => ['500'=>'#14b8a6','600'=>'#0d9488','700'=>'#0f766e','bg'=>'rgba(13,148,136,.08)'],
        'cyan'    => ['500'=>'#06b6d4','600'=>'#0891b2','700'=>'#0e7490','bg'=>'rgba(8,145,178,.08)'],
        'rose'    => ['500'=>'#f43f5e','600'=>'#e11d48','700'=>'#be123c','bg'=>'rgba(225,29,72,.08)'],
        'amber'   => ['500'=>'#f59e0b','600'=>'#d97706','700'=>'#b45309','bg'=>'rgba(217,119,6,.08)'],
    ];
    $fbAcc = $fbAccentMap[$fbSettings['accent_color'] ?? 'blue'] ?? $fbAccentMap['blue'];
@endphp

<style>
/* ── Variables locales del módulo Reportes ── */
.erx-wrap {
    margin: 16px 18px 32px;
    --erx-accent:     {{ $fbAcc['600'] }};
    --erx-accent-h:   {{ $fbAcc['700'] }};
    --erx-accent-lt:  {{ $fbAcc['500'] }};
    --erx-accent-bg:  {{ $fbAcc['bg'] }};
}

/* ── Hero / Banner ── */
.erx-hero {
    background: linear-gradient(135deg, #1e2432 0%, {{ $fbAcc['700'] }} 60%, {{ $fbAcc['500'] }} 100%);
    color: #fff;
    border-radius: 12px;
    padding: 22px 26px;
    box-shadow: 0 8px 32px rgba(0,0,0,.18);
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
}
.erx-hero-icon {
    flex-shrink: 0;
    background: rgba(255,255,255,.15);
    border-radius: 14px;
    width: 56px; height: 56px;
    display: flex; align-items: center; justify-content: center;
}
.erx-hero-icon i { font-size: 26px; color: #fff; }
.erx-hero-body { flex: 1; min-width: 0; }
.erx-hero-body h2 { margin: 0 0 4px; font-size: 22px; font-weight: 700; letter-spacing: -.3px; }
.erx-hero-body p  { margin: 0; font-size: 13px; opacity: .85; line-height: 1.5; }
.erx-hero-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px; }
.erx-hero-actions .btn { font-size: 13px; font-weight: 500; border-radius: 8px; }

/* ── Panel card base ── */
.erx-panel {
    margin-top: 16px;
    background: #fff;
    border: 1px solid #e5eaf0;
    border-radius: 12px;
    /* overflow: hidden eliminado — permite que el datepicker salga del card */
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
}
/* Encabezado con bordes redondeados sin overflow:hidden en el padre */
.erx-panel > .erx-panel-header:first-child {
    border-radius: 12px 12px 0 0;
}
.erx-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid #e5eaf0;
    background: #f8fafc;
}
.erx-panel-header-title { font-weight: 600; font-size: 14px; color: #1a2332; display: flex; align-items: center; gap: 8px; }
.erx-panel-header-title i { color: var(--erx-accent); font-size: 15px; }
.erx-panel-header-sub { font-size: 12px; color: #6b7280; margin-top: 2px; }
.erx-panel-body { padding: 16px 18px; }

/* ── Filtros ── */
.erx-filters-grid {
    display: grid;
    grid-template-columns: repeat(6, minmax(100px, 1fr));
    gap: 12px;
    align-items: end;
}
.erx-filters-grid .form-group { margin-bottom: 0; }
.erx-filters-grid label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .4px; }
.erx-filters-grid .form-control { border-radius: 8px; border-color: #d1d5db; font-size: 13px; }
.erx-filters-grid .form-control:focus { border-color: var(--erx-accent); box-shadow: 0 0 0 3px var(--erx-accent-bg); }

/* ── z-index datepicker ── */
.bootstrap-datetimepicker-widget { z-index: 9999 !important; }

/* ── Separador de fechas personalizadas ── */
.erx-dates-row {
    display: none;   /* oculto por defecto; JS lo muestra al elegir 'custom' */
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed #d1d5db;
    align-items: end;
    gap: 12px;
    grid-template-columns: 200px 200px auto;
}
.erx-dates-row.erx-dates-visible { display: grid; }
.erx-dates-row .form-group { margin-bottom: 0; }
.erx-dates-row label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .4px; }
.erx-dates-row .form-control { border-radius: 8px; border-color: #d1d5db; font-size: 13px; }
.erx-dates-hint { font-size: 12px; color: #9ca3af; align-self: center; }
.erx-divider { height: 1px; background: #e5eaf0; margin: 14px 0; }

/* ── Botón generar ── */
.erx-btn-gen {
    background: var(--erx-accent);
    border-color: var(--erx-accent);
    color: #fff;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    width: 100%;
    transition: background .15s, border-color .15s, transform .1s;
}
.erx-btn-gen:hover, .erx-btn-gen:focus {
    background: var(--erx-accent-h);
    border-color: var(--erx-accent-h);
    color: #fff;
    transform: translateY(-1px);
}

/* ── KPIs ── */
.erx-kpis {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    margin-top: 16px;
}
.erx-kpi {
    background: var(--erx-accent-bg);
    border: 1px solid rgba(0,0,0,.06);
    border-left: 4px solid var(--erx-accent);
    border-radius: 10px;
    padding: 14px 16px;
    display: flex; flex-direction: column; gap: 4px;
}
.erx-kpi-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #6b7280; }
.erx-kpi-value { font-size: 20px; font-weight: 700; color: #1a2332; line-height: 1.2; }
.erx-kpi-icon  { font-size: 22px; color: var(--erx-accent); opacity: .6; margin-bottom: 4px; }

/* ── Export badges ── */
.erx-exports { display: flex; gap: 8px; flex-wrap: wrap; }
.erx-exports .btn { font-size: 12px; font-weight: 600; border-radius: 8px; padding: 5px 14px; }

/* ── Tabla ── */
.erx-table-wrap { overflow-x: auto; margin-top: 4px; }
.erx-table-wrap table { margin-bottom: 0; font-size: 13px; }
.erx-table-wrap thead th {
    background: #f1f5f9;
    color: #374151;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 2px solid #e2e8f0 !important;
    white-space: nowrap;
}
.erx-table-wrap tbody tr:hover td { background: var(--erx-accent-bg); }

/* ── Audit log ── */
.erx-audit-wrap { overflow-x: auto; margin-top: 4px; }
.erx-audit-wrap table { margin-bottom: 0; font-size: 12px; }
.erx-audit-wrap thead th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #64748b;
    border-bottom: 2px solid #e5eaf0 !important;
    white-space: nowrap;
}
.erx-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 9px; border-radius: 99px;
    font-size: 11px; font-weight: 600;
}
.erx-badge-view   { background: #eff6ff; color: #1d4ed8; }
.erx-badge-csv    { background: #f0fdf4; color: #15803d; }
.erx-badge-excel  { background: #f0fdf4; color: #166534; }
.erx-badge-pdf    { background: #fff1f2; color: #be123c; }
.erx-badge-action { background: #faf5ff; color: #6d28d9; }

/* ── Gráfico Chart.js ── */
.erx-chart-wrap { position: relative; height: 240px; margin-top: 16px; }

/* ── KPI variantes de color ── */
.erx-kpi-warning { border-left-color: #f59e0b !important; background: rgba(245,158,11,.07) !important; }
.erx-kpi-danger  { border-left-color: #ef4444 !important; background: rgba(239,68,68,.07) !important; }
.erx-kpi-success { border-left-color: #10b981 !important; background: rgba(16,185,129,.07) !important; }

/* ── SLA badge ── */
.erx-sla {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 14px; border-radius: 99px;
    font-size: 13px; font-weight: 700; letter-spacing: .3px;
}
.erx-sla-success { background: #dcfce7; color: #15803d; }
.erx-sla-info    { background: #dbeafe; color: #1d4ed8; }
.erx-sla-warning { background: #fef9c3; color: #92400e; }
.erx-sla-danger  { background: #fee2e2; color: #b91c1c; }

/* ── Colores condicionales de fila ── */
.erx-row-warn td { background: #fffbeb !important; }
.erx-row-high td { background: #fff1f2 !important; }
.erx-row-ok   td { background: #f0fdf4 !important; }
.erx-cell-warn { color: #b45309; font-weight: 600; }
.erx-cell-high { color: #b91c1c; font-weight: 700; }
.erx-cell-ok   { color: #15803d; font-weight: 600; }

/* ── Barra de disponibilidad ── */
.erx-avail-bar-wrap { height: 14px; background: #fee2e2; border-radius: 99px; overflow: hidden; margin-top: 10px; }
.erx-avail-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #10b981, #34d399); transition: width .6s ease; }

/* ── Responsive ajuste gráfico ── */
@media (max-width: 600px) {
    .erx-chart-wrap { height: 180px; }
    .erx-kpis { grid-template-columns: 1fr 1fr; }
}
.erx-empty {
    text-align: center;
    color: #9ca3af;
    padding: 40px 16px;
}
.erx-empty i { opacity: .35; }
.erx-empty p { margin-top: 12px; font-size: 14px; color: #6b7280; }

/* ── Alert override ── */
.erx-alert { margin-top: 0; border-radius: 8px; }

/* ── Responsive ── */
@media (max-width: 1100px) {
    .erx-filters-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 900px) {
    .erx-filters-grid { grid-template-columns: 1fr 1fr; }
    .erx-dates-row    { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 600px) {
    .erx-filters-grid { grid-template-columns: 1fr; }
    .erx-dates-row    { grid-template-columns: 1fr; }
    .erx-kpis { grid-template-columns: 1fr 1fr; }
    .erx-hero { gap: 12px; padding: 16px 14px; }
    .erx-hero-body h2 { font-size: 18px; }
}
</style>

<div class="erx-wrap">

    {{-- ═══ HERO ═══ --}}
    <div class="erx-hero">
        <div class="erx-hero-icon">
            <i class="fa fa-line-chart" aria-hidden="true"></i>
        </div>
        <div class="erx-hero-body">
            <h2>{{ $title }}</h2>
            <p>{{ $subtitle }}</p>
            <div class="erx-hero-actions">
                @can('admin')
                <a class="btn btn-default btn-sm" href="{{ route('plugin.settings', ['plugin' => 'Reports']) }}"
                   style="background:rgba(255,255,255,.15); border-color:rgba(255,255,255,.35); color:#fff;">
                    <i class="fa fa-cog fa-fw"></i> Configuración
                </a>
                @endcan
                <span style="margin-left:auto; font-size:12px; opacity:.7; align-self:center;">
                    <i class="fa fa-clock-o fa-fw"></i> {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ═══ FILTROS ═══ --}}
    <div class="erx-panel">
        <div class="erx-panel-header">
            <div>
                <div class="erx-panel-header-title">
                    <i class="fa fa-filter" aria-hidden="true"></i> Parámetros del reporte
                </div>
                <div class="erx-panel-header-sub">Seleccione tipo, dispositivo y periodo para generar los datos</div>
            </div>
        </div>
        <div class="erx-panel-body">
            <form method="get" action="{{ url('plugin/Reports') }}">
                <input type="hidden" name="action" value="view">
                <div class="erx-filters-grid">
                    <div class="form-group">
                        <label>Tipo de reporte</label>
                        <select class="form-control" name="report_type" onchange="this.form.submit()">
                            @foreach($report_labels as $val => $lbl)
                                <option value="{{ $val }}" {{ $report_type === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dispositivo</label>
                        <select class="form-control" name="device_id" onchange="this.form.submit()">
                            <option value="0">— Seleccionar —</option>
                            @foreach($all_devices as $dev)
                                @php
                                    $devName = !empty($dev->sysName) ? $dev->sysName : $dev->hostname;
                                    $devIp   = ($devName !== $dev->hostname) ? ' (' . $dev->hostname . ')' : '';
                                @endphp
                                <option value="{{ (int) $dev->device_id }}" {{ (int) $device_id === (int) $dev->device_id ? 'selected' : '' }}>
                                    {{ $devName }}{{ $devIp }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="{{ in_array($report_type, ['bandwidth','packets']) ? '' : 'opacity:.5; pointer-events:none;' }}">
                        <label>Interfaz</label>
                        <select class="form-control" name="port_id">
                            <option value="0">— Seleccionar —</option>
                            @foreach($all_ports as $p)
                                <option value="{{ (int) $p->port_id }}" {{ (int) $port_id === (int) $p->port_id ? 'selected' : '' }}>
                                    {{ $p->ifName }}{{ !empty($p->ifAlias) ? ' · '.$p->ifAlias : '' }}
                                    @if(!empty($p->ifOperStatus))
                                        ({{ $p->ifOperStatus }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Periodo</label>
                        <select class="form-control" name="period" onchange="toggleDates(this.value)">
                            @foreach($period_labels as $val => $lbl)
                                <option value="{{ $val }}" {{ $period === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="btn erx-btn-gen" type="submit">
                            <i class="fa fa-search fa-fw"></i> Generar reporte
                        </button>
                    </div>
                </div>

                <div class="erx-dates-row{{ $period === 'custom' ? ' erx-dates-visible' : '' }}" id="custom-dates-row">
                    <div class="form-group">
                        <label for="date_from_input"><i class="fa fa-calendar fa-fw"></i> Desde</label>
                        <div class="input-group" id="dtpicker_from">
                            <input class="form-control" type="text" name="date_from" id="date_from_input"
                                   value="{{ $date_from }}" placeholder="YYYY-MM-DD" autocomplete="off"
                                   {{ $period !== 'custom' ? 'disabled' : '' }}>
                            <span class="input-group-addon" style="border-radius:0 8px 8px 0; cursor:pointer;">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date_to_input"><i class="fa fa-calendar-check-o fa-fw"></i> Hasta</label>
                        <div class="input-group" id="dtpicker_to">
                            <input class="form-control" type="text" name="date_to" id="date_to_input"
                                   value="{{ $date_to }}" placeholder="YYYY-MM-DD" autocomplete="off"
                                   {{ $period !== 'custom' ? 'disabled' : '' }}>
                            <span class="input-group-addon" style="border-radius:0 8px 8px 0; cursor:pointer;">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="erx-dates-hint">
                        <i class="fa fa-info-circle fa-fw"></i>
                        Rango máximo recomendado: 1 año
                    </div>
                </div>

                {{-- Nota de retención histórica --}}
                <div style="margin-top:12px; padding:8px 14px; background:#f0fdf4; border:1px solid #86efac; border-radius:8px; display:flex; align-items:center; gap:10px; font-size:12px; color:#166534;">
                    <i class="fa fa-database fa-fw" style="font-size:15px; color:#16a34a;"></i>
                    <span>
                        El sistema cuenta con retención de información histórica de monitoreo mayor a <strong>12 meses</strong>
                        &nbsp;·&nbsp; Datos disponibles desde <strong>{{ date('d/m/Y', strtotime('-396 days')) }}</strong> hasta el dia de hoy.
                    </span>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MENSAJE DE ERROR ═══ --}}
    @if(!empty($error_message))
        <div class="alert alert-warning erx-alert" style="margin-top:12px; border-radius:10px; display:flex; align-items:center; gap:10px;">
            <i class="fa fa-exclamation-triangle fa-lg"></i>
            <span>{{ $error_message }}</span>
        </div>
    @endif

    {{-- ═══ ESTADO VACÍO ═══ --}}
    @if((int) $device_id === 0)
        <div class="erx-panel">
            <div class="erx-empty">
                <i class="fa fa-sitemap fa-4x"></i>
                <p>Seleccione un dispositivo y periodo para generar el reporte ejecutivo.</p>
                <p style="font-size:12px; color:#9ca3af;">Los datos se obtendrán de RRD/DB según el tipo de reporte.</p>
            </div>
        </div>

    @elseif(empty($error_message))

        {{-- ═══ RESULTADOS ═══ --}}
        <div class="erx-panel">
            <div class="erx-panel-header">
                <div>
                    <div class="erx-panel-header-title">
                        <i class="fa fa-table" aria-hidden="true"></i>
                        {{ $report_labels[$report_type] ?? $report_type }}
                        @if(!empty($device->hostname))
                            &mdash; {{ $device->hostname }}
                        @endif
                    </div>
                    <div class="erx-panel-header-sub">
                        Periodo: <strong>{{ $date_from }}</strong> al <strong>{{ $date_to }}</strong>
                        &nbsp;·&nbsp; {{ count($report_data) }} registro(s)
                    </div>
                </div>
                @if(!empty($report_data))
                <div class="erx-exports">
                    <a class="btn btn-default btn-sm" href="{{ $export_csv_url }}"
                       style="color:#15803d; border-color:#86efac; background:#f0fdf4; font-weight:600;">
                        <i class="fa fa-file-text-o fa-fw" style="color:#16a34a;"></i> CSV
                    </a>
                    <a class="btn btn-default btn-sm" href="{{ $export_excel_url }}"
                       style="color:#166534; border-color:#6ee7b7; background:#ecfdf5; font-weight:600;">
                        <i class="fa fa-file-excel-o fa-fw" style="color:#217346;"></i> Excel
                    </a>
                    <a class="btn btn-default btn-sm" href="{{ $export_pdf_url }}"
                       style="color:#be123c; border-color:#fca5a5; background:#fff1f2; font-weight:600;">
                        <i class="fa fa-file-pdf-o fa-fw" style="color:#dc2626;"></i> PDF
                    </a>
                </div>
                @endif
            </div>

            <div class="erx-panel-body">
                @if(empty($report_data))
                    <div class="alert alert-info erx-alert" style="margin-bottom:0; display:flex; align-items:center; gap:10px;">
                        <i class="fa fa-info-circle fa-lg"></i>
                        <span>No hay datos para el rango seleccionado. Verifique el polling y los archivos RRD del equipo.</span>
                    </div>
                @else
                    {{-- ══════════════ KPIs por tipo ══════════════ --}}
                    @if(!empty($summary))
                        @if($summary['type'] === 'bandwidth')
                            <div class="erx-kpis">
                                <div class="erx-kpi">
                                    <span class="erx-kpi-icon"><i class="fa fa-arrow-circle-down"></i></span>
                                    <span class="erx-kpi-label">Prom. Entrada</span>
                                    <span class="erx-kpi-value">{{ $summary['avg_in'] !== null ? $summary['avg_in'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">Mbps</span>
                                </div>
                                <div class="erx-kpi">
                                    <span class="erx-kpi-icon"><i class="fa fa-arrow-circle-up"></i></span>
                                    <span class="erx-kpi-label">Prom. Salida</span>
                                    <span class="erx-kpi-value">{{ $summary['avg_out'] !== null ? $summary['avg_out'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">Mbps</span>
                                </div>
                                <div class="erx-kpi erx-kpi-warning">
                                    <span class="erx-kpi-icon"><i class="fa fa-bolt"></i></span>
                                    <span class="erx-kpi-label">Pico Entrada</span>
                                    <span class="erx-kpi-value">{{ $summary['max_in'] !== null ? $summary['max_in'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">Mbps</span>
                                </div>
                                <div class="erx-kpi erx-kpi-warning">
                                    <span class="erx-kpi-icon"><i class="fa fa-bolt"></i></span>
                                    <span class="erx-kpi-label">Pico Salida</span>
                                    <span class="erx-kpi-value">{{ $summary['max_out'] !== null ? $summary['max_out'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">Mbps</span>
                                </div>
                                <div class="erx-kpi erx-kpi-success">
                                    <span class="erx-kpi-icon"><i class="fa fa-database"></i></span>
                                    <span class="erx-kpi-label">Total Entrada</span>
                                    <span class="erx-kpi-value">{{ $summary['total_in_gb'] !== null ? $summary['total_in_gb'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">GB periodo</span>
                                </div>
                                <div class="erx-kpi erx-kpi-success">
                                    <span class="erx-kpi-icon"><i class="fa fa-database"></i></span>
                                    <span class="erx-kpi-label">Total Salida</span>
                                    <span class="erx-kpi-value">{{ $summary['total_out_gb'] !== null ? $summary['total_out_gb'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">GB periodo</span>
                                </div>
                            </div>

                        @elseif($summary['type'] === 'packets')
                            <div class="erx-kpis">
                                <div class="erx-kpi {{ ($summary['total_err_in'] ?? 0) > 1000 ? 'erx-kpi-danger' : (($summary['total_err_in'] ?? 0) > 100 ? 'erx-kpi-warning' : '') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <span class="erx-kpi-label">Total Errores In</span>
                                    <span class="erx-kpi-value">{{ $summary['total_err_in'] !== null ? number_format($summary['total_err_in']) : '—' }}</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['total_err_out'] ?? 0) > 1000 ? 'erx-kpi-danger' : (($summary['total_err_out'] ?? 0) > 100 ? 'erx-kpi-warning' : '') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <span class="erx-kpi-label">Total Errores Out</span>
                                    <span class="erx-kpi-value">{{ $summary['total_err_out'] !== null ? number_format($summary['total_err_out']) : '—' }}</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['total_dis_in'] ?? 0) > 500 ? 'erx-kpi-warning' : '' }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-trash-o"></i></span>
                                    <span class="erx-kpi-label">Descartados In</span>
                                    <span class="erx-kpi-value">{{ $summary['total_dis_in'] !== null ? number_format($summary['total_dis_in']) : '—' }}</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['total_dis_out'] ?? 0) > 500 ? 'erx-kpi-warning' : '' }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-trash-o"></i></span>
                                    <span class="erx-kpi-label">Descartados Out</span>
                                    <span class="erx-kpi-value">{{ $summary['total_dis_out'] !== null ? number_format($summary['total_dis_out']) : '—' }}</span>
                                </div>
                                @if($summary['peak_day'])
                                <div class="erx-kpi erx-kpi-danger">
                                    <span class="erx-kpi-icon"><i class="fa fa-calendar-times-o"></i></span>
                                    <span class="erx-kpi-label">Día Pico</span>
                                    <span class="erx-kpi-value" style="font-size:14px;">{{ $summary['peak_day'] }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">mayor incidencia</span>
                                </div>
                                @endif
                            </div>

                        @elseif($summary['type'] === 'resources')
                            <div class="erx-kpis">
                                <div class="erx-kpi {{ ($summary['avg_cpu'] ?? 0) > 80 ? 'erx-kpi-danger' : (($summary['avg_cpu'] ?? 0) > 60 ? 'erx-kpi-warning' : 'erx-kpi-success') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-microchip"></i></span>
                                    <span class="erx-kpi-label">CPU Prom.</span>
                                    <span class="erx-kpi-value">{{ $summary['avg_cpu'] !== null ? $summary['avg_cpu'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">%</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['max_cpu'] ?? 0) > 90 ? 'erx-kpi-danger' : (($summary['max_cpu'] ?? 0) > 75 ? 'erx-kpi-warning' : '') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-tachometer"></i></span>
                                    <span class="erx-kpi-label">CPU Pico</span>
                                    <span class="erx-kpi-value">{{ $summary['max_cpu'] !== null ? $summary['max_cpu'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">%</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['avg_mem'] ?? 0) > 80 ? 'erx-kpi-danger' : (($summary['avg_mem'] ?? 0) > 60 ? 'erx-kpi-warning' : 'erx-kpi-success') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-server"></i></span>
                                    <span class="erx-kpi-label">Mem. Prom.</span>
                                    <span class="erx-kpi-value">{{ $summary['avg_mem'] !== null ? $summary['avg_mem'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">%</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['max_mem'] ?? 0) > 90 ? 'erx-kpi-danger' : (($summary['max_mem'] ?? 0) > 75 ? 'erx-kpi-warning' : '') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-tachometer"></i></span>
                                    <span class="erx-kpi-label">Mem. Pico</span>
                                    <span class="erx-kpi-value">{{ $summary['max_mem'] !== null ? $summary['max_mem'] : '—' }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">%</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['days_high_cpu'] ?? 0) > 5 ? 'erx-kpi-danger' : (($summary['days_high_cpu'] ?? 0) > 0 ? 'erx-kpi-warning' : 'erx-kpi-success') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-calendar-check-o"></i></span>
                                    <span class="erx-kpi-label">Días CPU &gt;80%</span>
                                    <span class="erx-kpi-value">{{ $summary['days_high_cpu'] }}</span>
                                </div>
                                <div class="erx-kpi {{ ($summary['days_high_mem'] ?? 0) > 5 ? 'erx-kpi-danger' : (($summary['days_high_mem'] ?? 0) > 0 ? 'erx-kpi-warning' : 'erx-kpi-success') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-calendar-check-o"></i></span>
                                    <span class="erx-kpi-label">Días Mem &gt;80%</span>
                                    <span class="erx-kpi-value">{{ $summary['days_high_mem'] }}</span>
                                </div>
                            </div>

                        @elseif($summary['type'] === 'availability')
                            @php
                                $avail    = $summary['avail_pct'];
                                $hrsDown  = $summary['hrs_down'];
                                $minsDown = round($hrsDown * 60, 1);
                                $slaOk    = $summary['sla']['class'] === 'success';
                            @endphp
                            <div style="display:flex; align-items:flex-start; gap:20px; flex-wrap:wrap; margin-bottom:16px;">
                                <div>
                                    <div style="font-size:40px; font-weight:800; line-height:1;
                                        color:{{ $slaOk ? '#10b981' : '#ef4444' }};">
                                        {{ number_format($avail, 4) }}<span style="font-size:18px;">%</span>
                                    </div>
                                    <div style="font-size:12px; color:#9ca3af; margin-top:4px;">
                                        Disponibilidad del periodo
                                    </div>
                                </div>
                                <div style="display:flex; flex-direction:column; gap:6px; padding-top:4px;">
                                    <span class="erx-sla erx-sla-{{ $summary['sla']['class'] }}">
                                        <i class="fa fa-{{ $slaOk ? 'check-circle' : 'times-circle' }} fa-fw"></i>
                                        {{ $summary['sla']['label'] }}
                                    </span>
                                    <span style="font-size:12px; color:#6b7280;">
                                        Downtime real:
                                        <strong class="{{ $slaOk ? 'erx-cell-ok' : 'erx-cell-high' }}">
                                            {{ $minsDown }} min
                                        </strong>
                                        &nbsp;/&nbsp;
                                        umbral: <strong>{{ $summary['sla_threshold_mins'] }} min</strong>
                                        <span style="color:#9ca3af;">(43 min × {{ $summary['period_days'] }} días / 30)</span>
                                    </span>
                                </div>
                            </div>
                            <div class="erx-avail-bar-wrap">
                                <div class="erx-avail-bar-fill" style="width:{{ min(100, $avail) }}%;
                                    background: linear-gradient(90deg,
                                        {{ $slaOk ? '#10b981, #34d399' : '#ef4444, #f87171' }});"></div>
                            </div>
                            <div class="erx-kpis" style="margin-top:14px;">
                                <div class="erx-kpi erx-kpi-success">
                                    <span class="erx-kpi-icon"><i class="fa fa-check-circle"></i></span>
                                    <span class="erx-kpi-label">Horas Activo</span>
                                    <span class="erx-kpi-value">{{ number_format($summary['hrs_up'], 1) }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">hrs</span>
                                </div>
                                <div class="erx-kpi {{ $slaOk ? '' : 'erx-kpi-danger' }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-times-circle"></i></span>
                                    <span class="erx-kpi-label">Downtime</span>
                                    <span class="erx-kpi-value">{{ $minsDown }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">
                                        min
                                        @if(!$slaOk)
                                            <span class="erx-cell-high">(+{{ round($minsDown - $summary['sla_threshold_mins'], 1) }} sobre límite)</span>
                                        @else
                                            <span class="erx-cell-ok">({{ round($summary['sla_threshold_mins'] - $minsDown, 1) }} min de margen)</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="erx-kpi {{ ($summary['n_outages'] ?? 0) > 5 ? 'erx-kpi-warning' : (($summary['n_outages'] ?? 0) === 0 ? 'erx-kpi-success' : '') }}">
                                    <span class="erx-kpi-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                    <span class="erx-kpi-label">N° Caídas</span>
                                    <span class="erx-kpi-value">{{ $summary['n_outages'] }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">eventos</span>
                                </div>
                                <div class="erx-kpi">
                                    <span class="erx-kpi-icon"><i class="fa fa-clock-o"></i></span>
                                    <span class="erx-kpi-label">MTTR</span>
                                    @if(($summary['n_outages'] ?? 0) > 0)
                                        <span class="erx-kpi-value">{{ number_format($minsDown / $summary['n_outages'], 1) }}</span>
                                        <span style="font-size:11px; color:#9ca3af;">min/caída</span>
                                    @else
                                        <span class="erx-kpi-value">—</span>
                                    @endif
                                </div>
                                <div class="erx-kpi" style="border-left-color:#64748b; background:#f8fafc;">
                                    <span class="erx-kpi-icon" style="color:#64748b;"><i class="fa fa-sliders"></i></span>
                                    <span class="erx-kpi-label">Umbral SLA</span>
                                    <span class="erx-kpi-value" style="font-size:16px;">{{ $summary['sla_threshold_mins'] }}</span>
                                    <span style="font-size:11px; color:#9ca3af;">min / {{ $summary['period_days'] }} días</span>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- ══════════════ Gráfico inline ══════════════ --}}
                    @if(!empty($summary) && isset($summary['chart_labels']))
                        <div class="erx-divider"></div>
                        <div class="erx-chart-wrap">
                            <canvas id="erxChart"></canvas>
                        </div>
                    @endif

                    <div class="erx-divider"></div>

                    {{-- ══════════════ Tabla de datos ══════════════ --}}
                    <div class="erx-table-wrap">
                        <table class="table table-hover table-condensed">
                            <thead>
                            <tr>
                                @foreach(array_keys($report_data[0]) as $col)
                                    <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($report_data as $row)
                                @php
                                    $rowClass = '';
                                    if ($report_type === 'resources') {
                                        $cpu = is_numeric($row['CPU Promedio (%)'] ?? '') ? (float) $row['CPU Promedio (%)'] : null;
                                        $mem = is_numeric($row['Mem Uso (%)'] ?? '')       ? (float) $row['Mem Uso (%)']       : null;
                                        if (($cpu !== null && $cpu > 85) || ($mem !== null && $mem > 85)) $rowClass = 'erx-row-high';
                                        elseif (($cpu !== null && $cpu > 70) || ($mem !== null && $mem > 70)) $rowClass = 'erx-row-warn';
                                    } elseif ($report_type === 'packets') {
                                        $errSum = ((float)($row['Errores In'] ?: 0)) + ((float)($row['Errores Out'] ?: 0));
                                        if ($errSum > 1000) $rowClass = 'erx-row-high';
                                        elseif ($errSum > 100) $rowClass = 'erx-row-warn';
                                    } elseif ($report_type === 'availability') {
                                        $av = is_numeric($row['Disponibilidad (%)'] ?? '') ? (float) $row['Disponibilidad (%)'] : null;
                                        if ($av !== null && $av >= 99.9)  $rowClass = 'erx-row-ok';
                                        elseif ($av !== null && $av < 99.0) $rowClass = 'erx-row-high';
                                        elseif ($av !== null) $rowClass = 'erx-row-warn';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    @foreach($row as $col => $val)
                                        @php
                                            $cellClass = '';
                                            if ($report_type === 'resources') {
                                                if (in_array($col, ['CPU Promedio (%)','Mem Uso (%)']) && is_numeric($val)) {
                                                    $fv = (float) $val;
                                                    if ($fv > 85)      $cellClass = 'erx-cell-high';
                                                    elseif ($fv > 70)  $cellClass = 'erx-cell-warn';
                                                    else               $cellClass = 'erx-cell-ok';
                                                }
                                            } elseif ($report_type === 'availability' && $col === 'Disponibilidad (%)' && is_numeric($val)) {
                                                $fv = (float) $val;
                                                $cellClass = $fv >= 99.9 ? 'erx-cell-ok' : ($fv < 99.0 ? 'erx-cell-high' : 'erx-cell-warn');
                                            }
                                        @endphp
                                        <td class="{{ $cellClass }}">{{ $val }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ BITÁCORA DE EXPORTACIONES ═══ --}}
    <div class="erx-panel">
        <div class="erx-panel-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="erx-panel-header-title">
                    <i class="fa fa-history" aria-hidden="true"></i> Bitácora de Exportaciones
                </div>
                <div class="erx-panel-header-sub">Trazabilidad institucional de descargas CSV / XLSX / PDF</div>
            </div>
            @can('admin')
            @if(!empty($recent_audits))
            <div>
                <a href="{{ url('plugin/Reports') }}?action=clear_log"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('¿Eliminar TODOS los registros de la bitácora? Esta acción no se puede deshacer.');">
                    <i class="fa fa-trash"></i> Limpiar bitácora
                </a>
            </div>
            @endif
            @endcan
        </div>

        @if(empty($recent_audits))
            <div class="erx-panel-body">
                <div class="alert alert-info erx-alert" style="margin-bottom:0; display:flex; align-items:center; gap:10px;">
                    <i class="fa fa-info-circle fa-lg"></i>
                    <span>No hay eventos de auditoría registrados aún.</span>
                </div>
            </div>
        @else
            <div class="erx-audit-wrap" style="padding:0 18px 16px;">
                <table class="table table-hover table-condensed">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acción</th>
                        <th>Reporte</th>
                        <th>Dispositivo</th>
                        <th>Periodo</th>
                        <th>Rango</th>
                        <th>Filas</th>
                        <th>IP</th>
                        @can('admin')<th></th>@endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recent_audits as $evt)
                        @php
                            $actionClass = match($evt['action_type'] ?? '') {
                                'view'         => 'erx-badge-view',
                                'export_csv'   => 'erx-badge-csv',
                                'export_excel' => 'erx-badge-excel',
                                'export_pdf'   => 'erx-badge-pdf',
                                default        => 'erx-badge-action',
                            };
                            $actionIcon = match($evt['action_type'] ?? '') {
                                'view'         => 'fa-eye',
                                'export_csv'   => 'fa-file-text-o',
                                'export_excel' => 'fa-file-excel-o',
                                'export_pdf'   => 'fa-file-pdf-o',
                                default        => 'fa-bolt',
                            };
                        @endphp
                        <tr>
                            <td style="color:#9ca3af;">{{ $evt['id'] }}</td>
                            <td style="white-space:nowrap; font-size:11px;">{{ $evt['created_at'] }}</td>
                            <td><strong>{{ $evt['username'] }}</strong></td>
                            <td><span class="erx-badge erx-badge-action">{{ $evt['role_name'] }}</span></td>
                            <td>
                                <span class="erx-badge {{ $actionClass }}">
                                    <i class="fa {{ $actionIcon }}"></i>
                                    {{ $evt['action_type'] }}
                                </span>
                            </td>
                            <td>{{ $evt['report_type'] }}</td>
                            <td>{{ $evt['device_name'] ?? '—' }}</td>
                            <td>{{ $evt['period_name'] ?? '—' }}</td>
                            <td style="white-space:nowrap; font-size:11px;">
                                {{ $evt['date_from'] ?? '—' }} &rarr; {{ $evt['date_to'] ?? '—' }}
                            </td>
                            <td>
                                <span style="font-weight:600; color:var(--erx-accent);">{{ $evt['rows_count'] }}</span>
                            </td>
                            <td style="font-size:11px; color:#9ca3af;">{{ $evt['ip_address'] ?? '—' }}</td>
                            @can('admin')
                            <td>
                                <a href="{{ url('plugin/Reports') }}?action=delete_log_entry&line_index={{ $evt['line_index'] }}"
                                   class="btn btn-default btn-xs"
                                   title="Eliminar este registro"
                                   onclick="return confirm('¿Eliminar este registro de la bitácora?');">
                                    <i class="fa fa-trash text-danger"></i>
                                </a>
                            </td>
                            @endcan
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
function toggleDates(period) {
    var row = document.getElementById('custom-dates-row');
    if (!row) return;
    var isCustom = period === 'custom';
    row.classList.toggle('erx-dates-visible', isCustom);
    /* habilitar/deshabilitar inputs para que no se envíen en submit cuando están ocultos */
    row.querySelectorAll('input').forEach(function (inp) {
        inp.disabled = !isCustom;
    });
}
$(function () {
    var dtOpts = {
        format: 'YYYY-MM-DD',
        viewMode: 'days',
        useCurrent: false,
        widgetPositioning: { horizontal: 'left', vertical: 'bottom' },
        icons: {
            time: 'fa fa-clock-o',
            date: 'fa fa-calendar',
            up: 'fa fa-chevron-up',
            down: 'fa fa-chevron-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-calendar-check-o',
            clear: 'fa fa-trash-o',
            close: 'fa fa-close'
        }
    };
    $('#dtpicker_from').datetimepicker(dtOpts);
    $('#dtpicker_to').datetimepicker(dtOpts);
    $('#dtpicker_from').on('dp.change', function (e) {
        $('#dtpicker_to').data('DateTimePicker').minDate(e.date);
    });
    $('#dtpicker_to').on('dp.change', function (e) {
        $('#dtpicker_from').data('DateTimePicker').maxDate(e.date);
    });
    toggleDates('{{ $period }}');

    /* ── Chart.js inline ── */
    @if(!empty($summary) && isset($summary['chart_labels']))
    (function () {
        var canvas = document.getElementById('erxChart');
        if (!canvas) { return; }

        var accent  = getComputedStyle(document.querySelector('.erx-wrap') || document.body)
                        .getPropertyValue('--erx-accent').trim() || '#2563eb';
        var accent2 = getComputedStyle(document.querySelector('.erx-wrap') || document.body)
                        .getPropertyValue('--erx-accent-lt').trim() || '#3b82f6';

        var labels  = {!! json_encode($summary['chart_labels']) !!};

        @if($summary['type'] === 'bandwidth')
        var datasets = [
            {
                label: 'Entrada (Mbps)',
                data:  {!! json_encode($summary['chart_in']) !!},
                borderColor: accent,
                backgroundColor: accent + '22',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            },
            {
                label: 'Salida (Mbps)',
                data:  {!! json_encode($summary['chart_out']) !!},
                borderColor: accent2,
                backgroundColor: accent2 + '18',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            }
        ];
        var yLabel = 'Mbps';
        @elseif($summary['type'] === 'packets')
        var datasets = [
            {
                label: 'Errores In',
                data:  {!! json_encode($summary['chart_err_in']) !!},
                borderColor: '#ef4444',
                backgroundColor: '#ef444422',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.3,
                fill: true,
                spanGaps: true,
            },
            {
                label: 'Errores Out',
                data:  {!! json_encode($summary['chart_err_out']) !!},
                borderColor: '#f59e0b',
                backgroundColor: '#f59e0b18',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.3,
                fill: true,
                spanGaps: true,
            }
        ];
        var yLabel = 'Errores/día';
        @elseif($summary['type'] === 'resources')
        var datasets = [
            {
                label: 'CPU (%)',
                data:  {!! json_encode($summary['chart_cpu']) !!},
                borderColor: accent,
                backgroundColor: accent + '22',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            },
            {
                label: 'Memoria (%)',
                data:  {!! json_encode($summary['chart_mem']) !!},
                borderColor: '#8b5cf6',
                backgroundColor: '#8b5cf618',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            }
        ];
        var yLabel = '%';
        @else
        var datasets = [];
        var yLabel   = '';
        @endif

        /* Reducir etiquetas si hay muchos puntos */
        var maxTicks = 12;
        var step     = labels.length > maxTicks ? Math.ceil(labels.length / maxTicks) : 1;

        var annotations = {};
        @if(in_array($report_type, ['resources']))
        annotations['threshold80'] = {
            type: 'line', yMin: 80, yMax: 80,
            borderColor: '#ef4444', borderWidth: 1, borderDash: [4,3],
            label: { content: '80%', enabled: true, position: 'end', font: { size: 10 } }
        };
        @endif

        /* Intentar usar Chart.js disponible en LibreNMS o cargarlo dinámicamente */
        function buildChart(Chart) {
            if (!Chart) return;
            /* Registrar plugin de anotaciones si está disponible */
            if (window.ChartAnnotation) {
                Chart.register(window.ChartAnnotation);
            }
            new Chart(canvas, {
                type: 'line',
                data: { labels: labels, datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, padding: 16, font: { size: 12 } } },
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.dataset.label + ': ' + (ctx.parsed.y !== null ? ctx.parsed.y : '—') + ' ' + yLabel;
                                }
                            }
                        },
                        annotation: { annotations: annotations }
                    },
                    scales: {
                        x: {
                            ticks: {
                                maxTicksLimit: maxTicks,
                                callback: function(val, idx) {
                                    return idx % step === 0 ? this.getLabelForValue(val) : '';
                                },
                                font: { size: 11 }
                            },
                            grid: { color: '#e5e7eb' }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: yLabel, font: { size: 11 } },
                            ticks: { font: { size: 11 } },
                            grid: { color: '#e5e7eb' }
                        }
                    }
                }
            });
        }

        if (typeof Chart !== 'undefined') {
            buildChart(Chart);
        } else {
            /* LibreNMS usa Chart.js v4 — cargamos desde CDN como fallback */
            var s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js';
            s.onload = function() { buildChart(window.Chart); };
            document.head.appendChild(s);
        }
    }());
    @endif
});
</script>
