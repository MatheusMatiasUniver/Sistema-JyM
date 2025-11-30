<html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans, sans-serif;font-size:12px}h1{font-size:18px;margin:0 0 10px}h2{font-size:16px;margin:15px 0 10px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #ccc;padding:6px;text-align:left} .kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:10px} .kpi{border:1px solid #ccc;padding:6px}</style></head><body>
@include('relatorios.pdf.partials.header', ['academiaNome' => $academiaNome, 'modulo' => 'Faturamento e Lucratividade', 'usuarioNome' => $usuarioNome, 'dataEmissao' => $dataEmissao])
<h2>Indicadores</h2>
<div class="kpis">
    <div class="kpi"><div>Receita Total</div><div>R$ {{ number_format($receitaTotal,2,',','.') }}</div></div>
    <div class="kpi"><div>COGS</div><div>R$ {{ number_format($custoTotal,2,',','.') }}</div></div>
    <div class="kpi"><div>Despesas Pagas</div><div>R$ {{ number_format($despesasPagas,2,',','.') }}</div></div>
    <div class="kpi"><div>Lucro Operacional</div><div>R$ {{ number_format($lucroOperacional,2,',','.') }}</div></div>
    <div class="kpi"><div>Ticket Médio</div><div>R$ {{ number_format($ticketMedio,2,',','.') }}</div></div>
    <div class="kpi"><div>Margem %</div><div>{{ number_format($margemPercentual,2,',','.') }}%</div></div>
    <div class="kpi"><div>Receita Vendas</div><div>R$ {{ number_format($receitaVendas,2,',','.') }}</div></div>
    <div class="kpi"><div>Receita Mensalidades</div><div>R$ {{ number_format($receitaMensalidades,2,',','.') }}</div></div>
    <div class="kpi"><div>Contas Recebidas</div><div>R$ {{ number_format($receitaReceber,2,',','.') }}</div></div>
</div>
</body></html>