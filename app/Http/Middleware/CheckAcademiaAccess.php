<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAcademiaAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        // Administradores têm acesso a todas as academias
        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($request->route('academia')) {
            $academiaId = $request->route('academia')->idAcademia;
            
            if ($user->idAcademia !== $academiaId) {
                abort(403, 'Acesso negado a esta academia.');
            }
        }
        
        if ($academiaId && !$user->temAcessoAcademia($academiaId)) {
            return redirect('/dashboard')->with('error', 'Você não tem acesso a esta academia.');
        }

        return $next($request);
    }
}
