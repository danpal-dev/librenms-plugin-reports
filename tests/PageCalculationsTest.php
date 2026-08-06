<?php

declare(strict_types=1);

namespace App\Plugins\Reports\Tests;

use App\Plugins\Reports\Page;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class PageCalculationsTest extends TestCase
{
    private Page $page;

    protected function setUp(): void
    {
        $this->page = new Page();
    }

    public function testExactMonthlySlaBoundaryComplies(): void
    {
        $this->assertTrue($this->invoke('compliesWithSla', [43 * 60, 43.0]));
        $this->assertFalse($this->invoke('compliesWithSla', [(43 * 60) + 1, 43.0]));
    }

    public function testSlaMonthsMatchReportPeriod(): void
    {
        $this->assertSame(1, $this->invoke('slaMonths', ['monthly', 29.0]));
        $this->assertSame(12, $this->invoke('slaMonths', ['annual', 365.0]));
        $this->assertSame(2, $this->invoke('slaMonths', ['custom', 45.0]));
    }

    public function testOverlappingIntervalsAreCountedOnce(): void
    {
        $duration = $this->invoke('mergedDurationSeconds', [[
            [0, 100],
            [50, 150],
            [200, 250],
        ]]);

        $this->assertSame(200, $duration);
    }

    public function testPartialDayUsesObservedSampleDuration(): void
    {
        $start = strtotime('2026-07-25 01:00:00');
        $raw = [];
        for ($index = 0; $index < 6; $index++) {
            $raw[] = [
                'timestamp' => $start + ($index * 3600),
                '_step' => 3600,
                'RATE' => 100.0,
            ];
        }

        $daily = $this->invoke('aggregateDaily', [$raw, ['RATE']]);

        $this->assertSame(100.0, $daily[0]['RATE']);
        $this->assertSame(21600, $daily[0]['RATE_seconds']);
    }

    public function testCsvFormulaPrefixesAreNeutralized(): void
    {
        $this->assertSame("'=1+1", $this->invoke('sanitizeSpreadsheetCell', ['=1+1']));
        $this->assertSame("'@SUM(A1)", $this->invoke('sanitizeSpreadsheetCell', ['@SUM(A1)']));
        $this->assertSame("'\t=1+1", $this->invoke('sanitizeSpreadsheetCell', ["\t=1+1"]));
        $this->assertSame('normal', $this->invoke('sanitizeSpreadsheetCell', ['normal']));
        $this->assertSame(43, $this->invoke('sanitizeSpreadsheetCell', [43]));
    }

    private function invoke(string $methodName, array $arguments): mixed
    {
        $method = new ReflectionMethod($this->page, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->page, $arguments);
    }
}