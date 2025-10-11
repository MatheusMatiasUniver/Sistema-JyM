<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario' => ['required', 'string'],
            'senha' => ['required', 'string'],
        ]);

        $user = User::where('usuario', $credentials['usuario'])->first();

        if ($user && Hash::check($credentials['senha'], $user->senha)) {
            Auth::login($user);

            $request->session()->regenerate();

            return redirect()->intended('/dashboard')->with('success', 'Login realizado com sucesso!');
        }

        return back()->withErrors([
            'usuario' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('usuario');
    }

    public function showRegisterForm()
    {
        if (Auth::check() && Auth::user()->nivelAcesso === 'Administrador') {
            return view('auth.register');
        }
        return redirect()->route('login')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
    }

    public function register(Request $request)
    {
        if (!Auth::check() || Auth::user()->nivelAcesso !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
        }

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

        return redirect()->route('users.index')->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout realizado com sucesso!');
    }
}