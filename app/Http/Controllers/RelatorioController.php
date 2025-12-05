<?php

namespace App\Http\Controllers;

use App\Models\Academia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    private function configurePdfLimits(): void
    {
        set_time_limit(120);
        ini_set('memory_limit', '256M');
    }

    private function getPdfHeaderData(): array
    {
        $user = Auth::user();
        $academiaId = config('app.academia_atual');
        $academia = $academiaId ? Academia::find($academiaId) : null;
        return [
            'academiaNome' => $academia->nome ?? 'Academia',
            'usuarioNome' => $user->nome ?? $user->name ?? 'Usuário',
            'dataEmissao' => now()->format('d/m/Y H:i'),
        ];
    }

    public function compras(Request $request)
    {
        $query = DB::table('compras')
            ->join('fornecedores', 'compras.idFornecedor', '=', 'fornecedores.idFornecedor')
            ->select('compras.*', 'fornecedores.razaoSocial');
        if ($request->filled('data_inicial')) {
            $query->whereDate('dataEmissao', '>=', $request->data_inicial);
        }
        if ($request->filled('data_final')) {
            $query->whereDate('dataEmissao', '<=', $request->data_final);
        }
        $compras = $query->orderByDesc('idCompra')->paginate(20);
        return view('relatorios.compras', compact('compras'));
    }

    public function margemProdutos(Request $request)
    {
        $query = DB::table('itens_vendas')
            ->join('venda_produtos', 'itens_vendas.idVenda', '=', 'venda_produtos.idVenda')
            ->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')
            ->select('produtos.idProduto', 'produtos.nome',
                DB::raw('SUM(itens_vendas.quantidade) as quantidadeTotal'),
                DB::raw('SUM(itens_vendas.precoUnitario * itens_vendas.quantidade) as receitaTotal'),
                DB::raw('SUM((COALESCE(produtos.custoMedio, COALESCE(produtos.precoCompra, 0))) * itens_vendas.quantidade) as custoTotal'));
        if ($request->filled('data_inicial')) {
            $query->whereDate('venda_produtos.dataVenda', '>=', $request->data_inicial);
        }
        if ($request->filled('data_final')) {
            $query->whereDate('venda_produtos.dataVenda', '<=', $request->data_final);
        }
        $dados = $query->groupBy('produtos.idProduto', 'produtos.nome')->paginate(20);
        return view('relatorios.margem', compact('dados'));
    }

    public function ruptura(Request $request)
    {
        $produtos = DB::table('produtos')
            ->select('idProduto', 'nome', 'estoque', 'estoqueMinimo')
            ->whereNotNull('estoqueMinimo')
            ->whereColumn('estoque', '<=', 'estoqueMinimo')
            ->orderBy('nome')
            ->paginate(50);
        return view('relatorios.ruptura', compact('produtos'));
    }

    public function faturamentoLucro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $dataInicial = $request->input('dataInicial');
        $dataFinal = $request->input('dataFinal');

        $vendasQuery = DB::table('venda_produtos')
            ->select(DB::raw('COALESCE(SUM(valorTotal),0) as receitaVendas'));
        if ($dataInicial) {
            $vendasQuery->whereDate('dataVenda', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $vendasQuery->whereDate('dataVenda', '<=', $dataFinal);
        }
        $receitaVendas = (float) ($vendasQuery->first()->receitaVendas ?? 0);

        $mensalidadesQuery = DB::table('mensalidades')
            ->select(DB::raw('COALESCE(SUM(valor),0) as receitaMensalidades'))
            ->where('status', 'Paga');
        if ($dataInicial) {
            $mensalidadesQuery->whereDate('dataPagamento', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $mensalidadesQuery->whereDate('dataPagamento', '<=', $dataFinal);
        }
        $receitaMensalidades = (float) ($mensalidadesQuery->first()->receitaMensalidades ?? 0);

        $receberQuery = DB::table('contas_receber')
            ->select(DB::raw('COALESCE(SUM(valorTotal),0) as receitaReceber'))
            ->where('status', 'recebida');
        if ($dataInicial) {
            $receberQuery->whereDate('dataRecebimento', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $receberQuery->whereDate('dataRecebimento', '<=', $dataFinal);
        }
        $receitaReceber = (float) ($receberQuery->first()->receitaReceber ?? 0);

        $cogsQuery = DB::table('itens_vendas')
            ->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')
            ->join('venda_produtos', 'itens_vendas.idVenda', '=', 'venda_produtos.idVenda')
            ->select(DB::raw('COALESCE(SUM(itens_vendas.quantidade * COALESCE(produtos.custoMedio, COALESCE(produtos.precoCompra, 0))),0) as custoTotal'));
        if ($dataInicial) {
            $cogsQuery->whereDate('venda_produtos.dataVenda', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $cogsQuery->whereDate('venda_produtos.dataVenda', '<=', $dataFinal);
        }
        $custoTotal = (float) ($cogsQuery->first()->custoTotal ?? 0);

        $despesasQuery = DB::table('contas_pagar')
            ->select(DB::raw('COALESCE(SUM(valorTotal),0) as despesasPagas'))
            ->where('status', 'Paga');
        if ($dataInicial) {
            $despesasQuery->whereDate('dataPagamento', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $despesasQuery->whereDate('dataPagamento', '<=', $dataFinal);
        }
        $despesasPagas = (float) ($despesasQuery->first()->despesasPagas ?? 0);

        $receitaTotal = $receitaVendas + $receitaMensalidades + $receitaReceber;
        $lucroOperacional = $receitaTotal - ($custoTotal + $despesasPagas);
        $ticketMedioQuery = DB::table('venda_produtos')
            ->select(DB::raw('COALESCE(AVG(valorTotal),0) as ticketMedio'));
        if ($dataInicial) {
            $ticketMedioQuery->whereDate('dataVenda', '>=', $dataInicial);
        }
        if ($dataFinal) {
            $ticketMedioQuery->whereDate('dataVenda', '<=', $dataFinal);
        }
        $ticketMedio = (float) ($ticketMedioQuery->first()->ticketMedio ?? 0);
        $margemPercentual = $receitaTotal > 0 ? ($receitaTotal - $custoTotal) / $receitaTotal * 100 : 0;

        return view('relatorios.faturamento', compact(
            'receitaTotal', 'receitaVendas', 'receitaMensalidades', 'receitaReceber',
            'custoTotal', 'despesasPagas', 'lucroOperacional', 'ticketMedio', 'margemPercentual'
        ));
    }

    public function gastos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
            'idFuncionario' => ['nullable', 'integer'],
            'idFornecedor' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:Aberta,Paga,Cancelada']
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $query = DB::table('contas_pagar')
            ->leftJoin('fornecedores', 'contas_pagar.idFornecedor', '=', 'fornecedores.idFornecedor')
            ->leftJoin('users', 'contas_pagar.idFuncionario', '=', 'users.idUsuario')
            ->select('contas_pagar.*', 'fornecedores.razaoSocial', 'users.nome as funcionarioNome');
        if ($request->filled('dataInicial')) {
            $query->whereDate('dataVencimento', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $query->whereDate('dataVencimento', '<=', $request->dataFinal);
        }
        if ($request->filled('idFuncionario')) {
            $query->where('contas_pagar.idFuncionario', $request->idFuncionario);
        }
        if ($request->filled('idFornecedor')) {
            $query->where('contas_pagar.idFornecedor', $request->idFornecedor);
        }
        if ($request->filled('status')) {
            $query->where('contas_pagar.status', $request->status);
        }
        $contas = $query->orderByDesc('dataVencimento')->paginate(20);

        $totalGeral = $contas->sum('valorTotal');

        return view('relatorios.gastos', compact('contas', 'totalGeral'));
    }

    public function inadimplencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $baseQuery = DB::table('mensalidades')
            ->join('clientes', 'mensalidades.idCliente', '=', 'clientes.idCliente')
            ->select('mensalidades.*', 'clientes.nome', 'clientes.status as clienteStatus')
            ->where('clientes.status', 'Inadimplente')
            ->where('mensalidades.status', 'Pendente');
        if ($request->filled('dataInicial')) {
            $baseQuery->whereDate('mensalidades.dataVencimento', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $baseQuery->whereDate('mensalidades.dataVencimento', '<=', $request->dataFinal);
        }
        $mensalidades = $baseQuery->orderBy('mensalidades.dataVencimento')->paginate(20);

        $hoje = now();
        $clientesInadimplentes = DB::table('clientes')->where('status', 'Inadimplente')->pluck('idCliente');
        $bucket030 = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(30), $hoje])->sum('valor');
        $bucket3160 = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(60), $hoje->copy()->subDays(31)])->sum('valor');
        $bucket6190 = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(90), $hoje->copy()->subDays(61)])->sum('valor');
        $bucket90p = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->where('dataVencimento', '<', $hoje->copy()->subDays(90))->sum('valor');
        $totalAberto = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->sum('valor');

        return view('relatorios.inadimplencia', compact('mensalidades', 'bucket030', 'bucket3160', 'bucket6190', 'bucket90p', 'totalAberto'));
    }

    public function frequencia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
            'metodo' => ['nullable', 'in:Reconhecimento Facial,CPF/Senha,Manual']
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $query = DB::table('entradas')
            ->join('clientes', 'entradas.idCliente', '=', 'clientes.idCliente')
            ->select('entradas.*', 'clientes.nome');
        if ($request->filled('dataInicial')) {
            $query->whereDate('entradas.dataHora', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $query->whereDate('entradas.dataHora', '<=', $request->dataFinal);
        }
        if ($request->filled('metodo')) {
            $query->where('entradas.metodo', $request->metodo);
        }
        $entradas = $query->orderByDesc('entradas.dataHora')->paginate(20);

        $porDia = DB::table('entradas')
            ->select(DB::raw('DATE(dataHora) as dia'), DB::raw('COUNT(*) as quantidade'))
            ->groupBy(DB::raw('DATE(dataHora)'))
            ->orderBy('dia', 'desc')
            ->limit(30)
            ->get();

        return view('relatorios.frequencia', compact('entradas', 'porDia'));
    }

    public function vendas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $vendasQuery = DB::table('venda_produtos')
            ->leftJoin('users', 'venda_produtos.idUsuario', '=', 'users.idUsuario')
            ->leftJoin('clientes', 'venda_produtos.idCliente', '=', 'clientes.idCliente')
            ->select('venda_produtos.*', 'users.nome as funcionarioNome', 'clientes.nome as clienteNome');
        if ($request->filled('dataInicial')) {
            $vendasQuery->whereDate('venda_produtos.dataVenda', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $vendasQuery->whereDate('venda_produtos.dataVenda', '<=', $request->dataFinal);
        }
        $vendas = $vendasQuery->orderByDesc('venda_produtos.dataVenda')->paginate(20);

        $topProdutos = DB::table('itens_vendas')
            ->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')
            ->select('produtos.nome', DB::raw('SUM(itens_vendas.quantidade) as quantidade'), DB::raw('SUM(itens_vendas.quantidade * itens_vendas.precoUnitario) as receita'))
            ->groupBy('produtos.nome')
            ->orderByDesc('receita')
            ->limit(10)
            ->get();

        $ticketMedio = DB::table('venda_produtos')->select(DB::raw('COALESCE(AVG(valorTotal),0) as ticketMedio'))->first()->ticketMedio ?? 0;

        return view('relatorios.vendas', compact('vendas', 'topProdutos', 'ticketMedio'));
    }

    public function faturamentoLucroPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $dataInicial = $request->input('dataInicial');
        $dataFinal = $request->input('dataFinal');
        $vendasQuery = DB::table('venda_produtos')->select(DB::raw('COALESCE(SUM(valorTotal),0) as receitaVendas'));
        if ($dataInicial) { $vendasQuery->whereDate('dataVenda', '>=', $dataInicial); }
        if ($dataFinal) { $vendasQuery->whereDate('dataVenda', '<=', $dataFinal); }
        $receitaVendas = (float) ($vendasQuery->first()->receitaVendas ?? 0);
        $mensalidadesQuery = DB::table('mensalidades')->select(DB::raw('COALESCE(SUM(valor),0) as receitaMensalidades'))->where('status', 'Paga');
        if ($dataInicial) { $mensalidadesQuery->whereDate('dataPagamento', '>=', $dataInicial); }
        if ($dataFinal) { $mensalidadesQuery->whereDate('dataPagamento', '<=', $dataFinal); }
        $receitaMensalidades = (float) ($mensalidadesQuery->first()->receitaMensalidades ?? 0);
        $receberQuery = DB::table('contas_receber')->select(DB::raw('COALESCE(SUM(valorTotal),0) as receitaReceber'))->where('status', 'recebida');
        if ($dataInicial) { $receberQuery->whereDate('dataRecebimento', '>=', $dataInicial); }
        if ($dataFinal) { $receberQuery->whereDate('dataRecebimento', '<=', $dataFinal); }
        $receitaReceber = (float) ($receberQuery->first()->receitaReceber ?? 0);
        $cogsQuery = DB::table('itens_vendas')->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')->join('venda_produtos', 'itens_vendas.idVenda', '=', 'venda_produtos.idVenda')->select(DB::raw('COALESCE(SUM(itens_vendas.quantidade * COALESCE(produtos.custoMedio, COALESCE(produtos.precoCompra, 0))),0) as custoTotal'));
        if ($dataInicial) { $cogsQuery->whereDate('venda_produtos.dataVenda', '>=', $dataInicial); }
        if ($dataFinal) { $cogsQuery->whereDate('venda_produtos.dataVenda', '<=', $dataFinal); }
        $custoTotal = (float) ($cogsQuery->first()->custoTotal ?? 0);
        $despesasQuery = DB::table('contas_pagar')->select(DB::raw('COALESCE(SUM(valorTotal),0) as despesasPagas'))->where('status', 'Paga');
        if ($dataInicial) { $despesasQuery->whereDate('dataPagamento', '>=', $dataInicial); }
        if ($dataFinal) { $despesasQuery->whereDate('dataPagamento', '<=', $dataFinal); }
        $despesasPagas = (float) ($despesasQuery->first()->despesasPagas ?? 0);
        $receitaTotal = $receitaVendas + $receitaMensalidades + $receitaReceber;
        $lucroOperacional = $receitaTotal - ($custoTotal + $despesasPagas);
        $ticketMedioQuery = DB::table('venda_produtos')->select(DB::raw('COALESCE(AVG(valorTotal),0) as ticketMedio'));
        if ($dataInicial) { $ticketMedioQuery->whereDate('dataVenda', '>=', $dataInicial); }
        if ($dataFinal) { $ticketMedioQuery->whereDate('dataVenda', '<=', $dataFinal); }
        $ticketMedio = (float) ($ticketMedioQuery->first()->ticketMedio ?? 0);
        $margemPercentual = $receitaTotal > 0 ? ($receitaTotal - $custoTotal) / $receitaTotal * 100 : 0;
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.faturamento', array_merge(compact('receitaTotal','custoTotal','despesasPagas','lucroOperacional','ticketMedio','margemPercentual','receitaVendas','receitaMensalidades','receitaReceber'), $headerData))->download('relatorio-faturamento.pdf');
    }

    public function gastosPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
            'idFuncionario' => ['nullable', 'integer'],
            'idFornecedor' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:Aberta,Paga,Cancelada']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $query = DB::table('contas_pagar')
            ->leftJoin('fornecedores', 'contas_pagar.idFornecedor', '=', 'fornecedores.idFornecedor')
            ->leftJoin('users', 'contas_pagar.idFuncionario', '=', 'users.idUsuario')
            ->select('contas_pagar.*', 'fornecedores.razaoSocial', 'users.nome as funcionarioNome');
        if ($request->filled('dataInicial')) { $query->whereDate('dataVencimento', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $query->whereDate('dataVencimento', '<=', $request->dataFinal); }
        if ($request->filled('idFuncionario')) { $query->where('contas_pagar.idFuncionario', $request->idFuncionario); }
        if ($request->filled('idFornecedor')) { $query->where('contas_pagar.idFornecedor', $request->idFornecedor); }
        if ($request->filled('status')) { $query->where('contas_pagar.status', $request->status); }
        $contas = $query->orderByDesc('dataVencimento')->get();
        $totalGeral = $contas->sum('valorTotal');
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.gastos', array_merge(compact('contas','totalGeral'), $headerData))->download('relatorio-gastos.pdf');
    }

    public function inadimplenciaPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $baseQuery = DB::table('mensalidades')
            ->join('clientes', 'mensalidades.idCliente', '=', 'clientes.idCliente')
            ->select('mensalidades.*', 'clientes.nome', 'clientes.status as clienteStatus')
            ->where('clientes.status', 'Inadimplente')
            ->where('mensalidades.status', 'Pendente');
        if ($request->filled('dataInicial')) { $baseQuery->whereDate('mensalidades.dataVencimento', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $baseQuery->whereDate('mensalidades.dataVencimento', '<=', $request->dataFinal); }
        $mensalidades = $baseQuery->orderBy('mensalidades.dataVencimento')->get();
        $hoje = now();
        $clientesInadimplentes = DB::table('clientes')->where('status', 'Inadimplente')->pluck('idCliente');
        $bucket030 = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(30), $hoje])->sum('valor');
        $bucket3160 = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(60), $hoje->copy()->subDays(31)])->sum('valor');
        $bucket6190 = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(90), $hoje->copy()->subDays(61)])->sum('valor');
        $bucket90p = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->where('dataVencimento', '<', $hoje->copy()->subDays(90))->sum('valor');
        $totalAberto = DB::table('mensalidades')->whereIn('idCliente', $clientesInadimplentes)->where('status', 'Pendente')->sum('valor');
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.inadimplencia', array_merge(compact('mensalidades','bucket030','bucket3160','bucket6190','bucket90p','totalAberto'), $headerData))->download('relatorio-inadimplencia.pdf');
    }

    public function frequenciaPdf(Request $request)
    {
        $this->configurePdfLimits();
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
            'metodo' => ['nullable', 'in:Reconhecimento Facial,CPF/Senha,Manual']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $query = DB::table('entradas')
            ->join('clientes', 'entradas.idCliente', '=', 'clientes.idCliente')
            ->select('entradas.*', 'clientes.nome');
        if ($request->filled('dataInicial')) { $query->whereDate('entradas.dataHora', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $query->whereDate('entradas.dataHora', '<=', $request->dataFinal); }
        if ($request->filled('metodo')) { $query->where('entradas.metodo', $request->metodo); }
        $entradas = $query->orderByDesc('entradas.dataHora')->limit(500)->get();
        $porDia = DB::table('entradas')->select(DB::raw('DATE(dataHora) as dia'), DB::raw('COUNT(*) as quantidade'))->groupBy(DB::raw('DATE(dataHora)'))->orderBy('dia', 'desc')->limit(30)->get();
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.frequencia', array_merge(compact('entradas','porDia'), $headerData))->download('relatorio-frequencia.pdf');
    }

    public function vendasPdf(Request $request)
    {
        $this->configurePdfLimits();
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $vendasQuery = DB::table('venda_produtos')->leftJoin('users', 'venda_produtos.idUsuario', '=', 'users.idUsuario')->leftJoin('clientes', 'venda_produtos.idCliente', '=', 'clientes.idCliente')->select('venda_produtos.*', 'users.nome as funcionarioNome', 'clientes.nome as clienteNome');
        if ($request->filled('dataInicial')) { $vendasQuery->whereDate('venda_produtos.dataVenda', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $vendasQuery->whereDate('venda_produtos.dataVenda', '<=', $request->dataFinal); }
        $vendas = $vendasQuery->orderByDesc('venda_produtos.dataVenda')->limit(500)->get();
        $topProdutos = DB::table('itens_vendas')->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')->select('produtos.nome', DB::raw('SUM(itens_vendas.quantidade) as quantidade'), DB::raw('SUM(itens_vendas.quantidade * itens_vendas.precoUnitario) as receita'))->groupBy('produtos.nome')->orderByDesc('receita')->limit(10)->get();
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.vendas', array_merge(compact('vendas','topProdutos'), $headerData))->download('relatorio-vendas.pdf');
    }

    public function comprasPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_inicial' => ['nullable', 'date_format:Y-m-d'],
            'data_final' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $query = DB::table('compras')
            ->join('fornecedores', 'compras.idFornecedor', '=', 'fornecedores.idFornecedor')
            ->select('compras.*', 'fornecedores.razaoSocial');
        if ($request->filled('data_inicial')) { $query->whereDate('dataEmissao', '>=', $request->data_inicial); }
        if ($request->filled('data_final')) { $query->whereDate('dataEmissao', '<=', $request->data_final); }
        $compras = $query->orderByDesc('idCompra')->get();
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.compras', array_merge(compact('compras'), $headerData))->download('relatorio-compras.pdf');
    }

    public function margemProdutosPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_inicial' => ['nullable', 'date_format:Y-m-d'],
            'data_final' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $query = DB::table('itens_vendas')
            ->join('venda_produtos', 'itens_vendas.idVenda', '=', 'venda_produtos.idVenda')
            ->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')
            ->select('produtos.idProduto', 'produtos.nome',
                DB::raw('SUM(itens_vendas.quantidade) as quantidadeTotal'),
                DB::raw('SUM(itens_vendas.precoUnitario * itens_vendas.quantidade) as receitaTotal'),
                DB::raw('SUM((COALESCE(produtos.custoMedio, COALESCE(produtos.precoCompra, 0))) * itens_vendas.quantidade) as custoTotal'));
        if ($request->filled('data_inicial')) { $query->whereDate('venda_produtos.dataVenda', '>=', $request->data_inicial); }
        if ($request->filled('data_final')) { $query->whereDate('venda_produtos.dataVenda', '<=', $request->data_final); }
        $dados = $query->groupBy('produtos.idProduto', 'produtos.nome')->get();
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.margem', array_merge(compact('dados'), $headerData))->download('relatorio-margem.pdf');
    }

    public function rupturaPdf(Request $request)
    {
        $produtos = DB::table('produtos')
            ->select('idProduto', 'nome', 'estoque', 'estoqueMinimo')
            ->whereNotNull('estoqueMinimo')
            ->whereColumn('estoque', '<=', 'estoqueMinimo')
            ->orderBy('nome')
            ->get();
        $headerData = $this->getPdfHeaderData();
        return Pdf::loadView('relatorios.pdf.ruptura', array_merge(compact('produtos'), $headerData))->download('relatorio-ruptura.pdf');
    }
}

