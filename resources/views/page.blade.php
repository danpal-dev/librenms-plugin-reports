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
/* ════════════════════════════════════════════════════════════════
   REPORTS — Sistema de diseño coherente v3
   Hereda: --fb-brand-500/600/700, --fb-font-family, --fb-font-size
    Escala tipográfica: 11 metadatos / 12 contenido / 14 títulos / 20 cifras
   Palette: blue(brand) · amber · emerald · rose · violet · slate
   ════════════════════════════════════════════════════════════════ */

/* ─── Contenedor raíz ─────────────────────────────────────────── */
.rpt-wrap {
    font-family: var(--fb-font-family, 'Inter', system-ui, sans-serif);
    font-size:   12px;
    color:       #344054;
    padding:     12px 16px 24px;
    --acc:     var(--fb-brand-600, #2563eb);
    --acc-h:   var(--fb-brand-700, #1d4ed8);
    --acc-l:   var(--fb-brand-500, #3b82f6);
    --rpt-text: #172033;
    --rpt-muted: #667085;
    --rpt-subtle: #98a2b3;
    --rpt-border: #e4e7ec;
    --rpt-surface-soft: #f8fafc;
}

/* ─── Hero ────────────────────────────────────────────────────── */
.rpt-hero {
    background: linear-gradient(125deg, #0f172a 0%, var(--fb-brand-700, #1d4ed8) 55%, var(--fb-brand-500, #3b82f6) 100%);
    border-radius: 10px;
    padding: 16px 20px;
    position: relative; overflow: hidden; color: #fff;
    box-shadow: 0 6px 20px rgba(15,23,42,.18), inset 0 1px 0 rgba(255,255,255,.1);
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
}
.rpt-hero::before {
    content:""; position:absolute; right:-60px; top:-60px;
    width:240px; height:240px; border-radius:50%;
    background:radial-gradient(circle, rgba(255,255,255,.08) 0%, transparent 70%);
    pointer-events:none;
}
.rpt-hero-icon {
    width:44px; height:44px; border-radius:9px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.22);
}
.rpt-hero-icon i { font-size:19px; color:#fff; }
.rpt-hero-body { flex:1; min-width:200px; }
.rpt-hero h2 {
    color:#fff !important; font-size:18px !important;
    font-weight:800 !important; letter-spacing:0;
    margin:0 0 2px !important; line-height:1.2;
}
.rpt-hero p { color:rgba(255,255,255,.82); font-size:12px; margin:0; line-height:1.4; }
.rpt-hero-actions { display:flex; gap:7px; flex-wrap:wrap; margin-top:8px; align-items:center; }
.rpt-hero-pill {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 10px; border-radius:7px;
    background:rgba(255,255,255,.13); border:1px solid rgba(255,255,255,.22);
    color:#fff; font-size:11px; font-weight:650;
    text-decoration:none; transition:background .15s;
}
.rpt-hero-pill:hover { background:rgba(255,255,255,.24); color:#fff; text-decoration:none; }

/* ─── Cards ───────────────────────────────────────────────────── */
.rpt-card {
    background:#fff; border:1px solid var(--rpt-border);
    border-radius:8px; margin-top:10px;
    box-shadow:0 2px 6px rgba(15,23,42,.05);
}
.rpt-card-header {
    padding:10px 14px; background:var(--rpt-surface-soft);
    border-bottom:1px solid var(--rpt-border);
    border-radius:8px 8px 0 0;
    display:flex; justify-content:space-between; align-items:center;
    flex-wrap:wrap; gap:8px;
    position:relative;
}
/* Barra lateral de acento — cada sección tiene su color */
.rpt-card-header::before {
    content:""; position:absolute; left:0; top:4px; bottom:4px;
    width:3px; border-radius:0 3px 3px 0;
    background: var(--rpt-ch-c, var(--acc));
}
.rpt-card-header--filters { --rpt-ch-c: var(--acc); }
.rpt-card-header--results  { --rpt-ch-c: #7c3aed; }  /* violeta */
.rpt-card-header--audit    { --rpt-ch-c: #0891b2; }  /* cyan */

.rpt-card-title {
    font-size:14px; font-weight:750; color:var(--rpt-text);
    display:flex; align-items:center; gap:8px;
}
.rpt-card-title i { color: var(--rpt-ch-c, var(--acc)); font-size:15px; }
.rpt-card-sub   { font-size:11px; color:var(--rpt-muted); margin-top:2px; }
.rpt-card-body  { padding:12px 14px; }

/* ─── Filtros ─────────────────────────────────────────────────── */
/* 4 campos iguales + botón: más ancho por campo */
.rpt-filters {
    display: grid;
    grid-template-columns: repeat(4,minmax(0,1fr)) auto;
    gap: 10px; align-items: end;
}
.rpt-filters .form-group, .rpt-dates-row .form-group { margin-bottom:0; min-width:0; }
.rpt-filters label {
    font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:0; color:var(--rpt-muted); display:block; margin-bottom:4px;
}
.rpt-filters .form-control {
    border-radius:7px; border:1px solid #d0d5dd;
    font-size:12px; height:34px; color:var(--rpt-text);
    transition:border-color .15s, box-shadow .15s; background:#fff;
}
.rpt-filters .form-control:focus {
    border-color:var(--acc); box-shadow:0 0 0 3px rgba(37,99,235,.1); outline:none;
}

.rpt-dates-row {
    display:none; margin-top:10px; padding-top:10px;
    border-top:1px dashed #e2e8f0;
    grid-template-columns:200px 200px auto 1fr; gap:10px; align-items:end;
}
.rpt-dates-row.rpt-dates-visible { display:grid; }
.rpt-dates-row label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0; color:var(--rpt-muted); display:block; margin-bottom:4px; }
.rpt-dates-row .form-control { border-radius:7px; border:1px solid #d0d5dd; font-size:12px; height:34px; }
.bootstrap-datetimepicker-widget { z-index:9999 !important; }

.rpt-btn-gen {
    display:inline-flex; align-items:center; gap:7px;
    background:var(--acc); border:none; color:#fff;
    border-radius:7px; font-weight:700; font-size:12px;
    height:34px; padding:0 16px; white-space:nowrap; cursor:pointer;
    box-shadow:0 2px 8px rgba(37,99,235,.35);
    transition:all .15s;
}
.rpt-btn-gen:hover { background:var(--acc-h); color:#fff; transform:translateY(-1px); box-shadow:0 5px 15px rgba(37,99,235,.4); }

/* ─── Banda de info ───────────────────────────────────────────── */
.rpt-info-band {
    margin-top:10px; padding:7px 10px;
    background:#f0fdf4;
    border:1px solid #bbf7d0; border-radius:7px;
    display:flex; align-items:center; gap:9px;
    font-size:11px; color:#067647; font-weight:600;
}

/* ─── KPI Cards — tipografía FIJA ─────────────────────────────── */
.rpt-kpis {
    display:grid;
    grid-template-columns: repeat(6, 1fr);   /* siempre 6 col */
    gap:8px;
}
.rpt-kpis-availability { grid-template-columns:repeat(5,minmax(0,1fr)); }
.rpt-kpi {
    border-radius:7px; padding:11px 12px 10px; min-height:92px;
    border:1px solid var(--rpt-border); background:#fff;
    position:relative; overflow:hidden;
    box-shadow:0 1px 4px rgba(15,23,42,.05);
    transition:transform .18s, box-shadow .18s;
    display:flex; flex-direction:column; gap:3px; justify-content:center;
}
.rpt-kpi:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(15,23,42,.09); }
/* barra superior */
.rpt-kpi::after {
    content:""; position:absolute; top:0; left:0; right:0; height:2px;
    background:var(--kc, var(--acc));
    border-radius:10px 10px 0 0;
}
/* glow de esquina */
.rpt-kpi::before { display:none; }
/* escala FIJA de texto */
.rpt-kpi-icon  { font-size:14px; color:var(--kc, var(--acc)); line-height:1; }
.rpt-kpi-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0; color:var(--rpt-muted); }
.rpt-kpi-value { font-size:20px; font-weight:800; color:var(--rpt-text); line-height:1.05; letter-spacing:0; }
.rpt-kpi-unit, .rpt-kpi-meta { font-size:11px; color:var(--rpt-subtle); line-height:1.3; }

/* Paleta de colores para KPI — variada */
.rpt-kpi-blue    { --kc:#2563eb; border-color:#c7d7fe; background:#f7f9ff; }
.rpt-kpi-indigo  { --kc:#4f46e5; border-color:#d3d5ff; background:#f8f8ff; }
.rpt-kpi-amber   { --kc:#b54708; border-color:#fedf89; background:#fffbf3; }
.rpt-kpi-orange  { --kc:#c4320a; border-color:#f9dbaf; background:#fffaf5; }
.rpt-kpi-emerald { --kc:#067647; border-color:#abefc6; background:#f6fef9; }
.rpt-kpi-teal    { --kc:#0e7090; border-color:#a5e7f0; background:#f5fcfd; }
.rpt-kpi-rose    { --kc:#c01048; border-color:#fecdd6; background:#fff7f9; }
.rpt-kpi-violet  { --kc:#6938ef; border-color:#d9d6fe; background:#f9f8ff; }
.rpt-kpi-neutral { --kc:#667085; border-color:var(--rpt-border); background:var(--rpt-surface-soft); }

/* Legacy aliases */
.rpt-kpi-warn    { --kc:#b54708; border-color:#fedf89; background:#fffbf3; }
.rpt-kpi-danger  { --kc:#d92d20; border-color:#fecdca; background:#fff8f7; }
.rpt-kpi-success { --kc:#067647; border-color:#abefc6; background:#f6fef9; }

/* ─── Export buttons ──────────────────────────────────────────── */
.rpt-export-btn {
    display:inline-flex; align-items:center; gap:6px;
    padding:5px 10px; border-radius:7px; font-size:11px; font-weight:700;
    border:1.5px solid; text-decoration:none; transition:transform .15s, box-shadow .15s;
}
.rpt-export-btn:hover { transform:translateY(-1px); box-shadow:0 3px 10px rgba(0,0,0,.1); text-decoration:none; }
.rpt-csv   { background:#f0fdf4; border-color:#86efac; color:#15803d; }
.rpt-excel { background:#ecfdf5; border-color:#6ee7b7; color:#166534; }
.rpt-pdf   { background:#fff1f2; border-color:#fca5a5; color:#be123c; }

/* ─── Gráfico ─────────────────────────────────────────────────── */
.rpt-chart-wrap { position:relative; height:280px; }

/* ─── Tabla ───────────────────────────────────────────────────── */
.rpt-table-wrap { overflow-x:auto; border-radius:7px; border:1px solid #e2e8f0; }
.rpt-table-wrap table { margin-bottom:0; font-size:12px; }
.rpt-table-wrap thead th {
    background:#f8fafc; color:var(--rpt-muted);
    font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0;
    border-bottom:2px solid #e2e8f0 !important;
    padding:7px 10px; white-space:nowrap;
    position:sticky; top:0; z-index:2;
}
.rpt-table-wrap thead th:first-child { border-radius:10px 0 0 0; }
.rpt-table-wrap thead th:last-child  { border-radius:0 10px 0 0; }
.rpt-table-wrap tbody td { padding:7px 10px; vertical-align:middle; border-top:1px solid #f2f4f7; font-size:12px; color:#344054; }
.rpt-table-wrap tbody tr:nth-child(odd)  td { background:#f8fafc; }
.rpt-table-wrap tbody tr:nth-child(even) td { background:#fff; }
.rpt-table-wrap tbody tr:hover td { background:rgba(37,99,235,.05) !important; }
.rpt-row-warn td { background:#fffbf3 !important; }
.rpt-row-high td { background:#fff8f7 !important; }
.rpt-row-ok   td { background:#f6fef9 !important; }
.rpt-cell-warn { color:#b54708; font-weight:700; }
.rpt-cell-high { color:#b42318; font-weight:700; }
.rpt-cell-ok   { color:#067647; font-weight:700; }
.rpt-card--audit .rpt-card-header { padding-block:8px; }
.rpt-card--audit .rpt-table-wrap { margin:0 12px 12px; padding:0 !important; }
.rpt-card--audit .btn { min-height:26px; padding:3px 8px; border-radius:6px; font-size:10px; }
.rpt-card--audit .rpt-badge { padding:2px 6px; font-size:10px; }
.rpt-audit-time { white-space:nowrap; color:var(--rpt-muted); font-size:11px; }
.rpt-audit-ip { color:var(--rpt-subtle); font-size:11px; }

/* ─── Disponibilidad ──────────────────────────────────────────── */
.rpt-availability-summary { display:grid; grid-template-columns:minmax(210px,1fr) minmax(240px,1fr); gap:18px; align-items:center; padding:4px 0 12px; border-bottom:1px solid #e2e8f0; }
.rpt-availability-main { min-width:0; }
.rpt-availability-sla { display:grid; grid-template-columns:auto 1fr; gap:10px 14px; align-items:center; }
.rpt-avail-big { font-size:36px; font-weight:850; line-height:1; letter-spacing:0; }
.rpt-avail-percent { font-size:17px; font-weight:750; }
.rpt-availability-caption { font-size:11px; color:var(--rpt-muted); margin-top:4px; }
.rpt-avail-bar-bg   { width:min(100%,260px); height:6px; background:#fee2e2; border-radius:99px; overflow:hidden; margin-top:8px; }
.rpt-avail-bar-fill { height:100%; border-radius:99px; transition:width .8s cubic-bezier(.4,0,.2,1); }

/* ─── Badges ──────────────────────────────────────────────────── */
.rpt-badge { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:6px; font-size:10px; font-weight:700; }
.rpt-badge-view   { background:#eff6ff; color:#1d4ed8; }
.rpt-badge-csv    { background:#f0fdf4; color:#15803d; }
.rpt-badge-excel  { background:#ecfdf5; color:#166534; }
.rpt-badge-pdf    { background:#fff1f2; color:#be123c; }
.rpt-badge-action { background:#faf5ff; color:#6d28d9; }

/* ─── SLA ─────────────────────────────────────────────────────── */
.rpt-sla { display:inline-flex; align-items:center; justify-content:center; gap:5px; padding:5px 10px; border-radius:7px; font-size:11px; font-weight:700; white-space:nowrap; }
.rpt-sla-ok  { background:#ecfdf3; border:1px solid #abefc6; color:#067647; }
.rpt-sla-bad { background:#fef3f2; border:1px solid #fecdca; color:#b42318; }
.rpt-sla-mid { background:#fef9c3; color:#92400e; }

/* ─── Divider ─────────────────────────────────────────────────── */
.rpt-divider { height:1px; background:#e2e8f0; margin:16px 0; }

/* ─── Responsive ──────────────────────────────────────────────── */
@media (max-width:1200px) { .rpt-kpis { grid-template-columns:repeat(3,1fr); } .rpt-kpis-availability { grid-template-columns:repeat(5,minmax(120px,1fr)); } }
@media (max-width:900px)  { .rpt-kpis, .rpt-kpis-availability { grid-template-columns:repeat(3,1fr); } .rpt-filters { grid-template-columns:1fr 1fr; } .rpt-filters .form-group:last-child { grid-column:1 / -1; } .rpt-filters .rpt-btn-gen { width:100%; justify-content:center; } .rpt-dates-row { grid-template-columns:1fr 1fr; } .rpt-availability-summary { grid-template-columns:1fr 1fr; } }
@media (max-width:600px)  { .rpt-wrap { padding:8px; } .rpt-hero { padding:12px; } .rpt-kpis, .rpt-kpis-availability { grid-template-columns:1fr 1fr; } .rpt-filters { grid-template-columns:1fr; } .rpt-filters .form-group:last-child { grid-column:auto; } .rpt-dates-row { grid-template-columns:1fr; } .rpt-availability-summary { grid-template-columns:1fr; gap:10px; } .rpt-availability-sla { grid-template-columns:1fr; } .rpt-chart-wrap { height:200px; } }
</style>



<div class="rpt-wrap">

    {{-- ═══ HERO EJECUTIVO ═══ --}}
    <div class="rpt-hero tw:flex tw:items-center tw:gap-5 tw:flex-wrap">
        <div class="rpt-hero-icon">
            <i class="fa fa-line-chart" aria-hidden="true"></i>
        </div>
        <div class="rpt-hero-body tw:flex-1 tw:min-w-0">
            <h2>{{ $title }}</h2>
            <p>{{ $subtitle }}</p>
            <div class="rpt-hero-actions">
                @can('admin')
                <a class="rpt-hero-pill" href="{{ route('plugin.settings', ['plugin' => 'Reports']) }}">
                    <i class="fa fa-cog fa-fw"></i> Configuración
                </a>
                @endcan
                <span style="margin-left:auto; font-size:11px; color:rgba(255,255,255,.65); display:flex; align-items:center; gap:5px;">
                    <i class="fa fa-clock-o"></i> {{ now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>

    {{-- ═══ FILTROS ═══ --}}
    <div class="rpt-card tw:mt-4">
        <div class="rpt-card-header rpt-card-header--filters">
            <div>
                <div class="rpt-card-title">
                    <i class="fa fa-sliders fa-fw" aria-hidden="true"></i> Parámetros del reporte
                </div>
                <div class="rpt-card-sub">Seleccione tipo, dispositivo y periodo para generar los datos</div>
            </div>
        </div>
        <div class="rpt-card-body">
            <form method="get" action="{{ url('plugin/Reports') }}">
                <input type="hidden" name="action" value="view">
                <div class="rpt-filters">
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
                                    if (!empty($dev->display)) {
                                        $devName = $dev->display;
                                    } else {
                                        $devName = !empty($dev->sysName) ? $dev->sysName : $dev->hostname;
                                    }
                                    $devIp = ($devName !== $dev->hostname) ? ' (' . $dev->hostname . ')' : '';
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
                        <select class="form-control" name="period" onchange="rptToggleDates(this.value)">
                            @foreach($period_labels as $val => $lbl)
                                <option value="{{ $val }}" {{ $period === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="rpt-btn-gen" type="submit">
                            <i class="fa fa-search fa-fw"></i> Generar reporte
                        </button>
                    </div>
                </div>

                <div class="rpt-dates-row{{ $period === 'custom' ? ' rpt-dates-visible' : '' }}" id="rpt-custom-dates">
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
                    <div style="display:flex; align-items:center; gap:7px; padding-bottom:2px;">
                        <input type="hidden" name="include_last_day" value="0">
                        <input type="checkbox" name="include_last_day" id="include_last_day_cb"
                               value="1" {{ $include_last_day ? 'checked' : '' }}
                               {{ $period !== 'custom' ? 'disabled' : '' }}
                               onchange="this.form.submit()"
                               style="width:14px; height:14px; cursor:pointer; accent-color:var(--acc); flex-shrink:0;">
                        <label for="include_last_day_cb" style="margin:0; font-size:11px; font-weight:600; color:#475467; cursor:pointer; white-space:nowrap;">
                            Incluir último día completo
                        </label>
                    </div>
                    <div class="tw:text-xs tw:text-slate-400 tw:flex tw:items-center tw:gap-1 tw:pb-0.5">
                        <i class="fa fa-info-circle fa-fw"></i>
                        Rango máximo recomendado: 1 año
                    </div>
                </div>

                {{-- Nota de retención histórica --}}
                <div class="rpt-info-band">
                    <i class="fa fa-shield" style="font-size:14px; flex-shrink:0;"></i>
                    <span>Retención histórica <strong>&gt; 12 meses</strong></span>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ MENSAJE DE ERROR ═══ --}}
    @if(!empty($error_message))
        <div class="alert alert-warning tw:rounded-xl tw:border tw:flex tw:items-center tw:gap-3" style="margin-top:12px; border-radius:10px; display:flex; align-items:center; gap:10px;">
            <i class="fa fa-exclamation-triangle fa-lg"></i>
            <span>{{ $error_message }}</span>
        </div>
    @endif

    {{-- ═══ ESTADO VACÍO ═══ --}}
    @if((int) $device_id === 0)
        <div class="rpt-card tw:mt-4">
            <div class="tw:flex tw:flex-col tw:items-center tw:gap-3 tw:py-14 tw:text-center">
                <div class="tw:w-16 tw:h-16 tw:rounded-full tw:bg-slate-100 tw:border-2 tw:border-slate-200 tw:flex tw:items-center tw:justify-center tw:text-slate-400"><i class="fa fa-bar-chart"></i></div>
                <p class="tw:text-base tw:font-bold tw:text-slate-600 tw:m-0">Seleccione los parámetros del reporte</p>
                <p class="tw:text-sm tw:text-slate-400 tw:m-0 tw:max-w-sm tw:leading-relaxed">Elija un dispositivo y un periodo en el panel de arriba para generar el reporte ejecutivo con métricas, gráficos y exportaciones.</p>
            </div>
        </div>

    @elseif(empty($error_message))

        {{-- ═══ RESULTADOS ═══ --}}
        <div class="rpt-card tw:mt-4">
            <div class="rpt-card-header rpt-card-header--results">
                <div>
                    <div class="rpt-card-title">
                        <i class="fa fa-area-chart" aria-hidden="true"></i>
                        {{ $report_labels[$report_type] ?? $report_type }}
                        @if(!empty($device->hostname))
                            &mdash; {{ $device->hostname }}
                        @endif
                    </div>
                    <div class="rpt-card-sub">
                        Periodo: <strong>{{ $date_from }}</strong> al <strong>{{ $date_to }}</strong>
                        &nbsp;·&nbsp; {{ count($report_data) }} registro(s)
                    </div>
                </div>
                @if(!empty($report_data))
                <div class="tw:flex tw:gap-2 tw:flex-wrap">
                    <a class="rpt-export-btn rpt-csv" href="{{ $export_csv_url }}">
                        <i class="fa fa-file-text-o"></i> CSV
                    </a>
                    <a class="rpt-export-btn rpt-excel" href="{{ $export_excel_url }}">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </a>
                    <a class="rpt-export-btn rpt-pdf" href="{{ $export_pdf_url }}">
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
                </div>
                @endif
            </div>

            <div class="rpt-card-body">
                @if(empty($report_data))
                    <div class="alert alert-info tw:rounded-xl tw:border tw:flex tw:items-center tw:gap-3" style="margin-bottom:0; display:flex; align-items:center; gap:10px;">
                        <i class="fa fa-info-circle fa-lg"></i>
                        <span>No hay datos para el rango seleccionado. Verifique el polling y los archivos RRD del equipo.</span>
                    </div>
                @else
                    {{-- ══════════════ KPIs por tipo ══════════════ --}}
                    @if(!empty($summary))
                        @if($summary['type'] === 'bandwidth')
                            <div class="rpt-kpis">
                                <div class="rpt-kpi">
                                    <span class="rpt-kpi-icon"><i class="fa fa-arrow-circle-down"></i></span>
                                    <span class="rpt-kpi-label">Prom. Entrada</span>
                                    <span class="rpt-kpi-value">{{ $summary['avg_in'] !== null ? number_format((float)$summary['avg_in'], 2) : '—' }}</span>
                                    <span class="rpt-kpi-unit">Mbps</span>
                                </div>
                                <div class="rpt-kpi">
                                    <span class="rpt-kpi-icon"><i class="fa fa-arrow-circle-up"></i></span>
                                    <span class="rpt-kpi-label">Prom. Salida</span>
                                    <span class="rpt-kpi-value">{{ $summary['avg_out'] !== null ? number_format((float)$summary['avg_out'], 2) : '—' }}</span>
                                    <span class="rpt-kpi-unit">Mbps</span>
                                </div>
                                <div class="rpt-kpi rpt-kpi-amber">
                                    <span class="rpt-kpi-icon"><i class="fa fa-bolt"></i></span>
                                    <span class="rpt-kpi-label">Pico Entrada</span>
                                    <span class="rpt-kpi-value">{{ $summary['max_in'] !== null ? number_format((float)$summary['max_in'], 2) : '—' }}</span>
                                    <span class="rpt-kpi-unit">Mbps</span>
                                </div>
                                <div class="rpt-kpi rpt-kpi-orange">
                                    <span class="rpt-kpi-icon"><i class="fa fa-bolt"></i></span>
                                    <span class="rpt-kpi-label">Pico Salida</span>
                                    <span class="rpt-kpi-value">{{ $summary['max_out'] !== null ? number_format((float)$summary['max_out'], 2) : '—' }}</span>
                                    <span class="rpt-kpi-unit">Mbps</span>
                                </div>
                                <div class="rpt-kpi rpt-kpi-emerald">
                                    <span class="rpt-kpi-icon"><i class="fa fa-cloud-download"></i></span>
                                    <span class="rpt-kpi-label">Total Entrada</span>
                                    <span class="rpt-kpi-value">{{ $summary['total_in_gb'] !== null ? number_format((float)$summary['total_in_gb'], 1) : '—' }}</span>
                                    <span class="rpt-kpi-unit">GB en el periodo</span>
                                </div>
                                <div class="rpt-kpi rpt-kpi-teal">
                                    <span class="rpt-kpi-icon"><i class="fa fa-cloud-upload"></i></span>
                                    <span class="rpt-kpi-label">Total Salida</span>
                                    <span class="rpt-kpi-value">{{ $summary['total_out_gb'] !== null ? number_format((float)$summary['total_out_gb'], 1) : '—' }}</span>
                                    <span class="rpt-kpi-unit">GB en el periodo</span>
                                </div>
                            </div>

                        @elseif($summary['type'] === 'packets')
                            <div class="rpt-kpis">
                                <div class="rpt-kpi {{ ($summary['total_err_in'] ?? 0) > 1000 ? 'rpt-kpi-danger' : (($summary['total_err_in'] ?? 0) > 100 ? 'rpt-kpi-warn' : '') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <span class="rpt-kpi-label">Total Errores In</span>
                                    <span class="rpt-kpi-value">{{ $summary['total_err_in'] !== null ? number_format($summary['total_err_in']) : '—' }}</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['total_err_out'] ?? 0) > 1000 ? 'rpt-kpi-danger' : (($summary['total_err_out'] ?? 0) > 100 ? 'rpt-kpi-warn' : '') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-exclamation-circle"></i></span>
                                    <span class="rpt-kpi-label">Total Errores Out</span>
                                    <span class="rpt-kpi-value">{{ $summary['total_err_out'] !== null ? number_format($summary['total_err_out']) : '—' }}</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['total_dis_in'] ?? 0) > 500 ? 'rpt-kpi-warn' : '' }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-trash-o"></i></span>
                                    <span class="rpt-kpi-label">Descartados In</span>
                                    <span class="rpt-kpi-value">{{ $summary['total_dis_in'] !== null ? number_format($summary['total_dis_in']) : '—' }}</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['total_dis_out'] ?? 0) > 500 ? 'rpt-kpi-warn' : '' }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-trash-o"></i></span>
                                    <span class="rpt-kpi-label">Descartados Out</span>
                                    <span class="rpt-kpi-value">{{ $summary['total_dis_out'] !== null ? number_format($summary['total_dis_out']) : '—' }}</span>
                                </div>
                                @if($summary['peak_day'])
                                <div class="rpt-kpi rpt-kpi-danger">
                                    <span class="rpt-kpi-icon"><i class="fa fa-calendar-times-o"></i></span>
                                    <span class="rpt-kpi-label">Día Pico</span>
                                    <span class="rpt-kpi-value" style="font-size:14px;">{{ $summary['peak_day'] }}</span>
                                    <span class="rpt-kpi-meta">mayor incidencia</span>
                                </div>
                                @endif
                            </div>

                        @elseif($summary['type'] === 'resources')
                            <div class="rpt-kpis">
                                <div class="rpt-kpi {{ ($summary['avg_cpu'] ?? 0) > 80 ? 'rpt-kpi-danger' : (($summary['avg_cpu'] ?? 0) > 60 ? 'rpt-kpi-warn' : 'rpt-kpi-success') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-microchip"></i></span>
                                    <span class="rpt-kpi-label">CPU Prom.</span>
                                    <span class="rpt-kpi-value">{{ $summary['avg_cpu'] !== null ? $summary['avg_cpu'] : '—' }}</span>
                                    <span class="rpt-kpi-meta">%</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['max_cpu'] ?? 0) > 90 ? 'rpt-kpi-danger' : (($summary['max_cpu'] ?? 0) > 75 ? 'rpt-kpi-warn' : '') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-tachometer"></i></span>
                                    <span class="rpt-kpi-label">CPU Pico</span>
                                    <span class="rpt-kpi-value">{{ $summary['max_cpu'] !== null ? $summary['max_cpu'] : '—' }}</span>
                                    <span class="rpt-kpi-meta">%</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['avg_mem'] ?? 0) > 80 ? 'rpt-kpi-danger' : (($summary['avg_mem'] ?? 0) > 60 ? 'rpt-kpi-warn' : 'rpt-kpi-success') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-server"></i></span>
                                    <span class="rpt-kpi-label">Mem. Prom.</span>
                                    <span class="rpt-kpi-value">{{ $summary['avg_mem'] !== null ? $summary['avg_mem'] : '—' }}</span>
                                    <span class="rpt-kpi-meta">%</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['max_mem'] ?? 0) > 90 ? 'rpt-kpi-danger' : (($summary['max_mem'] ?? 0) > 75 ? 'rpt-kpi-warn' : '') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-tachometer"></i></span>
                                    <span class="rpt-kpi-label">Mem. Pico</span>
                                    <span class="rpt-kpi-value">{{ $summary['max_mem'] !== null ? $summary['max_mem'] : '—' }}</span>
                                    <span class="rpt-kpi-meta">%</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['days_high_cpu'] ?? 0) > 5 ? 'rpt-kpi-danger' : (($summary['days_high_cpu'] ?? 0) > 0 ? 'rpt-kpi-warn' : 'rpt-kpi-success') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-calendar-check-o"></i></span>
                                    <span class="rpt-kpi-label">Días CPU &gt;80%</span>
                                    <span class="rpt-kpi-value">{{ $summary['days_high_cpu'] }}</span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['days_high_mem'] ?? 0) > 5 ? 'rpt-kpi-danger' : (($summary['days_high_mem'] ?? 0) > 0 ? 'rpt-kpi-warn' : 'rpt-kpi-success') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-calendar-check-o"></i></span>
                                    <span class="rpt-kpi-label">Días Mem &gt;80%</span>
                                    <span class="rpt-kpi-value">{{ $summary['days_high_mem'] }}</span>
                                </div>
                            </div>

                        @elseif($summary['type'] === 'availability')
                            @php
                                $avail    = $summary['avail_pct'];
                                $hrsDown  = $summary['hrs_down'];
                                $minsDown = round($hrsDown * 60, 1);
                                $slaOk    = $summary['sla']['class'] === 'success';
                            @endphp
                            <div class="rpt-availability-summary">
                                <div class="rpt-availability-main">
                                    <div class="rpt-avail-big" style="color:{{ $slaOk ? '#10b981' : '#ef4444' }};">
                                        {{ number_format($avail, 4) }}<span class="rpt-avail-percent">%</span>
                                    </div>
                                    <div class="rpt-availability-caption">
                                        Disponibilidad del periodo · {{ $summary['period_days'] }} días
                                    </div>
                                    <div class="rpt-avail-bar-bg">
                                        <div class="rpt-avail-bar-fill" style="width:{{ min(100, $avail) }}%;
                                            background: linear-gradient(90deg,
                                                {{ $slaOk ? '#10b981, #34d399' : '#ef4444, #f87171' }});"></div>
                                    </div>
                                </div>
                                <div class="rpt-availability-sla">
                                    <span class="rpt-sla {{ $slaOk ? 'rpt-sla-ok' : 'rpt-sla-bad' }}">
                                        <i class="fa fa-{{ $slaOk ? 'check-circle' : 'times-circle' }} fa-fw"></i>
                                        {{ $summary['sla']['label'] }}
                                    </span>
                                    <div style="font-size:11px; color:#334155; line-height:1.45;">
                                        Downtime real: <strong class="{{ $slaOk ? 'rpt-cell-ok' : 'rpt-cell-high' }}">{{ $minsDown }} min</strong><br>
                                        SLA objetivo: <strong>{{ number_format($summary['sla_target_pct'], 2) }}%</strong>
                                        <span style="color:var(--rpt-muted, #64748b); font-size:11px;">
                                            (≤ {{ $summary['sla_threshold_mins'] }} min × {{ $summary['sla_months'] }} {{ $summary['sla_months'] === 1 ? 'mes' : 'meses' }})
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="rpt-kpis rpt-kpis-availability" style="margin-top:10px;">
                                <div class="rpt-kpi rpt-kpi-success">
                                    <span class="rpt-kpi-icon"><i class="fa fa-check-circle"></i></span>
                                    <span class="rpt-kpi-label">Horas Activo</span>
                                    <span class="rpt-kpi-value">{{ number_format($summary['hrs_up'], 1) }}</span>
                                    <span class="rpt-kpi-unit">hrs</span>
                                </div>
                                <div class="rpt-kpi {{ $slaOk ? '' : 'rpt-kpi-danger' }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-times-circle"></i></span>
                                    <span class="rpt-kpi-label">Downtime</span>
                                    <span class="rpt-kpi-value">{{ $minsDown }}</span>
                                    <span class="rpt-kpi-meta">
                                        min
                                        @if(!$slaOk)
                                            <span class="rpt-cell-high">(+{{ round($minsDown - $summary['sla_threshold_mins'], 1) }} sobre límite)</span>
                                        @else
                                            <span class="rpt-cell-ok">({{ round($summary['sla_threshold_mins'] - $minsDown, 1) }} min de margen)</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="rpt-kpi {{ ($summary['n_outages'] ?? 0) > 5 ? 'rpt-kpi-warn' : (($summary['n_outages'] ?? 0) === 0 ? 'rpt-kpi-success' : '') }}">
                                    <span class="rpt-kpi-icon"><i class="fa fa-exclamation-triangle"></i></span>
                                    <span class="rpt-kpi-label">N° Caídas</span>
                                    <span class="rpt-kpi-value">{{ $summary['n_outages'] }}</span>
                                    <span class="rpt-kpi-meta">eventos</span>
                                </div>
                                <div class="rpt-kpi">
                                    <span class="rpt-kpi-icon"><i class="fa fa-clock-o"></i></span>
                                    <span class="rpt-kpi-label">MTTR</span>
                                    @if(($summary['n_outages'] ?? 0) > 0)
                                        <span class="rpt-kpi-value">{{ number_format($minsDown / $summary['n_outages'], 1) }}</span>
                                        <span class="rpt-kpi-meta">min/caída</span>
                                    @else
                                        <span class="rpt-kpi-value">—</span>
                                    @endif
                                </div>
                                <div class="rpt-kpi rpt-kpi-neutral">
                                    <span class="rpt-kpi-icon"><i class="fa fa-sliders"></i></span>
                                    <span class="rpt-kpi-label">SLA objetivo</span>
                                    <span class="rpt-kpi-value">{{ number_format($summary['sla_target_pct'], 2) }}</span>
                                    <span class="rpt-kpi-unit">% (≤ {{ $summary['sla_threshold_mins'] }} min)</span>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- ══════════════ Gráfico ══════════════ --}}
                    @if(!empty($summary) && isset($summary['chart_labels']))
                        <div class="rpt-card tw:mt-4">
                            <div style="padding:9px 12px; background:#f8fafc; border-bottom:1px solid #e2e8f0; border-radius:7px 7px 0 0;">
                                <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0; color:#64748b; display:flex; align-items:center; gap:6px;">
                                    <i class="fa fa-area-chart" style="color:#7c3aed;"></i> Evolución del periodo
                                </div>
                            </div>
                            <div style="padding:16px 20px;">
                                <div class="rpt-chart-wrap"><canvas id="erxChart"></canvas></div>
                            </div>
                        </div>
                    @endif

                    <div style="margin-top:10px;"></div>

                    {{-- ══════════════ Tabla de datos ══════════════ --}}
                    <div class="rpt-table-wrap" style="border-radius:10px; overflow:hidden;">
                        @if($report_type === 'availability')
                        <table class="table" style="margin-bottom:0;">
                            <thead>
                            <tr>
                                <th>Inicio Caída</th>
                                <th>Fin Caída</th>
                                <th>Duración (min)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($availability_events as $event)
                                <tr class="rpt-row-high">
                                    <td>{{ $event['Inicio Caida'] }}</td>
                                    <td>{{ $event['Fin Caida'] }}</td>
                                    <td class="rpt-cell-high">{{ $event['Duracion Caida (min)'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No se registraron caídas en el período.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @else
                        <table class="table" style="margin-bottom:0;">
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
                                        if (($cpu !== null && $cpu > 85) || ($mem !== null && $mem > 85)) $rowClass = 'rpt-row-high';
                                        elseif (($cpu !== null && $cpu > 70) || ($mem !== null && $mem > 70)) $rowClass = 'rpt-row-warn';
                                    } elseif ($report_type === 'packets') {
                                        $errSum = ((float)($row['Errores In'] ?: 0)) + ((float)($row['Errores Out'] ?: 0));
                                        if ($errSum > 1000) $rowClass = 'rpt-row-high';
                                        elseif ($errSum > 100) $rowClass = 'rpt-row-warn';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    @foreach($row as $col => $val)
                                        @php
                                            $cellClass = '';
                                            if ($report_type === 'resources') {
                                                if (in_array($col, ['CPU Promedio (%)','Mem Uso (%)']) && is_numeric($val)) {
                                                    $fv = (float) $val;
                                                    if ($fv > 85)      $cellClass = 'rpt-cell-high';
                                                    elseif ($fv > 70)  $cellClass = 'rpt-cell-warn';
                                                    else               $cellClass = 'rpt-cell-ok';
                                                }
                                            }
                                        @endphp
                                        <td class="{{ $cellClass }}">{{ $val }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    @can('admin')
    {{-- ═══ BITÁCORA DE EXPORTACIONES ═══ --}}
    <div class="rpt-card rpt-card--audit tw:mt-4">
        <div class="rpt-card-header rpt-card-header--audit" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="rpt-card-title">
                    <i class="fa fa-history" aria-hidden="true"></i> Bitácora de Exportaciones
                </div>
                <div class="rpt-card-sub">Trazabilidad institucional de descargas CSV / XLSX / PDF</div>
            </div>
            @if(!empty($recent_audits))
            <form method="POST" action="{{ route('plugin.update', ['plugin' => 'Reports']) }}" style="margin:0;">
                @csrf
                <input type="hidden" name="action" value="clear_log">
                <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('¿Eliminar TODOS los registros de la bitácora? Esta acción no se puede deshacer.');">
                    <i class="fa fa-trash"></i> Limpiar bitácora
                </button>
            </form>
            @endif
        </div>

        @if(empty($recent_audits))
            <div class="rpt-card-body">
                <div class="alert alert-info tw:rounded-xl tw:border tw:flex tw:items-center tw:gap-3" style="margin-bottom:0; display:flex; align-items:center; gap:10px;">
                    <i class="fa fa-info-circle fa-lg"></i>
                    <span>No hay eventos de auditoría registrados aún.</span>
                </div>
            </div>
        @else
            <div class="rpt-table-wrap" style="padding:0 18px 16px;">
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
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recent_audits as $evt)
                        @php
                            $actionClass = match($evt['action_type'] ?? '') {
                                'view'         => 'rpt-badge-view',
                                'export_csv'   => 'rpt-badge-csv',
                                'export_excel' => 'rpt-badge-excel',
                                'export_pdf'   => 'rpt-badge-pdf',
                                default        => 'rpt-badge-action',
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
                            <td class="rpt-audit-ip">{{ $evt['id'] }}</td>
                            <td class="rpt-audit-time">{{ $evt['created_at'] }}</td>
                            <td><strong>{{ $evt['username'] }}</strong></td>
                            <td><span class="rpt-badge rpt-badge-action">{{ $evt['role_name'] }}</span></td>
                            <td>
                                <span class="rpt-badge {{ $actionClass }}">
                                    <i class="fa {{ $actionIcon }}"></i>
                                    {{ $evt['action_type'] }}
                                </span>
                            </td>
                            <td>{{ $evt['report_type'] }}</td>
                            <td>{{ $evt['device_name'] ?? '—' }}</td>
                            <td>{{ $evt['period_name'] ?? '—' }}</td>
                            <td class="rpt-audit-time">
                                {{ $evt['date_from'] ?? '—' }} &rarr; {{ $evt['date_to'] ?? '—' }}
                            </td>
                            <td>
                                <span style="font-weight:600; color:var(--fb-brand-600, #2563eb);">{{ $evt['rows_count'] }}</span>
                            </td>
                            <td class="rpt-audit-ip">{{ $evt['ip_address'] ?? '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('plugin.update', ['plugin' => 'Reports']) }}" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="action" value="delete_log_entry">
                                    <input type="hidden" name="line_index" value="{{ $evt['line_index'] }}">
                                    <button type="submit" class="btn btn-default btn-xs" title="Eliminar este registro"
                                            onclick="return confirm('¿Eliminar este registro de la bitácora?');">
                                        <i class="fa fa-trash text-danger"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endcan
</div>

<script>
function rptToggleDates(period) {
    var row = document.getElementById('rpt-custom-dates');
    if (!row) return;
    var isCustom = period === 'custom';
    row.classList.toggle('rpt-dates-visible', isCustom);
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
    rptToggleDates('{{ $period }}');

    /* ── Chart.js inline ── */
    @if(!empty($summary) && isset($summary['chart_labels']))
    (function () {
        var canvas = document.getElementById('erxChart');
        if (!canvas) { return; }

        /* Paleta armónica: brand-blue / teal / violet / amber / rose */
        var root   = document.querySelector('.rpt-wrap') || document.body;
        var cs     = getComputedStyle(root);
        var brand  = cs.getPropertyValue('--fb-brand-600').trim() || '#2563eb';
        var c1     = brand;           /* azul marca  — Entrada / CPU     */
        var c2     = '#0891b2';       /* teal-600    — Salida             */
        var c3     = '#7c3aed';       /* violet-600  — Memoria            */
        var c4     = '#d97706';       /* amber-600   — Errores In         */
        var c5     = '#ea580c';       /* orange-600  — Errores Out        */
        var cThres = '#dc2626';       /* red-600     — Umbral (exclusivo) */

        var labels  = {!! json_encode($summary['chart_labels']) !!};

        @if($summary['type'] === 'bandwidth')
        var datasets = [
            {
                label: 'Entrada Prom (Mbps)',
                data:  {!! json_encode($summary['chart_in']) !!},
                borderColor: c1,
                backgroundColor: c1 + '28',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            },
            {
                label: 'Salida Prom (Mbps)',
                data:  {!! json_encode($summary['chart_out']) !!},
                borderColor: c2,
                backgroundColor: c2 + '28',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            }
        ];
        @if((int)($summary['contract_bandwidth'] ?? 0) > 0)
        datasets.push({
            type: 'line', /* always a line regardless of global chart type */
            label: 'Umbral Contratado',
            data:  Array(labels.length).fill({{ (int)$summary['contract_bandwidth'] }}),
            borderColor: cThres,
            backgroundColor: 'transparent',
            borderWidth: 2,
            borderDash: [8, 4],
            pointRadius: 0,
            fill: false,
            tension: 0,
            spanGaps: true,
            order: -1,
        });
        @endif
        var yLabel = 'Mbps';
        @elseif($summary['type'] === 'packets')
        var datasets = [
            {
                label: 'Errores In',
                data:  {!! json_encode($summary['chart_err_in']) !!},
                borderColor: c4,
                backgroundColor: c4 + '28',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.3,
                fill: true,
                spanGaps: true,
            },
            {
                label: 'Errores Out',
                data:  {!! json_encode($summary['chart_err_out']) !!},
                borderColor: c5,
                backgroundColor: c5 + '28',
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
                borderColor: c1,
                backgroundColor: c1 + '28',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            },
            {
                label: 'Memoria (%)',
                data:  {!! json_encode($summary['chart_mem']) !!},
                borderColor: c3,
                backgroundColor: c3 + '28',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.35,
                fill: true,
                spanGaps: true,
            }
        ];
        var yLabel = '%';
        @elseif($summary['type'] === 'availability')
        var datasets = [
            {
                label: 'Disponibilidad diaria (%)',
                data:  {!! json_encode($summary['chart_avail']) !!},
                borderColor: '#10b981',
                backgroundColor: '#10b98128',
                borderWidth: 2,
                pointRadius: labels.length > 60 ? 0 : 3,
                tension: 0.25,
                fill: true,
                spanGaps: true,
            }
        ];
        datasets.push({
            type: 'line',
            label: 'SLA objetivo ({{ number_format($summary["sla_target_pct"], 2) }}%)',
            data:  Array(labels.length).fill({{ $summary['sla_target_pct'] }}),
            borderColor: cThres,
            backgroundColor: 'transparent',
            borderWidth: 2,
            borderDash: [8, 4],
            pointRadius: 0,
            fill: false,
            tension: 0,
            spanGaps: true,
            order: -1,
        });
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
            borderColor: cThres, borderWidth: 1, borderDash: [4,3],
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

            var chartType = '{{ $chart_type ?? 'line' }}';
            var isBar     = chartType === 'bar' || chartType === 'bar_h';
            var isBarH    = chartType === 'bar_h';

            /* Adaptar datasets al tipo de gráfica; preservar los que ya tienen type fijo */
            datasets = datasets.map(function(ds) {
                if (ds.type === 'line') { return ds; } /* umbral u otros fijos */
                if (isBar) {
                    return Object.assign({}, ds, {
                        type: isBarH ? 'bar' : undefined,
                        backgroundColor: ds.borderColor + 'bb',
                        borderRadius: 3,
                        borderWidth: 1,
                        fill: false,
                        tension: undefined,
                        pointRadius: undefined,
                    });
                }
                if (chartType === 'line_clean') {
                    return Object.assign({}, ds, { fill: false });
                }
                return ds;
            });

            new Chart(canvas, {
                type: isBar ? 'bar' : 'line',
                indexAxis: isBarH ? 'y' : 'x',
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
