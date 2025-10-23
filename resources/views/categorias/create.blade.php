@extends('layouts.app')

@section('title', 'Cadastrar Categoria - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Cadastrar Nova Categoria</h1>

    <div class="mb-4">
        <a href="{{ route('categorias.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Voltar para Lista
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('categorias.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome da Categoria <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white" placeholder="Digite o nome da categoria" maxlength="100" required>
                    @error('nome')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white" required>
                        <option value="">Selecione o status</option>
                        <option value="Ativo" {{ old('status') === 'Ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="Inativo" {{ old('status') === 'Inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição
                    </label>
                    <textarea id="descricao" name="descricao" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white" placeholder="Digite uma descrição para a categoria (opcional)">
                        {{ old('descricao') }}
                    </textarea>
                    
                    @error('descricao')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="{{ route('categorias.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Cadastrar Categoria
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('nome').addEventListener('input', function() {
            const maxLength = 100;
            const currentLength = this.value.length;
            const remaining = maxLength - currentLength;
            
            let counter = document.getElementById('nome-counter');
            if (!counter) {
                counter = document.createElement('p');
                counter.id = 'nome-counter';
                counter.className = 'text-sm text-gray-500 mt-1';
                this.parentNode.appendChild(counter);
            }
            
            counter.textContent = `${currentLength}/${maxLength} caracteres`;
            counter.className = remaining < 10 ? 'text-sm text-red-500 mt-1' : 'text-sm text-gray-500 mt-1';
        });
    </script>
@endsection