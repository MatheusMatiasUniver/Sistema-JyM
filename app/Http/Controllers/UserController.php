<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; 

class UserController extends Controller
{    
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Exibe uma listagem de usuários.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Mostra o formulário para editar um usuário específico.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Atualiza o usuário especificado no armazenamento.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->idUsuario, 'idUsuario')],
            'usuario' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->idUsuario, 'idUsuario')],
            'nivelAcesso' => ['required', 'in:Administrador,Funcionario'],
            'senha' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->nome = $request->nome;
        $user->email = $request->email;
        $user->usuario = $request->usuario;
        $user->nivelAcesso = $request->nivelAcesso;

        if ($request->filled('senha')) {
            $user->senha = Hash::make($request->senha);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuário ' . $user->nome . ' atualizado com sucesso!');
    }

    /**
     * Remove o usuário especificado do armazenamento.
     */
    public function destroy(User $user)
    {
        // Proteção extra: não permitir que um admin exclua a si mesmo
        if (Auth::id() === $user->idUsuario) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário ' . $user->nome . ' excluído com sucesso!');
    }
}