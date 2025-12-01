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

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('usuario', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('nivel_acesso')) {
            $query->where('nivelAcesso', $request->nivel_acesso);
        }

        $sortField = $request->get('sort', 'nome');
        $sortDirection = $request->get('direction', 'asc');
        
        $allowedSorts = ['nome', 'usuario', 'email', 'nivelAcesso'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $users = $query->get();
        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->idUsuario, 'idUsuario')],
            'usuario' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->idUsuario, 'idUsuario')],
            'nivelAcesso' => ['required', 'in:Administrador,Funcionário'],
            'senha' => ['nullable', 'string', 'min:8', 'confirmed'],
            'salarioMensal' => ['nullable', 'numeric', 'min:0'],
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'usuario.required' => 'O usuário é obrigatório.',
            'usuario.unique' => 'Usuário já existente. Escolha outro nome de usuário.',
            'nivelAcesso.required' => 'O nível de acesso é obrigatório.',
            'nivelAcesso.in' => 'O nível de acesso deve ser Administrador ou Funcionário.',
            'senha.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'senha.confirmed' => 'A confirmação da senha não confere.',
            'salarioMensal.numeric' => 'O salário deve ser um número.',
            'salarioMensal.min' => 'O salário não pode ser negativo.',
        ]);

        $user->nome = $request->nome;
        $user->email = $request->email;
        $user->usuario = $request->usuario;
        $user->nivelAcesso = $request->nivelAcesso;
        $user->salarioMensal = $request->salarioMensal;

        if ($request->filled('senha')) {
            $user->senha = Hash::make($request->senha);
        }

        $user->save();

        \App\Models\ActivityLog::create([
            'usuarioId' => \Illuminate\Support\Facades\Auth::id(),
            'modulo' => 'Administrativo',
            'acao' => 'update_user',
            'entidade' => 'User',
            'entidadeId' => $user->idUsuario,
            'dados' => [
                'nivelAcesso' => $user->nivelAcesso,
                'salarioMensal' => $user->salarioMensal,
            ],
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário ' . $user->nome . ' atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->idUsuario) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        if (!$user->podeDeletar()) {
            return back()->with('error', 'Não é possível excluir este usuário pois existem clientes associados.');
        }

        try {
            $user->delete();
            \App\Models\ActivityLog::create([
                'usuarioId' => \Illuminate\Support\Facades\Auth::id(),
                'modulo' => 'Administrativo',
                'acao' => 'delete_user',
                'entidade' => 'User',
                'entidadeId' => $user->idUsuario,
                'dados' => null,
            ]);
            return redirect()->route('users.index')->with('success', 'Usuário ' . $user->nome . ' excluído com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Falha ao excluir usuário.');
        }
    }
}
