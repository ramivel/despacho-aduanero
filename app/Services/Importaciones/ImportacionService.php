<?php

namespace App\Services\Importaciones;

use App\Models\Importacion;
use App\Models\ImportacionRegistro;
use App\Models\Certificado;
use App\Models\CertificadoItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;
use Carbon\Carbon;

class ImportacionService
{
    private const TAMANO_LOTE = 500;

    private const COLUMNAS = [
        0  => 'numero_fila',
        1  => 'codigo_certificado',
        2  => 'fecha_solicitud',
        3  => 'fecha_recepcion',
        4  => 'fecha_revision',
        5  => 'fecha_emision_certificado',
        6  => 'tipo_solicitud',

        7  => 'nit',
        8  => 'nombre_empresa',
        9  => 'decreto_supremo',

        10 => 'ci_representante_legal',
        11 => 'lugar_expedicion_ci',
        12 => 'correo_electronico',
        13 => 'nombre_representante_legal',
        14 => 'nro_resolucion_ministerial',
        15 => 'fecha_resolucion_ministerial',
        16 => 'nro_matricula_prof_regente',
        17 => 'nombre_regente',

        18 => 'uso',
        19 => 'tipo_producto',

        20 => 'proveedor',
        21 => 'producto_refrigerado',
        22 => 'nro_factura',

        23 => 'monto_factura',
        24 => 'procedencia',

        25 => 'cantidad_medicamento',
        26 => 'unidad',
        27 => 'producto',
        28 => 'registro_sanitario',
        29 => 'fecha_vencimiento',
        30 => 'nro_lote',
        31 => 'costo_item_medicamento',
        32 => 'evaluacion_item',
        33 => 'usuario',
    ];

