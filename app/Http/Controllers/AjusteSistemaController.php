<?php

namespace App\Http\Controllers;

use App\Models\AjusteSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AjusteSistemaController extends Controller
{
    public function index(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $ajuste = AjusteSistema::obterOuCriarParaAcademia($academiaId);
        $formasPagamentoDisponiveis = AjusteSistema::FORMAS_PAGAMENTO_PADRAO;

        return view('ajustes.index', compact('ajuste', 'formasPagamentoDisponiveis'));
    }

    public function update(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }

        $dados = $request->validate([
            'diaVencimentoSalarios' => 'required|integer|min:1|max:31',
            'clienteOpcionalVenda' => 'nullable|boolean',
            'permitirEdicaoManualEstoque' => 'nullable|boolean',
            'formasPagamentoAceitas' => ['required', 'array', 'min:1'],
            'formasPagamentoAceitas.*' => ['string', Rule::in(AjusteSistema::FORMAS_PAGAMENTO_PADRAO)],
        ], [
            'diaVencimentoSalarios.required' => 'O dia de vencimento dos salários é obrigatório.',
            'diaVencimentoSalarios.integer' => 'O dia de vencimento deve ser um número.',
            'diaVencimentoSalarios.min' => 'O dia de vencimento deve ser entre 1 e 31.',
            'diaVencimentoSalarios.max' => 'O dia de vencimento deve ser entre 1 e 31.',
            'formasPagamentoAceitas.required' => 'Selecione pelo menos uma forma de pagamento.',
            'formasPagamentoAceitas.min' => 'Selecione pelo menos uma forma de pagamento.',
        ]);

        $ajuste = AjusteSistema::obterOuCriarParaAcademia($academiaId);

        $ajuste->diaVencimentoSalarios = $dados['diaVencimentoSalarios'];
        $ajuste->clienteOpcionalVenda = $request->boolean('clienteOpcionalVenda');
        $ajuste->permitirEdicaoManualEstoque = $request->boolean('permitirEdicaoManualEstoque');
        $ajuste->formasPagamentoAceitas = array_values($dados['formasPagamentoAceitas']);
        $ajuste->save();
                'formasPagamentoAceitas' => $ajuste->formasPagamentoAtivas,
            ],
        ]);

        return redirect()->route('ajustes.index')->with('success', 'Ajustes atualizados');
    }
}
