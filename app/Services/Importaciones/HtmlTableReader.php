<?php

namespace App\Services\Importaciones;

use RuntimeException;

class HtmlTableReader
{
    public function read(
        string $path,
        callable $callback
    ): void {
        if (!is_file($path)) {
            throw new RuntimeException(
                'El archivo no existe.'
            );
        }

        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException(
                'No se pudo abrir el archivo.'
            );
        }

        $insideTable = false;
        $insideTbody = false;
        $insideRow = false;

        $rowHtml = '';

        try {

            while (($line = fgets($handle)) !== false) {

                $lower = strtolower($line);

                if (
                    !$insideTable &&
                    str_contains($lower, '<table')
                ) {
                    $insideTable = true;
                    continue;
                }

                if (!$insideTable) {
                    continue;
                }

                if (
                    !$insideTbody &&
                    str_contains($lower, '<tbody')
                ) {
                    $insideTbody = true;
                    continue;
                }

                if (!$insideTbody) {
                    continue;
                }

                if (
                    !$insideRow &&
                    preg_match('/<tr\b/i', $line)
                ) {
                    $insideRow = true;
                    $rowHtml = $line;

                    if (
                        preg_match('/<\/tr>/i', $line)
                    ) {
                        $insideRow = false;

                        $row = $this->parseRow(
                            $rowHtml
                        );

                        if ($row !== null) {
                            $callback($row);
                        }

                        $rowHtml = '';
                    }

                    continue;
                }

                if ($insideRow) {

                    $rowHtml .= $line;

                    if (
                        preg_match('/<\/tr>/i', $line)
                    ) {
                        $insideRow = false;

                        $row = $this->parseRow(
                            $rowHtml
                        );

                        if ($row !== null) {
                            $callback($row);
                        }

                        $rowHtml = '';
                    }
                }

                if (
                    str_contains($lower, '</tbody>')
                ) {
                    $insideTbody = false;
                }

                if (
                    str_contains($lower, '</table>')
                ) {
                    break;
                }
            }

        } finally {
            fclose($handle);
        }
    }

    private function parseRow(string $html): ?array
    {
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);

        $htmlCompleto = '<?xml encoding="UTF-8">' . $html;

        $dom->loadHTML(
            $htmlCompleto,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();

        $tds = $dom->getElementsByTagName('td');

        if ($tds->length === 0) {
            return null;
        }

        $row = [];

        foreach ($tds as $td) {
            $row[] = $this->cleanValue(
                $td->textContent
            );
        }

        return $row;
    }

    private function cleanValue(string $value): string
    {
        $value = html_entity_decode(
            $value,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        $value = strip_tags($value);

        $value = preg_replace(
            '/\s+/u',
            ' ',
            $value
        );

        return trim($value);
    }
}
