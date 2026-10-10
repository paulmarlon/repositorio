<?php

use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ConfiguracionInstitutoController;
use App\Http\Controllers\TipoTrabajoController;
use App\Http\Controllers\GestionAcademicaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RequireTwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth', RequireTwoFactor::class])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/configuracion', [ConfiguracionInstitutoController::class, 'index'])->name('configuracion.index');
    Route::put('/configuracion', [ConfiguracionInstitutoController::class, 'update'])->name('configuracion.update');

    // Gestión Académica
    Route::prefix('admin')->group(function () {
        Route::get('gestiones/papelera', [GestionAcademicaController::class, 'trash'])->name('admin.gestiones.papelera');
        Route::post('gestiones/{id}/restaurar', [GestionAcademicaController::class, 'restore'])->name('admin.gestiones.restaurar');
        Route::resource('gestiones', GestionAcademicaController::class)
            ->parameters(['gestiones' => 'gestion'])
            ->names('admin.gestiones');
    });
    // Tipos de Trabajo
    Route::prefix('admin')->group(function () {
        Route::get('tipo-trabajos/papelera', [TipoTrabajoController::class, 'trash'])->name('admin.tipo-trabajos.papelera');
        Route::post('tipo-trabajos/{id}/restaurar', [TipoTrabajoController::class, 'restore'])->name('admin.tipo-trabajos.restaurar');
        Route::resource('tipo-trabajos', TipoTrabajoController::class)
            ->parameters(['tipo-trabajos' => 'tipoTrabajo'])
            ->names('admin.tipo-trabajos');
    });
    // Rutas para Categorías
    Route::get('categorias/papelera', [CategoriaController::class, 'trash'])->name('admin.categorias.papelera');
    Route::post('categorias/{id}/restaurar', [CategoriaController::class, 'restore'])->name('admin.categorias.restaurar');
    Route::resource('categorias', CategoriaController::class)->names('admin.categorias');
});

Route::get('/two-factor-challenge', [TwoFactorController::class, 'create'])->name('two-factor.login');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'store'])->name('two-factor.verify');
