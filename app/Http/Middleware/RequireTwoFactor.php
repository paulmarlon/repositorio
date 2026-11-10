<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\ConfiguracionInstituto;

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

            // ⚠️ SIMULACIÓN TEMPORAL: Escribimos el código en los logs de Render
            // en lugar de enviar el correo por SMTP para evitar el TimeOut en producción.
            Log::info("--- CODIGO 2FA GENERADO PARA " . $user->email . " ---");
            Log::info("CODIGO: {$code}");

            /*
            // Bloque de correo original (comentado temporalmente para pruebas)
            $instituto = ConfiguracionInstituto::first();
            $fromName = $instituto->nombre ?? config('mail.from.name');
            $fromEmail = $instituto->email ?? config('mail.from.address');

            Mail::raw("Tu código de verificación de doble factor es: {$code}", function ($message) use ($user, $fromName, $fromEmail) {
                $message->to($user->email)
                    ->from($fromEmail, $fromName)
                    ->subject('Código de Autenticación 2FA');
            });
            */

            session(['auth.id' => $user->id, 'auth.remember' => session()->has('auth.remember')]);
            Auth::logout();

            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}
