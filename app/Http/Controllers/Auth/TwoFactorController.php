<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class TwoFactorController extends Controller
{
    public function create()
    {
        $authId = Session::get('auth.id');

        if (!$authId) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => ['required', 'string'],
        ]);

        /** @var int|string|null $authId */
        $authId = Session::get('auth.id');

        if (!$authId) {
            return redirect()->route('login')->withErrors(['email' => 'La sesión ha expirado. Por favor, inicia sesión de nuevo.']);
        }

        /** @var User|null $user */
        $user = User::find($authId);

        if (!$user) {
            Session::forget('auth.id');
            return redirect()->route('login')->withErrors(['email' => 'No se encontró el usuario asociado a esta sesión.']);
        }

        if (!$user->two_factor_code || $user->two_factor_code !== $request->input('two_factor_code') || now()->greaterThan($user->two_factor_expires_at)) {
            return back()->withErrors(['two_factor_code' => 'El código de verificación es inválido o ha expirado.']);
        }

        // Limpiar códigos temporales
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // Autenticar al usuario oficialmente
        Auth::login($user);

        // Actualizar variables de sesión usando la fachada Session
        Session::put('2fa_pending', false);
        Session::forget('auth.id');
        Session::forget('auth.remember');

        return redirect()->intended(route('home'));
    }
}
