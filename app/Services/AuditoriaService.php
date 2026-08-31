<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class AuditoriaService
{
    public function registrar(
        string $accion,
        Model $registro,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
    ): Auditoria {
        $accion = strtoupper(trim($accion));
        if (! in_array($accion, ['INSERT', 'UPDATE', 'DELETE'], true)) {
            throw new InvalidArgumentException(
                "La acción '{$accion}' no es válida para auditoría."
            );
        }
        return Auditoria::create([
            'usuario_id' => Auth::id(),
            'tabla' => $registro->getTable(),
            'registro_id' => $registro->getKey(),
            'accion' => $accion,
            'valor_anterior' => $datosAnteriores,
            'valor_nuevo' => $datosNuevos,
            'ip' => request()->ip(),
        ]);
    }
}
