<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\GestionAcademica;
use App\Models\ModelHasRoleGestion;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('rolesPorGestion.role', 'rolesPorGestion.gestion')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function toggleActive(User $usuario)
    {
        /** @var User $currentUser */
        $currentUser = auth()->user();

        // SEGURIDAD: Un supervisor no puede desactivar/activar a un Administrador
        if (!$currentUser->hasRole('Administrador') && $usuario->hasRole('Administrador')) {
            return redirect()->route('admin.usuarios.index')
                ->with('mensaje', 'Acción no permitida: No puedes modificar el estado de un Administrador.')
                ->with('icon', 'error');
        }

        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $estadoTexto = $usuario->activo ? 'habilitado' : 'inhabilitado';

        return redirect()->route('admin.usuarios.index')
            ->with('mensaje', "El usuario ha sido {$estadoTexto} exitosamente.")
            ->with('icon', 'success');
    }
    // Dentro de tu UserController existente:

    public function asignarRol(User $usuario)
    {
        $roles = Role::all();
        $gestiones = GestionAcademica::where('activo', true)->get();

        // Obtener los roles que ya tiene asignados en diferentes gestiones
        $rolesAsignados = $usuario->rolesPorGestion()->with(['role', 'gestion'])->get();

        return view('admin.usuarios.asignar_rol', compact('usuario', 'roles', 'gestiones', 'rolesAsignados'));
    }

    public function guardarRol(Request $request, User $usuario)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'gestion_id' => 'required|exists:gestion_academicas,id',
        ]);

        /** @var User $currentUser */
        $currentUser = auth()->user();

        // SEGURIDAD: Si el usuario a modificar es Administrador y el actual NO es admin, bloquear
        if (!$currentUser->hasRole('Administrador') && $usuario->hasRole('Administrador')) {
            return back()->with('mensaje', 'No tienes permisos para modificar los roles de un Administrador.')->with('icon', 'error');
        }

        // SEGURIDAD: Si el rol que intenta asignar es Administrador y el actual NO es admin, bloquear
        $roleAAsignar = Role::findOrFail($request->role_id);
        if (!$currentUser->hasRole('Administrador') && $roleAAsignar->name === 'Administrador') {
            return back()->with('mensaje', 'No tienes autorización para asignar el rol de Administrador.')->with('icon', 'error');
        }

        ModelHasRoleGestion::updateOrCreate(
            [
                'model_id' => $usuario->id,
                'model_type' => User::class,
                'gestion_id' => $request->gestion_id,
            ],
            [
                'role_id' => $request->role_id,
            ]
        );

        return redirect()->route('admin.usuarios.asignar.rol', $usuario->id)
            ->with('mensaje', 'Rol y gestión académica guardados correctamente.')
            ->with('icon', 'success');
    }

    public function eliminarRolGestion(int $id)
    {
        $asignacion = ModelHasRoleGestion::findOrFail($id);
        $asignacion->delete();

        return back()
            ->with('mensaje', 'Asignación de rol removida exitosamente.')
            ->with('icon', 'success');
    }
    // Mostrar la papelera de asignaciones de roles eliminadas
    public function papeleraRoles()
    {
        $rolesEliminados = ModelHasRoleGestion::onlyTrashed()->with(['user', 'role', 'gestion'])->get();
        return view('admin.usuarios.papelera_roles', compact('rolesEliminados'));
    }

    // Restaurar una asignación de rol eliminada
    public function restaurarRol(int $id)
    {
        $asignacion = ModelHasRoleGestion::onlyTrashed()->findOrFail($id);
        $asignacion->restore();

        return redirect()->route('admin.usuarios.roles.papelera')
            ->with('mensaje', 'Asignación de rol restaurada correctamente.')
            ->with('icon', 'success');
    }
}
