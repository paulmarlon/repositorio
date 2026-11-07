<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // <-- 1. Importante: Importar Str

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

            // 2. Generar un nombre personalizado y legible para el avatar
            $extension = $request->file('avatar')->getClientOriginalExtension();
            $nombreLimpio = Str::slug("{$request->nombres} {$request->paterno}");
            $nombrePersonalizado = "avatar_{$user->id}_{$nombreLimpio}_" . date('Ymd_His') . ".{$extension}";

            // 3. Guardar usando storeAs en la carpeta avatars
            $user->avatar = $request->file('avatar')->storeAs('avatars', $nombrePersonalizado, 'public');
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
