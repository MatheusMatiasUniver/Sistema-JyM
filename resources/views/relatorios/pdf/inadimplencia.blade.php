<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}h2{font-size:16px;margin:15px 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left} .kpis{display:grid;grid-template-columns:repeat(5,1fr);gap:10px} .kpi{border:1px solid #ccc;padding:6px}</style></head><body>
@include('relatorios.pdf.partials.header', ['academiaNome' => $academiaNome, 'modulo' => 'Inadimplência de Mensalidades', 'usuarioNome' => $usuarioNome, 'dataEmissao' => $dataEmissao])
<h2>Resumo por Faixa de Atraso</h2>
<div class="kpis">
    <div class="kpi"><div>0–30 dias</div><div>R$ {{ number_format($bucket030,2,',','.') }}</div></div>
    <div class="kpi"><div>31–60 dias</div><div>R$ {{ number_format($bucket3160,2,',','.') }}</div></div>
    <div class="kpi"><div>61–90 dias</div><div>R$ {{ number_format($bucket6190,2,',','.') }}</div></div>
    <div class="kpi"><div>> 90 dias</div><div>R$ {{ number_format($bucket90p,2,',','.') }}</div></div>
    <div class="kpi"><div>Total em Aberto</div><div>R$ {{ number_format($totalAberto,2,',','.') }}</div></div>
</div>

<h2>Detalhes</h2>
<table><thead><tr><th>Cliente</th><th>Vencimento</th><th>Valor</th><th>Status</th></tr></thead><tbody>
@foreach($mensalidades as $m)
<tr>
<td>{{ $m->nome }}</td>
<td>{{ \Carbon\Carbon::parse($m->dataVencimento)->format('d/m/Y') }}</td>
<td>R$ {{ number_format($m->valor,2,',','.') }}</td>
<td>{{ $m->status }}</td>
</tr>
@endforeach
</tbody></table>
</body></html>