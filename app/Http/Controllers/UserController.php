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
        // Cambiamos el estado de activación (Habilitar / Inhabilitar)
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

        // Si ya existe un registro para este usuario en esta gestión, lo actualiza al nuevo rol.
        // Si no existe, lo crea.
        ModelHasRoleGestion::updateOrCreate(
            [
                'model_id' => $usuario->id,
                'model_type' => User::class,
                'gestion_id' => $request->gestion_id, // Busca por gestión
            ],
            [
                'role_id' => $request->role_id, // Actualiza al nuevo rol seleccionado
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
