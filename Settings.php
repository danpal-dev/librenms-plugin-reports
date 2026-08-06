<?php

namespace App\Plugins\Reports;

use App\Plugins\Hooks\SettingsHook;
use Illuminate\Support\Facades\DB;

class Settings extends SettingsHook
{
    public function authorize(\Illuminate\Contracts\Auth\Authenticatable $user): bool
    {
        return $user->can('admin');
    }

    public function data(array $settings): array
    {
        if (request()->isMethod('post')) {
            // SettingsHook::handle() calls data() twice; process POST only once
            static $postHandled = false;
            if (! $postHandled) {
                $postHandled = true;
                $action = (string) request()->input('action', '');
                if (in_array($action, ['delete_log_entry', 'clear_log'], true)) {
                    app(Page::class)->handleAuditAction(
                        $action,
                        (int) request()->input('line_index', -1)
                    );
                } else {
                    $this->handleContractBandwidthPost();
                    $this->handleSlaPost();
                }
            }
        }

        $contractBandwidths = $this->getContractBandwidths();
        $slaTargets = $this->getSlaTargets();
        $devices = $this->getDevices();
        $portsByDevice = $this->getPortsByDevice();

        return [
            'settings' => [
                'menu_label' => (string) ($settings['menu_label'] ?? 'Reportes'),
                'menu_icon' => (string) ($settings['menu_icon'] ?? 'fa-line-chart'),
                'page_title' => (string) ($settings['page_title'] ?? 'Reportes'),
                'page_subtitle' => (string) ($settings['page_subtitle'] ?? 'Visualizacion profesional de desempeno y disponibilidad para toma de decisiones.'),
                'chart_type' => (string) ($settings['chart_type'] ?? 'line'),
            ],
            'devices' => $devices,
            'ports_by_device' => $portsByDevice,
            'contract_bandwidths' => $contractBandwidths,
            'sla_targets' => $slaTargets,
        ];
    }

    private function getDevices(): array
    {
        return DB::select(
            'SELECT device_id, hostname FROM devices WHERE disabled = 0 AND `ignore` = 0 ORDER BY hostname ASC'
        );
    }

    private function getPortsByDevice(): array
    {
        $ports = DB::select(
            'SELECT port_id, device_id, ifName, ifAlias, ifSpeed, ifOperStatus
             FROM ports
             WHERE deleted = 0 AND disabled = 0 AND `ignore` = 0
             ORDER BY device_id ASC,
                      CASE WHEN LOWER(ifOperStatus) = "up" THEN 0 ELSE 1 END ASC,
                      ifIndex ASC'
        );

        $result = [];
        foreach ($ports as $p) {
            $devId = (int) $p->device_id;
            if (! isset($result[$devId])) {
                $result[$devId] = [];
            }

            $result[$devId][] = [
                'port_id' => (int) $p->port_id,
                'ifName' => (string) $p->ifName,
                'ifAlias' => (string) ($p->ifAlias ?? ''),
                'ifSpeed' => (int) ($p->ifSpeed ?? 0),
                'ifOperStatus' => (string) ($p->ifOperStatus ?? 'unknown'),
            ];
        }

        return $result;
    }

    /**
     * Procesa POST para agregar/eliminar SLA por dispositivo.
     */
    private function handleSlaPost(): void
    {
        $action = request()->input('sla_action');
        if (! in_array($action, ['add', 'delete'], true)) {
            return;
        }

        $deviceId = (int) request()->input('sla_device_id', 0);
        if ($deviceId <= 0) {
            return;
        }

        $device = DB::selectOne(
            'SELECT device_id, hostname FROM devices WHERE device_id = ? AND disabled = 0',
            [$deviceId]
        );
        if (! $device) {
            session()->flash('error', 'Dispositivo no válido.');
            return;
        }

        if ($action === 'add') {
            $sla = (float) request()->input('sla_target', 0);
            if ($sla <= 0 || $sla > 100) {
                session()->flash('error', 'El SLA debe estar entre 0.01 y 100%.');
                return;
            }
            $existing = DB::selectOne(
                'SELECT attrib_id FROM devices_attribs WHERE device_id = ? AND attrib_type = ? LIMIT 1',
                [$deviceId, 'device_sla_target']
            );
            if ($existing) {
                DB::statement(
                    'UPDATE devices_attribs SET attrib_value = ? WHERE attrib_id = ?',
                    [(string) $sla, $existing->attrib_id]
                );
                // remove any accidental duplicates leaving only the updated row
                DB::statement(
                    'DELETE FROM devices_attribs WHERE device_id = ? AND attrib_type = ? AND attrib_id != ?',
                    [$deviceId, 'device_sla_target', $existing->attrib_id]
                );
            } else {
                DB::statement(
                    'INSERT INTO devices_attribs (device_id, attrib_type, attrib_value) VALUES (?, ?, ?)',
                    [$deviceId, 'device_sla_target', (string) $sla]
                );
            }
            session()->flash('success', "SLA de {$sla}% guardado para {$device->hostname}.");
        } elseif ($action === 'delete') {
            DB::statement(
                'DELETE FROM devices_attribs WHERE device_id = ? AND attrib_type = ?',
                [$deviceId, 'device_sla_target']
            );
            session()->flash('success', "SLA eliminado para {$device->hostname}.");
        }
    }

