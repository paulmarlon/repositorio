<?php

namespace App\Providers;

use App\Models\ConfiguracionInstituto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\User;
// 1. Agregamos estas dos importaciones para el Listener
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        // 2. Registramos el evento de inicio de sesión aquí
        Event::listen(
            Login::class,
            LogSuccessfulLogin::class
        );

        View::composer('*', function ($view) {
            $instituto = null;

            if (Schema::hasTable('configuracion_institutos')) {
                $instituto = ConfiguracionInstituto::first();
            }

            if ($instituto) {
                if ($instituto->nombre_instituto) {
                    config(['adminlte.logo' => '<b>' . e($instituto->nombre_instituto) . '</b>']);
                }
                if ($instituto->logo) {
                    config(['adminlte.logo_img' => 'storage/' . $instituto->logo]);
                }
            }

            $view->with('instituto', $instituto);
        });

        // Gate para Supervisor
        Gate::define('ver-supervisor', function (User $user) {
            return $user->hasRoleInActiveGestion('Supervisor');
        });

        // Gate para Estudiante / Usuario
        Gate::define('ver-estudiante', function (User $user) {
            return $user->hasRoleInActiveGestion('Usuario');
        });

        // Gate para Administrador global
        Gate::define('ver-admin', function (User $user) {
            return $user->hasRole('Administrador') || $user->hasRole('administrador') || $user->id === 1;
        });

        // Gate para Administrar Usuarios (Administrador global, ID 1 o Supervisor en gestión activa)
        Gate::define('ver-usuarios', function (User $user) {
            return $user->id === 1 || $user->hasRoleInActiveGestion('Administrador') || $user->hasRoleInActiveGestion('Supervisor');
        });
    }
}
