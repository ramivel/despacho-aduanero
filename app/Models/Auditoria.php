<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $fillable = [
        'uuid',
        'tabla',
        'registro_id',
        'accion',
        'valor_anterior',
        'valor_nuevo',
        'usuario_id',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'valor_anterior' => 'array',
            'valor_nuevo' => 'array',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
