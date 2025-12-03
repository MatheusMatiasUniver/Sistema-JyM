<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ContaPagar;
use App\Models\ContaPagarCategoria;
use App\Models\AjusteSistema;
use App\Models\ActivityLog;
use App\Http\Requests\StoreUserRequest;

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
    public function register(StoreUserRequest $request)
    {
        if (!Auth::check() || Auth::user()->nivelAcesso !== 'Administrador') {
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Apenas administradores podem cadastrar novos usuários.');
        }

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
                $user->salarioMensal = $request->salarioMensal;
                $user->save();
                Log::info("Funcionário {$user->idUsuario} vinculado à academia {$academiaId}");

                if ($request->salarioMensal && $request->salarioMensal > 0) {
                    $categoriaSalarios = ContaPagarCategoria::firstOrCreate([
                        'idAcademia' => $academiaId,
                        'nome' => 'Salários',
                    ], ['ativa' => true]);

                    $ajuste = AjusteSistema::obterOuCriarParaAcademia($academiaId);
                    $hoje = now();
                    $anoMes = $hoje->format('Y-m');
                    $dia = min(max((int)$ajuste->diaVencimentoSalarios, 1), 31);
                    $vencimento = $hoje->copy()->startOfMonth()->setDay($dia);
                    
                    if ($vencimento->lt($hoje)) {
                        $vencimento = $vencimento->addMonth();
                    }

                    $descricao = 'Salário funcionário - ' . $user->nome;

                    ContaPagar::create([
                        'idAcademia' => $academiaId,
                        'idFornecedor' => null,
                        'idFuncionario' => $user->idUsuario,
                        'idCategoriaContaPagar' => $categoriaSalarios->idCategoriaContaPagar,
                        'documentoRef' => null,
                        'descricao' => $descricao,
                        'valorTotal' => $request->salarioMensal,
                        'status' => 'aberta',
                        'dataVencimento' => $vencimento,
                        'dataPagamento' => null,
                        'formaPagamento' => null,
                    ]);

                    Log::info("Conta a pagar de salário criada para funcionário {$user->idUsuario}");
                }
            }

            ActivityLog::create([
                'usuarioId' => Auth::id(),
                'modulo' => 'Usuários',
                'acao' => 'criar',
                'entidade' => 'User',
                'entidadeId' => $user->idUsuario,
                'dados' => [
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'usuario' => $user->usuario,
                    'nivelAcesso' => $user->nivelAcesso,
                ],
            ]);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'Usuário cadastrado com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cadastrar usuário', ['error' => $e->getMessage()]);
            Log::error('Stack trace', ['trace' => $e->getTraceAsString()]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Falha ao cadastrar usuário.']);
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