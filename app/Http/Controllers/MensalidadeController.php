<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Mensalidade;
use App\Models\Cliente;
use App\Services\PlanoAssinaturaService;
use App\Models\AjusteSistema;
use Illuminate\Validation\Rule;

class MensalidadeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'funcionario']);
    }
    
    public function pagar(Request $request, Mensalidade $mensalidade, PlanoAssinaturaService $planoService)
    {
        $academiaId = session('academia_selecionada');
        if (!(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isAdministrador())) {
            if (!$academiaId || $mensalidade->idAcademia != $academiaId) {
                return back()->with('error', 'Mensalidade não pertence à academia selecionada.');
            }
        }

        $contextAcademiaId = $mensalidade->idAcademia ?? $academiaId;
        $formasPagamentoAtivas = AjusteSistema::formasPagamentoParaAcademia($contextAcademiaId ? (int) $contextAcademiaId : null);

        $request->validate([
            'formaPagamento' => ['required', Rule::in($formasPagamentoAtivas)],
            'dataPagamento' => 'nullable|date',
        ], [
            'formaPagamento.required' => 'A forma de pagamento é obrigatória.',
            'formaPagamento.in' => 'A forma de pagamento selecionada não é válida.',
            'dataPagamento.date' => 'A data de pagamento deve ser uma data válida.',
        ]);

        DB::beginTransaction();
        try {
            $mensalidade->status = 'Paga';
            $mensalidade->dataPagamento = $request->input('dataPagamento', now());
            $mensalidade->formaPagamento = $request->formaPagamento;
            $mensalidade->save();

            $cliente = Cliente::find($mensalidade->idCliente);
            if ($cliente && $planoService->podeAtivarCliente($cliente)) {
                $cliente->status = 'Ativo';
                $cliente->save();
            }

            $recebimentoData = $mensalidade->dataPagamento ?? now();
            $updated = DB::table('contas_receber')
                ->where('idAcademia', $mensalidade->idAcademia)
                ->where('documentoRef', $mensalidade->idMensalidade)
                ->where('status', 'aberta')
                ->update([
                    'status' => 'recebida',
                    'dataRecebimento' => $recebimentoData,
                    'formaRecebimento' => $mensalidade->formaPagamento,
                ]);

            if ($updated === 0) {
                DB::table('contas_receber')->insert([
                    'idAcademia' => $mensalidade->idAcademia,
                    'idCliente' => $mensalidade->idCliente,
                    'documentoRef' => $mensalidade->idMensalidade,
                    'descricao' => 'Mensalidade Cliente #'.$mensalidade->idCliente,
                    'valorTotal' => $mensalidade->valor,
                    'status' => 'recebida',
                    'dataVencimento' => $mensalidade->dataVencimento,
                    'dataRecebimento' => $recebimentoData,
                    'formaRecebimento' => $mensalidade->formaPagamento,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Pagamento registrado com sucesso.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar pagamento de mensalidade: ' . $e->getMessage(), [
                'mensalidade' => $mensalidade->idMensalidade,
            ]);
            return back()->with('error', 'Erro ao registrar pagamento: ' . $e->getMessage());
        }
    }
}