    /**
     * Obtiene todos los SLA configurados por dispositivo.
     */
    private function getSlaTargets(): array
    {
        $records = DB::select(
            'SELECT da.device_id, da.attrib_value, d.hostname
             FROM devices_attribs da
             JOIN devices d ON da.device_id = d.device_id
             WHERE da.attrib_type = "device_sla_target"
             ORDER BY d.hostname'
        );

        return array_map(fn ($r) => [
            'device_id' => (int) $r->device_id,
            'hostname' => (string) $r->hostname,
            'sla_target' => (float) $r->attrib_value,
        ], $records);
    }

    /**
     * Procesa POST para agregar/actualizar/eliminar velocidades contratadas.
     */
    private function handleContractBandwidthPost(): void
    {
        $action = request()->input('contract_action');

        if ($action === 'add' || $action === 'update') {
            $portId = (int) request()->input('contract_port_id', 0);
            $selectedDeviceId = (int) request()->input('contract_device_id', 0);
            $bandwidth = (int) request()->input('contract_bandwidth', 0);

            // Validaciones
            if ($portId <= 0) {
                session()->flash('error', 'Puerto inválido.');
                return;
            }

            if ($bandwidth <= 0 || $bandwidth > 1000000) {
                session()->flash('error', 'La velocidad debe estar entre 1 y 1.000.000 Mbps.');
                return;
            }

            // Verificar que el puerto existe y está activo para polling
            $port = DB::selectOne(
                'SELECT device_id, ifName FROM ports WHERE port_id = ? AND deleted = 0 AND disabled = 0 AND `ignore` = 0',
                [$portId]
            );
            if (!$port) {
                session()->flash('error', 'Puerto no encontrado o no activo para polling.');
                return;
            }

            // Si llega dispositivo seleccionado, validar coherencia puerto-dispositivo
            if ($selectedDeviceId > 0 && (int) $port->device_id !== $selectedDeviceId) {
                session()->flash('error', 'El puerto no pertenece al dispositivo seleccionado.');
                return;
            }

            // Verificar que no ya exista un umbral (evitar duplicados)
            $existing = DB::selectOne(
                'SELECT 1 FROM devices_attribs WHERE device_id = ? AND attrib_type = ?',
                [(int) $port->device_id, "port_{$portId}_contract_bandwidth"]
            );

            if ($existing && $action === 'add') {
                session()->flash('warning', "El puerto {$port->ifName} ya tiene un umbral configurado. Use eliminar y agregar de nuevo para cambiar.");
                return;
            }

            // Guardar
            DB::statement(
                'INSERT INTO devices_attribs (device_id, attrib_type, attrib_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE attrib_value = ?',
                [(int) $port->device_id, "port_{$portId}_contract_bandwidth", (string) $bandwidth, (string) $bandwidth]
            );

            session()->flash('success', "Umbral de {$bandwidth} Mbps guardado para puerto {$port->ifName}.");
        } elseif ($action === 'delete') {
            $portId = (int) request()->input('contract_port_id', 0);
            if ($portId > 0) {
                $port = DB::selectOne('SELECT device_id, ifName FROM ports WHERE port_id = ?', [$portId]);
                if ($port) {
                    DB::statement(
                        'DELETE FROM devices_attribs WHERE device_id = ? AND attrib_type = ?',
                        [(int) $port->device_id, "port_{$portId}_contract_bandwidth"]
                    );
                    session()->flash('success', "Umbral eliminado para puerto {$port->ifName}.");
                }
            }
        }
    }

    /**
     * Obtiene todas las velocidades contratadas configuradas.
     */
    private function getContractBandwidths(): array
    {
        $records = DB::select(
            'SELECT
                da.device_id,
                da.attrib_type,
                da.attrib_value,
                d.hostname,
                p.port_id,
                p.ifName,
                p.ifAlias
            FROM devices_attribs da
            JOIN devices d ON da.device_id = d.device_id
            JOIN ports p ON p.device_id = da.device_id
                AND da.attrib_type = CONCAT("port_", p.port_id, "_contract_bandwidth")
                AND p.deleted = 0
                AND p.disabled = 0
                AND p.ignore = 0
            WHERE da.attrib_type LIKE "port_%_contract_bandwidth"
            ORDER BY d.hostname'
        );

        $result = [];
        foreach ($records as $r) {
            $portId = (int) $r->port_id;
            $result[$portId] = [
                'port_id' => $portId,
                'device_id' => (int) $r->device_id,
                'hostname' => $r->hostname,
                'ifName' => $r->ifName,
                'ifAlias' => $r->ifAlias,
                'bandwidth' => (int) $r->attrib_value,
            ];
        }

        return array_values($result);
    }
}

