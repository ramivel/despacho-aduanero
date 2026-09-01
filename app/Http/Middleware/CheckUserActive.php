<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user && ! $user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()
                ->to('/admin/login')
                ->with('auth_error', 'Su sesión ya no es válida. Por favor, intente autenticarse nuevamente.');
        }
        return $next($request);
    }
}
