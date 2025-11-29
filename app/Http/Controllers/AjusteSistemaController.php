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
        ]);

        $ajuste = AjusteSistema::obterOuCriarParaAcademia($academiaId);

        $ajuste->diaVencimentoSalarios = $dados['diaVencimentoSalarios'];
        $ajuste->clienteOpcionalVenda = $request->boolean('clienteOpcionalVenda');
        $ajuste->permitirEdicaoManualEstoque = $request->boolean('permitirEdicaoManualEstoque');
        $ajuste->formasPagamentoAceitas = array_values($dados['formasPagamentoAceitas']);
        $ajuste->save();

        \App\Models\ActivityLog::create([
            'usuarioId' => \Illuminate\Support\Facades\Auth::id(),
            'modulo' => 'AjustesSistema',
            'acao' => 'update',
            'entidade' => 'AjusteSistema',
            'entidadeId' => null,
            'dados' => [
                'idAcademia' => $academiaId,
                'diaVencimentoSalarios' => $ajuste->diaVencimentoSalarios,
                'clienteOpcionalVenda' => $ajuste->clienteOpcionalVenda,
                'permitirEdicaoManualEstoque' => $ajuste->permitirEdicaoManualEstoque,
                'formasPagamentoAceitas' => $ajuste->formasPagamentoAtivas,
            ],
        ]);

        return redirect()->route('ajustes.index')->with('success', 'Ajustes atualizados');
    }
}
