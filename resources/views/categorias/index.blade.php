@extends('layouts.app')

@section('title', 'Gerenciar Categorias - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Gerenciar Categorias</h1>

    <div class="mb-4">
        <a href="{{ route('categorias.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Cadastrar Nova Categoria
        </a>
    </div>

    <!-- Filtros -->
    <x-search-filter-dropdown 
        placeholder="Nome ou descrição da categoria..."
        :filters="[
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => 'select',
                'options' => [
                    'Ativo' => 'Ativo',
                    'Inativo' => 'Inativo'
                ]
            ]
        ]"
        :sort-options="[
            'nome_asc' => 'Nome (A-Z)',
            'nome_desc' => 'Nome (Z-A)',
            'produtos_asc' => 'Produtos (Menor)',
            'produtos_desc' => 'Produtos (Maior)',
            'status_asc' => 'Status (A-Z)',
            'status_desc' => 'Status (Z-A)'
        ]"
    />

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Descrição
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Produtos
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="font-medium text-gray-900">{{ $categoria->nome }}</div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="text-gray-600">
                                {{ $categoria->descricao ? Str::limit($categoria->descricao, 50) : 'Sem descrição' }}
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $categoria->status === 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $categoria->status }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="text-gray-600">
                                {{ $categoria->produtos_count }} produto(s)
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('categorias.show', $categoria) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-sm">
                                    Ver
                                </a>
                                <a href="{{ route('categorias.edit', $categoria) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 text-sm">
                                    Editar
                                </a>
                                @if($categoria->produtos_count == 0)
                                    <form action="{{ route('categorias.destroy', $categoria) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                                            Excluir
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm cursor-not-allowed" 
                                          title="Não é possível excluir categoria com produtos associados">
                                        Excluir
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                            Nenhuma categoria encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categorias->hasPages())
        <div class="mt-6">
            {{ $categorias->links() }}
        </div>
    @endif
@endsection
