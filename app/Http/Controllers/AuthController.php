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

    /**
     * Processa o cadastro de novo usuário
     */
    public function register(Request $request)
    {
        if (!Auth::check() || Auth::user()->nivelAcesso !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
        }

        $validationRules = [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:150', 'unique:users,email'],
            'usuario' => ['required', 'string', 'max:50', 'unique:users,usuario'],
            'senha' => ['required', 'string', 'min:8'],
            'nivelAcesso' => ['required', 'in:Administrador,Funcionário'],
        ];

        if ($request->nivelAcesso === 'Funcionário') {
            $validationRules['idAcademia'] = ['required', 'exists:academias,idAcademia'];
        }

        $request->validate($validationRules);

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
                $academiaId = $request->idAcademia;
                $admin = Auth::user();
                
                if (!$admin->temAcessoAcademia($academiaId)) {
                    DB::rollBack();
                    Log::error("Admin {$admin->idUsuario} tentou cadastrar funcionário em academia {$academiaId} sem acesso.");
                    return back()
                        ->withInput()
                        ->withErrors(['error' => 'Você não tem acesso à academia selecionada.']);
                }
                
                $user->idAcademia = $academiaId;
                $user->save();
                Log::info("Funcionário {$user->idUsuario} vinculado à academia {$academiaId}");
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