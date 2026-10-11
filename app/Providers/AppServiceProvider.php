<?php

namespace App\Providers;

use App\Models\ConfiguracionInstituto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        View::composer('*', function ($view) {
            $instituto = null;

            if (Schema::hasTable('configuracion_institutos')) {
                $instituto = ConfiguracionInstituto::first();
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
        // Gate para Administrador global (robusta)
        Gate::define('ver-admin', function (User $user) {
            return $user->hasRole('Administrador') || $user->hasRole('administrador') || $user->id === 1;
        });
    }
}