    public function procesar(
        string $archivo,
        string $nombreOriginal,
        ?int $usuarioId = null
    ): Importacion {

        $importacion = Importacion::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'archivo_original' => $nombreOriginal,
            'nombre_archivo' => $archivo,
            'fecha_importacion' => now(),
            'usuario_id' => $usuarioId,
            'total_certificados' => 0,
            'total_items' => 0,
            'estado' => 'PROCESANDO',
        ]);

        try {

            $reader = app(HtmlTableReader::class);

            $batch = [];
            $numeroFila = 0;

            $certificados = [];

            $reader->read(
                $archivo,
                function (array $row) use (
                    &$batch,
                    &$numeroFila,
                    &$certificados,
                    $importacion
                ) {

                    $numeroFila++;
                    $datos = $this->mapearFila(
                        $row,
                        $numeroFila
                    );

                    if ($datos === null) {
                        $batch[] = [
                            'importacion_id' => $importacion->id,
                            'numero_fila' => $numeroFila,
                            'codigo_certificado' => null,
                            'datos_certificado' => json_encode([]),
                            'datos_item' => json_encode([]),
                            'estado' => 'ERROR',
                            'mensaje' => 'Cantidad de columnas incorrecta.',
                            'seleccionado' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {

                        $codigo = $datos[
                            'codigo_certificado'
                        ];

                        $certificados[$codigo] = true;

                        $batch[] = [
                            'importacion_id' => $importacion->id,
                            'numero_fila' => $numeroFila,
                            'codigo_certificado' => $codigo,
                            'datos_certificado' => json_encode(
                                $datos['certificado'],
                                JSON_UNESCAPED_UNICODE
                            ),
                            'datos_item' => json_encode(
                                $datos['item'],
                                JSON_UNESCAPED_UNICODE
                            ),
                            'estado' => 'VALIDO',
                            'mensaje' => 'Puede importarse.',
                            'seleccionado' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (
                        count($batch) >= self::TAMANO_LOTE
                    ) {
                        ImportacionRegistro::insert(
                            $batch
                        );

                        $batch = [];
                    }
                }
            );

            if (!empty($batch)) {
                ImportacionRegistro::insert(
                    $batch
                );
            }

            $this->validarExistentes($importacion);

            $resumen = $this->obtenerResumenValidacion(
                $importacion->id
            );

            $totalItems = $numeroFila;
            $totalCertificados = count($certificados);

            $importacion->update([
                'total_registros' => $resumen['total_registros'],
                'total_validos' => $resumen['total_validos'],
                'total_existentes' => $resumen['total_existentes'],
                'total_errores' => $resumen['total_errores'],
                'total_certificados' => $totalCertificados,
                'total_items' => $totalItems,
                'estado' => 'COMPLETADA',
            ]);

            // El archivo ya cumplió su función.
            // Los datos están almacenados en importacion_registros.
            if (is_file($archivo)) {
                unlink($archivo);
            }

            return $importacion;

        } catch (Throwable $e) {

            $importacion->update([
                'estado' => 'ERROR',
                'observacion' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function mapearFila(
        array $row,
        int $numeroFila
    ): ?array {

        if (count($row) !== count(self::COLUMNAS)) {
            return null;
        }

        $data = [];

        foreach (self::COLUMNAS as $index => $nombre) {
            $data[$nombre] = $row[$index] ?? null;
        }

        $codigo = trim(
            $data['codigo_certificado'] ?? ''
        );

        if ($codigo === '') {
            return null;
        }

        return [
            'codigo_certificado' => $codigo,

            'certificado' => [
                'codigo_certificado' => $codigo,
                'fecha_solicitud' => $data['fecha_solicitud'],
                'fecha_emision_certificado' =>
                    $data['fecha_emision_certificado'],
                'tipo_solicitud' => $data['tipo_solicitud'],
                'nit' => $data['nit'],
                'nombre_empresa' => $data['nombre_empresa'],
                'decreto_supremo' => $data['decreto_supremo'],
                'ci_representante_legal' =>
                    $data['ci_representante_legal'],
                'lugar_expedicion_ci' =>
                    $data['lugar_expedicion_ci'],
                'correo_electronico' =>
                    $data['correo_electronico'],
                'nombre_representante_legal' =>
                    $data['nombre_representante_legal'],
                'nro_resolucion_ministerial' =>
                    $data['nro_resolucion_ministerial'],
                'fecha_resolucion_ministerial' =>
                    $data['fecha_resolucion_ministerial'],
                'nro_matricula_prof_regente' =>
                    $data['nro_matricula_prof_regente'],
                'nombre_regente' =>
                    $data['nombre_regente'],
                'uso' => $data['uso'],
                'tipo_producto' =>
                    $data['tipo_producto'],
                'proveedor' => $data['proveedor'],
                'producto_refrigerado' =>
                    $data['producto_refrigerado'],
                'nro_factura' => $data['nro_factura'],
                'monto_factura' =>
                    $data['monto_factura'],
                'procedencia' =>
                    $data['procedencia'],
                'usuario_origen' =>
                    $data['usuario'],
            ],

            'item' => [
                'cantidad_medicamento' =>
                    $data['cantidad_medicamento'],
                'unidad' =>
                    $data['unidad'],
                'producto' =>
                    $data['producto'],
                'registro_sanitario' =>
                    $data['registro_sanitario'],
                'fecha_vencimiento' =>
                    $data['fecha_vencimiento'],
                'nro_lote' =>
                    $data['nro_lote'],
                'costo_item_medicamento' =>
                    $data['costo_item_medicamento'],
                'evaluacion_item' =>
                    $data['evaluacion_item'],
            ],
        ];
    }
    private function validarExistentes(
        Importacion $importacion
    ): void {

        ImportacionRegistro::query()
            ->where('importacion_id', $importacion->id)
            ->whereNotNull('codigo_certificado')
            ->where('estado', 'VALIDO')
            ->chunkById(500, function ($registros) {

                $codigos = $registros
                    ->pluck('codigo_certificado')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                if (empty($codigos)) {
                    return;
                }

                /*
                * Buscar los certificados que YA existen
                * en la tabla definitiva.
                */
                $existentes = Certificado::query()
                    ->whereIn(
                        'codigo_certificado',
                        $codigos
                    )
                    ->pluck('codigo_certificado')
                    ->flip();

                foreach ($registros as $registro) {

                    if (
                        $existentes->has(
                            $registro->codigo_certificado
                        )
                    ) {

                        $registro->update([
                            'estado' => 'EXISTENTE',
                            'seleccionado' => false,
                            'mensaje' =>
                                'El certificado ya se encuentra registrado en la base de datos.',
                        ]);
                    }
                }
            });
    }

    private function obtenerResumenValidacion(
        int $importacionId
    ): array {
        $resumen = ImportacionRegistro::query()
            ->where('importacion_id', $importacionId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                "COUNT(*) FILTER (WHERE estado = 'VALIDO') as validos"
            )
            ->selectRaw(
                "COUNT(*) FILTER (WHERE estado = 'EXISTENTE') as existentes"
            )
            ->selectRaw(
                "COUNT(*) FILTER (WHERE estado = 'ERROR') as errores"
            )
            ->first();

        return [
            'total_registros' => (int) $resumen->total,
            'total_validos' => (int) $resumen->validos,
            'total_existentes' => (int) $resumen->existentes,
            'total_errores' => (int) $resumen->errores,
        ];
    }

    public function guardarSeleccionados(
        int $importacionId
    ): void {
        DB::transaction(function () use ($importacionId) {

            $importacion = Importacion::query()
                ->lockForUpdate()
                ->findOrFail($importacionId);
            $registros = ImportacionRegistro::query()
                ->where('importacion_id', $importacionId)
                ->where('estado', 'VALIDO')
                ->where('seleccionado', true)
                ->orderBy('codigo_certificado')
                ->orderBy('numero_fila')
                ->lockForUpdate()
                ->get();

            if ($registros->isEmpty()) {
                throw new RuntimeException(
                    'No existen registros seleccionados para guardar.'
                );
            }

            /*
            * Agrupamos las filas por certificado.
            *
            * Un certificado puede tener varios items.
            */
            $grupos = $registros->groupBy(
                'codigo_certificado'
            );
            foreach ($grupos as $codigo => $filas) {
                /*
                * Verificamos nuevamente que el certificado
                * no haya sido registrado.
                *
                * Esto protege contra dobles importaciones.
                */
                $existe = Certificado::query()
                    ->where('importacion_id', $importacionId)
                    ->where('codigo_certificado', $codigo)
                    ->exists();

                if ($existe) {
                    throw new RuntimeException(
                        "El certificado {$codigo} ya existe en esta importación."
                    );
                }

                /*
                * La primera fila contiene los datos generales
                * del certificado.
                */
                $primeraFila = $filas->first();

                $datosCertificado = $primeraFila->datos_certificado;

                if (! is_array($datosCertificado)) {
                    throw new RuntimeException(
                        "Los datos del certificado {$codigo} no son válidos."
                    );
                }

                /*
                * Crear certificado.
                */
                $certificado = Certificado::create([
                    'importacion_id' => $importacion->id,
                    'codigo_certificado' =>
                        $datosCertificado['codigo_certificado'],
                    'tipo_solicitud' =>
                        $datosCertificado['tipo_solicitud'],
                    'nit' =>
                        $datosCertificado['nit'],
                    'nombre_empresa' =>
                        $datosCertificado['nombre_empresa'],
                    'fecha_solicitud' =>
                        $this->normalizarFecha($datosCertificado['fecha_solicitud'] ?? null),
                    'fecha_emision_certificado' =>
                        $this->normalizarFecha(
                            $datosCertificado['fecha_emision_certificado'] ?? null,
                            true
                        ),
                    'uso' =>
                        $datosCertificado['uso'],
                    'tipo_producto' =>
                        $datosCertificado['tipo_producto'],
                    'proveedor' =>
                        $datosCertificado['proveedor'],
                    'producto_refrigerado' =>
                        $this->normalizarBooleano(
                            $datosCertificado['producto_refrigerado'] ?? null
                        ),
                    'nro_factura' =>
                        $datosCertificado['nro_factura'],
                    'monto_factura' =>
                        $this->normalizarMontoFactura(
                            $datosCertificado['monto_factura'] ?? null
                        ),
                    'procedencia' =>
                        $datosCertificado['procedencia'],
                    'usuario_origen' =>
                        $datosCertificado['usuario_origen'],
                    'cantidad_items' =>
                        $filas->count(),
                ]);

                /*
                * Crear los items del certificado.
                */
                $numeroItem = 1;

                foreach ($filas as $registro) {
                    $datosItem = $registro->datos_item;

                    if (! is_array($datosItem)) {
                        throw new RuntimeException(
                            "Los datos del item de la fila {$registro->numero_fila} no son válidos."
                        );
                    }
                    CertificadoItem::create([
                        'certificado_id' =>
                            $certificado->id,
                        'numero_item' =>
                            $numeroItem,
                        'cantidad_medicamento' =>
                            $datosItem['cantidad_medicamento'],
                        'producto' =>
                            $datosItem['producto'],
                        'registro_sanitario' =>
                            $datosItem['registro_sanitario'],
                        'fecha_vencimiento' =>
                            $this->normalizarFecha($datosItem['fecha_vencimiento'] ?? null),
                        'nro_lote' =>
                            $datosItem['nro_lote'],
                        'evaluacion_item' =>
                            $datosItem['evaluacion_item'],
                    ]);

                    /*
                    * Marcamos la fila temporal como procesada.
                    */
                    $registro->update([
                        'seleccionado' => false,
                        'mensaje' =>
                            'Registro guardado correctamente.',
                    ]);

                    $numeroItem++;
                }
            }
        });
    }

    private function normalizarFecha(
        mixed $valor,
        bool $conHora = false
    ): ?string {
        if ($valor === null) {
            return null;
        }

        $valor = trim((string) $valor);

        if ($valor === '' || $valor === '-') {
            return null;
        }

        try {
            if ($conHora) {
                $fecha = Carbon::createFromFormat(
                    'd/n/Y H:i:s',
                    $valor
                );

                return $fecha->format('Y-m-d H:i:s');
            }

            $fecha = Carbon::createFromFormat(
                'd/n/Y',
                $valor
            );

            return $fecha->format('Y-m-d');

        } catch (\Throwable $e) {
            throw new RuntimeException(
                "La fecha '{$valor}' no tiene un formato válido."
            );
        }
    }

    private function normalizarBooleano(mixed $valor): ?bool
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        $valor = strtoupper(trim((string) $valor));

        return match ($valor) {
            'SI', 'SÍ', 'YES', 'TRUE', '1' => true,
            'NO', 'FALSE', '0' => false,
            default => null,
        };
    }

    private function normalizarMontoFactura(mixed $valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        $valor = trim((string) $valor);

        // La fuente puede utilizar coma o punto como separador decimal.
        $valor = str_replace(',', '.', $valor);

        return is_numeric($valor) ? (float) $valor : null;
    }

}
