<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AcademiaContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->isAdministrador()) {
                if (!session()->has('academia_selecionada')) {
                    $primeiraAcademia = $user->academias()->first();
                    if ($primeiraAcademia) {
                        session(['academia_selecionada' => $primeiraAcademia->idAcademia]);
                    }
                }
                
                $academiaId = session('academia_selecionada');
                
                if ($academiaId && !$user->temAcessoAcademia($academiaId)) {
                    abort(403, 'Você não tem acesso a esta academia.');
                }
                
                config(['app.academia_atual' => $academiaId]);
                
            } elseif ($user->isFuncionario()) {
                if (!$user->idAcademia) {
                    abort(403, 'Funcionário sem academia vinculada.');
                }
                
                config(['app.academia_atual' => $user->idAcademia]);
                session(['academia_selecionada' => $user->idAcademia]);
            }
        }
        
        return $next($request);
    }
}