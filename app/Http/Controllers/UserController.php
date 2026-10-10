<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::all();
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
}
