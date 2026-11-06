<?php

namespace App\Plugins\Reports;

use App\Plugins\Hooks\MenuEntryHook;
use Illuminate\Contracts\Auth\Authenticatable;

class Menu extends MenuEntryHook
{
    public function authorize(?Authenticatable $user): bool
    {
        return $user !== null;
    }

    public function data(array $settings = []): array
    {
        return [
            'label' => (string) ($settings['menu_label'] ?? 'Reportes'),
            'icon' => (string) ($settings['menu_icon'] ?? 'fa-line-chart'),
        ];
    }
}
