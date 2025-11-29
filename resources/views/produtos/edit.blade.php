@extends('layouts.app')

@section('title', 'Editar Produto - Sistema JyM')

@section('content')
    @php
        $permitirEdicaoManualEstoque = $permitirEdicaoManualEstoque ?? false;
    @endphp

    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Produto: {{ $produto->nome }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ route('produtos.update', $produto->idProduto) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="idMarca" class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                <select id="idMarca" name="idMarca" required
                        class="select @error('idMarca') border-red-500 @enderror">
                    <option value="">Selecione uma marca</option>
                    @isset($marcas)
                        @foreach($marcas as $marca)
                            <option value="{{ $marca->idMarca }}" {{ old('idMarca', $produto->idMarca) == $marca->idMarca ? 'selected' : '' }}>
                                {{ $marca->nome }}
                            </option>
                        @endforeach
                    @endisset
                </select>
                @error('idMarca')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idFornecedor" class="block text-gray-700 text-sm font-bold mb-2">Fornecedor (opcional):</label>
                <select id="idFornecedor" name="idFornecedor"
                        class="select @error('idFornecedor') border-red-500 @enderror">
                    <option value="">Selecione um fornecedor</option>
                    @isset($fornecedores)
                        @foreach($fornecedores as $fornecedor)
                            <option value="{{ $fornecedor->idFornecedor }}" {{ old('idFornecedor', $produto->idFornecedor) == $fornecedor->idFornecedor ? 'selected' : '' }}>
                                {{ $fornecedor->razaoSocial }}
                            </option>
                        @endforeach
                    @endisset
                </select>
                @error('idFornecedor')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="precoCompra" class="block text-gray-700 text-sm font-bold mb-2">Preço de Custo:</label>
                <input type="number" step="0.01" id="precoCompra" name="precoCompra" value="{{ old('precoCompra', $produto->precoCompra) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('precoCompra') border-red-500 @enderror">
                @error('precoCompra')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome do Produto:</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idCategoria" class="block text-gray-700 text-sm font-bold mb-2">Categoria:</label>
                <select id="idCategoria" name="idCategoria" required
                        class="select @error('idCategoria') border-red-500 @enderror">
                    <option value="">Selecione uma categoria</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->idCategoria }}" 
                                {{ old('idCategoria', $produto->idCategoria) == $categoria->idCategoria ? 'selected' : '' }}>
                            {{ $categoria->nome }}
                        </option>
                    @endforeach
                </select>
                @error('idCategoria')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                @if($categorias->isEmpty())
                    <small class="text-gray-600">
                        Nenhuma categoria ativa encontrada. 
                        <a href="{{ route('categorias.create') }}" class="text-blue-500 hover:text-blue-800">Criar nova categoria</a>
                    </small>
                @endif
            </div>

            <div class="mb-4">
                <label for="preco" class="block text-gray-700 text-sm font-bold mb-2">Preço:</label>
                <input type="number" step="0.01" id="preco" name="preco" value="{{ old('preco', $produto->preco) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('preco') border-red-500 @enderror">
                @error('preco')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="estoqueMinimo" class="block text-gray-700 text-sm font-bold mb-2">Estoque Mínimo:</label>
                <input type="number" id="estoqueMinimo" name="estoqueMinimo" value="{{ old('estoqueMinimo', $produto->estoqueMinimo) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('estoqueMinimo') border-red-500 @enderror">
                @error('estoqueMinimo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="estoque" class="block text-gray-700 text-sm font-bold mb-2">Estoque:</label>
                @if($permitirEdicaoManualEstoque)
                    <input type="number" id="estoque" name="estoque" value="{{ old('estoque', $produto->estoque) }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('estoque') border-red-500 @enderror">
                @else
                    <input type="number" id="estoque" value="{{ $produto->estoque }}" disabled
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1">A edição manual de estoque está desativada nos ajustes. Utilize o botão "Ajustar Estoque" ou registre uma compra.</p>
                @endif
                @error('estoque')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="descricao" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="3"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descricao') border-red-500 @enderror">{{ old('descricao', $produto->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="imagem" class="block text-gray-700 text-sm font-bold mb-2">Imagem do Produto:</label>
                @if ($produto->imagem)
                    <div class="flex items-center space-x-4 mb-2">
                        <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}" class="w-24 h-24 object-cover rounded-md">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remover_imagem" value="1" class="form-checkbox h-5 w-5 text-red-600 text-black">
                            <span class="ml-2 text-gray-700">Remover imagem existente</span>
                        </label>
                    </div>
                @else
                    <p class="text-gray-600 mb-2">Nenhuma imagem cadastrada.</p>
                @endif
                <input type="file" id="imagem" name="imagem"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('imagem') border-red-500 @enderror">
                @error('imagem')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Atualizar Produto
                </button>
                <a href="{{ route('produtos.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
