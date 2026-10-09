<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && session('2fa_pending', true)) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if (!$user) {
                return $next($request);
            }

            // Generar código de 6 dígitos
            $code = rand(100000, 999999);
            $user->two_factor_code = $code;
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();

            Mail::raw("Tu código de verificación de doble factor es: {$code}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Código de Autenticación 2FA');
            });

            session(['auth.id' => $user->id, 'auth.remember' => session()->has('auth.remember')]);
            Auth::logout();

            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}
