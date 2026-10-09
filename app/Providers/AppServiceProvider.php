<?php

namespace App\Providers;

use App\Models\ConfiguracionInstituto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
    }
}
