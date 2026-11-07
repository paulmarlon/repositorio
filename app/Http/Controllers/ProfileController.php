<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    // Mostrar la vista de edición del perfil
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Actualizar los datos del perfil (nombre, contraseña y avatar)
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nombres' => ['required', 'string', 'max:255'],
            'paterno' => ['nullable', 'string', 'max:255'],
            'materno' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user->nombres = $request->nombres;
        $user->paterno = $request->paterno;
        $user->materno = $request->materno;

if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // 1. Obtenemos la extensión original del archivo (ej. jpg, png)
            $extension = $request->file('avatar')->getClientOriginalExtension();

            // 2. Construimos un nombre personalizado combinando el ID del usuario, la fecha y un ID único
            // Ejemplo resultante: avatar_user_5_20261104_192000_6543abc.jpg
            $nombrePersonalizado = 'avatar_user_' . $user->id . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;

            // 3. Guardamos usando storeAs para mantener la compatibilidad agnóstica (Local/S3)
            $user->avatar = $request->file('avatar')->storeAs('avatars', $nombrePersonalizado, 'public');
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
