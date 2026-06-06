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
.rpt-settings-wrap { max-width: 680px; margin: 20px auto 40px; }
.rpt-settings-wrap .panel { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
.rpt-settings-wrap .panel-heading {
    background: linear-gradient(135deg, #1e2432 0%, {{ $accent }} 100%);
    border: none; padding: 22px 24px;
}
.rpt-settings-wrap .panel-heading .rpt-icon {
    background: rgba(255,255,255,.15); border-radius: 12px;
    width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.rpt-settings-wrap .panel-heading h4 { margin: 0 0 3px; color: #fff; font-size: 18px; font-weight: 700; }
.rpt-settings-wrap .panel-heading p  { margin: 0; color: rgba(255,255,255,.75); font-size: 13px; }
.rpt-settings-wrap .panel-body { padding: 24px; }
.rpt-field label {
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: #374151; margin-bottom: 6px;
}
.rpt-field .form-control {
    border-radius: 8px; border-color: #d1d5db; font-size: 14px;
    transition: border-color .15s, box-shadow .15s;
}
.rpt-field .form-control:focus {
    border-color: {{ $accent }};
    box-shadow: 0 0 0 3px {{ $accent }}22;
    outline: none;
}
.rpt-field .help-block { font-size: 12px; color: #9ca3af; margin-top: 5px; }
.rpt-field { margin-bottom: 20px; }
.rpt-divider { height: 1px; background: #e5eaf0; margin: 20px 0; }
.rpt-btn-save {
    background: {{ $accent }}; border-color: {{ $accent }};
    color: #fff; border-radius: 8px; font-weight: 600;
    padding: 10px 28px; font-size: 14px;
    transition: opacity .15s, transform .1s;
}
.rpt-btn-save:hover { opacity: .9; color: #fff; transform: translateY(-1px); }
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

                <div>
                    <button class="btn rpt-btn-save" type="submit">
                        <i class="fa fa-save fa-fw" aria-hidden="true"></i> Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
