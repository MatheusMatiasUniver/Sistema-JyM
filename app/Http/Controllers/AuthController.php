<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Para hashear e verificar senhas
use App\Models\User; // Seu Model User (Usuario no DB)

class AuthController extends Controller
{
    // Exibe o formulário de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Processa a tentativa de login
    public function login(Request $request)
    {
        // 1. Validação agora usa 'usuario' em vez de 'email'
        $credentials = $request->validate([
            'usuario' => ['required', 'string'], // Campo 'usuario' para login
            'senha' => ['required', 'string'], // Campo 'senha' do formulário
        ]);

        // 2. Busca o usuário pelo campo 'usuario'
        $user = User::where('usuario', $credentials['usuario'])->first();

        // Se o usuário existir E a senha hasheada coincidir
        if ($user && Hash::check($credentials['senha'], $user->senha)) {
            Auth::login($user); // Faz o login do usuário encontrado

            // Regenera a sessão para evitar ataques de fixação de sessão
            $request->session()->regenerate();

            // Redireciona para o dashboard após o login bem-sucedido
            return redirect()->intended('/dashboard')->with('success', 'Login realizado com sucesso!');
        }

        // Se a autenticação falhar, retorna com erro
        return back()->withErrors([
            'usuario' => 'As credenciais fornecidas não correspondem aos nossos registros.', // Mensagem de erro para 'usuario'
        ])->onlyInput('usuario'); // Mantém o 'usuario' preenchido
    }

    // Exibe o formulário de registro
    public function showRegisterForm()
    {
        // 3. Proteção para acesso à página de registro: Apenas administradores logados
        if (Auth::check() && Auth::user()->nivelAcesso === 'Administrador') {
            return view('auth.register');
        }
        // Se não for admin ou não estiver logado, redireciona com mensagem de erro
        return redirect()->route('login')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
    }

    // Processa o registro de um novo usuário
    public function register(Request $request)
    {
        // Proteção: Apenas administradores logados podem realizar o registro
        if (!Auth::check() || Auth::user()->nivelAcesso !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
        }

        // ... (validação e criação do usuário, como já está) ...
        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'usuario' => ['required', 'string', 'max:50', 'unique:'.User::class.',usuario'],
            'senha' => ['required', 'string', 'min:8'],
            'nivelAcesso' => ['required', 'in:Administrador,Funcionario'],
        ]);

        User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'usuario' => $request->usuario,
            'senha' => Hash::make($request->senha),
            'nivelAcesso' => $request->nivelAcesso,
        ]);

        // --- MUDANÇA NO REDIRECIONAMENTO AQUI ---
        // Redireciona para a página de listagem de usuários com mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    // Realiza o logout do usuário (sem alterações)
    public function logout(Request $request)
    {
        Auth::logout(); // Desloga o usuário

        $request->session()->invalidate(); // Invalida a sessão
        $request->session()->regenerateToken(); // Regenera o token CSRF

        return redirect('/')->with('success', 'Logout realizado com sucesso!');
    }
}