<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\AdminPanel;
use App\Livewire\ReceptorPanel;
use App\Http\Controllers\ProfileController;

// Página principal (pública)
Route::get('/', fn() => view('welcome'));

// Dashboard para usuarios comunes (rol por defecto)
Route::middleware(['auth', 'verified'])
    ->get('/dashboard', fn() => view('dashboard'))
    ->name('dashboard');

Route::middleware(['auth', 'rol:administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', AdminPanel::class)->name('panel');
    });

Route::middleware(['auth', 'rol:recepcionista'])
    ->prefix('receptor')
    ->name('receptor.')
    ->group(function () {
        Route::get('/', ReceptorPanel::class)->name('panel');
    });

// PERFIL DE USUARIO
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Auth (login, registro, etc.)
require __DIR__.'/auth.php';