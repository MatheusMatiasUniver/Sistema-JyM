@extends('layouts.app')

@section('title', 'Novo Equipamento - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Novo Equipamento</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('equipamentos.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                <input type="text" name="descricao" required class="border rounded px-2 py-1 w-full text-black">
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fabricante</label>
                    <input type="text" name="fabricante" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Modelo</label>
                    <input type="text" name="modelo" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Número de Série</label>
                    <input type="text" name="numeroSerie" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" class="select">
                        <option value="Ativo">Ativo</option>
                        <option value="Em Manutenção">Em Manutenção</option>
                        <option value="Desativado">Desativado</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Data de Aquisição</label>
                    <input type="date" name="dataAquisicao" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Valor de Aquisição</label>
                    <input type="number" step="0.01" name="valorAquisicao" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Garantia até</label>
                    <input type="date" name="garantiaFim" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Centro de Custo</label>
                    <input type="text" name="centroCusto" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('equipamentos.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
