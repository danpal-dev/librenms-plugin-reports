<?php

declare(strict_types=1);

namespace App\Plugins\Reports\Support;

/**
 * Pure-PHP PDF generator — A4 portrait, no external dependencies.
 * Uses PDF drawing operators: rectangles, lines, text (Helvetica / Helvetica-Bold).
 */
class SimplePdfWriter
{
    private const PAGE_W  = 595;
    private const PAGE_H  = 842;
    private const M_LEFT  = 30;
    private const M_RIGHT = 30;
    private const M_TOP   = 30;
    private const M_BOT   = 36;

    private const FS_TITLE = 15;
    private const FS_META  = 9;
    private const FS_HEAD  = 9;
    private const FS_ROW   = 8;

    private const LH_TITLE = 32;
    private const LH_META  = 13;
    private const LH_HEAD  = 17;
    private const LH_ROW   = 13;

    private const COL_MAX_CHARS = 28;

    // ── Public API ────────────────────────────────────────────────────────────

    public function fromTable(string $title, array $headers, array $rows, array $meta = []): string
    {
        $usableW   = self::PAGE_W - self::M_LEFT - self::M_RIGHT;
        $colWidths = $this->calcColumnWidths($headers, $rows, $usableW);

        $pages   = [];
        $current = [];
        $y       = self::PAGE_H - self::M_TOP;

        // ── Title bar ──────────────────────────────────────────────────────
        $current = array_merge($current, $this->titleBar($title, $usableW, $y));
        $y -= self::LH_TITLE + 8;

        // ── Meta block ────────────────────────────────────────────────────
        if (! empty($meta)) {
            foreach ($meta as $k => $v) {
                $current[] = $this->textCmd(
                    $k . ': ' . $v,
                    self::M_LEFT, $y - self::LH_META + 4,
                    self::FS_META, false, '0.35 0.35 0.35'
                );
                $y -= self::LH_META;
            }
            $y -= 6;
        }

        // ── Separator ─────────────────────────────────────────────────────
        $current[] = $this->hline($y, $usableW, '0.75 0.75 0.75');
        $y -= 5;

        // ── Table header ──────────────────────────────────────────────────
        $current = array_merge($current, $this->tableHeader($headers, $colWidths, $y));
        $y -= self::LH_HEAD;

        // ── Data rows ─────────────────────────────────────────────────────
        $even = false;
        foreach ($rows as $row) {
            if ($y - self::LH_ROW < self::M_BOT + 10) {
                $pages[]  = $this->addFooter($current, count($pages) + 1);
                $current  = [];
                $y        = self::PAGE_H - self::M_TOP;
                $current  = array_merge($current, $this->tableHeader($headers, $colWidths, $y));
                $y       -= self::LH_HEAD;
                $even     = false;
            }
            $current = array_merge($current, $this->tableRow(array_values($row), $colWidths, $y, $even));
            $y -= self::LH_ROW;
            $even = ! $even;
        }

        $pages[] = $this->addFooter($current, count($pages) + 1);

        return $this->buildPdf($pages);
    }

    // ── Drawing helpers ──────────────────────────────────────────────────────

    private function titleBar(string $title, float $w, float $y): array
    {
        return [
            $this->rectCmd(self::M_LEFT, $y - self::LH_TITLE, $w, self::LH_TITLE, '0.10 0.22 0.45'),
            $this->textCmd($title, self::M_LEFT + 10, $y - self::LH_TITLE + 10, self::FS_TITLE, true, '1 1 1'),
        ];
    }

    private function tableHeader(array $headers, array $colWidths, float $y): array
    {
        $cmds   = [];
        $totalW = (float) array_sum($colWidths);
        $cmds[] = $this->rectCmd(self::M_LEFT, $y - self::LH_HEAD, $totalW, self::LH_HEAD, '0.20 0.40 0.70');
        $x = (float) self::M_LEFT;
        foreach ($headers as $i => $h) {
            $cw     = (float) ($colWidths[$i] ?? 40);
            $limit  = max(4, (int) ($cw / 5.0));
            $cmds[] = $this->textCmd($this->trunc((string) $h, $limit), $x + 3, $y - self::LH_HEAD + 5, self::FS_HEAD, true, '1 1 1');
            $x += $cw;
        }
        return $cmds;
    }

    private function tableRow(array $vals, array $colWidths, float $y, bool $even): array
    {
        $cmds   = [];
        $totalW = (float) array_sum($colWidths);
        $bg     = $even ? '0.95 0.97 1.00' : '1 1 1';
        $cmds[] = $this->rectCmd(self::M_LEFT, $y - self::LH_ROW, $totalW, self::LH_ROW, $bg);
        $cmds[] = $this->hline($y - self::LH_ROW, $totalW, '0.88 0.88 0.88');
        $x = (float) self::M_LEFT;
        foreach ($colWidths as $i => $cw) {
            $val    = isset($vals[$i]) ? (string) $vals[$i] : '';
            $limit  = max(4, (int) ($cw / 5.0));
            $cmds[] = $this->textCmd($this->trunc($val, $limit), $x + 3, $y - self::LH_ROW + 4, self::FS_ROW, false, '0.10 0.10 0.10');
            $x += (float) $cw;
        }
        return $cmds;
    }

