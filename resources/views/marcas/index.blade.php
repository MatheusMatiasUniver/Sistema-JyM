@extends('layouts.app')

@section('title', 'Marcas - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Marcas</h1>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('marcas.create') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Cadastrar Nova Marca
        </a>
        <form action="{{ route('marcas.index') }}" method="GET" class="flex gap-3 items-end">
            <div>
                <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Pesquisar:</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nome, país ou site"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div>
                <label for="ativo" class="block text-gray-700 text-sm font-bold mb-2">Ativo:</label>
                <select id="ativo" name="ativo" class="select">
                    <option value="">Todos</option>
                    <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativo</option>
                    <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativo</option>
                </select>
            </div>
            <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Filtrar</button>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">País de Origem</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Site</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($marcas as $marca)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $marca->nome }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $marca->paisOrigem ?? 'N/A' }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            @if($marca->site)
                                <a href="{{ $marca->site }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ $marca->site }}</a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="inline-block px-3 py-1 rounded-full font-semibold {{ $marca->ativo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $marca->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('marcas.edit', $marca->idMarca) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                <form action="{{ route('marcas.destroy', $marca->idMarca) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta marca?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Nenhuma marca cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6 px-5 py-3">{{ $marcas->links() }}</div>
    </div>
@endsection
