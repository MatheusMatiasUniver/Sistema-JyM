<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa o login do usuário
     */
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

    /**
     * Exibe o formulário de cadastro de usuário
     */
    public function showRegisterForm()
    {
        if (Auth::check() && Auth::user()->nivelAcesso === 'Administrador') {
            return view('auth.register');
        }
        return redirect()->route('login')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
    }

    /**
     * Processa o cadastro de novo usuário
     */
    public function register(Request $request)
    {
        // Verifica se é administrador
        if (!Auth::check() || Auth::user()->nivelAcesso !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
        }

        $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150', 'unique:users,email'],
            'usuario' => ['required', 'string', 'max:50', 'unique:users,usuario'],
            'senha' => ['required', 'string', 'min:8'],
            'nivelAcesso' => ['required', 'in:Administrador,Funcionário'],
        ]);

        DB::beginTransaction();
        
        try {
            $user = User::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'usuario' => $request->usuario,
                'senha' => Hash::make($request->senha),
                'nivelAcesso' => $request->nivelAcesso,
            ]);

            Log::info("Usuário criado: ID {$user->idUsuario}, Nível: {$user->nivelAcesso}");

            if ($request->nivelAcesso === 'Administrador') {
                Log::info("Novo usuário Administrador {$user->idUsuario} criado.");
            }
            elseif ($request->nivelAcesso === 'Funcionário') {
                $admin = Auth::user();
                if ($admin->idAcademia) {
                    $user->idAcademia = $admin->idAcademia;
                    $user->save();
                    Log::info("Funcionário {$user->idUsuario} vinculado à academia {$admin->idAcademia}");
                } else {
                    DB::rollBack();
                    Log::error("Admin {$admin->idUsuario} sem academia definida não pode cadastrar funcionário.");
                    return back()
                        ->withInput()
                        ->withErrors(['error' => 'Você não está associado a uma academia, portanto não pode cadastrar funcionários.']);
                }
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'Usuário cadastrado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar usuário: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao cadastrar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Processa o logout do usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logout realizado com sucesso!');
    }
}