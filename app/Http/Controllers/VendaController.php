<?php

namespace App\Http\Controllers;

use App\Models\AjusteSistema;
use App\Models\VendaProduto;
use App\Models\Produto;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VendaController extends Controller
{
    public function index(Request $request)
    {
        $academiaId = session('academia_selecionada') ?? (Auth::user()->idAcademia ?? null);
        $ajuste = $academiaId ? AjusteSistema::obterOuCriarParaAcademia((int) $academiaId) : null;
        $formasPagamentoAtivas = $ajuste ? $ajuste->formasPagamentoAtivas : AjusteSistema::FORMAS_PAGAMENTO_PADRAO;

        $query = VendaProduto::with(['cliente', 'itens']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('idVenda', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($clienteQuery) use ($search) {
                      $clienteQuery->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('formaPagamento', $request->forma_pagamento);
        }

        if ($request->filled('data_inicial')) {
            $query->whereDate('dataVenda', '>=', $request->data_inicial);
        }

        if ($request->filled('data_final')) {
            $query->whereDate('dataVenda', '<=', $request->data_final);
        }

        if ($request->filled('valor_min')) {
            $query->where('valorTotal', '>=', $request->valor_min);
        }

        if ($request->filled('valor_max')) {
            $query->where('valorTotal', '<=', $request->valor_max);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $sortField = 'dataVenda';
        $sortDirection = 'desc';
        
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'data_asc':
                    $sortField = 'dataVenda';
                    $sortDirection = 'asc';
                    break;
                case 'valor_asc':
                    $sortField = 'valorTotal';
                    $sortDirection = 'asc';
                    break;
                case 'valor_desc':
                    $sortField = 'valorTotal';
                    $sortDirection = 'desc';
                    break;
                case 'id_asc':
                    $sortField = 'idVenda';
                    $sortDirection = 'asc';
                    break;
                case 'id_desc':
                    $sortField = 'idVenda';
                    $sortDirection = 'desc';
                    break;
                default:
                    $sortField = 'dataVenda';
                    $sortDirection = 'desc';
            }
        }

        $vendas = $query->orderBy($sortField, $sortDirection)->paginate(15);
        
        return view('vendas.index', compact('vendas', 'formasPagamentoAtivas'));
    }

    public function create()
    {
        $academiaId = session('academia_selecionada');
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }
        $ajuste = AjusteSistema::obterOuCriarParaAcademia((int) $academiaId);
        $produtos = Produto::where('idAcademia', $academiaId)
                           ->where('estoque', '>', 0)
                           ->get();
        $clientes = Cliente::where('idAcademia', $academiaId)->get();
        
        $tiposPagamento = $ajuste->formasPagamentoAtivas;
        $clienteOpcionalVenda = $ajuste->clienteOpcionalVenda;
        
        return view('vendas.create', compact('produtos', 'clientes', 'tiposPagamento', 'clienteOpcionalVenda'));
    }

    public function store(\App\Http\Requests\StoreVendaRequest $request)
    {
        try {
            $dadosValidados = $request->validated();

            if (empty($dadosValidados['produtos'])) {
                return back()->with('error', 'Adicione pelo menos um produto à venda!')->withInput();
            }

            DB::beginTransaction();
            
            $academiaId = session('academia_selecionada');
            if (!$academiaId) {
                return back()->with('error', 'Selecione uma academia primeiro.')->withInput();
            }

            $dados = [
                'idCliente' => $dadosValidados['idCliente'] ?? null,
                'idUsuario' => Auth::id(),
                'idAcademia' => $academiaId,
                'dataVenda' => now(),
                'formaPagamento' => $dadosValidados['formaPagamento'],
                'valorTotal' => 0,
            ];

            $vendaId = DB::table('venda_produtos')->insertGetId($dados);

            $valorTotal = 0;

            foreach ($dadosValidados['produtos'] as $item) {
                $produto = Produto::findOrFail($item['idProduto']);
                
                if ($produto->idAcademia != $academiaId) {
                    throw new \Exception('Produto não pertence a esta academia.');
                }

                if (!$produto->temEstoque($item['quantidade'])) {
                    throw new \Exception("Estoque insuficiente para o produto: {$produto->nome}");
                }

                DB::table('itens_vendas')->insert([
                    'idVenda' => $vendaId,
                    'idProduto' => $produto->idProduto,
                    'quantidade' => $item['quantidade'],
                    'precoUnitario' => $produto->preco,
                ]);

                $produto->baixarEstoque($item['quantidade']);
                $custoUnitario = $produto->custoMedio ?? $produto->precoCompra ?? 0;
                DB::table('movimentacoes_estoque')->insert([
                    'idAcademia' => $academiaId,
                    'idProduto' => $produto->idProduto,
                    'tipo' => 'saida',
                    'quantidade' => $item['quantidade'],
                    'custoUnitario' => $custoUnitario,
                    'custoTotal' => $custoUnitario * $item['quantidade'],
                    'origem' => 'venda',
                    'referenciaId' => $vendaId,
                    'motivo' => null,
                    'dataMovimentacao' => now(),
                    'usuarioId' => Auth::id(),
                ]);
                
                $valorTotal += $produto->preco * $item['quantidade'];

                if (method_exists($produto, 'atingiuEstoqueMinimo') && $produto->atingiuEstoqueMinimo()) {
                    session()->flash('warning', "Produto {$produto->nome} atingiu estoque mínimo");
                }
            }

            DB::table('venda_produtos')->where('idVenda', $vendaId)->update(['valorTotal' => $valorTotal]);

        DB::table('contas_receber')->insert([
            'idAcademia' => $academiaId,
            'idCliente' => $dadosValidados['idCliente'] ?? null,
            'documentoRef' => $vendaId,
            'descricao' => 'Venda #'.$vendaId,
            'valorTotal' => $valorTotal,
            'status' => 'recebida',
            'dataVencimento' => null,
            'dataRecebimento' => now(),
            'formaRecebimento' => $dadosValidados['formaPagamento'],
        ]);

        DB::commit();

        event(new \App\Events\DashboardUpdated('sale'));

        return redirect()->route('vendas.index')
                        ->with('success', 'Venda realizada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Erro de validação: Verifique se todos os campos estão preenchidos corretamente.')
                        ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao realizar venda.')
                        ->withInput();
        }
    }

    public function show($id)
    {
        $venda = VendaProduto::with(['cliente', 'usuario', 'itens.produto'])->findOrFail($id);
        
        return view('vendas.show', compact('venda'));
    }

    public function destroy($id)
    {
        $venda = VendaProduto::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            foreach ($venda->itens as $item) {
                $item->produto->adicionarEstoque($item->quantidade);
                $custoUnitario = $item->produto->custoMedio ?? $item->produto->precoCompra ?? 0;
                \Illuminate\Support\Facades\DB::table('movimentacoes_estoque')->insert([
                    'idAcademia' => $venda->idAcademia,
                    'idProduto' => $item->idProduto,
                    'tipo' => 'entrada',
                    'quantidade' => $item->quantidade,
                    'custoUnitario' => $custoUnitario,
                    'custoTotal' => $custoUnitario * $item->quantidade,
                    'origem' => 'devolucao',
                    'referenciaId' => $venda->idVenda,
                    'motivo' => 'estorno_venda',
                    'dataMovimentacao' => now(),
                    'usuarioId' => \Illuminate\Support\Facades\Auth::id(),
                ]);
            }

            $venda->delete();

            DB::commit();

            return redirect()->route('vendas.index')
                             ->with('success', 'Venda estornada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Falha ao estornar venda.');
        }
    }
}
