<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\ConfiguracionInstituto;

class RequireTwoFactor
{
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario está autenticado y su sesión tiene pendiente el 2FA
        if (Auth::check() && session('2fa_pending', false)) {

            // Si ya está intentando entrar a la ruta del formulario 2FA, déjalo pasar para que escriba su código
            if ($request->routeIs('two-factor.login') || $request->routeIs('two-factor.verify')) {
                return $next($request);
            }

            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            if ($user) {
                // Verificamos si ya se le generó un código recientemente para no spamear correos en cada recarga
                if (!$user->two_factor_code || now()->greaterThan($user->two_factor_expires_at)) {

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
                        Mail::raw("Tu código de verificación de doble factor es: {$code}", function ($message) use ($user, $fromName, $fromEmail) {
                            $message->to($user->email)
                                ->from($fromEmail, $fromName)
                                ->subject('Código de Autenticación 2FA');
                        });
                    } catch (\Exception $e) {
                        Log::error("Error al enviar correo 2FA: " . $e->getMessage());
                    }
                }
            }

            // Redirigimos al formulario para que ingrese el código de 6 dígitos
            return redirect()->route('two-factor.login');
        }

        return $next($request);
    }
}
