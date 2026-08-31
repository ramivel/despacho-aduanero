<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificado extends Model
{
    protected $table = 'certificados';
    protected $fillable = [
        'importacion_id',
        'codigo_certificado',
        'tipo_solicitud',
        'nit',
        'nombre_empresa',
        'fecha_solicitud',
        'fecha_emision_certificado',
        'uso',
        'tipo_producto',
        'proveedor',
        'producto_refrigerado',
        'nro_factura',
        'monto_factura',
        'procedencia',
        'usuario_origen',
        'cantidad_items',
        'activo',
        'editado',
        'observacion',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function casts(): array
    {
        return [
            'fecha_solicitud' => 'date',
            'fecha_emision_certificado' => 'datetime',
            'producto_refrigerado' => 'boolean',
            'monto_factura' => 'decimal:2',
            'cantidad_items' => 'integer',
            'activo' => 'boolean',
            'editado' => 'boolean',
        ];
    }

    public function importacion(): BelongsTo
    {
        return $this->belongsTo(Importacion::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CertificadoItem::class);
    }
}
