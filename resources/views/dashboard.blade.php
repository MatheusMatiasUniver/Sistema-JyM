@extends('layouts.app')

@section('title', 'Dashboard - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Acessos Hoje</p>
                <p class="text-3xl font-bold text-gray-800">{{ $acessosHoje }}</p>
            </div>
            <div class="text-blue-500 text-4xl">
                <i class="fas fa-door-open"></i>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Clientes Ativos</p>
                <p class="text-3xl font-bold text-gray-800">{{ $clientesAtivos }}</p>
            </div>
            <div class="text-green-500 text-4xl">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-semibold">Faturamento Mês</p>
                <p class="text-3xl font-bold text-gray-800">R\$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
            </div>
            <div class="text-purple-500 text-4xl">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-red-600 mb-4">Mensalidades Atrasadas ({{ $mensalidadesAtrasadas->count() }})</h2>
            @if($mensalidadesAtrasadas->isEmpty())
                <p class="text-gray-600">Nenhuma mensalidade atrasada. Bom trabalho!</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($mensalidadesAtrasadas as $mensalidade)
                        <li>
                            {{ $mensalidade->cliente->nome ?? 'Cliente Removido' }} - Vencimento: {{ $mensalidade->dataVencimento->format('d/m/Y') }}
                            <span class="text-red-500 font-semibold">(R\$ {{ number_format($mensalidade->valor, 2, ',', '.') }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-yellow-600 mb-4">Mensalidades Próximas ({{ $mensalidadesProximas->count() }})</h2>
            @if($mensalidadesProximas->isEmpty())
                <p class="text-gray-600">Nenhuma mensalidade próxima do vencimento nos próximos 7 dias.</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($mensalidadesProximas as $mensalidade)
                        <li>
                            {{ $mensalidade->cliente->nome ?? 'Cliente Removido' }} - Vence em: {{ $mensalidade->dataVencimento->format('d/m/Y') }}
                            <span class="text-yellow-500 font-semibold">(R\$ {{ number_format($mensalidade->valor, 2, ',', '.') }})</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Últimas Vendas</h2>
            @if($ultimasVendas->isEmpty())
                <p class="text-gray-600">Nenhuma venda recente.</p>
            @else
                <ul class="divide-y divide-gray-200">
                    @foreach($ultimasVendas as $venda)
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-gray-800 font-semibold">Venda #{{ $venda->idVenda }} - {{ $venda->cliente->nome ?? 'Cliente Removido' }}</p>
                                <p class="text-gray-600 text-sm">{{ $venda->dataVenda->format('d/m/Y H:i') }}</p>
                            </div>
                            <span class="text-green-600 font-bold">R$ {{ number_format($venda->valorTotal, 2, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-orange-600 mb-4">Produtos com Baixo Estoque ({{ $produtosBaixoEstoque->count() }})</h2>
            @if($produtosBaixoEstoque->isEmpty())
                <p class="text-gray-600">Todos os produtos estão com estoque adequado.</p>
            @else
                <ul class="list-disc list-inside text-gray-700">
                    @foreach($produtosBaixoEstoque as $produto)
                        <li>
                            {{ $produto->nome }} - Estoque: <span class="text-orange-500 font-semibold">{{ $produto->estoque }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <!-- Additional Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-500 text-white mr-4">
                    <i class="fas fa-door-open"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Entradas Hoje</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $entradasHoje }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-500 text-white mr-4">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Total de Clientes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalClientes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-500 text-white mr-4">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm">Vendas Hoje</p>
                    <p class="text-2xl font-bold text-gray-800">R$ {{ number_format($vendasHoje, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection
