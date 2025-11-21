<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left} .kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:10px} .kpi{border:1px solid #ccc;padding:6px}</style></head><body>
<h1>Por Funcionário</h1>
<div class="kpis">
    <div class="kpi"><div>Total de Vendas</div><div>R$ {{ number_format($totalVendas,2,',','.') }}</div></div>
    <div class="kpi"><div>Total de Despesas</div><div>R$ {{ number_format($totalDespesas,2,',','.') }}</div></div>
    <div class="kpi"><div>Quantidade de Vendas</div><div>{{ $qtdVendas }}</div></div>
</div>

<h2>Vendas</h2>
<table><thead><tr><th>#</th><th>Funcionário</th><th>Data</th><th>Total</th></tr></thead><tbody>
@foreach($vendas as $v)
<tr>
<td>{{ $v->idVenda }}</td>
<td>{{ $v->funcionarioNome ?? '—' }}</td>
<td>{{ \Carbon\Carbon::parse($v->dataVenda)->format('d/m/Y H:i') }}</td>
<td>R$ {{ number_format($v->valorTotal,2,',','.') }}</td>
</tr>
@endforeach
</tbody></table>

<h2>Despesas</h2>
<table><thead><tr><th>Descrição</th><th>Funcionário</th><th>Pagamento</th><th>Valor</th></tr></thead><tbody>
@foreach($despesas as $d)
<tr>
<td>{{ $d->descricao }}</td>
<td>{{ $d->funcionarioNome ?? '—' }}</td>
<td>{{ $d->dataPagamento ? \Carbon\Carbon::parse($d->dataPagamento)->format('d/m/Y') : '—' }}</td>
<td>R$ {{ number_format($d->valorTotal,2,',','.') }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>