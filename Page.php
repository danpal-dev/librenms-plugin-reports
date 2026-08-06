<?php

namespace App\Plugins\Reports;

use App\Plugins\Hooks\PageHook;
use App\Plugins\Reports\Support\SimplePdfWriter;
use App\Plugins\Reports\Support\SimpleXlsxWriter;
use DateTime;
use Illuminate\Support\Facades\DB;

class Page extends PageHook
{
    private const STEP_DAY = 86400;
    private const STEP_HOUR = 3600;
    private const SLA_DOWNTIME_MINUTES_PER_MONTH = 43.0;

    private array $availabilityMetrics = [];
    private array $availabilityEvents = [];

    public function authorize(\Illuminate\Contracts\Auth\Authenticatable $user): bool
    {
        return true;
    }

    public function data(array $settings = []): array
    {
        $allowedActions = ['view', 'export_csv', 'export_excel', 'export_pdf'];
        $allowedReports = ['bandwidth', 'packets', 'resources', 'availability'];
        $allowedPeriods = ['custom', 'daily', 'weekly', 'monthly', 'annual'];

        $action = request()->input('action', 'view');
        if (! in_array($action, $allowedActions, true)) {
            $action = 'view';
        }

        $reportType = request()->query('report_type', 'bandwidth');
        if (! in_array($reportType, $allowedReports, true)) {
            $reportType = 'bandwidth';
        }

        $period = request()->query('period', 'daily');
        if (! in_array($period, $allowedPeriods, true)) {
            $period = 'daily';
        }

        $deviceId = (int) request()->query('device_id', 0);
        $portId = (int) request()->query('port_id', 0);

        $today = date('Y-m-d');
        $defaultTo = $today;
        switch ($period) {
            case 'daily':   // Últimas 24 horas
                $defaultFrom = $today;
                break;
            case 'weekly':  // Últimos 7 días inclusivos
                $defaultFrom = date('Y-m-d', strtotime('-6 days'));
                break;
            case 'monthly': // Últimos 30 días inclusivos
                $defaultFrom = date('Y-m-d', strtotime('-29 days'));
                break;
            case 'annual':  // Últimos 365 días inclusivos
                $defaultFrom = date('Y-m-d', strtotime('-364 days'));
                break;
            default:
                $defaultFrom = $today;
        }

        $dateFrom = $this->validateDate((string) request()->query('date_from', $defaultFrom), $defaultFrom);
        $dateTo = $this->validateDate((string) request()->query('date_to', $defaultTo), $defaultTo);
        $includeLastDay = (bool) request()->query('include_last_day', false);
        if ($dateFrom > $dateTo) {
            $tmp = $dateFrom;
            $dateFrom = $dateTo;
            $dateTo = $tmp;
        }

        $startTs = (new DateTime($dateFrom))->setTime(0, 0, 0)->getTimestamp();
        if ($period === 'custom' && ! $includeLastDay) {
            $endTs = (new DateTime($dateTo))->setTime(0, 0, 0)->getTimestamp();
            if ($endTs <= $startTs) {
                $endTs = $startTs + self::STEP_DAY;
            }
        } else {
            $endTs = (new DateTime($dateTo))->setTime(23, 59, 59)->getTimestamp();
        }

        $reportLabels = [
            'bandwidth' => 'Ancho de Banda',
            'packets' => 'Paquetes / Errores',
            'resources' => 'CPU y Memoria',
            'availability' => 'Disponibilidad',
        ];

        $periodLabels = [
            'daily'   => 'Diario (Últimas 24 horas)',
            'weekly'  => 'Semanal (Últimos 7 días)',
            'monthly' => 'Mensual (Últimos 30 días)',
            'annual'  => 'Anual (Últimos 365 días)',
            'custom'  => 'Personalizado',
        ];

        $allDevices = $this->getDevices();
        $device = $deviceId > 0 ? $this->getDevice($deviceId) : null;
        $allPorts = $device ? $this->getPorts((int) $device->device_id) : [];
        $port = ($device && $portId > 0) ? $this->getPort($portId, (int) $device->device_id) : null;

        // Obtener velocidad contratada del puerto actual (solo lectura)
        $contractBandwidth = 0;
        if ($port && $device) {
            $contractBandwidth = $this->getContractBandwidth((int) $port->port_id);
        }

        // Obtener SLA objetivo configurado para el dispositivo
        $deviceSla = $deviceId > 0 ? $this->getDeviceSla($deviceId) : 0.0;

        $errorMessage = '';
        $reportData = [];
        if ($deviceId > 0 && ! $device) {
            $errorMessage = 'Dispositivo no encontrado.';
        }

        if ($device && in_array($reportType, ['bandwidth', 'packets'], true) && $portId <= 0) {
            $errorMessage = 'Seleccione una interfaz para este tipo de reporte.';
        }

        if ($port && $device && (int) $port->device_id !== (int) $device->device_id) {
            // Solo bloquear reportes que requieren puerto; resources/availability no lo usan
            if (in_array($reportType, ['bandwidth', 'packets'], true)) {
                $errorMessage = 'Interfaz no valida para el dispositivo seleccionado.';
            }
            $port = null;
        }

        if ($device && $errorMessage === '') {
            if ($reportType === 'bandwidth' && $port) {
                $reportData = $this->dataBandwidth((array) $device, (array) $port, $startTs, $endTs);
            } elseif ($reportType === 'packets' && $port) {
                $reportData = $this->dataPackets((array) $device, (array) $port, $startTs, $endTs);
            } elseif ($reportType === 'resources') {
                $reportData = $this->dataResources((array) $device, $startTs, $endTs);
            } elseif ($reportType === 'availability') {
                $reportData = $this->dataAvailability((array) $device, $startTs, $endTs);
            }
        }

        if ($action !== 'view' && ! empty($reportData) && $device) {
            $label = [
                'bandwidth' => 'AnchoBanda',
                'packets' => 'Paquetes',
                'resources' => 'RecursosCPU-Mem',
                'availability' => 'Disponibilidad',
            ][$reportType] ?? $reportType;
            // Sanitizar hostname para uso en nombre de archivo
            $safeHost = preg_replace('/[^A-Za-z0-9._-]/', '-', (string) $device->hostname);

            if ($action === 'export_csv') {
                $this->recordAudit(
                    'export_csv',
                    $reportType,
                    $period,
                    $deviceId,
                    $device->hostname,
                    $portId,
                    $dateFrom,
                    $dateTo,
                    count($reportData)
                );
                $this->exportCsv($reportData, sprintf('Moni_%s_%s_%s_al_%s.csv', $label, $safeHost, $dateFrom, $dateTo));
            }

            if ($action === 'export_excel') {
                $this->recordAudit(
                    'export_excel',
                    $reportType,
                    $period,
                    $deviceId,
                    $device->hostname,
                    $portId,
                    $dateFrom,
                    $dateTo,
                    count($reportData)
                );
                $this->exportExcel(
                    $reportData,
                    sprintf('Moni_%s_%s_%s_al_%s.xlsx', $label, $safeHost, $dateFrom, $dateTo),
                    $reportLabels[$reportType] ?? 'Reporte'
                );
            }

            if ($action === 'export_pdf') {
                $this->recordAudit(
                    'export_pdf',
                    $reportType,
                    $period,
                    $deviceId,
                    $device->hostname,
                    $portId,
                    $dateFrom,
                    $dateTo,
                    count($reportData)
                );
                $this->exportPdf(
                    $reportData,
                    sprintf('Moni_%s_%s_%s_al_%s.pdf', $label, $safeHost, $dateFrom, $dateTo),
                    (string) ($reportLabels[$reportType] ?? 'Reporte Ejecutivo'),
                    [
                        'Dispositivo' => (string) $device->hostname,
                        'Periodo' => $dateFrom . ' al ' . $dateTo,
                        'Generado por' => (string) (auth()->user()->username ?? 'usuario'),
                    ]
                );
            }
        }

        $summary = null;
        if (! empty($reportData)) {
            $floatCol = function (array $rows, string $col): array {
                return array_values(array_filter(
                    array_column($rows, $col),
                    fn ($v) => $v !== '' && $v !== null && is_numeric($v)
                ));
            };

            if ($reportType === 'bandwidth') {
                $inVals   = $floatCol($reportData, 'Entrada Prom (Mbps)');
                $outVals  = $floatCol($reportData, 'Salida Prom (Mbps)');
                $inPicos  = $floatCol($reportData, 'Entrada Pico (Mbps)');
                $outPicos = $floatCol($reportData, 'Salida Pico (Mbps)');
                $inGb     = $floatCol($reportData, 'Total In (GB)');
                $outGb    = $floatCol($reportData, 'Total Out (GB)');
                $summary = [
                    'type'          => 'bandwidth',
                    'avg_in'        => count($inVals)   ? round(array_sum($inVals)  / count($inVals),  2) : null,
                    'max_in'        => count($inPicos)  ? (float) max($inPicos)  : null,
                    'avg_out'       => count($outVals)  ? round(array_sum($outVals) / count($outVals), 2) : null,
                    'max_out'       => count($outPicos) ? (float) max($outPicos) : null,
                    'total_in_gb'   => count($inGb)     ? round(array_sum($inGb),  1) : null,
                    'total_out_gb'  => count($outGb)    ? round(array_sum($outGb), 1) : null,
                    'chart_labels'  => array_column($reportData, 'Fecha'),
                    'chart_in'      => array_map(fn ($v) => $v === '' || $v === null ? null : (float) $v, array_column($reportData, 'Entrada Prom (Mbps)')),
                    'chart_out'     => array_map(fn ($v) => $v === '' || $v === null ? null : (float) $v, array_column($reportData, 'Salida Prom (Mbps)')),
                    'contract_bandwidth' => $contractBandwidth > 0 ? (int) $contractBandwidth : 0,
                ];
            } elseif ($reportType === 'packets') {
                $eIn  = $floatCol($reportData, 'Errores In');
                $eOut = $floatCol($reportData, 'Errores Out');
                $dIn  = $floatCol($reportData, 'Descartados In');
                $dOut = $floatCol($reportData, 'Descartados Out');
                $peakDay = null;
                $peakVal = -1;
                foreach ($reportData as $row) {
                    $total = ((float)($row['Errores In'] ?: 0)) + ((float)($row['Errores Out'] ?: 0))
                           + ((float)($row['Descartados In'] ?: 0)) + ((float)($row['Descartados Out'] ?: 0));
                    if ($total > $peakVal) { $peakVal = $total; $peakDay = $row['Fecha']; }
                }
                $summary = [
                    'type'          => 'packets',
                    'total_err_in'  => count($eIn)  ? (int) round(array_sum($eIn))  : null,
                    'total_err_out' => count($eOut) ? (int) round(array_sum($eOut)) : null,
                    'total_dis_in'  => count($dIn)  ? (int) round(array_sum($dIn))  : null,
                    'total_dis_out' => count($dOut) ? (int) round(array_sum($dOut)) : null,
                    'peak_day'      => $peakVal > 0 ? $peakDay : null,
                    'chart_labels'  => array_column($reportData, 'Fecha'),
                    'chart_err_in'  => array_map(fn ($v) => $v === '' ? null : (float) $v, array_column($reportData, 'Errores In')),
                    'chart_err_out' => array_map(fn ($v) => $v === '' ? null : (float) $v, array_column($reportData, 'Errores Out')),
                ];
            } elseif ($reportType === 'resources') {
                $cpuVals  = $floatCol($reportData, 'CPU Promedio (%)');
                $memVals  = $floatCol($reportData, 'Mem Uso (%)');
                $daysHigh = count(array_filter($cpuVals, fn ($v) => $v > 80));
                $daysHighMem = count(array_filter($memVals, fn ($v) => $v > 80));
                $summary = [
                    'type'          => 'resources',
                    'avg_cpu'       => count($cpuVals) ? round(array_sum($cpuVals) / count($cpuVals), 2) : null,
                    'max_cpu'       => count($cpuVals) ? (float) max($cpuVals) : null,
                    'avg_mem'       => count($memVals) ? round(array_sum($memVals) / count($memVals), 2) : null,
                    'max_mem'       => count($memVals) ? (float) max($memVals) : null,
                    'days_high_cpu' => $daysHigh,
                    'days_high_mem' => $daysHighMem,
                    'chart_labels'  => array_column($reportData, 'Fecha'),
                    'chart_cpu'     => array_map(fn ($v) => $v === '' ? null : (float) $v, array_column($reportData, 'CPU Promedio (%)')),
                    'chart_mem'     => array_map(fn ($v) => $v === '' ? null : (float) $v, array_column($reportData, 'Mem Uso (%)')),
                ];
            } elseif ($reportType === 'availability' && ! empty($reportData)) {
                $row         = $reportData[0];
                $avail       = (float) ($row['Disponibilidad (%)'] ?? 0);
                $downSeconds = (int) ($this->availabilityMetrics['down_seconds'] ?? 0);
                $hrsDown     = $downSeconds / self::STEP_HOUR;

                $hrsTotal  = (float) ($row['Hrs Totales'] ?? 0);
                $periodDays = $hrsTotal > 0 ? $hrsTotal / 24.0 : max(1, ($endTs - $startTs) / 86400);
                $slaMonths = $this->slaMonths($period, $periodDays);

                // Usar SLA configurado por dispositivo si existe; si no, el hardcoded (99.9%)
                if ($deviceSla > 0) {
                    $slaTargetPct     = $deviceSla;
                    $downtimeRatio    = (100.0 - $deviceSla) / 100.0;
                    $slaThresholdMins = $downtimeRatio * 30 * 24 * 60 * $slaMonths;
                } else {
                    $slaTargetPct     = round(100.0 - (self::SLA_DOWNTIME_MINUTES_PER_MONTH / (30 * 24 * 60)) * 100, 3);
                    $slaThresholdMins = self::SLA_DOWNTIME_MINUTES_PER_MONTH * $slaMonths;
                }
                $slaThresholdHrs = $slaThresholdMins / 60.0;

                $slaOk = $this->compliesWithSla($downSeconds, $slaThresholdMins);
                $sla   = $slaOk
                    ? ['label' => 'Cumple SLA', 'class' => 'success']
                    : ['label' => 'Bajo SLA',   'class' => 'danger'];

                // Construir gráfica diaria de disponibilidad
                $dailyLabels = [];
                $dailyAvail  = [];
                $cursor = $startTs;
                $mergedIntervals = $this->getMergedIntervals();
                while ($cursor < $endTs) {
                    $dayEnd = min($cursor + self::STEP_DAY, $endTs);
                    $dayDownSecs = 0;
                    foreach ($mergedIntervals as [$iStart, $iEnd]) {
                        $overlap = min($iEnd, $dayEnd) - max($iStart, $cursor);
                        if ($overlap > 0) {
                            $dayDownSecs += $overlap;
                        }
                    }
                    $dayTotal = $dayEnd - $cursor;
                    $dailyLabels[] = date('Y-m-d', $cursor);
                    $dailyAvail[]  = $dayTotal > 0 ? round((($dayTotal - $dayDownSecs) / $dayTotal) * 100, 4) : 100.0;
                    $cursor = $dayEnd;
                }

                $summary = [
                    'type'                => 'availability',
                    'avail_pct'           => $avail,
                    'hrs_up'              => (float) ($row['Hrs Activo'] ?? 0),
                    'hrs_down'            => $hrsDown,
                    'n_outages'           => (int)   ($row['N Caidas']  ?? 0),
                    'sla'                 => $sla,
                    'sla_target_pct'      => $slaTargetPct,
                    'sla_threshold_hrs'   => round($slaThresholdHrs, 4),
                    'sla_threshold_mins'  => round($slaThresholdMins, 1),
                    'sla_months'          => $slaMonths,
                    'period_days'         => round($periodDays, 1),
                    'chart_labels'        => $dailyLabels,
                    'chart_avail'         => $dailyAvail,
                ];
            }
        }

        $baseParams = [
            'report_type' => $reportType,
            'device_id' => $deviceId,
            'port_id' => $portId,
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'include_last_day' => $includeLastDay ? '1' : '0',
        ];

        $recentAudits = auth()->user()?->can('admin') ? $this->getRecentAudits(20) : [];

        return [
            'title' => (string) ($settings['page_title'] ?? 'Reportes'),
            'subtitle' => (string) ($settings['page_subtitle'] ?? 'Visualizacion profesional de desempeno y disponibilidad para toma de decisiones.'),
            'chart_type' => (string) ($settings['chart_type'] ?? 'line'),
            'report_type' => $reportType,
            'report_labels' => $reportLabels,
            'period' => $period,
            'period_labels' => $periodLabels,
            'device_id' => $deviceId,
            'port_id' => $portId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'include_last_day' => $includeLastDay,
            'today' => $today,
            'all_devices' => $allDevices,
            'all_ports' => $allPorts,
            'device' => $device,
            'port' => $port,
            'error_message' => $errorMessage,
            'report_data' => $reportData,
            'availability_events' => $this->availabilityEvents,
            'summary' => $summary,
            'recent_audits' => $recentAudits,
            'export_csv_url' => url('plugin/Reports') . '?' . http_build_query(array_merge($baseParams, ['action' => 'export_csv'])),
            'export_excel_url' => url('plugin/Reports') . '?' . http_build_query(array_merge($baseParams, ['action' => 'export_excel'])),
            'export_pdf_url' => url('plugin/Reports') . '?' . http_build_query(array_merge($baseParams, ['action' => 'export_pdf'])),
            'contract_bandwidth' => $contractBandwidth,
        ];
    }

