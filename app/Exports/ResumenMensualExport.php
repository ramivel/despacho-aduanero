<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResumenMensualExport implements FromArray, WithHeadings
{
    public function __construct(
        protected array $resumen,
        protected string $tipo,
        protected ?string $fechaInicio,
        protected ?string $fechaFin,
    ) {
    }

    public function headings(): array
    {
        $titulo = $this->tipo === 'certificados'
            ? 'Total de Certificados del período'
            : 'Total de Items del período';
        return [
            [
                $titulo,
            ],
            [
                'Rango de fechas:',
                $this->fechaInicio
                    ? Carbon::parse($this->fechaInicio)->format('d/m/Y')
                    : '',
                'al',
                $this->fechaFin
                    ? Carbon::parse($this->fechaFin)->format('d/m/Y')
                    : '',
            ],
            [],
            [
                'Mes',
                ...$this->resumen['tipos_solicitud'],
                'Total',
            ],
        ];
    }
    public function array(): array
    {
        $filas = $this->tipo === 'certificados'
            ? $this->resumen['certificados']
            : $this->resumen['items'];
        $filasExcel = collect($filas)
            ->map(function (array $fila): array {
                $resultado = [
                    ucfirst($fila['mes']) . ' ' . $fila['anio'],
                ];
                foreach (
                    $this->resumen['tipos_solicitud']
                    as $tipoSolicitud
                ) {
                    $resultado[] =
                        $fila['totales'][$tipoSolicitud] ?? 0;
                }
                $resultado[] = $fila['total'] ?? 0;
                return $resultado;
            })
            ->values()
            ->all();
        $totales = $this->tipo === 'certificados'
            ? $this->resumen['totales_certificados']
            : $this->resumen['totales_items'];
        $filaTotal = [
            'TOTAL',
        ];
        foreach (
            $this->resumen['tipos_solicitud']
            as $tipoSolicitud
        ) {
            $filaTotal[] =
                $totales[$tipoSolicitud] ?? 0;
        }
        $filaTotal[] = array_sum($totales);
        $filasExcel[] = $filaTotal;
        return $filasExcel;
    }
}
