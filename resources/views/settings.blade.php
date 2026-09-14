@php
    $fbSettings = \App\Plugins\FlowbiteTheme\Settings::getSettings();
    $fbAccentMap = [
        'blue'    => '#2563eb', 'indigo' => '#4f46e5', 'violet' => '#7c3aed',
        'emerald' => '#059669', 'teal'   => '#0d9488', 'cyan'   => '#0891b2',
        'rose'    => '#e11d48', 'amber'  => '#d97706',
    ];
    $accent = $fbAccentMap[$fbSettings['accent_color'] ?? 'blue'] ?? '#2563eb';
@endphp

<style>
.rpt-settings-wrap {
    max-width:1100px; margin:14px auto 28px; color:#344054;
    font-family:var(--fb-font-family, 'Inter', system-ui, sans-serif);
    --rpt-text:#172033; --rpt-muted:#475467; --rpt-subtle:#667085;
    --rpt-border:#d0d5dd; --rpt-soft:#f8fafc;
}
.rpt-settings-wrap .panel { border:1px solid var(--rpt-border); border-radius:8px; overflow:hidden; box-shadow:0 2px 8px rgba(15,23,42,.06); }
.rpt-settings-wrap .panel-heading {
    background: linear-gradient(135deg, #1e2432 0%, {{ $accent }} 100%);
    border:none; padding:15px 18px;
}
.rpt-settings-wrap .panel-heading .rpt-icon {
    background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); border-radius:8px;
    width:42px; height:42px; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.rpt-settings-wrap .panel-heading h4 { margin: 0 0 3px; color: #fff; font-size: 18px; font-weight: 700; }
.rpt-settings-wrap .panel-heading p  { margin:0; color:rgba(255,255,255,.9); font-size:12px; }
.rpt-settings-wrap .panel-body { padding:18px; }
.rpt-field label {
    font-size:11px; font-weight:700; text-transform:uppercase;
    letter-spacing:0; color:var(--rpt-muted); margin-bottom:5px;
}
.rpt-field .form-control {
    border-radius:7px; border-color:var(--rpt-border); font-size:13px; color:var(--rpt-text);
    transition: border-color .15s, box-shadow .15s;
}
.rpt-field .form-control[disabled], .rpt-field .form-control option:disabled { color:#475467; background:#f2f4f7; opacity:1; }
.rpt-field .form-control:focus {
    border-color: {{ $accent }};
    box-shadow: 0 0 0 3px {{ $accent }}22;
    outline: none;
}
.rpt-field .help-block, .rpt-field small { font-size:11px; color:var(--rpt-subtle) !important; margin-top:5px; }
.rpt-field { margin-bottom:14px; }
.rpt-divider { height:1px; background:var(--rpt-border); margin:16px 0; }
.rpt-btn-save {
    background: {{ $accent }}; border-color: {{ $accent }};
    color: #fff; border-radius: 8px; font-weight: 600;
    padding: 10px 28px; font-size: 14px;
    transition: opacity .15s, transform .1s;
}
.rpt-btn-save:hover { opacity: .9; color: #fff; transform: translateY(-1px); }
.rpt-btn-save[disabled] {
    color:#fff;
    filter:saturate(.65);
    cursor: not-allowed;
    transform: none;
}
.rpt-inline-status {
    margin-top: 8px;
    font-size: 12px;
    color:var(--rpt-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}
.rpt-settings-wrap .table { color:#344054; font-size:12px; }
.rpt-settings-wrap .table > thead > tr > th {
    background:var(--rpt-soft); color:var(--rpt-muted) !important; border-bottom:2px solid var(--rpt-border);
    font-size:11px !important; font-weight:700; text-transform:uppercase; letter-spacing:0 !important;
}
.rpt-settings-wrap .table > tbody > tr > td { color:#344054; border-top:1px solid #e4e7ec; vertical-align:middle; }
.rpt-settings-wrap .alert { border-radius:7px; font-size:12px; font-weight:600; }
.rpt-settings-wrap .alert-success { color:#05603a; background:#ecfdf3; border-color:#75e0a7; }
.rpt-settings-wrap .alert-warning { color:#7a2e0e; background:#fffaeb; border-color:#fec84b; }
.rpt-settings-wrap .alert-danger { color:#912018; background:#fef3f2; border-color:#fda29b; }
.rpt-settings-wrap .alert-info { color:#194185; background:#eff8ff; border-color:#84caff; }
.rpt-settings-note { background:var(--rpt-soft); border:1px solid var(--rpt-border); border-radius:7px; padding:10px 12px; font-size:12px; color:var(--rpt-muted); display:flex; gap:8px; }

@media print {
    @page { margin:12mm; }
    .rpt-settings-wrap { max-width:none; margin:0; color:#111827; --rpt-muted:#27364a; --rpt-subtle:#374151; --rpt-border:#9ca3af; --rpt-soft:#fff; }
    .rpt-settings-wrap .panel { box-shadow:none; break-inside:avoid; }
    .rpt-settings-wrap .panel-heading { background:#fff !important; border-bottom:2px solid #111827; }
    .rpt-settings-wrap .panel-heading h4, .rpt-settings-wrap .panel-heading p { color:#111827 !important; }
    .rpt-settings-wrap .rpt-icon, .rpt-settings-wrap form, .rpt-settings-wrap .btn { display:none !important; }
    .rpt-settings-wrap .table > thead > tr > th, .rpt-settings-wrap .table > tbody > tr > td { color:#111827 !important; background:#fff !important; border-color:#9ca3af !important; }
}
</style>

<div class="rpt-settings-wrap">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div style="display:flex; align-items:center; gap:16px;">
                <div class="rpt-icon">
                    <i class="fa fa-line-chart" style="font-size:22px; color:#fff;" aria-hidden="true"></i>
                </div>
                <div>
                    <h4>Configuración · Reportes</h4>
                    <p>Personalice la apariencia y textos del módulo de reportes.</p>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <form method="post" action="{{ route('plugin.update', $plugin_name) }}">
                @csrf
                @method('POST')
                <input type="hidden" name="plugin_active" value="1">

                <div class="rpt-field">
                    <label for="menu_label">Etiqueta de menú</label>
                    <input id="menu_label" class="form-control" type="text"
                           name="settings[menu_label]"
                           value="{{ $settings['menu_label'] ?? 'Reportes' }}"
                           placeholder="Reportes">
                    <span class="help-block">Texto que aparece en el menú lateral de navegación.</span>
                </div>

                <div class="rpt-field">
                    <label for="menu_icon">Icono FontAwesome</label>
                    <div class="input-group">
                        <span class="input-group-addon" style="border-radius:8px 0 0 8px; min-width:40px; text-align:center;">
                            <i class="fa {{ $settings['menu_icon'] ?? 'fa-line-chart' }}" id="icon-preview" aria-hidden="true"></i>
                        </span>
                        <input id="menu_icon" class="form-control" type="text"
                               name="settings[menu_icon]"
                               value="{{ $settings['menu_icon'] ?? 'fa-line-chart' }}"
                               placeholder="fa-line-chart"
                               oninput="document.getElementById('icon-preview').className='fa '+this.value"
                               style="border-radius:0 8px 8px 0;">
                    </div>
                    <span class="help-block">Ejemplos: <code>fa-line-chart</code>, <code>fa-area-chart</code>, <code>fa-signal</code>, <code>fa-bar-chart</code></span>
                </div>

                <div class="rpt-divider"></div>

                <div class="rpt-field">
                    <label for="page_title">Título principal</label>
                    <input id="page_title" class="form-control" type="text"
                           name="settings[page_title]"
                           value="{{ $settings['page_title'] ?? 'Reportes' }}"
                           placeholder="Reportes">
                    <span class="help-block">Encabezado grande que aparece en el banner de la página.</span>
                </div>

                <div class="rpt-field">
                    <label for="page_subtitle">Subtítulo</label>
                    <textarea id="page_subtitle" class="form-control" rows="3"
                              name="settings[page_subtitle]"
                              placeholder="Descripción del módulo...">{{ $settings['page_subtitle'] ?? 'Visualización profesional de desempeño y disponibilidad para toma de decisiones.' }}</textarea>
                    <span class="help-block">Descripción breve mostrada bajo el título en el banner.</span>
                </div>

                <div class="rpt-divider"></div>

                <div class="rpt-field">
                    <label for="chart_type">Tipo de gráfica</label>
                    <select id="chart_type" class="form-control" name="settings[chart_type]">
                        @php $ct = $settings['chart_type'] ?? 'line'; @endphp
                        <option value="line"     {{ $ct === 'line'     ? 'selected' : '' }}>Línea suave (área rellena)</option>
                        <option value="line_clean" {{ $ct === 'line_clean' ? 'selected' : '' }}>Línea simple (sin relleno)</option>
                        <option value="bar"      {{ $ct === 'bar'      ? 'selected' : '' }}>Barras verticales</option>
                        <option value="bar_h"    {{ $ct === 'bar_h'    ? 'selected' : '' }}>Barras horizontales</option>
                    </select>
                    <span class="help-block">Aplica a todos los reportes de evolución temporal.</span>
                </div>

                <div class="rpt-divider"></div>

                <div>
                    <button class="btn rpt-btn-save" type="submit">
                        <i class="fa fa-save fa-fw" aria-hidden="true"></i> Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ Gestión de Velocidades Contratadas ═══ --}}
    <div class="panel panel-default" style="margin-top:24px;">
        <div class="panel-heading">
            <div style="display:flex; align-items:center; gap:16px;">
                <div class="rpt-icon">
                    <i class="fa fa-tachometer" style="font-size:22px; color:#fff;" aria-hidden="true"></i>
                </div>
                <div>
                    <h4>Umbrales de Velocidad · Puertos</h4>
                    <p>Configure la velocidad contratada para cada puerto. Se mostrará como línea roja en los reportes.</p>
                </div>
            </div>
        </div>

        <div class="panel-body">
            {{-- Mensajes de éxito/error --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible" style="margin-bottom:16px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible" style="margin-bottom:16px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible" style="margin-bottom:16px;">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <i class="fa fa-times-circle"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Agregar nuevo umbral --}}
            <form method="post" action="{{ route('plugin.update', $plugin_name) }}" style="margin-bottom:20px;">
                @csrf
                @method('POST')
                <input type="hidden" name="plugin_active" value="1">
                <input type="hidden" name="contract_action" value="add">

                <div style="display:grid; grid-template-columns:1fr 1fr 1fr 120px; gap:10px; align-items:end; margin-bottom:20px;">
                    <div class="rpt-field">
                        <label>Dispositivo</label>
                        <select name="contract_device_id" class="form-control" id="contractDeviceSelect" 
                                onchange="loadPortsForDevice(this.value)" required>
                            <option value="">— Seleccionar —</option>
                            @foreach(($devices ?? []) as $dev)
                                <option value="{{ $dev->device_id }}" {{ (string) old('contract_device_id', '') === (string) $dev->device_id ? 'selected' : '' }}>{{ $dev->hostname }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="rpt-field">
                        <label>Puerto (Interfaz)</label>
                        <div style="position:relative;">
                            <select name="contract_port_id" class="form-control" id="contractPortSelect" required>
                                <option value="">— Seleccionar puerto —</option>
                            </select>
                            <small id="contractPortHint" style="color:var(--rpt-subtle); margin-top:3px; display:block;">
                                <i class="fa fa-info-circle"></i> Selecciona dispositivo primero
                            </small>
                            <div class="rpt-inline-status" id="contractPortInlineStatus" style="display:none;"></div>
                        </div>
                    </div>

                    <div class="rpt-field">
                        <label>Velocidad (Mbps)</label>
                        <input type="number" name="contract_bandwidth" class="form-control" 
                               id="contractBandwidthInput" value="{{ old('contract_bandwidth', '') }}"
                               placeholder="Ej: 400" min="1" max="1000000" required>
                        <small style="color:var(--rpt-subtle); margin-top:3px; display:block;">
                            Rango: 1 - 1.000.000 Mbps
                        </small>
                    </div>

                    <div style="text-align:center;">
                        <button type="submit" class="btn rpt-btn-save" id="contractSubmitBtn" style="width:100%; margin:0;" disabled>
                            <i class="fa fa-plus fa-fw"></i> Agregar
                        </button>
                    </div>
                </div>

                <div class="rpt-settings-note">
                    <i class="fa fa-lightbulb-o" style="color:{{ $accent }}; font-size:14px;"></i>
                    <span><strong>Validaciones:</strong> El puerto debe ser único por dispositivo. Velocidad debe estar entre 1 y 1.000.000 Mbps. No se permite duplicar umbrales.</span>
                </div>
            </form>

            {{-- Tabla de umbrales existentes --}}
            @if(!empty($contract_bandwidths))
            <div class="table-responsive" style="margin-top:20px;">
                <table class="table table-hover table-condensed" style="margin-bottom:0;">
                    <thead>
                    <tr style="background:#f8fafc;">
                        <th>Dispositivo</th>
                        <th>Puerto (Interfaz)</th>
                        <th>Descripción</th>
                        <th style="text-align:right;">Velocidad (Mbps)</th>
                        <th style="text-align:center;">Acción</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($contract_bandwidths as $item)
                    <tr>
                        <td><strong>{{ $item['hostname'] }}</strong></td>
                        <td><code style="font-weight:600; color:{{ $accent }};">{{ $item['ifName'] }}</code></td>
                        <td style="color:var(--rpt-muted); font-size:12px;">{{ $item['ifAlias'] ?? '—' }}</td>
                        <td style="text-align:right; font-weight:600; color:{{ $accent }};">{{ $item['bandwidth'] }}</td>
                        <td style="text-align:center;">
                            <form method="post" action="{{ route('plugin.update', $plugin_name) }}" style="display:inline;">
                                @csrf
                                @method('POST')
                                <input type="hidden" name="plugin_active" value="1">
                                <input type="hidden" name="contract_action" value="delete">
                                <input type="hidden" name="contract_port_id" value="{{ $item['port_id'] }}">
                                <button type="submit" class="btn btn-danger btn-xs"
                                        onclick="return confirm('¿Eliminar el umbral de {{ $item['bandwidth'] }} Mbps para {{ $item['ifName'] }}?');"
                                        style="padding:3px 8px; font-size:11px;">
                                    <i class="fa fa-trash"></i> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="alert alert-info" style="margin-bottom:0; display:flex; align-items:center; gap:10px;">
                <i class="fa fa-info-circle fa-lg"></i>
                <span>No hay umbrales configurados aún. Agregue uno usando el formulario arriba.</span>
            </div>
            @endif
        </div>
    </div>

<script>
window.reportPortsByDevice = @json($ports_by_device ?? []);
window.initialContractDeviceId = @json((string) old('contract_device_id', ''));
window.initialContractPortId = @json((string) old('contract_port_id', ''));

/**
 * Obtener puertos de un dispositivo y poblar select.
 */
window.loadPortsForDevice = function(deviceId) {
    var portSelect = document.getElementById('contractPortSelect');
    var portHint = document.getElementById('contractPortHint');
    var portInlineStatus = document.getElementById('contractPortInlineStatus');
    
    if (!portSelect || !deviceId) {
        portSelect.innerHTML = '<option value="">— Seleccionar puerto —</option>';
        if (portHint) {
            portHint.innerHTML = '<i class="fa fa-info-circle"></i> Selecciona dispositivo primero';
        }
        if (portInlineStatus) {
            portInlineStatus.style.display = 'none';
        }
        validateContractForm();
        return;
    }

    var ports = (window.reportPortsByDevice && window.reportPortsByDevice[deviceId])
        ? window.reportPortsByDevice[deviceId]
        : [];

    populatePortSelect(ports, window.initialContractPortId);
    window.initialContractPortId = '';

    if (portHint) {
        if (ports.length > 0) {
            portHint.innerHTML = '<i class="fa fa-check-circle"></i> ' + ports.length + ' puerto(s) disponible(s) para este equipo';
            if (portInlineStatus) {
                portInlineStatus.style.display = 'flex';
                portInlineStatus.innerHTML = '<i class="fa fa-info-circle"></i> Selecciona una interfaz y luego la velocidad contratada.';
            }
        } else {
            portHint.innerHTML = '<i class="fa fa-exclamation-triangle"></i> Este equipo no tiene puertos activos para polling';
            if (portInlineStatus) {
                portInlineStatus.style.display = 'flex';
                portInlineStatus.innerHTML = '<i class="fa fa-ban"></i> No hay interfaces disponibles para este equipo.';
            }
        }
    }

    validateContractForm();
};

/**
 * Poblar select de puertos.
 */
window.populatePortSelect = function(ports, selectedPortId) {
    var select = document.getElementById('contractPortSelect');
    var html = '<option value="">— Seleccionar puerto —</option>';

    if (!Array.isArray(ports) || ports.length === 0) {
        select.innerHTML = html;
        return;
    }
    
    ports.forEach(p => {
        var oper = String(p.ifOperStatus || '').toLowerCase();
        var statusTag = oper === 'up' ? '[UP] ' : '[DOWN] ';
        var label = p.ifName;
        if (p.ifAlias) label += ' · ' + p.ifAlias;
        if (p.ifSpeed) {
            var speed = p.ifSpeed >= 1000000000 ? (p.ifSpeed / 1000000000).toFixed(0) + 'G' :
                        p.ifSpeed >= 1000000 ? (p.ifSpeed / 1000000).toFixed(0) + 'M' : p.ifSpeed;
            label += ' (' + speed + 'bps)';
        }
        var selected = (selectedPortId && String(selectedPortId) === String(p.port_id)) ? ' selected' : '';
        html += '<option value="' + p.port_id + '"' + selected + '>' + statusTag + label + '</option>';
    });
    
    select.innerHTML = html;
};

window.validateContractForm = function() {
    var device = document.getElementById('contractDeviceSelect');
    var port = document.getElementById('contractPortSelect');
    var bw = document.getElementById('contractBandwidthInput');
    var btn = document.getElementById('contractSubmitBtn');

    if (!device || !port || !bw || !btn) {
        return;
    }

    var bwVal = Number(bw.value || 0);
    var valid = !!device.value && !!port.value && bwVal >= 1 && bwVal <= 1000000;
    btn.disabled = !valid;
};

document.addEventListener('DOMContentLoaded', function() {
    var device = document.getElementById('contractDeviceSelect');
    var port = document.getElementById('contractPortSelect');
    var bw = document.getElementById('contractBandwidthInput');

    if (device) {
        if (device.value) {
            loadPortsForDevice(device.value);
        }
        device.addEventListener('change', validateContractForm);
    }

    if (port) {
        port.addEventListener('change', validateContractForm);
    }

    if (bw) {
        bw.addEventListener('input', validateContractForm);
        bw.addEventListener('change', validateContractForm);
    }

    validateContractForm();
});
</script>

{{-- ══════════════ Panel SLA por Dispositivo ══════════════ --}}
    <div class="panel panel-default" style="margin-top:24px;">
    <div class="panel-heading">
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="rpt-icon">
                <i class="fa fa-check-circle" style="font-size:22px; color:#fff;" aria-hidden="true"></i>
            </div>
            <div>
                <h4>SLA · Disponibilidad por Dispositivo</h4>
                <p>Defina el objetivo de disponibilidad para cada equipo. Se mostrará como línea de referencia en la gráfica.</p>
            </div>
        </div>
    </div>

    <div class="panel-body">

        @if(session('success') || session('sla_success'))
        <div class="alert alert-success alert-dismissible" style="margin-bottom:16px; border-radius:8px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check-circle"></i> {{ session('success') ?? session('sla_success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" style="margin-bottom:16px; border-radius:8px;">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-times-circle"></i> {{ session('error') }}
        </div>
        @endif

        {{-- Presets rápidos --}}
        <div style="margin-bottom:16px; display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
            <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0; color:var(--rpt-muted);">Presets:</span>
            @foreach([['99.9','43 min/mes'],['99.5','216 min/mes'],['99.0','432 min/mes'],['98.0','864 min/mes']] as [$pct,$hint])
            <button type="button" class="btn btn-xs sla-preset-btn"
                    data-pct="{{ $pct }}"
                    style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:6px; font-size:12px; font-weight:600; color:#334155; padding:3px 10px; transition:all .15s;"
                    onmouseover="this.style.background='{{ $accent }}'; this.style.color='#fff'; this.style.borderColor='{{ $accent }}';"
                    onmouseout="this.style.background='#f1f5f9'; this.style.color='#334155'; this.style.borderColor='#cbd5e1';">
                {{ $pct }}% <span style="font-weight:500; color:inherit;">({{ $hint }})</span>
            </button>
            @endforeach
        </div>

        <form method="post" action="{{ route('plugin.update', $plugin_name) }}" id="slaForm">
            {{ csrf_field() }}
            {{ method_field('POST') }}
            <input type="hidden" name="plugin_active" value="1">
            <input type="hidden" name="sla_action" value="add">

            <div style="display:grid; grid-template-columns:1fr 200px 130px; gap:10px; align-items:end;">
                <div class="rpt-field">
                    <label for="sla_device_id">Dispositivo</label>
                    <select name="sla_device_id" id="sla_device_id" class="form-control" required onchange="slaValidate()">
                        <option value="">— Seleccionar —</option>
                        @foreach($devices as $dev)
                            @php $alreadySet = collect($sla_targets ?? [])->firstWhere('device_id', $dev->device_id); @endphp
                            <option value="{{ $dev->device_id }}" {{ $alreadySet ? 'disabled' : '' }}>
                                {{ $dev->hostname }}{{ $alreadySet ? ' (configurado)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="rpt-field">
                    <label for="sla_target_input">SLA objetivo (%)</label>
                    <input type="number" name="sla_target" id="sla_target_input"
                           class="form-control" step="0.01" min="90" max="100"
                           placeholder="Ej: 99.9" required oninput="slaPreview(this.value); slaValidate();">
                    <div id="sla_preview" style="margin-top:4px; font-size:11px; color:var(--rpt-muted); min-height:16px;"></div>
                </div>
                <div>
                    <button type="submit" class="btn rpt-btn-save" id="slaSubmitBtn"
                            style="width:100%; margin:0;" disabled>
                        <i class="fa fa-plus fa-fw"></i> Agregar
                    </button>
                </div>
            </div>
        </form>

        <div class="rpt-settings-note" style="align-items:flex-start; margin-top:12px;">
            <i class="fa fa-lightbulb-o" style="color:{{ $accent }}; font-size:14px; margin-top:1px; flex-shrink:0;"></i>
            <span>El SLA se calcula sobre el período seleccionado en el reporte. La línea roja discontinua en la gráfica muestra el límite de disponibilidad comprometido.</span>
        </div>

        {{-- Tabla de SLAs configurados --}}
        <div style="margin-top:20px;">
        @if(!empty($sla_targets))
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0; color:var(--rpt-muted); margin-bottom:8px;">
                {{ count($sla_targets) }} {{ count($sla_targets) === 1 ? 'dispositivo configurado' : 'dispositivos configurados' }}
            </div>
            <div style="border:1px solid #e5eaf0; border-radius:8px; overflow:hidden;">
                <table class="table table-hover" style="margin-bottom:0; font-size:13px;">
                    <thead>
                    <tr style="background:#f8fafc;">
                        <th style="border-top:none; padding:10px 14px;">Dispositivo</th>
                        <th style="border-top:none; padding:10px 14px; text-align:center;">SLA objetivo</th>
                        <th style="border-top:none; padding:10px 14px; text-align:center;">Downtime permitido / mes</th>
                        <th style="border-top:none; padding:10px 14px; text-align:center;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sla_targets as $sla)
                        @php
                            $downtimeMins = $sla['downtime_minutes'];
                            $slaColor = $sla['sla_target'] >= 99.9 ? '#067647' : ($sla['sla_target'] >= 99.0 ? '#92400e' : '#b42318');
                            $slaBg    = $sla['sla_target'] >= 99.9 ? '#ecfdf3' : ($sla['sla_target'] >= 99.0 ? '#fef9c3' : '#fef3f2');
                            $slaBorder= $sla['sla_target'] >= 99.9 ? '#abefc6' : ($sla['sla_target'] >= 99.0 ? '#fde68a' : '#fecdca');
                        @endphp
                        <tr>
                            <td style="padding:10px 14px; vertical-align:middle;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="width:8px; height:8px; border-radius:50%; background:{{ $accent }};"></div>
                                    <strong>{{ $sla['hostname'] }}</strong>
                                </div>
                            </td>
                            <td style="padding:10px 14px; text-align:center; vertical-align:middle;">
                                <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; background:{{ $slaBg }}; border:1px solid {{ $slaBorder }}; color:{{ $slaColor }};">
                                    <i class="fa fa-circle" style="font-size:6px;"></i>
                                    {{ $sla['sla_target'] }}%
                                </span>
                            </td>
                            <td style="padding:10px 14px; text-align:center; vertical-align:middle; color:var(--rpt-muted); font-size:12px;">
                                ≤ <strong style="color:#374151;">{{ $downtimeMins }}</strong> min
                            </td>
                            <td style="padding:10px 14px; text-align:center; vertical-align:middle;">
                                <form method="post" action="{{ route('plugin.update', $plugin_name) }}" style="display:inline;"
                                      onsubmit="return confirm('¿Eliminar SLA de {{ addslashes($sla['hostname']) }}?');">
                                    {{ csrf_field() }}
                                    {{ method_field('POST') }}
                                    <input type="hidden" name="plugin_active" value="1">
                                    <input type="hidden" name="sla_action" value="delete">
                                    <input type="hidden" name="sla_device_id" value="{{ $sla['device_id'] }}">
                                    <button type="submit" class="btn btn-xs"
                                            style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:6px; padding:4px 10px; font-size:11px; font-weight:600;">
                                        <i class="fa fa-trash fa-fw"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="border:1px dashed var(--rpt-border); border-radius:8px; padding:20px; text-align:center; color:var(--rpt-subtle);">
                <i class="fa fa-check-circle" style="font-size:28px; margin-bottom:8px; display:block; color:#667085;"></i>
                <div style="font-size:13px; font-weight:700; color:var(--rpt-muted);">Sin SLA configurados</div>
                <div style="font-size:12px; margin-top:4px;">Seleccione un dispositivo y defina el objetivo de disponibilidad.</div>
            </div>
        @endif
        </div>
    </div>
</div>

<script>
function slaPreview(val) {
    var el = document.getElementById('sla_preview');
    if (!el) return;
    var v = parseFloat(val);
    if (isNaN(v) || v <= 0 || v > 100) { el.innerHTML = ''; return; }
    var mins = Math.abs(v - 99.9) < 0.00001
        ? 43
        : Math.round(((1 - v / 100) * 30 * 24 * 60) * 10) / 10;
    var hrs = (mins / 60).toFixed(2);
    el.innerHTML = '<i class="fa fa-info-circle"></i> Límite contractual mensual: <strong>≤ ' + mins + ' min</strong> (' + hrs + ' h)';
}
function slaValidate() {
    var dev = document.getElementById('sla_device_id');
    var inp = document.getElementById('sla_target_input');
    var btn = document.getElementById('slaSubmitBtn');
    if (!dev || !inp || !btn) return;
    var v = parseFloat(inp.value);
    btn.disabled = !(dev.value && !isNaN(v) && v >= 90 && v <= 100);
}
document.querySelectorAll('.sla-preset-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var inp = document.getElementById('sla_target_input');
        if (inp) { inp.value = this.dataset.pct; slaPreview(this.dataset.pct); slaValidate(); inp.focus(); }
    });
});
</script>
</div>
