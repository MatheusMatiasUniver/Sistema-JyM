<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\ItemCompra;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ContaPagarCategoria;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        $query = Compra::with(['fornecedor']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('data_inicial')) {
            $query->whereDate('dataEmissao', '>=', $request->data_inicial);
        }
        if ($request->filled('data_final')) {
            $query->whereDate('dataEmissao', '<=', $request->data_final);
        }

        $compras = $query->orderByDesc('idCompra')->paginate(20);
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $fornecedores = Fornecedor::orderBy('razaoSocial')->get();
        $produtos = Produto::orderBy('nome')->get();
        return view('compras.create', compact('fornecedores', 'produtos'));
    }

    public function store(Request $request)
    {
        $academiaId = Auth::user()->idAcademia ?? config('app.academia_atual');

        try {
            $dados = $request->validate([
                'idFornecedor' => 'required|exists:fornecedores,idFornecedor',
                'itens' => 'required|array|min:1',
                'itens.*.idProduto' => 'required|exists:produtos,idProduto',
                'itens.*.quantidade' => 'required|integer|min:1',
                'itens.*.precoUnitario' => 'required|numeric|min:0',
                'valorFrete' => 'nullable|numeric|min:0',
                'valorDesconto' => 'nullable|numeric|min:0',
                'valorImpostos' => 'nullable|numeric|min:0',
                'dataVencimento' => 'nullable|date',
                'observacoes' => 'nullable|string',
            ], [
                'idFornecedor.required' => 'O fornecedor é obrigatório.',
                'idFornecedor.exists' => 'O fornecedor selecionado não existe.',
                'itens.required' => 'Adicione pelo menos um item à compra.',
                'itens.min' => 'Adicione pelo menos um item à compra.',
                'itens.*.idProduto.required' => 'O produto é obrigatório.',
                'itens.*.quantidade.required' => 'A quantidade é obrigatória.',
                'itens.*.quantidade.min' => 'A quantidade deve ser pelo menos 1.',
                'itens.*.precoUnitario.required' => 'O preço unitário é obrigatório.',
                'valorFrete.numeric' => 'O valor do frete deve ser um número.',
                'valorDesconto.numeric' => 'O valor do desconto deve ser um número.',
                'dataVencimento.date' => 'A data de vencimento deve ser uma data válida.',
            ]);

            DB::beginTransaction();

            $compra = Compra::create([
                'idAcademia' => $academiaId,
                'idFornecedor' => $dados['idFornecedor'],
                'dataEmissao' => now(),
                'status' => 'aberta',
                'valorProdutos' => 0,
                'valorFrete' => $dados['valorFrete'] ?? 0,
                'valorDesconto' => $dados['valorDesconto'] ?? 0,
                'valorImpostos' => $dados['valorImpostos'] ?? 0,
                'valorTotal' => 0,
                'observacoes' => $dados['observacoes'] ?? null,
            ]);

            $valorProdutos = 0;
            foreach ($dados['itens'] as $item) {
                ItemCompra::create([
                    'idCompra' => $compra->idCompra,
                    'idProduto' => $item['idProduto'],
                    'quantidade' => $item['quantidade'],
                    'precoUnitario' => $item['precoUnitario'],
                    'descontoPercent' => null,
                    'custoRateadoTotal' => null,
                ]);
                $valorProdutos += $item['precoUnitario'] * $item['quantidade'];
            }

            $valorTotal = $valorProdutos + $compra->valorFrete + $compra->valorImpostos - $compra->valorDesconto;
            $compra->update(['valorProdutos' => $valorProdutos, 'valorTotal' => $valorTotal]);

            $categoria = ContaPagarCategoria::firstOrCreate([
                'idAcademia' => $academiaId,
                'nome' => 'Compra de produtos',
            ], [
                'ativa' => true,
            ]);

            DB::table('contas_pagar')->insert([
                'idAcademia' => $academiaId,
                'idFornecedor' => $compra->idFornecedor,
                'idCategoriaContaPagar' => $categoria->idCategoriaContaPagar,
                'documentoRef' => $compra->idCompra,
                'descricao' => 'Compra #'.$compra->idCompra,
                'valorTotal' => $valorTotal,
                'status' => 'aberta',
                'dataVencimento' => $dados['dataVencimento'] ?? now()->addDays(30),
                'dataPagamento' => null,
                'formaPagamento' => null,
            ]);

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra registrada com sucesso');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Dados inválidos'], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao registrar compra')->withInput();
        }
    }

    public function show($id)
    {
        $compra = Compra::with(['fornecedor', 'itens.produto'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function receber($id)
    {
        $compra = Compra::with(['itens.produto'])->findOrFail($id);
        if ($compra->status !== 'aberta') {
            return back()->with('error', 'Compra não está aberta para recebimento');
        }

        DB::beginTransaction();
        try {
            foreach ($compra->itens as $item) {
                $produto = $item->produto;
                $estoqueAtual = $produto->estoque ?? 0;
                $novoEstoque = $estoqueAtual + $item->quantidade;
                $produto->estoque = $novoEstoque;
                $produto->precoCompra = $item->precoUnitario;
                $produto->custoMedio = $novoEstoque > 0
                    ? ((($produto->custoMedio ?? 0) * $estoqueAtual) + ($item->precoUnitario * $item->quantidade)) / $novoEstoque
                    : $item->precoUnitario;
                $produto->save();

                DB::table('movimentacoes_estoque')->insert([
                    'idAcademia' => $compra->idAcademia,
                    'idProduto' => $item->idProduto,
                    'tipo' => 'entrada',
                    'quantidade' => $item->quantidade,
                    'custoUnitario' => $item->precoUnitario,
                    'custoTotal' => $item->precoUnitario * $item->quantidade,
                    'origem' => 'compra',
                    'referenciaId' => $compra->idCompra,
                    'motivo' => null,
                    'dataMovimentacao' => now(),
                    'usuarioId' => Auth::id(),
                ]);
            }

            $compra->status = 'recebida';
            $compra->save();

            DB::commit();
            return redirect()->route('compras.show', $compra->idCompra)->with('success', 'Compra recebida e estoque atualizado');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao receber compra');
        }
    }
}
