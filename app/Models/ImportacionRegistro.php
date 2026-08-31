<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacionRegistro extends Model
{
    protected $table = 'importacion_registros';
    public const ESTADO_VALIDO = 'VALIDO';
    public const ESTADO_EXISTENTE = 'EXISTENTE';
    public const ESTADO_ERROR = 'ERROR';
    protected $fillable = [
        'importacion_id',
        'numero_fila',
        'codigo_certificado',
        'datos_certificado',
        'datos_item',
        'estado',
        'mensaje',
        'seleccionado',
    ];

    protected function casts(): array
    {
        return [
            'datos_certificado' => 'array',
            'datos_item' => 'array',
            'seleccionado' => 'boolean',
        ];
    }

    public function importacion(): BelongsTo
    {
        return $this->belongsTo(
            Importacion::class,
            'importacion_id'
        );
    }
}
