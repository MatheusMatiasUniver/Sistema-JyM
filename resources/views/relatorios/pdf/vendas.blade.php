<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style></head><body>
<h1>Vendas</h1>
<h2>Top Produtos</h2>
<table><thead><tr><th>Produto</th><th>Quantidade</th><th>Receita</th></tr></thead><tbody>
@foreach($topProdutos as $p)
<tr><td>{{ $p->nome }}</td><td>{{ $p->quantidade }}</td><td>R$ {{ number_format($p->receita,2,',','.') }}</td></tr>
@endforeach
</tbody></table>

<h2>Vendas</h2>
<table><thead><tr><th>#</th><th>Funcionário</th><th>Cliente</th><th>Data</th><th>Total</th></tr></thead><tbody>
@foreach($vendas as $v)
<tr>
<td>{{ $v->idVenda }}</td>
<td>{{ $v->funcionarioNome ?? '—' }}</td>
<td>{{ $v->clienteNome ?? '—' }}</td>
<td>{{ \Carbon\Carbon::parse($v->dataVenda)->format('d/m/Y H:i') }}</td>
<td>R$ {{ number_format($v->valorTotal,2,',','.') }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>