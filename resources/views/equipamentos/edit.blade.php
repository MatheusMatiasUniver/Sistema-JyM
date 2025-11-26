@extends('layouts.app')

@section('title', 'Editar Equipamento - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Equipamento</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('equipamentos.update', $equipamento->idEquipamento) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Descrição</label>
                <input type="text" name="descricao" value="{{ old('descricao', $equipamento->descricao) }}" required class="border rounded px-2 py-1 w-full text-black">
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fabricante</label>
                    <input type="text" name="fabricante" value="{{ old('fabricante', $equipamento->fabricante) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Modelo</label>
                    <input type="text" name="modelo" value="{{ old('modelo', $equipamento->modelo) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Número de Série</label>
                    <input type="text" name="numeroSerie" value="{{ old('numeroSerie', $equipamento->numeroSerie) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" class="border rounded px-2 py-1 w-full text-black">
                        <option value="Ativo" @if(old('status', $equipamento->status->value) == 'Ativo') selected @endif>Ativo</option>
                        <option value="Em Manutenção" @if(old('status', $equipamento->status->value) == 'Em Manutenção') selected @endif>Em Manutenção</option>
                        <option value="Desativado" @if(old('status', $equipamento->status->value) == 'Desativado') selected @endif>Desativado</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Data de Aquisição</label>
                    <input type="date" name="dataAquisicao" value="{{ old('dataAquisicao', optional($equipamento->dataAquisicao)->format('Y-m-d')) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Valor de Aquisição</label>
                    <input type="number" step="0.01" name="valorAquisicao" value="{{ old('valorAquisicao', $equipamento->valorAquisicao) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Garantia até</label>
                    <input type="date" name="garantiaFim" value="{{ old('garantiaFim', optional($equipamento->garantiaFim)->format('Y-m-d')) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Centro de Custo</label>
                    <input type="text" name="centroCusto" value="{{ old('centroCusto', $equipamento->centroCusto) }}" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('equipamentos.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

