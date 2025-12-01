@extends('layouts.app')

@section('title', 'Detalhes da Categoria - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Detalhes da Categoria</h1>

    <div class="mb-4 flex space-x-2">
        <a href="{{ route('categorias.index') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Voltar para Lista
        </a>
        <a href="{{ route('categorias.edit', $categoria) }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Editar Categoria
        </a>
        @if($categoria->podeDeletar())
            <form action="{{ route('categorias.destroy', $categoria) }}" autocomplete="off"
                  method="POST" 
                  class="inline"
                  data-confirm="Tem certeza que deseja excluir esta categoria?"
                  data-confirm-title="Excluir Categoria"
                  data-confirm-icon="danger"
                  data-confirm-text="Excluir"
                  data-cancel-text="Cancelar">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">
                    Excluir Categoria
                </button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações da Categoria -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Informações da Categoria</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <p class="text-gray-900 font-medium">{{ $categoria->nome }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $categoria->status === 'Ativo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $categoria->status }}
                        </span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Academia</label>
                        <p class="text-gray-900">{{ $categoria->academia->nome ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produtos Associados</label>
                        <p class="text-gray-900 font-medium">{{ $categoria->contarProdutos() }} produto(s)</p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <p class="text-gray-900">{{ $categoria->descricao ?: 'Sem descrição' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Criado em</label>
                        <p class="text-gray-600">{{ $categoria->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Atualizado em</label>
                        <p class="text-gray-600">{{ $categoria->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div>
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Estatísticas</h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $categoria->contarProdutos() }}</div>
                        <div class="text-sm text-blue-600">Produtos Associados</div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">
                            {{ $categoria->isAtiva() ? 'Ativa' : 'Inativa' }}
                        </div>
                        <div class="text-sm text-green-600">Status Atual</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Produtos -->
    @if($categoria->produtos->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Produtos desta Categoria</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Nome
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Preço
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Estoque
                                </th>
                                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoria->produtos as $produto)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="font-medium text-gray-900">{{ $produto->nome }}</div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="text-gray-900">R$ {{ number_format($produto->preco, 2, ',', '.') }}</div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="text-gray-900">{{ $produto->estoque }}</div>
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <a href="{{ route('produtos.show', $produto) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm">
                                            Ver Produto
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="mt-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <div class="text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto associado</h3>
                    <p class="mt-1 text-sm text-gray-500">Esta categoria ainda não possui produtos associados.</p>
                </div>
            </div>
        </div>
    @endif
@endsection
