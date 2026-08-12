<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GerenteMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('funcionario')->user()?->perfil !== 'GERENTE') {
            abort(403, 'Acesso restrito a gerentes.');
        }

        return $next($request);
    }
}
