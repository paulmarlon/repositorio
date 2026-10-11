<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionAcademica;
use App\Models\ModelHasRoleGestion;
use App\Models\User; // <-- 1. Importa el modelo User

class CheckRoleByGestion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        /** @var User $user */
        $user = Auth::user(); // <-- 2. Añade la anotación de tipo aquí

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

        // 4. Verificar si el usuario tiene asignado el rol requerido para ESTA gestión específica usando Eloquent
        $tieneRolEnGestion = ModelHasRoleGestion::where('model_id', $user->id)
            ->where('model_type', get_class($user))
            ->where('gestion_id', $gestionActiva->id)
            ->whereHas('role', function ($query) use ($role) {
                $query->where('name', $role);
            })
            ->exists();

        if (!$tieneRolEnGestion) {
            abort(403, 'No cuenta con el rol de "' . $role . '" autorizado para la gestión académica actual (' . $gestionActiva->anio . ' - ' . $gestionActiva->periodo . ').');
        }

        return $next($request);
    }
}
