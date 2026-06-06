<?php

namespace App\Plugins\Reports;

use App\Plugins\Hooks\SettingsHook;

class Settings extends SettingsHook
{
    public function authorize(\Illuminate\Contracts\Auth\Authenticatable $user): bool
    {
        return $user->can('admin');
    }

    public function data(array $settings): array
    {
        return [
            'settings' => [
                'menu_label' => (string) ($settings['menu_label'] ?? 'Reportes'),
                'menu_icon' => (string) ($settings['menu_icon'] ?? 'fa-line-chart'),
                'page_title' => (string) ($settings['page_title'] ?? 'Reportes'),
                'page_subtitle' => (string) ($settings['page_subtitle'] ?? 'Visualizacion profesional de desempeno y disponibilidad para toma de decisiones.'),
            ],
        ];
    }
}
