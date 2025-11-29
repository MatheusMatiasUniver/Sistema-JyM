<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Cliente;
use App\Models\Mensalidade;
use App\Models\VendaProduto;
use App\Models\Produto;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Compra;
use App\Models\AjusteSistema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $academiaId = config('app.academia_atual');
        $ajuste = $academiaId ? AjusteSistema::obterOuCriarParaAcademia((int) $academiaId) : null;
        $formasPagamentoAtivas = $ajuste ? $ajuste->formasPagamentoAtivas : AjusteSistema::FORMAS_PAGAMENTO_PADRAO;

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
                                     ->whereDate('dataVenda', Carbon::today())
                                     ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                                     ->orderBy('dataVenda', 'desc')
                                     ->take(5)
                                     ->get();

        $produtosBaixoEstoque = Produto::whereColumn('estoque', '<=', 'estoqueMinimo')
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

        $contasPagarAbertas = \App\Models\ContaPagar::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->get();
        $contasPagarResumo = [
            'abertas' => [
                'quantidade' => $contasPagarAbertas->count(),
                'total' => $contasPagarAbertas->sum('valorTotal'),
            ],
            'vencidas' => [
                'quantidade' => $contasPagarAbertas->where('dataVencimento', '<', Carbon::today())->count(),
                'total' => $contasPagarAbertas->where('dataVencimento', '<', Carbon::today())->sum('valorTotal'),
            ],
            'pagasHoje' => [
                'quantidade' => \App\Models\ContaPagar::where('status', 'paga')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataPagamento', Carbon::today())
                    ->count(),
                'total' => \App\Models\ContaPagar::where('status', 'paga')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataPagamento', Carbon::today())
                    ->sum('valorTotal'),
            ],
        ];

        $contasReceberAbertas = \App\Models\ContaReceber::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->get();
        $contasReceberResumo = [
            'abertas' => [
                'quantidade' => $contasReceberAbertas->count(),
                'total' => $contasReceberAbertas->sum('valorTotal'),
            ],
            'vencidas' => [
                'quantidade' => $contasReceberAbertas->where('dataVencimento', '<', Carbon::today())->count(),
                'total' => $contasReceberAbertas->where('dataVencimento', '<', Carbon::today())->sum('valorTotal'),
            ],
            'recebidasHoje' => [
                'quantidade' => \App\Models\ContaReceber::where('status', 'recebida')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataRecebimento', Carbon::today())
                    ->count(),
                'total' => \App\Models\ContaReceber::where('status', 'recebida')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataRecebimento', Carbon::today())
                    ->sum('valorTotal'),
            ],
        ];

        $comprasAbertas = \App\Models\Compra::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->withCount('itens')
            ->orderByDesc('dataEmissao')
            ->take(5)
            ->get();

        $contasPagarLista = ContaPagar::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->with(['fornecedor'])
            ->orderBy('dataVencimento', 'asc')
            ->take(8)
            ->get();

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
            'vendasHoje',
            'contasPagarResumo',
            'contasReceberResumo',
            'comprasAbertas',
            'contasPagarLista',
            'formasPagamentoAtivas'
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

    public function cards()
    {
        $academiaId = config('app.academia_atual');

        $acessosHoje = Entrada::whereDate('dataHora', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $clientesAtivos = Cliente::where('status', 'Ativo')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $totalClientes = Cliente::when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $mensalidadesAtrasadasCount = Mensalidade::where('dataVencimento', '<', Carbon::today())
            ->where('status', 'Pendente')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $faturamentoMes = VendaProduto::whereBetween('dataVenda', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->sum('valorTotal');

        $vendasHoje = VendaProduto::whereDate('dataVenda', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->sum('valorTotal');

        $contasPagarAbertas = ContaPagar::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->get();
        $contasPagarResumo = [
            'abertas' => [
                'quantidade' => $contasPagarAbertas->count(),
                'total' => $contasPagarAbertas->sum('valorTotal'),
            ],
            'vencidas' => [
                'quantidade' => $contasPagarAbertas->where('dataVencimento', '<', Carbon::today())->count(),
                'total' => $contasPagarAbertas->where('dataVencimento', '<', Carbon::today())->sum('valorTotal'),
            ],
            'pagasHoje' => [
                'quantidade' => ContaPagar::where('status', 'paga')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataPagamento', Carbon::today())
                    ->count(),
                'total' => ContaPagar::where('status', 'paga')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataPagamento', Carbon::today())
                    ->sum('valorTotal'),
            ],
        ];

        $contasReceberAbertas = ContaReceber::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->get();
        $contasReceberResumo = [
            'abertas' => [
                'quantidade' => $contasReceberAbertas->count(),
                'total' => $contasReceberAbertas->sum('valorTotal'),
            ],
            'vencidas' => [
                'quantidade' => $contasReceberAbertas->where('dataVencimento', '<', Carbon::today())->count(),
                'total' => $contasReceberAbertas->where('dataVencimento', '<', Carbon::today())->sum('valorTotal'),
            ],
            'recebidasHoje' => [
                'quantidade' => ContaReceber::where('status', 'recebida')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataRecebimento', Carbon::today())
                    ->count(),
                'total' => ContaReceber::where('status', 'recebida')
                    ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
                    ->whereDate('dataRecebimento', Carbon::today())
                    ->sum('valorTotal'),
            ],
        ];

        $comprasAbertasCount = Compra::where('status', 'aberta')
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->count();

        $ultimasVendas = VendaProduto::with('cliente')
            ->whereDate('dataVenda', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->orderBy('dataVenda', 'desc')
            ->take(5)
            ->get()
            ->map(function ($v) {
                return [
                    'idVenda' => $v->idVenda,
                    'cliente' => $v->cliente_nome_exibicao,
                    'dataVenda' => $v->dataVenda ? $v->dataVenda->toDateTimeString() : null,
                    'valorTotal' => $v->valorTotal,
                ];
            });

        $diasMes = Carbon::now()->daysInMonth;
        $inicioMes = Carbon::now()->startOfMonth();
        $faturamentoPorDia = array_fill(0, $diasMes, 0);
        $vendasPorDia = VendaProduto::select(DB::raw('DATE(dataVenda) as dia'), DB::raw('SUM(valorTotal) as total'))
            ->whereBetween('dataVenda', [$inicioMes, Carbon::now()->endOfMonth()])
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->groupBy('dia')
            ->get();
        foreach ($vendasPorDia as $row) {
            $index = Carbon::parse($row->dia)->day - 1;
            if ($index >= 0 && $index < $diasMes) {
                $faturamentoPorDia[$index] = (float) $row->total;
            }
        }

        $acessosPorHora = array_fill(0, 24, 0);
        $entradasHojeGrupo = Entrada::select(DB::raw('HOUR(dataHora) as hora'), DB::raw('COUNT(*) as total'))
            ->whereDate('dataHora', Carbon::today())
            ->when($academiaId, fn($q) => $q->where('idAcademia', $academiaId))
            ->groupBy('hora')
            ->get();
        foreach ($entradasHojeGrupo as $row) {
            $h = (int) $row->hora;
            if ($h >= 0 && $h < 24) {
                $acessosPorHora[$h] = (int) $row->total;
            }
        }

        return response()->json([
            'acessosHoje' => $acessosHoje,
            'clientesAtivos' => $clientesAtivos,
            'totalClientes' => $totalClientes,
            'mensalidadesAtrasadasCount' => $mensalidadesAtrasadasCount,
            'faturamentoMes' => $faturamentoMes,
            'vendasHoje' => $vendasHoje,
            'contasPagarResumo' => $contasPagarResumo,
            'contasReceberResumo' => $contasReceberResumo,
            'comprasAbertasCount' => $comprasAbertasCount,
            'ultimasVendas' => $ultimasVendas,
            'faturamentoPorDia' => $faturamentoPorDia,
            'acessosPorHora' => $acessosPorHora,
        ]);
    }
}
