@extends('layouts.app')

@section('title', 'Cadastrar Cliente - Sistema JyM')

@section('content')
        <h1 class="text-3xl font-bold mb-6 text-grip-6">Cadastrar Novo Cliente</h1>
    
        @if ($errors->any())
            <div class="alert alert-danger mb-4 p-4 rounded-lg bg-red-100 text-red-700">
                <strong class="font-bold">Opa!</strong> Algo deu errado. Verifique os erros abaixo:
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror" value="{{ old('nome') }}" required>
                @error('nome')
                    <div class="text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="cpf" class="block text-gray-700 text-sm font-bold mb-2">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                       placeholder="000.000.000-00">
                @error('cpf')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefone" class="block text-gray-700 text-sm font-bold mb-2">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror"
                       placeholder="(00) 00000-0000">
                @error('telefone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>



            <div class="mb-4">
                <label for="dataNascimento" class="block text-gray-700 text-sm font-bold mb-2">Data de Nascimento:</label>
                <input type="date" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('dataNascimento') border-red-500 @enderror">
                @error('dataNascimento')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select id="status" name="status" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-white leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror">
                    <option value="Ativo" {{ old('status', $cliente->status ?? '') == 'Ativo' ? 'selected' : '' }}>
                        Ativo
                    </option>
                    <option value="Inativo" {{ old('status', $cliente->status ?? '') == 'Inativo' ? 'selected' : '' }}>
                        Inativo
                    </option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idPlano" class="block text-gray-700 text-sm font-bold mb-2">Plano de Assinatura:</label>
                <select id="idPlano" name="idPlano"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('idPlano') border-red-500 @enderror">
                    <option value="">Selecione um Plano (Opcional)</option>
                    @foreach($planos as $plano)
                        <option value="{{ $plano->idPlano }}" {{ old('idPlano') == $plano->idPlano ? 'selected' : '' }}>
                            {{ $plano->nome }} (R\$ {{ number_format($plano->valor, 2, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @error('idPlano')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="foto" class="block text-gray-700 text-sm font-bold mb-2">Foto (Opcional):</label>
                <input type="file" id="foto" name="foto" accept="image/*"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('foto') border-red-500 @enderror"
                       onchange="previewImage(this)">
                @error('foto')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                
                <div id="imagePreview" class="mt-3 hidden">
                    <p class="text-sm text-gray-600 mb-2">Preview da imagem:</p>
                    <img id="preview" src="" alt="Preview" class="max-w-xs max-h-48 rounded border shadow">
                    <button type="button" onclick="removeImage()" class="ml-3 text-red-500 hover:text-red-700 text-sm">
                        Remover imagem
                    </button>
                </div>
            </div>

            <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cadastrar Cliente
            </button>
        </form>    

        <script>
            function previewImage(input) {
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');
                
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                    }
                    
                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewContainer.classList.add('hidden');
                }
            }
            
            function removeImage() {
                const input = document.getElementById('foto');
                const previewContainer = document.getElementById('imagePreview');
                
                input.value = '';
                previewContainer.classList.add('hidden');
            }
        </script>
@endsection
