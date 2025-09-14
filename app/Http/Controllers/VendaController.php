<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\VendaService;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\VendaProduto;
use App\Http\Requests\StoreVendaRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VendaController extends Controller
{
    protected $vendaService;

    public function __construct(VendaService $vendaService)
    {
        $this->vendaService = $vendaService;
    }

    /**
     * Display a listing of the resource (histórico de vendas).
     */
    public function index()
    {
        $vendas = VendaProduto::with('cliente', 'itensVenda.produto')
                               ->orderByDesc('dataVenda')
                               ->paginate(10);

        return view('vendas.index', compact('vendas'));
    }

    /**
     * Show the form for creating a new resource (registro de venda).
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::where('estoque', '>', 0)->orderBy('nome')->get();
        $tiposPagamento = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'Pix'];

        return view('vendas.create', compact('clientes', 'produtos', 'tiposPagamento'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendaRequest $request)
    {
        try {
            $venda = $this->vendaService->registrarVenda($request->validated());
            return redirect()->route('vendas.index')->with('success', 'Venda #' . $venda->idVenda . ' registrada com sucesso!');
        } catch (\Exception $e) {
            Log::error("Erro em VendaController@store: " . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao registrar venda: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (detalhes de uma venda).
     */
    public function show(VendaProduto $venda)
    {
        $venda->load('cliente', 'itensVenda.produto');
        return view('vendas.show', compact('venda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VendaProduto $venda)
    {
        return redirect()->route('vendas.show', $venda->idVenda)->with('info', 'Edição de vendas não é permitida diretamente. Consulte o histórico de itens.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VendaProduto $venda)
    {
      return redirect()->route('vendas.show', $venda->idVenda)->with('error', 'Atualização de vendas não implementada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VendaProduto $venda)
    {
        try {
            DB::beginTransaction();
            foreach ($venda->itensVenda as $item) {
                Produto::where('idProduto', $item->idProduto)
                       ->increment('estoque', $item->quantidade);
            }
            $venda->delete();
            DB::commit();
            return redirect()->route('vendas.index')->with('success', 'Venda #' . $venda->idVenda . ' estornada com sucesso! Produtos retornados ao estoque.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao estornar venda ID {$venda->idVenda}: " . $e->getMessage());
            return back()->with('error', 'Erro ao estornar venda: ' . $e->getMessage());
        }
    }
}