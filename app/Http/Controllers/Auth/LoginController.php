<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // Añadimos la condición de que el usuario debe estar activo (habilitado por el supervisor)
        return array_merge($request->only($this->username(), 'password'), ['activo' => true]);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // 1. Generar código de 6 dígitos
        $code = rand(100000, 999999);
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // 2. Obtener la configuración institucional para el remitente
        $instituto = \App\Models\ConfiguracionInstituto::first();
        $fromName = $instituto->nombre ?? config('mail.from.name');
        $fromEmail = $instituto->email ?? config('mail.from.address');

        // 3. Enviar el correo con Brevo
        try {
            \Illuminate\Support\Facades\Mail::raw("Tu código de verificación de doble factor es: {$code}", function ($message) use ($user, $fromName, $fromEmail) {
                $message->to($user->email)
                    ->from($fromEmail, $fromName)
                    ->subject('Código de Autenticación 2FA');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error al enviar correo 2FA: " . $e->getMessage());
        }

        // 4. Marcar la sesión y guardar el ID temporal
        session(['2fa_pending' => true]);
        session(['auth.id' => $user->id]);

        // 5. Cerrar sesión temporalmente para exigir el código
        Auth::logout();

        // 6. Redirigir a la vista del desafío 2FA
        return redirect()->route('two-factor.login');
    }
}
