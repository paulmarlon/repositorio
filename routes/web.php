<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ConfiguracionInstitutoController;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RequireTwoFactor;
use App\Http\Controllers\ProfileController;


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
});

Route::get('/two-factor-challenge', [TwoFactorController::class, 'create'])->name('two-factor.login');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'store'])->name('two-factor.verify');
