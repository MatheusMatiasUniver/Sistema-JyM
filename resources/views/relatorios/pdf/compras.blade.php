<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}h2{font-size:16px;margin:15px 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style></head><body>
@include('relatorios.pdf.partials.header', ['academiaNome' => $academiaNome, 'modulo' => 'Compras de Fornecedores', 'usuarioNome' => $usuarioNome, 'dataEmissao' => $dataEmissao])
<h2>Compras</h2>
<table><thead><tr><th>#</th><th>Fornecedor</th><th>Data</th><th>Status</th><th>Total</th></tr></thead><tbody>
@foreach($compras as $c)
<tr>
<td>{{ $c->idCompra }}</td>
<td>{{ $c->razaoSocial }}</td>
<td>{{ \Carbon\Carbon::parse($c->dataEmissao)->format('d/m/Y H:i') }}</td>
<td>{{ ucfirst($c->status) }}</td>
<td>R$ {{ number_format($c->valorTotal,2,',','.') }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>
