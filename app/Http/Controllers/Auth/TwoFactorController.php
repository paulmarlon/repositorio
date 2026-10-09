<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function create()
    {
        if (!session()->has('auth.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request)
    {
        $request->validate([
            'two_factor_code' => ['required', 'string'],
        ]);

        $userId = session('auth.id');
        if (!$userId) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User|null $user */
        $user = \App\Models\User::find($userId);

        if (!$user || $user->two_factor_code !== $request->two_factor_code || now()->greaterThan($user->two_factor_expires_at)) {
            return back()->withErrors(['two_factor_code' => 'El código de verificación es inválido o ha expirado.']);
        }

        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        Auth::login($user, session('auth.remember', false));

        // Marcar el 2FA como superado en esta sesión y limpiar credenciales temporales
        session(['2fa_pending' => false]);
        session()->forget(['auth.id', 'auth.remember']);

        return redirect()->intended(route('home'));
    }
}
