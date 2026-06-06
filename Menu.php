<?php

namespace App\Plugins\Reports;

use App\Plugins\Hooks\MenuEntryHook;

class Menu extends MenuEntryHook
{
    public function authorize(\Illuminate\Contracts\Auth\Authenticatable $user): bool
    {
        return true;
    }

    public function data(array $settings = []): array
    {
        return [
            'label' => (string) ($settings['menu_label'] ?? 'Reportes'),
            'icon' => (string) ($settings['menu_icon'] ?? 'fa-line-chart'),
        ];
    }
}
