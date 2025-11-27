@extends('layouts.app')

@section('title', 'Clientes - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Lista de Clientes</h1>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('clientes.create') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Adicionar Novo Cliente
        </a>
        @auth
            @if(Auth::user()->isAdministrador())
                <a href="{{ route('clientes.excluidos.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Clientes Excluídos
                </a>
            @endif
        @endauth
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('clientes.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-grow">
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Pesquisar:</label>
                <input type="text" id="search" name="search" placeholder="Nome ou CPF"
                       value="{{ request('search') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label for="status_filter" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select id="status_filter" name="status_filter"
                        class="select">
                    <option value="">Todos</option>
                    <option value="Ativo" {{ request('status_filter') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="Inadimplente" {{ request('status_filter') == 'Inadimplente' ? 'selected' : '' }}>Inadimplente</option>
                    <option value="Inativo" {{ request('status_filter') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <div>
                <label for="plano_id" class="block text-gray-700 text-sm font-bold mb-2">Plano:</label>
                <select id="plano_id" name="plano_id"
                        class="select">
                    <option value="">Todos</option>
                    @foreach ($allPlanos as $plano)
                        <option value="{{ $plano->idPlano }}" {{ request('plano_id') == $plano->idPlano ? 'selected' : '' }}>
                            {{ $plano->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filtrar
                </button>
                <a href="{{ route('clientes.index') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <div class="px-5 py-5 bg-white border-b flex flex-col sm:flex-row items-center sm:justify-between mb-0">
        <div class="text-sm text-gray-600">
            Mostrando {{ $clientes->firstItem() ?? 0 }}–{{ $clientes->lastItem() ?? 0 }} de {{ $clientes->total() }} clientes
        </div>
        <div class="flex items-center gap-2">
            @if ($clientes->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Anterior</span>
            @else
                <a href="{{ $clientes->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Anterior</a>
            @endif
            {{ $clientes->appends(request()->query())->links() }}
            @if ($clientes->hasMorePages())
                <a href="{{ $clientes->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Próxima</a>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Próxima</span>
            @endif
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Plano
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Próx. Vencimento
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $cliente->nome }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $cliente->email }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="inline-block px-3 py-1 rounded-full font-semibold {{ $cliente->status_badge_class }}">
                                {{ $cliente->status }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $cliente->plano->nome ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            @php
                                $proximoVencimentoExibicao = 'N/A';
                                $ultimaMensalidadePaga = $cliente->mensalidades()->where('status', 'Paga')->latest('dataVencimento')->first();
                                $classeCorVencimento = 'text-gray-500';

                                if ($ultimaMensalidadePaga && $cliente->plano) {
                                    $dataVencimentoCalculada = \Carbon\Carbon::parse($ultimaMensalidadePaga->dataVencimento)->addDays($cliente->plano->duracaoDias);
                                    if ($dataVencimentoCalculada->isPast()) {
                                        $classeCorVencimento = 'text-red-600';
                                    } elseif ($dataVencimentoCalculada->diffInDays(\Carbon\Carbon::now()) <= 7) {
                                        $classeCorVencimento = 'text-orange-600';
                                    } else {
                                        $classeCorVencimento = 'text-green-600';
                                    }
                                    $proximoVencimentoExibicao = $dataVencimentoCalculada->format('d/m/Y');
                                } else if ($cliente->plano) {
                                    $dataVencimentoCalculada = \Carbon\Carbon::now()->addDays($cliente->plano->duracaoDias);
                                    $classeCorVencimento = 'text-blue-600';
                                    $proximoVencimentoExibicao = $dataVencimentoCalculada->format('d/m/Y') . ' (Novo)';
                                }
                            @endphp
                            <strong class="{{ $classeCorVencimento }}">
                                {{ $proximoVencimentoExibicao }}
                            </strong>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('clientes.edit', $cliente->idCliente) }}" class="text-blue-600 hover:text-blue-900">Editar</a>

                                @if($cliente->plano)
                                    <button type="button" data-action="{{ route('clientes.renew', $cliente->idCliente) }}" class="text-green-600 hover:text-green-900 text-sm btn-renovar-cliente">Renovar</button>
                                @endif

                                    <form action="{{ route('clientes.destroy', $cliente->idCliente) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Nenhum cliente cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-5 bg-white border-t flex flex-col sm:flex-row items-center sm:justify-between mt-6">
            <div class="flex items-center gap-2">
                @if ($clientes->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Anterior</span>
                @else
                    <a href="{{ $clientes->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Anterior</a>
                @endif
                {{ $clientes->appends(request()->query())->links() }}
                @if ($clientes->hasMorePages())
                    <a href="{{ $clientes->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Próxima</a>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5 rounded-md">Próxima</span>
                @endif
            </div>
        </div>
    </div>
    <div id="modalRenovarCliente" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4 text-black">Selecione a forma de pagamento</h3>
            <form id="formRenovarCliente" method="POST" action="#" class="space-y-4">
                @csrf
                <div>
                    <label for="formaPagamentoCliente" class="block text-sm text-gray-700 mb-1">Forma de pagamento</label>
                    <select id="formaPagamentoCliente" name="formaPagamento" class="select" required>
                        <option value="">Selecione</option>
                        <option value="Dinheiro">Dinheiro</option>
                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                        <option value="Cartão de Débito">Cartão de Débito</option>
                        <option value="PIX">PIX</option>
                        <option value="Boleto">Boleto</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" id="btnCancelarModalCliente" class="px-3 py-1 rounded bg-gray-200 text-gray-800 hover:bg-gray-300">Cancelar</button>
                    <button type="submit" class="px-3 py-1 rounded bg-green-600 text-white hover:bg-green-700">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('body_scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalCliente = document.getElementById('modalRenovarCliente');
    const formCliente = document.getElementById('formRenovarCliente');
    const cancelCliente = document.getElementById('btnCancelarModalCliente');
    const buttons = document.querySelectorAll('.btn-renovar-cliente');
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const action = btn.getAttribute('data-action');
            if (formCliente) formCliente.setAttribute('action', action);
            if (modalCliente) modalCliente.classList.remove('hidden');
            if (modalCliente) modalCliente.classList.add('flex');
        });
    });
    cancelCliente && cancelCliente.addEventListener('click', () => {
        modalCliente.classList.add('hidden');
        modalCliente.classList.remove('flex');
    });
    modalCliente && modalCliente.addEventListener('click', (e) => {
        if (e.target === modalCliente) {
            modalCliente.classList.add('hidden');
            modalCliente.classList.remove('flex');
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalCliente && !modalCliente.classList.contains('hidden')) {
            modalCliente.classList.add('hidden');
            modalCliente.classList.remove('flex');
        }
    });
});
</script>
 
@endpush
