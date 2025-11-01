<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FirebaseAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Más adelante aquí validaremos el ID Token de Firebase.
        return $next($request);
    }
}
