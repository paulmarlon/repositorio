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

            // Obtenemos la configuración institucional de la base de datos
            $instituto = ConfiguracionInstituto::first();
            $fromName = $instituto->nombre ?? config('mail.from.name');
            $fromEmail = $instituto->email ?? config('mail.from.address');

            try {
                // Envío real del correo electrónico
                Mail::raw("Tu código de verificación de doble factor es: {$code}", function ($message) use ($user, $fromName, $fromEmail) {
                    $message->to($user->email)
                        ->from($fromEmail, $fromName)
                        ->subject('Código de Autenticación 2FA');
                });
            } catch (\Exception $e) {
                // Si ocurre un error de conexión con el servidor SMTP, lo guardamos en los logs para depuración
                Log::error("Error al enviar correo 2FA: " . $e->getMessage());
            }

            session(['auth.id' => $user->id, 'auth.remember' => session()->has('auth.remember')]);
            Auth::logout();

            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}
