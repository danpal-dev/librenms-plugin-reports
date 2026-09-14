<?php

declare(strict_types=1);

namespace App\Plugins\Reports\Support;

class SlaPolicy
{
    public const CONTRACT_TARGET_PERCENT = 99.9;
    public const CONTRACT_DOWNTIME_MINUTES = 43.0;

    public static function monthlyDowntimeMinutes(float $targetPercent): float
    {
        if (abs($targetPercent - self::CONTRACT_TARGET_PERCENT) < 0.00001) {
            return self::CONTRACT_DOWNTIME_MINUTES;
        }

        return round((100.0 - $targetPercent) / 100.0 * 30 * 24 * 60, 1);
    }
}