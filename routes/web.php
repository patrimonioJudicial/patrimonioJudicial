<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\AdminPanel;
use App\Livewire\ReceptorPanel;
use App\Livewire\GestorInventario;
use App\Livewire\DataentryPanel;
use App\Livewire\ConsultorPanel;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Redirección inicial inteligente
|--------------------------------------------------------------------------
|
| Si el usuario está autenticado, lo lleva directo a su panel según el rol.
| Si no lo está, muestra la página de login.
|
*/
Route::get('/', function () {
    if (Auth::check()) {
        $rol = Auth::user()->rol?->nombre;

        return match ($rol) {
            'administrador'     => redirect()->route('admin.panel'),
            'recepcionista'     => redirect()->route('receptor.panel'),
            'gestorInventario'  => redirect()->route('gestor.panel'),
            'dataEntry'         => redirect()->route('dataentry.panel'),
            'consultor'         => redirect()->route('consultor.panel'),
            default             => redirect()->route('login'),
        };
    }

    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Rutas por rol
|--------------------------------------------------------------------------
*/
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

Route::middleware(['auth', 'rol:gestorInventario'])
    ->prefix('gestor')
    ->name('gestor.')
    ->group(function () {
        Route::get('/', GestorInventario::class)->name('panel');
    });

Route::middleware(['auth', 'rol:dataEntry'])
    ->prefix('dataentry')
    ->name('dataentry.')
    ->group(function () {
        Route::get('/', DataentryPanel::class)->name('panel');
    });

Route::middleware(['auth', 'rol:consultor'])
    ->prefix('consultor')
    ->name('consultor.')
    ->group(function () {
        Route::get('/', ConsultorPanel::class)->name('panel');
    });


/*
|--------------------------------------------------------------------------
| Perfil del usuario autenticado
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/dependencias', [DependenciaController::class, 'index'])->name('dependencias.index');
Route::get('/dependencias/importar', [DependenciaController::class, 'mostrarImportador'])->name('dependencias.importar.form');
Route::post('/dependencias/importar', [DependenciaController::class, 'procesarImportacion'])->name('dependencias.importar.procesar');


/*
|--------------------------------------------------------------------------
| Autenticación (login, registro, etc.)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.panel');
    })->name('admin.panel');
});