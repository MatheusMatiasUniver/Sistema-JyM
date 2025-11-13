<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Mensalidade;
use App\Models\Cliente;
use App\Services\PlanoAssinaturaService;

class MensalidadeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'funcionario']);
    }

    /**
     * Registra o pagamento de uma mensalidade.
     */
    public function pagar(Request $request, Mensalidade $mensalidade, PlanoAssinaturaService $planoService)
    {
        $academiaId = session('academia_selecionada');
        if (!$academiaId || $mensalidade->idAcademia != $academiaId) {
            return back()->with('error', 'Mensalidade não pertence à academia selecionada.');
        }

        // Validar forma de pagamento
        $request->validate([
            'formaPagamento' => 'required|in:Dinheiro,Cartão de Crédito,Cartão de Débito,PIX,Boleto',
            'dataPagamento' => 'nullable|date',
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