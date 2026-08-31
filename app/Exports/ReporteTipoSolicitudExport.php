<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReporteTipoSolicitudExport implements FromArray, WithHeadings
{
    public function __construct(
        protected array $reporte,
        protected ?string $fechaInicio,
        protected ?string $fechaFin,
    ) {
    }

    public function headings(): array
    {
        return [
            [$this->reporte['tipo_solicitud']],

            [
                'Rango de fechas:',
                $this->fechaInicio
                    ? \Carbon\Carbon::parse($this->fechaInicio)->format('d/m/Y')
                    : '',
                'al',
                $this->fechaFin
                    ? \Carbon\Carbon::parse($this->fechaFin)->format('d/m/Y')
                    : '',
            ],

            [],

            [
                'Mes',
                ...$this->reporte['tipos_producto'],
                'Total',
            ],
        ];
    }

    public function array(): array
    {
        return collect($this->reporte['filas'])
            ->map(function (array $fila): array {

                $resultado = [
                    ucfirst($fila['mes']) . ' ' . $fila['anio'],
                ];

                foreach (
                    $this->reporte['tipos_producto']
                    as $tipoProducto
                ) {
                    $resultado[] =
                        $fila['productos'][$tipoProducto] ?? 0;
                }

                $resultado[] = $fila['total'];

                return $resultado;
            })
            ->values()
            ->all();
    }
}
