@extends('layouts.app')

@section('title', 'Contas a Pagar - Sistema JyM')

@section('content')
    @php
        $formasPagamentoAtivas = $formasPagamentoAtivas ?? \App\Models\AjusteSistema::FORMAS_PAGAMENTO_PADRAO;
    @endphp

    <h1 class="text-3xl font-bold mb-6 text-grip-6">Contas a Pagar</h1>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('financeiro.contas_pagar.index') }}" method="GET" class="flex flex-wrap items-end gap-4" autocomplete="off">
            <div>
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select id="status" name="status" class="select">
                    <option value="">Todos</option>
                    <option value="aberta" {{ request('status') == 'aberta' ? 'selected' : '' }}>Aberta</option>
                    <option value="paga" {{ request('status') == 'paga' ? 'selected' : '' }}>Paga</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>

            <div>
                <label for="categoria" class="block text-gray-700 text-sm font-bold mb-2">Categoria:</label>
                <select id="categoria" name="categoria" class="select">
                    <option value="">Todas</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->idCategoriaContaPagar }}" {{ request('categoria') == $categoria->idCategoriaContaPagar ? 'selected' : '' }}>
                            {{ $categoria->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="data_inicial" class="block text-gray-700 text-sm font-bold mb-2">Vencimento Inicial:</label>
                <input type="date" id="data_inicial" name="data_inicial" value="{{ request('data_inicial') }}"
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label for="data_final" class="block text-gray-700 text-sm font-bold mb-2">Vencimento Final:</label>
                <input type="date" id="data_final" name="data_final" value="{{ request('data_final') }}"
                       class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filtrar
                </button>
                <a href="{{ route('financeiro.contas_pagar.index') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <div class="px-5 py-5 bg-white border-b flex flex-col sm:flex-row items-center sm:justify-between mb-0 rounded-t-lg">
        <div class="text-sm text-gray-600">
            Mostrando {{ $contas->firstItem() ?? 0 }}–{{ $contas->lastItem() ?? 0 }} de {{ $contas->total() }} contas
        </div>
        <div class="flex items-center gap-2">
            @if ($contas->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Anterior</span>
            @else
                <a href="{{ $contas->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Anterior</a>
            @endif
            {{ $contas->links() }}
            @if ($contas->hasMorePages())
                <a href="{{ $contas->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Próxima</a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Próxima</span>
            @endif
        </div>
    </div>

    <div class="bg-white shadow-md rounded-b-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Categoria</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fornecedor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valor</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Vencimento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contas as $c)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ optional($c->categoria)->nome ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($c->fornecedor)->razaoSocial ?? '—' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm font-medium">R$ {{ number_format($c->valorTotal,2,',','.') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $c->dataVencimento->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            @if($c->status === 'aberta')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Aberta
                                </span>
                            @elseif($c->status === 'paga')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Paga
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Cancelada
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            @if($c->status === 'aberta')
                                <form action="{{ route('financeiro.contas_pagar.pagar', $c->idContaPagar) }}" method="POST" class="flex items-center space-x-2" autocomplete="off">
                                    @csrf
                                    <select name="formaPagamento" class="select text-sm">
                                        @foreach($formasPagamentoAtivas as $forma)
                                            <option value="{{ $forma }}">{{ $forma }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline text-sm">Faturar</button>
                                </form>
                            @elseif($c->status === 'paga')
                                <span class="text-gray-500 text-xs">Pago em {{ $c->dataPagamento?->format('d/m/Y') ?? '—' }}</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma conta encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($contas->hasPages())
        <div class="px-5 py-5 bg-white border-t flex flex-col sm:flex-row items-center sm:justify-between mt-0 rounded-b-lg shadow-md">
            <div class="text-sm text-gray-600">
                Mostrando {{ $contas->firstItem() ?? 0 }}–{{ $contas->lastItem() ?? 0 }} de {{ $contas->total() }} contas
            </div>
            <div class="flex items-center gap-2">
                @if ($contas->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Anterior</span>
                @else
                    <a href="{{ $contas->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Anterior</a>
                @endif
                {{ $contas->links() }}
                @if ($contas->hasMorePages())
                    <a href="{{ $contas->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Próxima</a>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Próxima</span>
                @endif
            </div>
        </div>
    @endif
@endsection
