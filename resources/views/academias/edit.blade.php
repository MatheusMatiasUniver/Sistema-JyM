@extends('layouts.app')

@section('title', 'Editar Academia - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Academia: {{ $academia->nome }}</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 max-w-lg mx-auto">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 max-w-lg mx-auto">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ route('academias.update', $academia->idAcademia) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome da Academia:</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $academia->nome) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="CNPJ" class="block text-gray-700 text-sm font-bold mb-2">CNPJ:</label>
                <input type="text" id="CNPJ" name="CNPJ" value="{{ old('CNPJ', $academia->CNPJ) }}" required placeholder="00.000.000/0000-00"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('CNPJ') border-red-500 @enderror">
                @error('CNPJ')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefone" class="block text-gray-700 text-sm font-bold mb-2">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $academia->telefone) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror"
                       placeholder="(00) 00000-0000">
                @error('telefone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $academia->email) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="endereco" class="block text-gray-700 text-sm font-bold mb-2">Endereço:</label>
                <textarea id="endereco" name="endereco"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('endereco') border-red-500 @enderror">{{ old('endereco', $academia->endereco) }}</textarea>
                @error('endereco')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="responsavel" class="block text-gray-700 text-sm font-bold mb-2">Responsável:</label>
                <input type="text" id="responsavel" name="responsavel" value="{{ old('responsavel', $academia->responsavel) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('responsavel') border-red-500 @enderror">
                @error('responsavel')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Atualizar Academia
                </button>
                <a href="{{ route('academias.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
