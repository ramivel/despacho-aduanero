<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificadoItem extends Model
{
    protected $table = 'certificado_items';
    protected $fillable = [
        'certificado_id',
        'numero_item',
        'cantidad_medicamento',
        'producto',
        'registro_sanitario',
        'fecha_vencimiento',
        'nro_lote',
        'evaluacion_item',
        'activo',
        'editado',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'cantidad_medicamento' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'activo' => 'boolean',
            'editado' => 'boolean',
        ];
    }

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(Certificado::class);
    }
}
