@extends('layouts.app')

@section('title', 'Clientes - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Lista de Clientes</h1>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('clientes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Adicionar Novo Cliente
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('clientes.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-grow">
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Pesquisar:</label>
                <input type="text" id="search" name="search" placeholder="Nome, CPF ou Email"
                       value="{{ request('search') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label for="status_filter" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select id="status_filter" name="status_filter"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todos</option>
                    <option value="Ativo" {{ request('status_filter') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="Inativo" {{ request('status_filter') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>

            <div>
                <label for="plano_id" class="block text-gray-700 text-sm font-bold mb-2">Plano:</label>
                <select id="plano_id" name="plano_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Todos</option>
                    @foreach ($allPlanos as $plano)
                        <option value="{{ $plano->idPlano }}" {{ request('plano_id') == $plano->idPlano ? 'selected' : '' }}>
                            {{ $plano->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filtrar
                </button>
                <a href="{{ route('clientes.index') }}" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Limpar Filtros
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        CPF
                    </th>
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
                            {{ $cliente->cpfFormatado }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $cliente->nome }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $cliente->email }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="relative inline-block px-3 py-1 font-semibold leading-tight {{ $cliente->status == 'Ativo' ? 'text-green-900' : 'text-red-900' }}">
                                <span aria-hidden="true" class="absolute inset-0 opacity-50 rounded-full {{ $cliente->status == 'Ativo' ? 'bg-green-200' : 'bg-red-200' }}"></span>
                                <span class="relative">{{ $cliente->status }}</span>
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
                                    <form action="{{ route('clientes.renew', $cliente->idCliente) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja renovar o plano {{ $cliente->plano->nome }} para {{ $cliente->nome }}?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 text-sm">Renovar</button>
                                    </form>
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
                        <td colspan="7" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Nenhum cliente cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- Paginação --}}
        <div class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between">
            {{ $clientes->appends(request()->except('page'))->links() }}
        </div>
    </div>
@endsection