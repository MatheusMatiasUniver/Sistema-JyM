@extends('layouts.app')

@section('title', 'Novo Material - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Novo Material</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('materiais.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                <input type="text" name="descricao" required class="border rounded px-2 py-1 w-full text-black">
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Estoque</label>
                    <input type="number" name="estoque" min="0" value="0" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Unidade</label>
                    <input type="text" name="unidadeMedida" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mínimo</label>
                    <input type="number" name="estoqueMinimo" min="0" value="0" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('materiais.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

