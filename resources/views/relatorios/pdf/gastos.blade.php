<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style></head><body>
<h1>Gastos</h1>
<table><thead><tr><th>Categoria</th><th>Total</th></tr></thead><tbody>
@foreach($totaisPorCategoria as $t)
<tr><td>{{ $t->categoria ?? 'Sem Categoria' }}</td><td>R$ {{ number_format($t->total,2,',','.') }}</td></tr>
@endforeach
</tbody><tfoot><tr><td>Total Geral</td><td>R$ {{ number_format($totalGeral,2,',','.') }}</td></tr></tfoot></table>

<h2>Detalhes</h2>
<table><thead><tr><th>Descrição</th><th>Categoria</th><th>Fornecedor</th><th>Funcionário</th><th>Status</th><th>Vencimento</th><th>Valor</th></tr></thead><tbody>
@foreach($contas as $c)
<tr>
<td>{{ $c->descricao }}</td>
<td>{{ $c->categoriaNome ?? '—' }}</td>
<td>{{ $c->razaoSocial ?? '—' }}</td>
<td>{{ $c->funcionarioNome ?? '—' }}</td>
<td>{{ $c->status }}</td>
<td>{{ \Carbon\Carbon::parse($c->dataVencimento)->format('d/m/Y') }}</td>
<td>R$ {{ number_format($c->valorTotal,2,',','.') }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>