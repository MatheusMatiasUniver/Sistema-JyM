<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
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
            'idCategoria' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:Aberta,Paga,Cancelada']
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $query = DB::table('contas_pagar')
            ->leftJoin('fornecedores', 'contas_pagar.idFornecedor', '=', 'fornecedores.idFornecedor')
            ->leftJoin('users', 'contas_pagar.idFuncionario', '=', 'users.idUsuario')
            ->leftJoin('categorias_contas_pagar', 'contas_pagar.idCategoriaContaPagar', '=', 'categorias_contas_pagar.idCategoriaContaPagar')
            ->select('contas_pagar.*', 'fornecedores.razaoSocial', 'users.nome as funcionarioNome', 'categorias_contas_pagar.nome as categoriaNome');
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
        if ($request->filled('idCategoria')) {
            $query->where('contas_pagar.idCategoriaContaPagar', $request->idCategoria);
        }
        if ($request->filled('status')) {
            $query->where('contas_pagar.status', $request->status);
        }
        $contas = $query->orderByDesc('dataVencimento')->paginate(20);

        $totaisPorCategoria = DB::table('contas_pagar')
            ->leftJoin('categorias_contas_pagar', 'contas_pagar.idCategoriaContaPagar', '=', 'categorias_contas_pagar.idCategoriaContaPagar')
            ->select('categorias_contas_pagar.nome as categoria', DB::raw('COALESCE(SUM(contas_pagar.valorTotal),0) as total'))
            ->groupBy('categorias_contas_pagar.nome')
            ->orderBy('categoria')
            ->get();
        $totalGeral = $totaisPorCategoria->sum('total');

        return view('relatorios.gastos', compact('contas', 'totaisPorCategoria', 'totalGeral'));
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
            ->select('mensalidades.*', 'clientes.nome');
        $baseQuery->where('mensalidades.status', 'Pendente');
        if ($request->filled('dataInicial')) {
            $baseQuery->whereDate('mensalidades.dataVencimento', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $baseQuery->whereDate('mensalidades.dataVencimento', '<=', $request->dataFinal);
        }
        $mensalidades = $baseQuery->orderBy('mensalidades.dataVencimento')->paginate(20);

        $hoje = now();
        $bucket030 = DB::table('mensalidades')->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(30), $hoje])->sum('valor');
        $bucket3160 = DB::table('mensalidades')->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(60), $hoje->copy()->subDays(31)])->sum('valor');
        $bucket6190 = DB::table('mensalidades')->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(90), $hoje->copy()->subDays(61)])->sum('valor');
        $bucket90p = DB::table('mensalidades')->where('status', 'Pendente')->where('dataVencimento', '<', $hoje->copy()->subDays(90))->sum('valor');
        $totalAberto = DB::table('mensalidades')->where('status', 'Pendente')->sum('valor');

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

    public function porFuncionario(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idFuncionario' => ['nullable', 'integer'],
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) {
            abort(400, 'Entrada inválida');
        }
        $idFuncionario = $request->input('idFuncionario');
        $vendasQuery = DB::table('venda_produtos')
            ->leftJoin('users', 'venda_produtos.idUsuario', '=', 'users.idUsuario')
            ->select('venda_produtos.*', 'users.nome as funcionarioNome');
        if ($idFuncionario) {
            $vendasQuery->where('venda_produtos.idUsuario', $idFuncionario);
        }
        if ($request->filled('dataInicial')) {
            $vendasQuery->whereDate('venda_produtos.dataVenda', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $vendasQuery->whereDate('venda_produtos.dataVenda', '<=', $request->dataFinal);
        }
        $vendas = $vendasQuery->orderByDesc('venda_produtos.dataVenda')->paginate(20);

        $despesasQuery = DB::table('contas_pagar')->leftJoin('users', 'contas_pagar.idFuncionario', '=', 'users.idUsuario')
            ->select('contas_pagar.*', 'users.nome as funcionarioNome');
        if ($idFuncionario) {
            $despesasQuery->where('contas_pagar.idFuncionario', $idFuncionario);
        }
        if ($request->filled('dataInicial')) {
            $despesasQuery->whereDate('contas_pagar.dataPagamento', '>=', $request->dataInicial);
        }
        if ($request->filled('dataFinal')) {
            $despesasQuery->whereDate('contas_pagar.dataPagamento', '<=', $request->dataFinal);
        }
        $despesas = $despesasQuery->orderByDesc('contas_pagar.dataPagamento')->paginate(20);

        $totalVendas = DB::table('venda_produtos')->when($idFuncionario, function ($q) use ($idFuncionario) { return $q->where('idUsuario', $idFuncionario); })->sum('valorTotal');
        $totalDespesas = DB::table('contas_pagar')->when($idFuncionario, function ($q) use ($idFuncionario) { return $q->where('idFuncionario', $idFuncionario); })->sum('valorTotal');
        $qtdVendas = DB::table('venda_produtos')->when($idFuncionario, function ($q) use ($idFuncionario) { return $q->where('idUsuario', $idFuncionario); })->count();

        return view('relatorios.por_funcionario', compact('vendas', 'despesas', 'totalVendas', 'totalDespesas', 'qtdVendas', 'idFuncionario'));
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
        return Pdf::loadView('relatorios.pdf.faturamento', compact('receitaTotal','custoTotal','despesasPagas','lucroOperacional','ticketMedio','margemPercentual','receitaVendas','receitaMensalidades','receitaReceber'))->download('relatorio-faturamento.pdf');
    }

    public function gastosPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d'],
            'idFuncionario' => ['nullable', 'integer'],
            'idFornecedor' => ['nullable', 'integer'],
            'idCategoria' => ['nullable', 'integer'],
            'status' => ['nullable', 'in:Aberta,Paga,Cancelada']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $query = DB::table('contas_pagar')
            ->leftJoin('fornecedores', 'contas_pagar.idFornecedor', '=', 'fornecedores.idFornecedor')
            ->leftJoin('users', 'contas_pagar.idFuncionario', '=', 'users.idUsuario')
            ->leftJoin('categorias_contas_pagar', 'contas_pagar.idCategoriaContaPagar', '=', 'categorias_contas_pagar.idCategoriaContaPagar')
            ->select('contas_pagar.*', 'fornecedores.razaoSocial', 'users.nome as funcionarioNome', 'categorias_contas_pagar.nome as categoriaNome');
        if ($request->filled('dataInicial')) { $query->whereDate('dataVencimento', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $query->whereDate('dataVencimento', '<=', $request->dataFinal); }
        if ($request->filled('idFuncionario')) { $query->where('contas_pagar.idFuncionario', $request->idFuncionario); }
        if ($request->filled('idFornecedor')) { $query->where('contas_pagar.idFornecedor', $request->idFornecedor); }
        if ($request->filled('idCategoria')) { $query->where('contas_pagar.idCategoriaContaPagar', $request->idCategoria); }
        if ($request->filled('status')) { $query->where('contas_pagar.status', $request->status); }
        $contas = $query->orderByDesc('dataVencimento')->get();
        $totaisPorCategoria = DB::table('contas_pagar')
            ->leftJoin('categorias_contas_pagar', 'contas_pagar.idCategoriaContaPagar', '=', 'categorias_contas_pagar.idCategoriaContaPagar')
            ->select('categorias_contas_pagar.nome as categoria', DB::raw('COALESCE(SUM(contas_pagar.valorTotal),0) as total'))
            ->groupBy('categorias_contas_pagar.nome')
            ->orderBy('categoria')
            ->get();
        $totalGeral = $totaisPorCategoria->sum('total');
        return Pdf::loadView('relatorios.pdf.gastos', compact('contas','totaisPorCategoria','totalGeral'))->download('relatorio-gastos.pdf');
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
            ->select('mensalidades.*', 'clientes.nome')
            ->where('mensalidades.status', 'Pendente');
        if ($request->filled('dataInicial')) { $baseQuery->whereDate('mensalidades.dataVencimento', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $baseQuery->whereDate('mensalidades.dataVencimento', '<=', $request->dataFinal); }
        $mensalidades = $baseQuery->orderBy('mensalidades.dataVencimento')->get();
        $hoje = now();
        $bucket030 = DB::table('mensalidades')->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(30), $hoje])->sum('valor');
        $bucket3160 = DB::table('mensalidades')->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(60), $hoje->copy()->subDays(31)])->sum('valor');
        $bucket6190 = DB::table('mensalidades')->where('status', 'Pendente')->whereBetween('dataVencimento', [$hoje->copy()->subDays(90), $hoje->copy()->subDays(61)])->sum('valor');
        $bucket90p = DB::table('mensalidades')->where('status', 'Pendente')->where('dataVencimento', '<', $hoje->copy()->subDays(90))->sum('valor');
        $totalAberto = DB::table('mensalidades')->where('status', 'Pendente')->sum('valor');
        return Pdf::loadView('relatorios.pdf.inadimplencia', compact('mensalidades','bucket030','bucket3160','bucket6190','bucket90p','totalAberto'))->download('relatorio-inadimplencia.pdf');
    }

    public function frequenciaPdf(Request $request)
    {
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
        $entradas = $query->orderByDesc('entradas.dataHora')->get();
        $porDia = DB::table('entradas')->select(DB::raw('DATE(dataHora) as dia'), DB::raw('COUNT(*) as quantidade'))->groupBy(DB::raw('DATE(dataHora)'))->orderBy('dia', 'desc')->limit(30)->get();
        return Pdf::loadView('relatorios.pdf.frequencia', compact('entradas','porDia'))->download('relatorio-frequencia.pdf');
    }

    public function vendasPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $vendasQuery = DB::table('venda_produtos')->leftJoin('users', 'venda_produtos.idUsuario', '=', 'users.idUsuario')->leftJoin('clientes', 'venda_produtos.idCliente', '=', 'clientes.idCliente')->select('venda_produtos.*', 'users.nome as funcionarioNome', 'clientes.nome as clienteNome');
        if ($request->filled('dataInicial')) { $vendasQuery->whereDate('venda_produtos.dataVenda', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $vendasQuery->whereDate('venda_produtos.dataVenda', '<=', $request->dataFinal); }
        $vendas = $vendasQuery->orderByDesc('venda_produtos.dataVenda')->get();
        $topProdutos = DB::table('itens_vendas')->join('produtos', 'itens_vendas.idProduto', '=', 'produtos.idProduto')->select('produtos.nome', DB::raw('SUM(itens_vendas.quantidade) as quantidade'), DB::raw('SUM(itens_vendas.quantidade * itens_vendas.precoUnitario) as receita'))->groupBy('produtos.nome')->orderByDesc('receita')->limit(10)->get();
        return Pdf::loadView('relatorios.pdf.vendas', compact('vendas','topProdutos'))->download('relatorio-vendas.pdf');
    }

    public function porFuncionarioPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idFuncionario' => ['nullable', 'integer'],
            'dataInicial' => ['nullable', 'date_format:Y-m-d'],
            'dataFinal' => ['nullable', 'date_format:Y-m-d']
        ]);
        if ($validator->fails()) { abort(400, 'Entrada inválida'); }
        $idFuncionario = $request->input('idFuncionario');
        $vendasQuery = DB::table('venda_produtos')->leftJoin('users', 'venda_produtos.idUsuario', '=', 'users.idUsuario')->select('venda_produtos.*', 'users.nome as funcionarioNome');
        if ($idFuncionario) { $vendasQuery->where('venda_produtos.idUsuario', $idFuncionario); }
        if ($request->filled('dataInicial')) { $vendasQuery->whereDate('venda_produtos.dataVenda', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $vendasQuery->whereDate('venda_produtos.dataVenda', '<=', $request->dataFinal); }
        $vendas = $vendasQuery->orderByDesc('venda_produtos.dataVenda')->get();
        $despesasQuery = DB::table('contas_pagar')->leftJoin('users', 'contas_pagar.idFuncionario', '=', 'users.idUsuario')->select('contas_pagar.*', 'users.nome as funcionarioNome');
        if ($idFuncionario) { $despesasQuery->where('contas_pagar.idFuncionario', $idFuncionario); }
        if ($request->filled('dataInicial')) { $despesasQuery->whereDate('contas_pagar.dataPagamento', '>=', $request->dataInicial); }
        if ($request->filled('dataFinal')) { $despesasQuery->whereDate('contas_pagar.dataPagamento', '<=', $request->dataFinal); }
        $despesas = $despesasQuery->orderByDesc('contas_pagar.dataPagamento')->get();
        $totalVendas = DB::table('venda_produtos')->when($idFuncionario, function ($q) use ($idFuncionario) { return $q->where('idUsuario', $idFuncionario); })->sum('valorTotal');
        $totalDespesas = DB::table('contas_pagar')->when($idFuncionario, function ($q) use ($idFuncionario) { return $q->where('idFuncionario', $idFuncionario); })->sum('valorTotal');
        $qtdVendas = DB::table('venda_produtos')->when($idFuncionario, function ($q) use ($idFuncionario) { return $q->where('idUsuario', $idFuncionario); })->count();
        return Pdf::loadView('relatorios.pdf.por_funcionario', compact('vendas','despesas','totalVendas','totalDespesas','qtdVendas','idFuncionario'))->download('relatorio-por-funcionario.pdf');
    }
}

