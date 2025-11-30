<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}h2{font-size:16px;margin:15px 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style></head><body>
@include('relatorios.pdf.partials.header', ['academiaNome' => $academiaNome, 'modulo' => 'Ruptura de Estoque', 'usuarioNome' => $usuarioNome, 'dataEmissao' => $dataEmissao])
<h2>Produtos em Ruptura</h2>
<table><thead><tr><th>Produto</th><th>Estoque Atual</th><th>Estoque Mínimo</th></tr></thead><tbody>
@foreach($produtos as $p)
<tr>
<td>{{ $p->nome }}</td>
<td>{{ $p->estoque }}</td>
<td>{{ $p->estoqueMinimo }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>
