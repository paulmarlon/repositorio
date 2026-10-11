<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ConfiguracionInstitutoController;
use App\Http\Controllers\TipoTrabajoController;
use App\Http\Controllers\GestionAcademicaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\RequireTwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Two-Factor Authentication Challenges
Route::get('/two-factor-challenge', [TwoFactorController::class, 'create'])->name('two-factor.login');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'store'])->name('two-factor.verify');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Requires 2FA & Active Session)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RequireTwoFactor::class])->group(function () {

    // Dashboard / Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // User Profile Management
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });

    // Administrative Panel
    Route::prefix('admin')->name('admin.')->group(function () {

        // Institute Configuration
        Route::get('/configuracion', [ConfiguracionInstitutoController::class, 'index'])->name('configuracion.index');
        Route::put('/configuracion', [ConfiguracionInstitutoController::class, 'update'])->name('configuracion.update');

        // Gestión Académica
        Route::get('gestiones/papelera', [GestionAcademicaController::class, 'trash'])->name('gestiones.papelera');
        Route::post('gestiones/{id}/restaurar', [GestionAcademicaController::class, 'restore'])->name('gestiones.restaurar');
        Route::resource('gestiones', GestionAcademicaController::class)->parameters(['gestiones' => 'gestion']);

        // Tipos de Trabajo
        Route::get('tipo-trabajos/papelera', [TipoTrabajoController::class, 'trash'])->name('tipo-trabajos.papelera');
        Route::post('tipo-trabajos/{id}/restaurar', [TipoTrabajoController::class, 'restore'])->name('tipo-trabajos.restaurar');
        Route::resource('tipo-trabajos', TipoTrabajoController::class)->parameters(['tipo-trabajos' => 'tipoTrabajo']);

        // Categorías
        Route::get('categorias/papelera', [CategoriaController::class, 'trash'])->name('categorias.papelera');
        Route::post('categorias/{id}/restaurar', [CategoriaController::class, 'restore'])->name('categorias.restaurar');
        Route::resource('categorias', CategoriaController::class);

        // Usuarios y Asignación de Roles por Gestión
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::patch('/{usuario}/toggle', [UserController::class, 'toggleActive'])->name('toggle');
            Route::get('/{usuario}/roles', [UserController::class, 'asignarRol'])->name('asignar.rol');
            Route::post('/{usuario}/roles', [UserController::class, 'guardarRol'])->name('guardar.rol');
            Route::delete('/roles/{id}', [UserController::class, 'eliminarRolGestion'])->name('eliminar.rol');

            // CORREGIDO AQUÍ (sin repetir la palabra usuarios)
            Route::get('roles/papelera', [UserController::class, 'papeleraRoles'])->name('roles.papelera');
            Route::post('roles/{id}/restaurar', [UserController::class, 'restaurarRol'])->name('roles.restaurar');
        });
    });
});
