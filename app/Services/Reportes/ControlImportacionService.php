<?php

namespace App\Services\Reportes;

use App\Models\Certificado;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ControlImportacionService
{
    public function generar(
        string $fechaInicio,
        string $fechaFin
    ): array {
        $inicio = Carbon::parse($fechaInicio)->startOfDay();
        $fin = Carbon::parse($fechaFin)->endOfDay();
        $totalCertificados = Certificado::query()
            ->whereBetween('fecha_emision_certificado',[$inicio, $fin])
            ->count();
        $totalItems = Certificado::query()
            ->join('certificado_items','certificado_items.certificado_id','=','certificados.id')
            ->whereBetween('certificados.fecha_emision_certificado',[$inicio, $fin])
            ->count('certificado_items.id');
        $tiposSolicitud = Certificado::query()
            ->whereBetween('fecha_emision_certificado',[$inicio, $fin])
            ->whereNotNull('tipo_solicitud')
            ->where('tipo_solicitud', '<>', '')
            ->select('tipo_solicitud')
            ->distinct()
            ->orderBy('tipo_solicitud')
            ->pluck('tipo_solicitud');
        $resumenMensual = $this->generarResumenMensual(
            $inicio,
            $fin,
            $tiposSolicitud
        );
        $reportes = $tiposSolicitud
            ->map(function (string $tipoSolicitud) use (
                $inicio,
                $fin
            ) {
                return $this->generarReporteTipoSolicitud(
                    $tipoSolicitud,
                    $inicio,
                    $fin
                );
            })
            ->values()
            ->all();
        return [
            'total_certificados' => $totalCertificados,
            'total_items' => $totalItems,
            'resumen_mensual' => $resumenMensual,
            'reportes' => $reportes,
        ];
    }
    private function generarResumenMensual(
        Carbon $inicio,
        Carbon $fin,
        Collection $tiposSolicitud
    ): array {
        $meses = $this->generarMeses(
            $inicio,
            $fin
        );
        $datosCertificados = Certificado::query()
            ->whereBetween(
                'fecha_emision_certificado',
                [$inicio, $fin]
            )
            ->whereNotNull('tipo_solicitud')
            ->where('tipo_solicitud', '<>', '')
            ->selectRaw(
                "EXTRACT(YEAR FROM fecha_emision_certificado)::integer AS anio"
            )
            ->selectRaw(
                "EXTRACT(MONTH FROM fecha_emision_certificado)::integer AS mes"
            )
            ->addSelect('tipo_solicitud')
            ->selectRaw(
                'COUNT(DISTINCT certificados.id) AS cantidad'
            )
            ->groupByRaw(
                "EXTRACT(YEAR FROM fecha_emision_certificado)"
            )
            ->groupByRaw(
                "EXTRACT(MONTH FROM fecha_emision_certificado)"
            )
            ->groupBy('tipo_solicitud')
            ->get();
        $certificadosIndexados = $datosCertificados
            ->keyBy(
                fn ($item) =>
                    "{$item->anio}-{$item->mes}-{$item->tipo_solicitud}"
            );
        $filasCertificados = $meses
            ->map(function (Carbon $mes) use (
                $tiposSolicitud,
                $certificadosIndexados
            ) {
                $totales = [];
                foreach ($tiposSolicitud as $tipoSolicitud) {
                    $clave = "{$mes->year}-{$mes->month}-{$tipoSolicitud}";
                    $registro = $certificadosIndexados->get($clave);
                    $totales[$tipoSolicitud] = $registro ? (int) $registro->cantidad : 0;
                }
                return [
                    'mes' => strtoupper($mes->translatedFormat('F')),
                    'mes_numero' => $mes->month,
                    'anio' => $mes->year,
                    'totales' => $totales,
                    'total' => array_sum($totales),
                ];
            })
            ->values()
            ->all();
        $datosItems = Certificado::query()
            ->join(
                'certificado_items',
                'certificado_items.certificado_id',
                '=',
                'certificados.id'
            )
            ->whereBetween(
                'certificados.fecha_emision_certificado',
                [$inicio, $fin]
            )
            ->whereNotNull('certificados.tipo_solicitud')
            ->where(
                'certificados.tipo_solicitud',
                '<>',
                ''
            )
            ->selectRaw(
                "EXTRACT(YEAR FROM certificados.fecha_emision_certificado)::integer AS anio"
            )
            ->selectRaw(
                "EXTRACT(MONTH FROM certificados.fecha_emision_certificado)::integer AS mes"
            )
            ->addSelect(
                'certificados.tipo_solicitud'
            )
            ->selectRaw(
                'COUNT(certificado_items.id) AS cantidad'
            )
            ->groupByRaw(
                "EXTRACT(YEAR FROM certificados.fecha_emision_certificado)"
            )
            ->groupByRaw(
                "EXTRACT(MONTH FROM certificados.fecha_emision_certificado)"
            )
            ->groupBy(
                'certificados.tipo_solicitud'
            )
            ->get();
        $itemsIndexados = $datosItems
            ->keyBy(
                fn ($item) =>
                    "{$item->anio}-{$item->mes}-{$item->tipo_solicitud}"
            );
        $filasItems = $meses
            ->map(function (Carbon $mes) use (
                $tiposSolicitud,
                $itemsIndexados
            ) {
                $totales = [];
                foreach ($tiposSolicitud as $tipoSolicitud) {
                    $clave = "{$mes->year}-{$mes->month}-{$tipoSolicitud}";
                    $registro = $itemsIndexados->get($clave);
                    $totales[$tipoSolicitud] = $registro ? (int) $registro->cantidad : 0;
                }
                return [
                    'mes' => strtoupper($mes->translatedFormat('F')),
                    'mes_numero' => $mes->month,
                    'anio' => $mes->year,
                    'totales' => $totales,
                    'total' => array_sum($totales),
                ];
            })
            ->values()
            ->all();
        $totalesCertificados = [];
        $totalesItems = [];
        $graficoCertificados = [];
        $graficoItems = [];
        foreach ($tiposSolicitud as $tipoSolicitud) {
            $totalesCertificados[$tipoSolicitud] =
                collect($filasCertificados)
                    ->sum(
                        fn ($fila) =>
                            $fila['totales'][$tipoSolicitud] ?? 0
                    );
            $totalesItems[$tipoSolicitud] =
                collect($filasItems)
                    ->sum(
                        fn ($fila) =>
                            $fila['totales'][$tipoSolicitud] ?? 0
                    );
            $graficoCertificados = [
                'categorias' => collect($filasCertificados)
                    ->map(
                        fn ($fila) =>
                            $fila['mes'] . ' ' . $fila['anio']
                    )
                    ->values()
                    ->all(),
                'series' => $tiposSolicitud
                    ->map(
                        fn ($tipoSolicitud) => [
                            'nombre' => $tipoSolicitud,
                            'datos' => collect($filasCertificados)
                                ->map(
                                    fn ($fila) =>
                                        $fila['totales'][$tipoSolicitud] ?? 0
                                )
                                ->values()
                                ->all(),
                        ]
                    )
                    ->values()
                    ->all(),
            ];
            $graficoItems = [
                'categorias' => collect($filasItems)
                    ->map(
                        fn ($fila) =>
                            $fila['mes'] . ' ' . $fila['anio']
                    )
                    ->values()
                    ->all(),
                'series' => $tiposSolicitud
                    ->map(
                        fn ($tipoSolicitud) => [
                            'nombre' => $tipoSolicitud,
                            'datos' => collect($filasItems)
                                ->map(
                                    fn ($fila) =>
                                        $fila['totales'][$tipoSolicitud] ?? 0
                                )
                                ->values()
                                ->all(),
                        ]
                    )
                    ->values()
                    ->all(),
            ];
        }
        return [
            'tipos_solicitud' => $tiposSolicitud->all(),
            'certificados' => $filasCertificados,
            'items' => $filasItems,
            'totales_certificados' => $totalesCertificados,
            'totales_items' => $totalesItems,
            'grafico_certificados' => $graficoCertificados,
            'grafico_items' => $graficoItems,
        ];
    }
    private function generarReporteTipoSolicitud(
        string $tipoSolicitud,
        Carbon $inicio,
        Carbon $fin
    ): array {
        $tiposProducto = Certificado::query()
            ->join(
                'certificado_items',
                'certificado_items.certificado_id',
                '=',
                'certificados.id'
            )
            ->where(
                'certificados.tipo_solicitud',
                $tipoSolicitud
            )
            ->whereBetween(
                'certificados.fecha_emision_certificado',
                [$inicio, $fin]
            )
            ->whereNotNull('certificados.tipo_producto')
            ->where(
                'certificados.tipo_producto',
                '<>',
                ''
            )
            ->select('certificados.tipo_producto')
            ->distinct()
            ->orderBy('certificados.tipo_producto')
            ->pluck('certificados.tipo_producto')
            ->values();
        $datos = Certificado::query()
            ->join(
                'certificado_items',
                'certificado_items.certificado_id',
                '=',
                'certificados.id'
            )
            ->where(
                'certificados.tipo_solicitud',
                $tipoSolicitud
            )
            ->whereBetween(
                'certificados.fecha_emision_certificado',
                [$inicio, $fin]
            )
            ->selectRaw(
                "EXTRACT(YEAR FROM certificados.fecha_emision_certificado)::integer AS anio"
            )
            ->selectRaw(
                "EXTRACT(MONTH FROM certificados.fecha_emision_certificado)::integer AS mes"
            )
            ->addSelect(
                'certificados.tipo_producto'
            )
            ->selectRaw(
                'COUNT(certificado_items.id) AS cantidad'
            )
            ->groupByRaw(
                "EXTRACT(YEAR FROM certificados.fecha_emision_certificado)"
            )
            ->groupByRaw(
                "EXTRACT(MONTH FROM certificados.fecha_emision_certificado)"
            )
            ->groupBy(
                'certificados.tipo_producto'
            )
            ->orderByRaw(
                "EXTRACT(YEAR FROM certificados.fecha_emision_certificado) ASC"
            )
            ->orderByRaw(
                "EXTRACT(MONTH FROM certificados.fecha_emision_certificado) ASC"
            )
            ->get();
        $datosIndexados = $datos->keyBy(
            fn ($item) =>
                "{$item->anio}-{$item->mes}-{$item->tipo_producto}"
        );
        $meses = $this->generarMeses(
            $inicio,
            $fin
        );
        $filas = $meses->map(
            function (Carbon $mes) use (
                $tiposProducto,
                $datosIndexados
            ) {
                $productos = [];
                $total = 0;
                foreach ($tiposProducto as $tipoProducto) {
                    $clave = "{$mes->year}-{$mes->month}-{$tipoProducto}";
                    $registro = $datosIndexados->get($clave);
                    $cantidad = $registro
                        ? (int) $registro->cantidad
                        : 0;
                    $productos[$tipoProducto] = $cantidad;
                    $total += $cantidad;
                }
                return [
                    'mes' => $mes->translatedFormat('F'),
                    'mes_numero' => $mes->month,
                    'anio' => $mes->year,
                    'productos' => $productos,
                    'total' => $total,
                ];
            }
        )->values();
        $totales = [];
        foreach ($tiposProducto as $tipoProducto) {
            $totales[$tipoProducto] = $filas->sum(
                fn ($fila) =>
                    $fila['productos'][$tipoProducto] ?? 0
            );
        }
        $total = array_sum($totales);
        $grafico = [
            'categorias' => $filas
                ->map(
                    fn ($fila) =>
                        ucfirst($fila['mes']) . ' ' . $fila['anio']
                )
                ->values()
                ->all(),
            'series' => $tiposProducto
                ->map(
                    fn ($tipoProducto) => [
                        'nombre' => $tipoProducto,
                        'datos' => $filas
                            ->map(
                                fn ($fila) =>
                                    $fila['productos'][$tipoProducto] ?? 0
                            )
                            ->values()
                            ->all(),
                    ]
                )
                ->values()
                ->all(),
        ];
        $totalItems = $total;
        $totalCertificados = Certificado::query()
            ->where(
                'tipo_solicitud',
                $tipoSolicitud
            )
            ->whereBetween(
                'fecha_emision_certificado',
                [$inicio, $fin]
            )
            ->count();
        return [
            'tipo_solicitud' => $tipoSolicitud,
            'titulo' => $tipoSolicitud,
            'tipos_producto' => $tiposProducto
                ->values()
                ->all(),
            'filas' => $filas
                ->all(),
            'totales' => $totales,
            'total' => $totalItems,
            'total_certificados' => $totalCertificados,
            'total_items' => $totalItems,
            'grafico' => $grafico,
        ];
    }
    private function generarMeses(
        Carbon $inicio,
        Carbon $fin
    ): Collection {
        $meses = collect();
        $actual = $inicio->copy()->startOfMonth();
        $ultimo = $fin->copy()->startOfMonth();
        while ($actual <= $ultimo) {
            $meses->push(
                $actual->copy()
            );
            $actual->addMonth();
        }
        return $meses;
    }

}
