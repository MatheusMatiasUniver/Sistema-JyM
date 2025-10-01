<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Cliente;
use App\Models\Mensalidade;
use App\Models\VendaProduto;
use App\Models\Produto;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $acessosHoje = Entrada::whereDate('dataHora', Carbon::today())->count();

        $clientesAtivos = Cliente::where('status', 'Ativo')->count();

        $mensalidadesAtrasadas = Mensalidade::where('dataVencimento', '<', Carbon::today())
                                            ->where('status', 'Pendente')
                                            ->with('cliente')
                                            ->get();

        $mensalidadesProximas = Mensalidade::whereBetween('dataVencimento', [Carbon::today(), Carbon::today()->addDays(7)])
                                            ->where('status', 'Pendente')
                                            ->with('cliente')
                                            ->get();

        $ultimasVendas = VendaProduto::with('cliente') 
                                     ->orderBy('dataVenda', 'desc')
                                     ->take(5)
                                     ->get();

        $limiteBaixoEstoque = 5;                             
        $produtosBaixoEstoque = Produto::where('estoque', '<=', $limiteBaixoEstoque)
                                        ->orderBy('estoque', 'asc')
                                        ->get();

        $primeiroDiaMes = Carbon::now()->startOfMonth();
        $ultimoDiaMes = Carbon::now()->endOfMonth();
        $faturamentoMes = VendaProduto::whereBetween('dataVenda', [$primeiroDiaMes, $ultimoDiaMes])
                                      ->sum('valorTotal');

        return view('dashboard', compact(
            'acessosHoje',
            'clientesAtivos',
            'mensalidadesAtrasadas',
            'mensalidadesProximas',
            'ultimasVendas',
            'produtosBaixoEstoque',
            'faturamentoMes'
        ));
    }
}