    private function addFooter(array $cmds, int $pageNum): array
    {
        $footerY = self::M_BOT - 10;
        $usableW = self::PAGE_W - self::M_LEFT - self::M_RIGHT;
        $date    = date('d/m/Y H:i');

        $cmds[] = $this->hline($footerY + 10, $usableW, '0.75 0.75 0.75');
        $cmds[] = $this->textCmd("Generado por Moni  |  $date", self::M_LEFT, $footerY, 8, false, '0.55 0.55 0.55');
        $cmds[] = $this->textCmd("Pag. $pageNum", self::PAGE_W - self::M_RIGHT - 30, $footerY, 8, false, '0.55 0.55 0.55');

        return $cmds;
    }

    // ── Low-level PDF operators ──────────────────────────────────────────────

    private function rectCmd(float $x, float $y, float $w, float $h, string $rgb): string
    {
        return sprintf("q %s rg %.2f %.2f %.2f %.2f re f Q\n", $rgb, $x, $y, $w, $h);
    }

    private function hline(float $y, float $w, string $rgb): string
    {
        return sprintf("q %s RG 0.4 w %.2f %.2f m %.2f %.2f l S Q\n",
            $rgb, (float) self::M_LEFT, $y, self::M_LEFT + $w, $y);
    }

    private function textCmd(string $t, float $x, float $y, int $fs, bool $bold, string $rgb): string
    {
        $font = $bold ? '/F2' : '/F1';
        return sprintf("q %s rg BT %s %d Tf %.2f %.2f Td (%s) Tj ET Q\n",
            $rgb, $font, $fs, $x, $y, $this->pdfEscape($t));
    }

    // ── Column width calculation ─────────────────────────────────────────────

    private function calcColumnWidths(array $headers, array $rows, float $totalW): array
    {
        $maxChars = [];
        foreach ($headers as $i => $h) {
            $maxChars[$i] = mb_strlen((string) $h);
        }
        foreach ($rows as $row) {
            foreach (array_values($row) as $i => $v) {
                if (isset($maxChars[$i])) {
                    $maxChars[$i] = max($maxChars[$i], mb_strlen((string) $v));
                }
            }
        }

        $capped = array_map(fn ($c) => min($c, self::COL_MAX_CHARS), $maxChars);
        $total  = array_sum($capped) ?: 1;

        return array_map(fn ($c) => round($c / $total * $totalW, 1), $capped);
    }

    // ── PDF structure builder ────────────────────────────────────────────────

    private function buildPdf(array $pages): string
    {
        $objects     = [];
        $fontReg     = 3;
        $fontBold    = 4;
        $nextObj     = 5;
        $pageObjNums = [];

        $objects[1]         = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[$fontReg]  = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBold] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        foreach ($pages as $cmds) {
            $stream        = implode('', $cmds);
            $contentObj    = $nextObj++;
            $pageObj       = $nextObj++;
            $pageObjNums[] = $pageObj;

            $objects[$contentObj] = sprintf(
                "<< /Length %d >>\nstream\n%sendstream",
                strlen($stream), $stream
            );
            $objects[$pageObj] = sprintf(
                '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %d %d] /Resources << /Font << /F1 %d 0 R /F2 %d 0 R >> >> /Contents %d 0 R >>',
                self::PAGE_W, self::PAGE_H, $fontReg, $fontBold, $contentObj
            );
        }

        $kids      = implode(' ', array_map(fn ($n) => $n . ' 0 R', $pageObjNums));
        $objects[2] = '<< /Type /Pages /Kids [' . $kids . '] /Count ' . count($pageObjNums) . ' >>';

        ksort($objects);

        $pdf     = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $num . " 0 obj\n" . $body . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $maxObj     = max(array_keys($objects));

        $pdf .= "xref\n0 " . ($maxObj + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $maxObj; $i++) {
            $pdf .= sprintf('%010d 00000 n ' . "\n", $offsets[$i] ?? 0);
        }

        $pdf .= "trailer\n<< /Size " . ($maxObj + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

        return $pdf;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function trunc(string $text, int $max): string
    {
        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 1) . '~' : $text;
    }

    private function pdfEscape(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace('(', '\\(', $text);
        $text = str_replace(')', '\\)', $text);
        return preg_replace('/[^\x20-\x7E]/', '?', $text) ?? '';
    }
}
