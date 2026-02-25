<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

trait Auditable
{
    public static function registrar(
        string $tipoAccion,
        string $entidad,
        ?int $entidadId,
        ?array $antes,
        ?array $despues,
        ?string $descripcion = null
    ): void {
        try {
            DB::table('auditoria')->insert([
                'user_id'     => Auth::id(),
                'tipo_accion' => $tipoAccion,
                'entidad'     => $entidad,
                'entidad_id'  => $entidadId,
                'antes'       => $antes  ? json_encode($antes)  : null,
                'despues'     => $despues ? json_encode($despues) : null,
                'ip'          => Request::ip(),
                'descripcion' => $descripcion,
                'created_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            // La auditoría nunca debe romper el flujo principal
            \Illuminate\Support\Facades\Log::error('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}