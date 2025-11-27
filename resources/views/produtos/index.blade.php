@extends('layouts.app')

@section('title', 'Gerenciar Produtos - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Gerenciar Produtos</h1>

    <div class="mb-4">
        <a href="{{ route('produtos.create') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Cadastrar Novo Produto
        </a>
    </div>

    <!-- Filtros -->
    <x-search-filter-dropdown 
        placeholder="Nome ou descrição do produto..."
        :filters="[
            [
                'name' => 'categoria',
                'label' => 'Categoria',
                'type' => 'select',
                'options' => $categorias->pluck('nome', 'idCategoria')->toArray()
            ],
            [
                'name' => 'preco_minimo',
                'label' => 'Preço Mínimo',
                'type' => 'number',
                'placeholder' => '0.00',
                'step' => '0.01',
                'min' => '0'
            ],
            [
                'name' => 'preco_maximo',
                'label' => 'Preço Máximo',
                'type' => 'number',
                'placeholder' => '999.99',
                'step' => '0.01',
                'min' => '0'
            ],
            [
                'name' => 'estoque_minimo',
                'label' => 'Estoque Mínimo',
                'type' => 'number',
                'placeholder' => '0',
                'min' => '0'
            ],
            [
                'name' => 'estoque_maximo',
                'label' => 'Estoque Máximo',
                'type' => 'number',
                'placeholder' => '999',
                'min' => '0'
            ]
        ]"
        :sort-options="[
            'nome_asc' => 'Nome (A-Z)',
            'nome_desc' => 'Nome (Z-A)',
            'preco_asc' => 'Preço (Menor)',
            'preco_desc' => 'Preço (Maior)',
            'estoque_asc' => 'Estoque (Menor)',
            'estoque_desc' => 'Estoque (Maior)',
            'categoria_asc' => 'Categoria (A-Z)',
            'categoria_desc' => 'Categoria (Z-A)'
        ]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Imagem
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Categoria
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
                @forelse ($produtos as $produto)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            @if ($produto->imagem)
                                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="w-16 h-16 object-cover rounded-md">
                            @else
                                <span class="text-gray-500">Sem Imagem</span>
                            @endif
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $produto->nome }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $produto->categoria ? $produto->categoria->nome : 'Sem categoria' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $produto->estoque }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('produtos.edit', $produto->idProduto) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                @if($produto->podeDeletar())
                                    <form action="{{ route('produtos.destroy', $produto->idProduto) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" 
                                          title="Não é possível excluir produto com vendas associadas">
                                        Excluir
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Nenhum produto cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
