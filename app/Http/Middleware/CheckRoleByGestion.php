<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionAcademica;
use App\Models\ModelHasRoleGestion;
use App\Models\User;

class CheckRoleByGestion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Validar si está autenticado y activo
        if (!$user || !$user->activo) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Su cuenta está inactiva o no autenticada.']);
        }

        // 2. Si el usuario es Administrador global (según Spatie), tiene acceso total
        if ($user->hasRole('Administrador')) {
            return $next($request);
        }

        // 3. Obtener la gestión académica actualmente activa en el sistema
        $gestionActiva = GestionAcademica::where('activo', true)->first();

        if (!$gestionActiva) {
            abort(403, 'No existe una gestión académica activa configurada en el sistema.');
        }

        // 4. Convertir los roles permitidos en un array (ej. "Administrador|Supervisor" -> ['Administrador', 'Supervisor'])
        $rolesArray = explode('|', $roles);

        // 5. Verificar si el usuario tiene asignado alguno de los roles requeridos para ESTA gestión específica
        $tieneRolEnGestion = ModelHasRoleGestion::where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('gestion_id', $gestionActiva->id)
            ->whereHas('role', function ($query) use ($rolesArray) {
                $query->whereIn('name', $rolesArray);
            })
            ->exists();

        if (!$tieneRolEnGestion) {
            abort(403, 'No cuenta con los roles autorizados (' . $roles . ') para la gestión académica actual (' . $gestionActiva->anio . ' - ' . $gestionActiva->periodo . ').');
        }

        return $next($request);
    }
}
