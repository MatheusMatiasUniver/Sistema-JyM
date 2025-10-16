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
    public function index()
    {
        $vendas = VendaProduto::with(['cliente', 'itens'])
                              ->orderBy('dataVenda', 'desc')
                              ->paginate(15);
        
        return view('vendas.index', compact('vendas'));
    }

    public function create()
    {
        $academiaId = config('app.academia_atual');
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
            
            $dados = [
                'idCliente' => $request->idCliente,
                'idAcademia' => config('app.academia_atual'),
                'dataVenda' => now(),
                'formaPagamento' => $request->formaPagamento,
                'valorTotal' => 0,
            ];

            $vendaId = DB::table('venda_produtos')->insertGetId($dados);

            $valorTotal = 0;

            foreach ($request->produtos as $item) {
                $produto = Produto::findOrFail($item['idProduto']);
                
                if ($produto->idAcademia != config('app.academia_atual')) {
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
        
        if ($venda->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para visualizar esta venda.');
        }

        return view('vendas.show', compact('venda'));
    }

    public function destroy($id)
    {
        $venda = VendaProduto::findOrFail($id);
        
        if ($venda->idAcademia !== config('app.academia_atual')) {
            abort(403, 'Você não tem permissão para cancelar esta venda.');
        }

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