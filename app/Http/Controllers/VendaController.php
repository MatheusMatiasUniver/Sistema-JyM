<?php

namespace App\Http\Controllers;

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

        // Filtro por valor mínimo
        if ($request->filled('valor_min')) {
            $query->where('valor_total', '>=', $request->valor_min);
        }

        if ($request->filled('valor_max')) {
            $query->where('valor_total', '<=', $request->valor_max);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Define default values for sorting
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
        
        return view('vendas.index', compact('vendas'));
    }

    public function create()
    {
        $academiaId = session('academia_selecionada');
        if (!$academiaId) {
            return redirect()->route('dashboard')->with('error', 'Selecione uma academia primeiro.');
        }
        $produtos = Produto::where('idAcademia', $academiaId)
                           ->where('estoque', '>', 0)
                           ->get();
        $clientes = Cliente::where('idAcademia', $academiaId)->get();
        
        $tiposPagamento = ['Dinheiro', 'Cartão de Crédito', 'Cartão de Débito', 'PIX', 'Boleto'];
        
        return view('vendas.create', compact('produtos', 'clientes', 'tiposPagamento'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'idCliente' => 'nullable|exists:clientes,idCliente',
                'formaPagamento' => 'required|in:Dinheiro,Cartão de Crédito,Cartão de Débito,PIX,Boleto',
                'produtos.*.idProduto' => 'required|exists:produtos,idProduto',
            ]);

            if (empty($request->produtos)) {
                return back()->with('error', 'Adicione pelo menos um produto à venda!')->withInput();
            }

            DB::beginTransaction();
            
            $academiaId = session('academia_selecionada');
            if (!$academiaId) {
                return back()->with('error', 'Selecione uma academia primeiro.')->withInput();
            }

            $dados = [
                'idCliente' => $request->idCliente,
                'idAcademia' => $academiaId,
                'dataVenda' => now(),
                'formaPagamento' => $request->formaPagamento,
                'valorTotal' => 0,
            ];

            $vendaId = DB::table('venda_produtos')->insertGetId($dados);

            $valorTotal = 0;

            foreach ($request->produtos as $item) {
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
                
                $valorTotal += $produto->preco * $item['quantidade'];
            }

            DB::table('venda_produtos')->where('idVenda', $vendaId)->update(['valorTotal' => $valorTotal]);

            DB::commit();

            return redirect()->route('vendas.index')
                            ->with('success', 'Venda realizada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', 'Erro de validação: Verifique se todos os campos estão preenchidos corretamente.')
                        ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao realizar venda: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show($id)
    {
        $venda = VendaProduto::with(['cliente', 'itens.produto'])->findOrFail($id);
        
        return view('vendas.show', compact('venda'));
    }

    public function destroy($id)
    {
        $venda = VendaProduto::findOrFail($id);
        
        DB::beginTransaction();
        
        try {
            foreach ($venda->itens as $item) {
                $item->produto->adicionarEstoque($item->quantidade);
            }

            $venda->delete();

            DB::commit();

            return redirect()->route('vendas.index')
                             ->with('success', 'Venda cancelada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cancelar venda: ' . $e->getMessage());
        }
    }
}