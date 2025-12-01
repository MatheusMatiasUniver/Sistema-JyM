@extends('layouts.app')

@section('title', 'Nova Categoria - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Nova Categoria de Contas a Pagar</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-xl">
        <form method="POST" action="{{ route('financeiro.categorias_contas_pagar.store') }}" autocomplete="off">
            @csrf
            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Salvar</button>
                <a href="{{ route('financeiro.categorias_contas_pagar.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
            </div>
        </form>
    </div>
@endsection