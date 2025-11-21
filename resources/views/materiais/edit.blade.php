@extends('layouts.app')

@section('title', 'Editar Material - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Material</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('materiais.update', $material->idMaterial) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                <input type="text" name="descricao" value="{{ old('descricao', $material->descricao) }}" required class="border rounded px-2 py-1 w-full text-black">
            </div>
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Estoque</label>
                    <input type="number" name="estoque" min="0" value="{{ old('estoque', $material->estoque) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Unidade</label>
                    <input type="text" name="unidadeMedida" value="{{ old('unidadeMedida', $material->unidadeMedida) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mínimo</label>
                    <input type="number" name="estoqueMinimo" min="0" value="{{ old('estoqueMinimo', $material->estoqueMinimo) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('materiais.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

