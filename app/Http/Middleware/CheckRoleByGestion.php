<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\GestionAcademica;

class CheckRoleByGestion
{
    public function handle(Request $request, Closure $next, string $roleName)
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        // 1. EL ADMINISTRADOR PASA LIBREMENTE GLOBALMENTE
        $esAdmin = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_id', $user->id)
            ->where('model_has_roles.model_type', get_class($user))
            ->where('roles.name', 'Administrador')
            ->exists();

        if ($esAdmin) {
            return $next($request);
        }

        // 2. PARA OTROS ROLES (COMO SUPERVISOR O ESTUDIANTE), SÍ IMPORTA LA GESTIÓN ACTIVA
        $gestionActiva = GestionAcademica::where('activo', true)->first();

        if (!$gestionActiva) {
            abort(403, 'No hay una gestión académica activa.');
        }

        $tieneRolEnGestion = DB::table('model_has_role_gestions')
            ->join('roles', 'model_has_role_gestions.role_id', '=', 'roles.id')
            ->where('model_has_role_gestions.model_id', $user->id)
            ->where('model_has_role_gestions.model_type', get_class($user))
            ->where('model_has_role_gestions.gestion_id', $gestionActiva->id)
            ->where('roles.name', $roleName)
            ->exists();

        if (!$tieneRolEnGestion) {
            abort(403, 'No tienes autorización para acceder a este módulo en la gestión académica actual.');
        }

        return $next($request);
    }
}
