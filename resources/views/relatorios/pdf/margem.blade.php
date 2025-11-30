<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}h2{font-size:16px;margin:15px 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style></head><body>
@include('relatorios.pdf.partials.header', ['academiaNome' => $academiaNome, 'modulo' => 'Margem de Lucro por Produto', 'usuarioNome' => $usuarioNome, 'dataEmissao' => $dataEmissao])
<h2>Margem por Produto</h2>
<table><thead><tr><th>Produto</th><th>Qtde</th><th>Receita</th><th>Custo</th><th>Margem</th></tr></thead><tbody>
@foreach($dados as $d)
<tr>
<td>{{ $d->nome }}</td>
<td>{{ $d->quantidadeTotal }}</td>
<td>R$ {{ number_format($d->receitaTotal,2,',','.') }}</td>
<td>R$ {{ number_format($d->custoTotal,2,',','.') }}</td>
<td>R$ {{ number_format($d->receitaTotal - $d->custoTotal,2,',','.') }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>
