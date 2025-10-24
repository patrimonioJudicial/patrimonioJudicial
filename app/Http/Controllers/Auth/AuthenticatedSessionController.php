<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra la vista de login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Maneja la autenticación del usuario.
     */
   public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();
    session()->regenerate();
    // Limpiar redirección previa
   session()->forget('url.intended');

    $user = Auth::user();
    $rol = $user->rol?->nombre;

    switch ($rol) {
        case 'administrador':
            return redirect()->route('admin.panel');

        case 'recepcionista':
            return redirect()->route('receptor.panel');

        // Para los roles que aún no tienen panel, redirigir al dashboard
        case 'dataEntry':
        case 'gestorInventario':
        case 'consultor':
        default:
            return redirect()->route('dashboard');
    }
}

    /**
     * Cierra la sesión de usuario.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }
}
