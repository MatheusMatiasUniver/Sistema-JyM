@extends('layouts.app')

@section('title', 'Nova Requisição de Material - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Nova Requisição de Material</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('materiais.requisicoes.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Material</label>
                <select name="idMaterial" class="select" required>
                    @foreach($materiais as $m)
                        <option value="{{ $m->idMaterial }}">{{ $m->descricao }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Quantidade</label>
                    <input type="number" name="quantidade" min="1" class="border rounded px-2 py-1 w-full text-black" required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Centro de Custo</label>
                    <input type="text" name="centroCusto" class="border rounded px-2 py-1 w-full text-black" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Motivo</label>
                <input type="text" name="motivo" class="border rounded px-2 py-1 w-full text-black">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('materiais.requisicoes.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
