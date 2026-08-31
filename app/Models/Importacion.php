<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Importacion extends Model
{
    protected $table = 'importaciones';
    public const ESTADO_PROCESANDO = 'PROCESANDO';
    public const ESTADO_COMPLETADA = 'COMPLETADA';
    public const ESTADO_ERROR = 'ERROR';
    public const ESTADO_ANULADA = 'ANULADA';
    protected $fillable = [
        'archivo_original',
        'nombre_archivo',
        'fecha_importacion',
        'usuario_id',
        'total_registros',
        'total_validos',
        'total_existentes',
        'total_errores',
        'total_certificados',
        'total_items',
        'estado',
        'observacion',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'fecha_importacion'   => 'datetime',
            'total_certificados'  => 'integer',
            'total_items'         => 'integer',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class);
    }

    public function registros(): HasMany
    {
        return $this->hasMany(
            ImportacionRegistro::class,
            'importacion_id'
        );
    }

}
