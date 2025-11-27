@extends('layouts.app')

@section('title', 'Editar Marca - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Marca: {{ $marca->nome }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ route('marcas.update', $marca->idMarca) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $marca->nome) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="paisOrigem" class="block text-gray-700 text-sm font-bold mb-2">País de Origem:</label>
                <input type="text" id="paisOrigem" name="paisOrigem" value="{{ old('paisOrigem', $marca->paisOrigem) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('paisOrigem') border-red-500 @enderror">
                @error('paisOrigem')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="site" class="block text-gray-700 text-sm font-bold mb-2">Site:</label>
                <input type="url" id="site" name="site" value="{{ old('site', $marca->site) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('site') border-red-500 @enderror">
                @error('site')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="ativo" value="1" class="form-checkbox h-5 w-5 text-black" {{ old('ativo', $marca->ativo) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Ativo</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Atualizar Marca</button>
                <a href="{{ route('marcas.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
            </div>
        </form>
    </div>
@endsection