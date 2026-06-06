<?php

declare(strict_types=1);

namespace App\Plugins\Reports\Support;

class SimpleXlsxWriter
{
    private string $sheetTitle;
    private array $headers = [];
    private array $rows = [];

    public function __construct(string $sheetTitle = 'Reporte')
    {
        $this->sheetTitle = mb_substr($sheetTitle, 0, 31);
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = array_values($headers);
    }

    public function addRow(array $row): void
    {
        $this->rows[] = array_values($row);
    }

    public function addRows(array $rows): void
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    public function toString(): string
    {
        if (! class_exists('ZipArchive')) {
            throw new \RuntimeException('La extension PHP ZipArchive no esta habilitada.');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_erx_');
        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->buildContentTypes());
        $zip->addFromString('_rels/.rels', $this->buildRootRels());
        $zip->addFromString('xl/workbook.xml', $this->buildWorkbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->buildWorkbookRels());
        $zip->addFromString('xl/styles.xml', $this->buildStyles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheet());

        $zip->close();
        $content = file_get_contents($tmp) ?: '';
        @unlink($tmp);

        return $content;
    }

    private function buildContentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function buildRootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function buildWorkbook(): string
    {
        $title = $this->xmlEscape($this->sheetTitle);

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $title . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function buildWorkbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function buildStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0"/>'
            . '</cellXfs>'
            . '</styleSheet>';
    }

    private function buildSheet(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        $rowIndex = 1;
        if (! empty($this->headers)) {
            $xml .= '<row r="' . $rowIndex . '">';
            foreach ($this->headers as $colIndex => $cell) {
                $ref = $this->colLetter($colIndex) . $rowIndex;
                $xml .= '<c r="' . $ref . '" s="1" t="inlineStr"><is><t>' . $this->xmlEscape((string) $cell) . '</t></is></c>';
            }
            $xml .= '</row>';
            $rowIndex++;
        }

        foreach ($this->rows as $row) {
            $xml .= '<row r="' . $rowIndex . '">';
            foreach ($row as $colIndex => $cell) {
                $ref = $this->colLetter($colIndex) . $rowIndex;
                if (is_int($cell) || is_float($cell) || (is_string($cell) && is_numeric($cell) && $cell !== '')) {
                    $xml .= '<c r="' . $ref . '" t="n"><v>' . $cell . '</v></c>';
                } else {
                    $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . $this->xmlEscape((string) $cell) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
            $rowIndex++;
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private function colLetter(int $index): string
    {
        $letter = '';
        do {
            $letter = chr(65 + ($index % 26)) . $letter;
            $index = intdiv($index, 26) - 1;
        } while ($index >= 0);

        return $letter;
    }

    private function xmlEscape(string $str): string
    {
        return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
