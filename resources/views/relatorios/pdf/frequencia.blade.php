<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}h2{font-size:16px;margin:15px 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left}</style></head><body>
@include('relatorios.pdf.partials.header', ['academiaNome' => $academiaNome, 'modulo' => 'Frequência de Clientes', 'usuarioNome' => $usuarioNome, 'dataEmissao' => $dataEmissao])
<h2>Entradas por Dia</h2>
<table><thead><tr><th>Dia</th><th>Entradas</th></tr></thead><tbody>
@foreach($porDia as $d)
<tr><td>{{ \Carbon\Carbon::parse($d->dia)->format('d/m/Y') }}</td><td>{{ $d->quantidade }}</td></tr>
@endforeach
</tbody></table>

<h2>Detalhes</h2>
<table><thead><tr><th>Cliente</th><th>Data/Hora</th><th>Método</th></tr></thead><tbody>
@foreach($entradas as $e)
<tr>
<td>{{ $e->nome }}</td>
<td>{{ \Carbon\Carbon::parse($e->dataHora)->format('d/m/Y H:i') }}</td>
<td>{{ $e->metodo }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>