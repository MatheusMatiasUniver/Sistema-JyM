<?php

namespace App\Http\Controllers;

use App\Models\ContaPagar;
use App\Models\AjusteSistema;
use App\Models\User;
use App\Models\ContaPagarCategoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ContaPagarController extends Controller
{
    public function index(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        $ajuste = $academiaId ? AjusteSistema::where('idAcademia', $academiaId)->first() : null;
        if ($ajuste) {
            $categoriaSalarios = ContaPagarCategoria::firstOrCreate([
                'idAcademia' => $academiaId,
                'nome' => 'Salários',
            ], ['ativa' => true]);

            $hoje = now();
            $anoMes = $hoje->format('Y-m');
            $dia = min(max((int)$ajuste->diaVencimentoSalarios, 1), 31);
            $vencimento = $hoje->copy()->startOfMonth()->setDay($dia);

            $funcionarios = User::where('nivelAcesso', 'Funcionário')
                ->when($academiaId, function ($q) use ($academiaId) { $q->where('idAcademia', $academiaId); })
                ->whereNotNull('salarioMensal')
                ->where('salarioMensal', '>', 0)
                ->get();

            foreach ($funcionarios as $func) {
                $descricao = 'Salário '.$func->nome.' '.$anoMes;
                $existe = ContaPagar::where('descricao', $descricao)->exists();
                if (!$existe) {
                    \Illuminate\Support\Facades\DB::table('contas_pagar')->insert([
                        'idAcademia' => $academiaId,
                        'idFornecedor' => null,
                        'idFuncionario' => $func->idUsuario,
                        'idCategoriaContaPagar' => $categoriaSalarios->idCategoriaContaPagar,
                        'documentoRef' => null,
                        'descricao' => $descricao,
                        'valorTotal' => $func->salarioMensal,
                        'status' => 'aberta',
                        'dataVencimento' => $vencimento,
                        'dataPagamento' => null,
                        'formaPagamento' => null,
                    ]);
                }
            }
        }

        $query = ContaPagar::with('fornecedor');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('data_inicial')) {
            $query->whereDate('dataVencimento', '>=', $request->data_inicial);
        }
        if ($request->filled('data_final')) {
            $query->whereDate('dataVencimento', '<=', $request->data_final);
        }
        $contas = $query->orderBy('dataVencimento')->paginate(20);
        return view('financeiro.contas_pagar.index', compact('contas'));
    }

    public function pagar(Request $request, ContaPagar $conta)
    {
        if ($conta->status !== 'aberta') {
            return back()->with('error', 'Conta não está aberta para faturamento');
        }

        $dados = $request->validate([
            'formaPagamento' => 'required|string|in:Dinheiro,Cartão de Crédito,Cartão de Débito,PIX,Boleto',
            'dataPagamento' => 'nullable|date',
        ]);

        $conta->formaPagamento = $dados['formaPagamento'];
        $conta->dataPagamento = $dados['dataPagamento'] ?? now();
        $conta->status = 'paga';
        $conta->save();

        \App\Models\ActivityLog::create([
            'usuarioId' => \Illuminate\Support\Facades\Auth::id(),
            'modulo' => 'Financeiro',
            'acao' => 'pagar_conta',
            'entidade' => 'ContaPagar',
            'entidadeId' => $conta->idContaPagar,
            'dados' => [
                'formaPagamento' => $conta->formaPagamento,
                'dataPagamento' => $conta->dataPagamento,
                'valorTotal' => $conta->valorTotal,
            ],
        ]);

        return back()->with('success', 'Conta faturada com sucesso');
    }
}

