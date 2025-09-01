<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Importe o Model User
use Illuminate\Support\Facades\Auth; // Para verificar o nível de acesso

class UserController extends Controller
{
    /**
     * Exibe uma listagem de usuários.
     * Apenas administradores podem acessar esta função.
     */
    public function index()
    {
        // Proteção de rota: Garante que apenas administradores possam ver esta página
        if (!Auth::check() || Auth::user()->nivelAcesso !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem gerenciar usuários.');
        }

        // Obtém todos os usuários do banco de dados
        $users = User::all(); // Pode adicionar orderBy('nome') ou filtros aqui

        // Retorna a view 'users.index' e passa a variável $users para ela
        return view('users.index', compact('users'));
    }

    // Futuramente, você pode adicionar métodos para editar, excluir, etc., usuários aqui.
}