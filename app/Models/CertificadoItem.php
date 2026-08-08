<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificadoItem extends Model
{
    use HasUuids;

    protected $table = 'certificado_items';
    protected $fillable = [
        'uuid',
        'certificado_id',
        'numero_item',
        'cantidad',
        'producto',
        'registro_sanitario',
        'fecha_vencimiento',
        'numero_lote',
        'evaluacion',
        'activo',
        'editado',
    ];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    protected function casts(): array
    {
        return [
            'cantidad' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'evaluacion' => 'boolean',
            'activo' => 'boolean',
            'editado' => 'boolean',
        ];
    }

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(Certificado::class);
    }
}
