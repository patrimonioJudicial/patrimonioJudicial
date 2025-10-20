<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DependenciaController;
use App\Http\Controllers\CuentaController;
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/dependencias', [DependenciaController::class, 'index'])->name('dependencias.index');
Route::get('/dependencias/importar', [DependenciaController::class, 'mostrarImportador'])->name('dependencias.importar.form');
Route::post('/dependencias/importar', [DependenciaController::class, 'procesarImportacion'])->name('dependencias.importar.procesar');

Route::get('/importar-cuentas', [CuentaController::class, 'importar'])->name('cuentas.importar');