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
        $academiaId = config('app.academia_atual');

        $acessosHoje = Entrada::whereDate('dataHora', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $clientesAtivos = Cliente::where('status', 'Ativo')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $mensalidadesAtrasadas = Mensalidade::where('dataVencimento', '<', Carbon::today())
                                            ->where('status', 'Pendente')
                                            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                                            ->with('cliente')
                                            ->get();

        $mensalidadesProximas = Mensalidade::whereBetween('dataVencimento', [Carbon::today(), Carbon::today()->addDays(7)])
                                            ->where('status', 'Pendente')
                                            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                                            ->with('cliente')
                                            ->get();

        $ultimasVendas = VendaProduto::with('cliente') 
                                     ->orderBy('dataVenda', 'desc')
                                     ->take(5)
                                     ->get();

        $limiteBaixoEstoque = 5;                             
        $produtosBaixoEstoque = Produto::where('estoque', '<=', $limiteBaixoEstoque)
                                        ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                                        ->orderBy('estoque', 'asc')
                                        ->get();

        $primeiroDiaMes = Carbon::now()->startOfMonth();
        $ultimoDiaMes = Carbon::now()->endOfMonth();
        $faturamentoMes = VendaProduto::whereBetween('dataVenda', [$primeiroDiaMes, $ultimoDiaMes])
                                      ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                                      ->sum('valorTotal');

        // Additional variables for dashboard cards
        $entradasHoje = Entrada::whereDate('dataHora', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();
        $totalClientes = Cliente::when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();
        $vendasHoje = VendaProduto::whereDate('dataVenda', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->sum('valorTotal');

        return view('dashboard', compact(
            'acessosHoje',
            'clientesAtivos',
            'mensalidadesAtrasadas',
            'mensalidadesProximas',
            'ultimasVendas',
            'produtosBaixoEstoque',
            'faturamentoMes',
            'entradasHoje',
            'totalClientes',
            'vendasHoje'
        ));
    }

    public function metrics(Request $request)
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end'   => ['required', 'date'],
        ]);

        $start = Carbon::parse($request->input('start'))->startOfDay();
        $end   = Carbon::parse($request->input('end'))->endOfDay();

        if ($start->gt($end)) {
            return response()->json(['message' => 'Intervalo inválido: start deve ser anterior ao end'], 400);
        }

        $academiaId = config('app.academia_atual');

        $acessosNoPeriodo = Entrada::whereBetween('dataHora', [$start, $end])
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $vendasNoPeriodo = VendaProduto::whereBetween('dataVenda', [$start, $end])
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->sum('valorTotal');

        return response()->json([
            'acessos' => $acessosNoPeriodo,
            'vendasTotal' => $vendasNoPeriodo,
        ]);
    }
}