    private function recordAudit(
        string $actionType,
        string $reportType,
        string $period,
        int $deviceId,
        string $deviceName,
        int $portId,
        string $dateFrom,
        string $dateTo,
        int $rowsCount
    ): void {
        try {
            $user = auth()->user();

            $roleName = 'user';
            if ($user && $user->can('admin')) {
                $roleName = 'admin';
            } elseif ($user && $user->can('global-read')) {
                $roleName = 'global-read';
            }

            $record = [
                'created_at' => date('Y-m-d H:i:s'),
                'username' => $user?->username ?? 'unknown',
                'role_name' => $roleName,
                'action_type' => $actionType,
                'report_type' => $reportType,
                'device_id' => $deviceId > 0 ? $deviceId : null,
                'device_name' => $deviceName !== '' ? $deviceName : null,
                'port_id' => $portId > 0 ? $portId : null,
                'period_name' => $period,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'rows_count' => max(0, $rowsCount),
                'ip_address' => (string) request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 512),
            ];

            $this->appendAuditLog($record);
        } catch (\Throwable $e) {
            // No bloquear exportaciones por fallos de auditoria
        }
    }

    private function getRecentAudits(int $limit = 20): array
    {
        try {
            $file = $this->auditLogPath();
            if (! is_readable($file)) {
                return [];
            }

            $lines = @file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (! is_array($lines) || empty($lines)) {
                return [];
            }

            $events = [];
            $id = 0;
            for ($i = count($lines) - 1; $i >= 0 && count($events) < $limit; $i--) {
                $row = json_decode((string) $lines[$i], true);
                if (! is_array($row)) {
                    continue;
                }
                $id++;
                $events[] = [
                    'id' => $id,
                    'line_index' => $i,
                    'created_at' => (string) ($row['created_at'] ?? ''),
                    'username' => (string) ($row['username'] ?? 'unknown'),
                    'role_name' => (string) ($row['role_name'] ?? 'user'),
                    'action_type' => (string) ($row['action_type'] ?? ''),
                    'report_type' => (string) ($row['report_type'] ?? ''),
                    'device_name' => (string) ($row['device_name'] ?? ''),
                    'period_name' => (string) ($row['period_name'] ?? ''),
                    'date_from' => (string) ($row['date_from'] ?? ''),
                    'date_to' => (string) ($row['date_to'] ?? ''),
                    'rows_count' => (int) ($row['rows_count'] ?? 0),
                    'ip_address' => (string) ($row['ip_address'] ?? ''),
                ];
            }

            return $events;
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function auditLogPath(): string
    {
        return storage_path('logs/enlaces_report_audit.log');
    }

    public function handleAuditAction(string $action, int $lineIndex = -1): void
    {
        $user = auth()->user();
        if (! $user || ! $user->can('admin')) {
            abort(403, 'No autorizado.');
        }

        if ($action === 'clear_log') {
            $this->clearAuditLog();
        } elseif ($action === 'delete_log_entry' && $lineIndex >= 0) {
            $this->deleteAuditLogEntry($lineIndex);
        }
    }

    private function deleteAuditLogEntry(int $lineIndex): void
    {
        try {
            $file = $this->auditLogPath();
            $handle = @fopen($file, 'c+');
            if ($handle === false) {
                return;
            }
            if (! flock($handle, LOCK_EX)) {
                fclose($handle);

                return;
            }

            $contents = stream_get_contents($handle);
            $lines = $contents === false ? [] : preg_split('/\R/', rtrim($contents));
            if (! is_array($lines) || ! array_key_exists($lineIndex, $lines)) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return;
            }

            array_splice($lines, $lineIndex, 1);
            $content = empty($lines) ? '' : implode(PHP_EOL, $lines) . PHP_EOL;
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, $content);
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);
        } catch (\Throwable $e) {
            // No bloquear por errores de IO
        }
    }

    private function clearAuditLog(): void
    {
        try {
            $file = $this->auditLogPath();
            $handle = @fopen($file, 'c+');
            if ($handle !== false) {
                if (flock($handle, LOCK_EX)) {
                    ftruncate($handle, 0);
                    fflush($handle);
                    flock($handle, LOCK_UN);
                }
                fclose($handle);
            }
        } catch (\Throwable $e) {
            // No bloquear por errores de IO
        }
    }

    private function appendAuditLog(array $record): void
    {
        $file = $this->auditLogPath();
        $dir = dirname($file);
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents($file, json_encode($record, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    private function validateDate(string $date, string $fallback): string
    {
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        if (! $dt || $dt->format('Y-m-d') !== $date || $dt->getTimestamp() < 0) {
            return $fallback;
        }

        return $date;
    }

    private function getDevices(): array
    {
        return \App\Models\Device::hasAccess(auth()->user())
            ->select('device_id', 'hostname', 'sysName', 'display', 'ip', 'status')
            ->orderBy('hostname', 'ASC')
            ->toBase()
            ->get()
            ->toArray();
    }

    private function getDevice(int $deviceId): ?object
    {
        return \App\Models\Device::hasAccess(auth()->user())
            ->select('device_id', 'hostname', 'sysName', 'ip')
            ->where('device_id', $deviceId)
            ->toBase()
            ->first();
    }

    private function getPorts(int $deviceId): array
    {
        return DB::select(
            'SELECT port_id, device_id, ifName, ifAlias, ifOperStatus FROM ports WHERE device_id = ? AND deleted = 0 AND disabled = 0 AND `ignore` = 0 ORDER BY ifIndex ASC',
            [$deviceId]
        );
    }

    private function getPort(int $portId, int $deviceId): ?object
    {
        return DB::selectOne(
            'SELECT * FROM ports WHERE port_id = ? AND device_id = ? AND deleted = 0 AND disabled = 0 AND `ignore` = 0',
            [$portId, $deviceId]
        );
    }

    private function dataBandwidth(array $device, array $port, int $startTs, int $endTs): array
    {
        $rrd = $this->resolvePortRrdPath($device, $port);
        if ($rrd === null) {
            return [];
        }

        $contractBw = $this->getContractBandwidth((int) $port['port_id'] ?? 0);

        // AVERAGE para promedios y totales
        $raw = $this->rrdFetch($rrd, ['INOCTETS', 'OUTOCTETS'], $startTs, $endTs, self::STEP_HOUR, 'AVERAGE');
        $daily = $this->aggregateDaily($raw, ['INOCTETS', 'OUTOCTETS']);

        // MAX para picos reales (igual a lo que muestra el gráfico RRD)
        $rawMax = $this->rrdFetch($rrd, ['INOCTETS', 'OUTOCTETS'], $startTs, $endTs, self::STEP_HOUR, 'MAX');
        $dailyMax = $this->aggregateMax($rawMax, ['INOCTETS', 'OUTOCTETS']);
        // Indexar dailyMax por fecha para join rápido
        $maxByDate = [];
        foreach ($dailyMax as $r) {
            $maxByDate[date('Y-m-d', $r['timestamp'])] = $r;
        }

        $rows = [];
        foreach ($daily as $r) {
            if ($r['INOCTETS'] === null && $r['OUTOCTETS'] === null) {
                continue;
            }

            $fecha = date('Y-m-d', $r['timestamp']);
            $mxRow = $maxByDate[$fecha] ?? null;

            $rows[] = [
                'Fecha'                     => $fecha,
                'Entrada Prom (Mbps)'       => $r['INOCTETS'] !== null ? round($r['INOCTETS'] * 8 / 1000000, 4) : '',
                'Salida Prom (Mbps)'        => $r['OUTOCTETS'] !== null ? round($r['OUTOCTETS'] * 8 / 1000000, 4) : '',
                'Entrada Pico (Mbps)'       => ($mxRow['INOCTETS'] ?? null) !== null ? round($mxRow['INOCTETS'] * 8 / 1000000, 4) : '',
                'Salida Pico (Mbps)'        => ($mxRow['OUTOCTETS'] ?? null) !== null ? round($mxRow['OUTOCTETS'] * 8 / 1000000, 4) : '',
                'Total In (GB)'             => $r['INOCTETS'] !== null ? round($r['INOCTETS'] * $r['INOCTETS_seconds'] / 1073741824, 3) : '',
                'Total Out (GB)'            => $r['OUTOCTETS'] !== null ? round($r['OUTOCTETS'] * $r['OUTOCTETS_seconds'] / 1073741824, 3) : '',
                'Umbral Contratado (Mbps)'  => $contractBw > 0 ? $contractBw : '',
            ];
        }

        return $rows;
    }

    private function dataPackets(array $device, array $port, int $startTs, int $endTs): array
    {
        $rrd = $this->resolvePortRrdPath($device, $port);
        if ($rrd === null) {
            return [];
        }

        $raw = $this->rrdFetch($rrd, ['INERRORS', 'OUTERRORS', 'INDISCARDS', 'OUTDISCARDS'], $startTs, $endTs, self::STEP_HOUR);
        $daily = $this->aggregateDaily($raw, ['INERRORS', 'OUTERRORS', 'INDISCARDS', 'OUTDISCARDS']);

        $rows = [];
        foreach ($daily as $r) {
            $hasData = ($r['INERRORS'] !== null || $r['OUTERRORS'] !== null || $r['INDISCARDS'] !== null || $r['OUTDISCARDS'] !== null);
            if (! $hasData) {
                continue;
            }

            $rows[] = [
                'Fecha' => date('Y-m-d', $r['timestamp']),
                'Errores In' => $r['INERRORS'] !== null ? round($r['INERRORS'] * $r['INERRORS_seconds']) : '',
                'Errores Out' => $r['OUTERRORS'] !== null ? round($r['OUTERRORS'] * $r['OUTERRORS_seconds']) : '',
                'Descartados In' => $r['INDISCARDS'] !== null ? round($r['INDISCARDS'] * $r['INDISCARDS_seconds']) : '',
                'Descartados Out' => $r['OUTDISCARDS'] !== null ? round($r['OUTDISCARDS'] * $r['OUTDISCARDS_seconds']) : '',
            ];
        }

        return $rows;
    }

    private function dataResources(array $device, int $startTs, int $endTs): array
    {
        $rrdBase = $this->rrdDir() . '/' . $device['hostname'];
        $devId = (int) $device['device_id'];

        $procs = DB::select(
            'SELECT processor_type, processor_index FROM processors WHERE device_id = ?',
            [$devId]
        );

        $pools = DB::select(
            'SELECT mempool_type, mempool_index FROM mempools WHERE device_id = ?',
            [$devId]
        );

        $cpuByDate = [];
        foreach ($procs as $p) {
            $rrd = $this->resolveTypedRrdPath(
                $rrdBase,
                'processor',
                (string) $p->processor_type,
                (string) $p->processor_index
            );
            if ($rrd === null) {
                continue;
            }

            $dailyCpu = $this->aggregateDaily(
                $this->rrdFetch($rrd, ['usage'], $startTs, $endTs, self::STEP_HOUR),
                ['usage']
            );

            foreach ($dailyCpu as $r) {
                if ($r['usage'] === null) {
                    continue;
                }
                $date = date('Y-m-d', $r['timestamp']);
                $cpuByDate[$date][] = $r['usage'];
            }
        }

        $memByDate = [];
        foreach ($pools as $pool) {
            $rrd = $this->resolveTypedRrdPath(
                $rrdBase,
                'mempool',
                (string) $pool->mempool_type,
                (string) $pool->mempool_index
            );
            if ($rrd === null) {
                continue;
            }

            $dailyMem = $this->aggregateDaily(
                $this->rrdFetch($rrd, ['used', 'free'], $startTs, $endTs, self::STEP_HOUR),
                ['used', 'free']
            );

            foreach ($dailyMem as $r) {
                $date = date('Y-m-d', $r['timestamp']);
                $hasUsed = $r['used'] !== null;
                $hasFree = $r['free'] !== null;
                if (! $hasUsed && ! $hasFree) {
                    continue;
                }

                if (! isset($memByDate[$date])) {
                    $memByDate[$date] = [
                        'used_sum' => 0.0,
                        'used_cnt' => 0,
                        'free_sum' => 0.0,
                        'free_cnt' => 0,
                    ];
                }
                if ($r['used'] !== null) {
                    $memByDate[$date]['used_sum'] += (float) $r['used'];
                    $memByDate[$date]['used_cnt']++;
                }
                if ($r['free'] !== null) {
                    $memByDate[$date]['free_sum'] += (float) $r['free'];
                    $memByDate[$date]['free_cnt']++;
                }
            }
        }

        $dates = array_unique(array_merge(array_keys($cpuByDate), array_keys($memByDate)));
        sort($dates);

        $rows = [];
        foreach ($dates as $date) {
            $cpuVals = $cpuByDate[$date] ?? [];
            $cpuAvg = count($cpuVals) > 0 ? round(array_sum($cpuVals) / count($cpuVals), 2) : null;

            $usedCnt = (int) ($memByDate[$date]['used_cnt'] ?? 0);
            $freeCnt = (int) ($memByDate[$date]['free_cnt'] ?? 0);
            $used = $usedCnt > 0 ? ($memByDate[$date]['used_sum'] / $usedCnt) : null;
            $free = $freeCnt > 0 ? ($memByDate[$date]['free_sum'] / $freeCnt) : null;
            $total = ($used !== null && $free !== null) ? ($used + $free) : null;
            $memPct = ($total && $total > 0) ? round($used / $total * 100, 2) : null;

            $rows[] = [
                'Fecha' => $date,
                'CPU Promedio (%)' => $cpuAvg ?? '',
                'Mem Usada (MB)' => $used !== null ? round($used / 1048576, 2) : '',
                'Mem Libre (MB)' => $free !== null ? round($free / 1048576, 2) : '',
                'Mem Total (MB)' => $total !== null ? round($total / 1048576, 2) : '',
                'Mem Uso (%)' => $memPct ?? '',
            ];
        }

        return $rows;
    }

    private function dataAvailability(array $device, int $startTs, int $endTs): array
    {
        $devId = (int) $device['device_id'];
        $totalSecs = $endTs - $startTs;

        static $upColumnCache = null;
        if ($upColumnCache === null) {
            $hasCameBack = DB::selectOne(
                'SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['device_outages', 'came_back']
            );
            $upColumnCache = ((int) ($hasCameBack->cnt ?? 0) > 0) ? 'came_back' : 'up_again';
        }
        $upColumn = $upColumnCache;

        $outages = DB::select(
            sprintf(
                'SELECT going_down, %1$s AS outage_up FROM device_outages WHERE device_id = ? AND going_down < ? AND (%1$s > ? OR %1$s IS NULL) ORDER BY going_down ASC',
                $upColumn
            ),
            [$devId, $endTs, $startTs]
        );

        $events = [];
        $effectiveIntervals = [];
        foreach ($outages as $outage) {
            $outageStart = (int) $outage->going_down;
            $outageEnd = $outage->outage_up ? (int) $outage->outage_up : null;
            $effectiveStart = max($outageStart, $startTs);
            $effectiveEnd = min($outageEnd ?? $endTs, $endTs);
            if ($effectiveEnd <= $effectiveStart) {
                continue;
            }

            $events[] = [
                'start' => $outageStart,
                'end' => $outageEnd,
                'duration_seconds' => $effectiveEnd - $effectiveStart,
            ];

            $effectiveIntervals[] = [$effectiveStart, $effectiveEnd];
        }

        $downSecs = $this->mergedDurationSeconds($effectiveIntervals);
        $this->mergedIntervalsCache = $this->buildMergedIntervals($effectiveIntervals);

        $upSecs = max(0, $totalSecs - $downSecs);
        $avail = $totalSecs > 0 ? round(($upSecs / $totalSecs) * 100, 4) : 100.0;
        $this->availabilityMetrics = [
            'down_seconds' => $downSecs,
            'outage_count' => count($events),
        ];
        $this->availabilityEvents = array_map(fn (array $event): array => [
            'Inicio Caida' => date('Y-m-d H:i:s', $event['start']),
            'Fin Caida' => $event['end'] === null ? 'En curso' : date('Y-m-d H:i:s', $event['end']),
            'Duracion Caida (min)' => round($event['duration_seconds'] / 60, 1),
        ], $events);

        $baseRow = [
            'Dispositivo' => $device['hostname'],
            'Periodo Desde' => date('Y-m-d H:i:s', $startTs),
            'Periodo Hasta' => date('Y-m-d H:i:s', $endTs),
        ];
        $summaryRow = [
            'Hrs Totales' => round($totalSecs / 3600, 2),
            'Hrs Activo' => round($upSecs / 3600, 2),
            'Hrs Caido' => round($downSecs / 3600, 2),
            'Disponibilidad (%)' => $avail,
            'N Caidas' => count($events),
        ];

        if (empty($events)) {
            return [[
                ...$baseRow,
                'Inicio Caida' => '',
                'Fin Caida' => '',
                'Duracion Caida (min)' => 0,
                ...$summaryRow,
            ]];
        }

        return array_map(fn (array $event): array => [
            ...$baseRow,
            ...$event,
            ...$summaryRow,
        ], $this->availabilityEvents);
    }

    private function exportCsv(array $reportData, string $filename): void
    {
        $headers = array_keys($reportData[0]);
        $this->sendHeaders([
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);

        $stream = fopen('php://output', 'w');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, $headers);
        foreach ($reportData as $row) {
            fputcsv($stream, array_map($this->sanitizeSpreadsheetCell(...), array_values($row)));
        }
        fclose($stream);
        exit;
    }

    private function exportExcel(array $reportData, string $filename, string $sheetTitle): void
    {
        $writer = new SimpleXlsxWriter($sheetTitle);
        $writer->setHeaders(array_keys($reportData[0]));
        $writer->addRows($reportData);
        $content = $writer->toString();

        $this->sendDownload($content, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Content-Length' => (string) strlen($content),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function exportPdf(array $reportData, string $filename, string $title, array $meta = []): void
    {
        $headers = array_keys(reset($reportData) ?: []);
        $rows    = array_map(fn ($r) => array_values($r), $reportData);

        $writer  = new SimplePdfWriter();
        $content = $writer->fromTable($title, $headers, $rows, $meta);

        $this->sendDownload($content, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . addslashes($filename) . '"',
            'Content-Length'      => (string) strlen($content),
            'Cache-Control'       => 'no-cache, no-store, must-revalidate',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
    }

    private function sendDownload(string $content, array $headers): void
    {
        $this->sendHeaders($headers);

        echo $content;
        exit;
    }

    private function sendHeaders(array $headers): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code(200);
        foreach ($headers as $name => $value) {
            header($name . ': ' . $value);
        }
    }

    private function sanitizeSpreadsheetCell(mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        return preg_match('/^[\x00-\x20]*[=+\-@]/', $value) === 1 ? "'" . $value : $value;
    }

    private function slaMonths(string $period, float $periodDays): int
    {
        return match ($period) {
            'annual' => 12,
            'custom' => max(1, (int) round($periodDays / 30)),
            default => 1,
        };
    }

    private function compliesWithSla(int $downSeconds, float $thresholdMinutes): bool
    {
        return $downSeconds <= (int) round($thresholdMinutes * 60);
    }

    /** Intervalos fusionados de la última llamada a dataAvailability, para la gráfica diaria. */
    private array $mergedIntervalsCache = [];

    private function getMergedIntervals(): array
    {
        return $this->mergedIntervalsCache;
    }

    private function mergedDurationSeconds(array $intervals): int
    {
        return array_sum(array_map(
            fn (array $i): int => $i[1] - $i[0],
            $this->buildMergedIntervals($intervals)
        ));
    }

    private function buildMergedIntervals(array $intervals): array
    {
        if (empty($intervals)) {
            return [];
        }

        usort($intervals, fn (array $left, array $right): int => $left[0] <=> $right[0]);
        $merged = [];
        foreach ($intervals as [$start, $end]) {
            $lastIndex = count($merged) - 1;
            if ($lastIndex >= 0 && $start <= $merged[$lastIndex][1]) {
                $merged[$lastIndex][1] = max($merged[$lastIndex][1], $end);
            } else {
                $merged[] = [$start, $end];
            }
        }

        return $merged;
    }

    private function getDeviceSla(int $deviceId): float
    {
        $attr = DB::selectOne(
            'SELECT attrib_value FROM devices_attribs WHERE attrib_type = ? AND device_id = ? LIMIT 1',
            ['device_sla_target', $deviceId]
        );

        return $attr ? (float) $attr->attrib_value : 0.0;
    }

    private function rrdDir(): string
    {
        return rtrim((string) config('librenms.rrd_dir', '/opt/librenms/rrd'), '/');
    }

    /**
     * Obtiene la velocidad contratada para un puerto desde devices_attribs.
     * @return int velocidad en Mbps (0 si no está configurada)
     */
    private function getContractBandwidth(int $portId): int
    {
        if ($portId <= 0) {
            return 0;
        }

        $attr = DB::selectOne(
            'SELECT attrib_value FROM devices_attribs WHERE attrib_type = ? AND device_id = (SELECT device_id FROM ports WHERE port_id = ?) LIMIT 1',
            ["port_{$portId}_contract_bandwidth", $portId]
        );

        return $attr ? (int) $attr->attrib_value : 0;
    }

    /**
     * Guarda la velocidad contratada para un puerto.
     */
    private function saveContractBandwidth(int $portId, int $bandwidth): void
    {
        if ($portId <= 0) {
            return;
        }

        $port = DB::selectOne('SELECT device_id FROM ports WHERE port_id = ?', [$portId]);
        if (! $port) {
            return;
        }

        $attribType = "port_{$portId}_contract_bandwidth";
        $deviceId = (int) $port->device_id;

        if ($bandwidth > 0) {
            DB::statement(
                'INSERT INTO devices_attribs (device_id, attrib_type, attrib_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE attrib_value = ?',
                [$deviceId, $attribType, (string) $bandwidth, (string) $bandwidth]
            );
        } else {
            DB::statement('DELETE FROM devices_attribs WHERE device_id = ? AND attrib_type = ?', [$deviceId, $attribType]);
        }
    }

    private function resolvePortRrdPath(array $device, array $port): ?string
    {
        $rrdBase = $this->rrdDir() . '/' . (string) $device['hostname'];
        $portId = (string) ($port['port_id'] ?? '');
        if ($portId === '') {
            return null;
        }

        $candidates = [
            sprintf('%s/port-id%s.rrd', $rrdBase, $portId),
            sprintf('%s/port-%s.rrd', $rrdBase, $portId),
        ];

        $ifIndex = (string) ($port['ifIndex'] ?? '');
        if ($ifIndex !== '') {
            $candidates[] = sprintf('%s/port-id%s.rrd', $rrdBase, $ifIndex);
            $candidates[] = sprintf('%s/port-%s.rrd', $rrdBase, $ifIndex);
        }

        foreach ($candidates as $candidate) {
            $resolved = $this->resolveExistingRrdPath($candidate);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    private function resolveTypedRrdPath(string $rrdBase, string $prefix, string $type, string $index): ?string
    {
        if ($index === '') {
            return null;
        }

        $normalizedType = preg_replace('/[^A-Za-z0-9_-]/', '-', $type) ?: $type;

        $candidates = [
            sprintf('%s/%s-%s-%s.rrd', $rrdBase, $prefix, $normalizedType, $index),
            sprintf('%s/%s-*-%s.rrd', $rrdBase, $prefix, $index),
        ];

        foreach ($candidates as $pattern) {
            if (strpos($pattern, '*') !== false) {
                foreach (glob($pattern) ?: [] as $match) {
                    $resolved = $this->resolveExistingRrdPath($match);
                    if ($resolved !== null) {
                        return $resolved;
                    }
                }
                continue;
            }

            $resolved = $this->resolveExistingRrdPath($pattern);
            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    private function resolveExistingRrdPath(string $rrdFile): ?string
    {
        $rrdDir = $this->rrdDir();
        $realRrd = realpath($rrdFile);
        $realDir = realpath($rrdDir);
        if ($realRrd === false || $realDir === false) {
            return null;
        }

        if (strpos($realRrd, $realDir . DIRECTORY_SEPARATOR) !== 0 || ! is_readable($realRrd) || ! str_ends_with($realRrd, '.rrd')) {
            return null;
        }

        return $realRrd;
    }

    private function aggregateDaily(array $raw, array $dsNames): array
    {
        $bucket = [];

        foreach ($raw as $r) {
            // rrdtool timestamps data at the END of the interval; subtract 1s to assign to the correct day
            $date = date('Y-m-d', max(0, (int) ($r['timestamp'] ?? 0) - 1));
            $sampleSeconds = max(1, (int) ($r['_step'] ?? self::STEP_HOUR));
            if (! isset($bucket[$date])) {
                $bucket[$date] = ['timestamp' => strtotime($date . ' 00:00:00')];
                foreach ($dsNames as $ds) {
                    $bucket[$date][$ds . '_sum'] = 0.0;
                    $bucket[$date][$ds . '_cnt'] = 0;
                    $bucket[$date][$ds . '_seconds'] = 0;
                }
            }

            foreach ($dsNames as $ds) {
                $val = $r[$ds] ?? null;
                if ($val !== null) {
                    $bucket[$date][$ds . '_sum'] += (float) $val;
                    $bucket[$date][$ds . '_cnt']++;
                    $bucket[$date][$ds . '_seconds'] += $sampleSeconds;
                }
            }
        }

        ksort($bucket);
        $out = [];
        foreach ($bucket as $row) {
            $daily = ['timestamp' => $row['timestamp']];
            foreach ($dsNames as $ds) {
                $cnt = (int) $row[$ds . '_cnt'];
                $daily[$ds] = $cnt > 0 ? ($row[$ds . '_sum'] / $cnt) : null;
                $daily[$ds . '_seconds'] = (int) $row[$ds . '_seconds'];
            }
            $out[] = $daily;
        }

        return $out;
    }

    /**
     * Agrega datos horarios por día retornando el valor MÁXIMO de cada DS por día.
     * Usado para calcular picos reales (equivalente al MAX que muestra el gráfico RRD).
     */
    private function aggregateMax(array $raw, array $dsNames): array
    {
        $bucket = [];

        foreach ($raw as $r) {
            $date = date('Y-m-d', max(0, (int) ($r['timestamp'] ?? 0) - 1));
            if (! isset($bucket[$date])) {
                $bucket[$date] = ['timestamp' => strtotime($date . ' 00:00:00')];
                foreach ($dsNames as $ds) {
                    $bucket[$date][$ds] = null;
                }
            }

            foreach ($dsNames as $ds) {
                $val = $r[$ds] ?? null;
                if ($val !== null) {
                    $current = $bucket[$date][$ds];
                    if ($current === null || (float) $val > $current) {
                        $bucket[$date][$ds] = (float) $val;
                    }
                }
            }
        }

        ksort($bucket);
        $out = [];
        foreach ($bucket as $row) {
            $daily = ['timestamp' => $row['timestamp']];
            foreach ($dsNames as $ds) {
                $daily[$ds] = $row[$ds];
            }
            $out[] = $daily;
        }

        return $out;
    }


    private function rrdFetch(string $rrdFile, array $dsNames, int $startTs, int $endTs, int $resolution = self::STEP_DAY, string $cf = 'AVERAGE'): array
    {
        $realRrd = $this->resolveExistingRrdPath($rrdFile);
        if ($realRrd === null) {
            return [];
        }

        // Validar CF para evitar inyección de comandos
        $cf = in_array(strtoupper($cf), ['AVERAGE', 'MAX', 'MIN', 'LAST'], true)
            ? strtoupper($cf) : 'AVERAGE';

        $args = [
            'rrdtool', 'xport',
            '--start', (string) $startTs,
            '--end', (string) $endTs,
            '--step', (string) $resolution,
            '--json',
        ];

        // Agregar DEF para cada datasource con el CF solicitado
        foreach ($dsNames as $ds) {
            $args[] = "DEF:$ds=$realRrd:$ds:$cf";
        }

        // Agregar XPORT para cada datasource
        foreach ($dsNames as $ds) {
            $args[] = "XPORT:$ds:$ds";
        }

        $proc = proc_open($args, [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (! is_resource($proc)) {
            return [];
        }

        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($proc);

        if ($exitCode !== 0 || ! $output || ! empty($stderr)) {
            return [];
        }

        $json = json_decode($output, true);
        if (! is_array($json) || ! isset($json['data']) || ! is_array($json['data'])) {
            return [];
        }

        $metaStart = (int) ($json['meta']['start'] ?? $startTs);
        $metaStep = (int) ($json['meta']['step'] ?? $resolution);
        if ($metaStep <= 0) {
            $metaStep = $resolution > 0 ? $resolution : self::STEP_DAY;
        }

        $data = [];
        foreach ($json['data'] as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $ts = $metaStart + ((int) $index * $metaStep);
            $parsed = ['timestamp' => $ts, '_step' => $metaStep];

            for ($i = 0; $i < count($dsNames); $i++) {
                $val = $row[$i] ?? null;

                if ($val === null) {
                    $parsed[$dsNames[$i]] = null;
                    continue;
                }

                if (is_string($val)) {
                    $raw = strtolower(trim($val));
                    if ($raw === 'nan' || $raw === '-nan' || $raw === 'inf' || $raw === '+inf' || $raw === '-inf') {
                        $parsed[$dsNames[$i]] = null;
                        continue;
                    }
                }

                if (! is_numeric($val)) {
                    $parsed[$dsNames[$i]] = null;
                    continue;
                }

                $num = (float) $val;
                // Compatibilidad: algunos exportadores usan sentinelas extremos para null
                $parsed[$dsNames[$i]] = (is_finite($num) && $num > -1.7e308) ? $num : null;
            }

            $data[] = $parsed;
        }

        return $data;
    }
}
