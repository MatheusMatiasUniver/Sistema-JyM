@extends('layouts.app')

@section('title', 'Nova Manutenção - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Nova Manutenção</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
        <form action="{{ route('manutencoes.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Equipamento</label>
                <select name="idEquipamento" class="border rounded px-2 py-1 w-full" required>
                    @foreach($equipamentos as $e)
                        <option value="{{ $e->idEquipamento }}">{{ $e->descricao }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Tipo</label>
                <select name="tipo" class="border rounded px-2 py-1 w-full" required>
                    <option value="preventiva">Preventiva</option>
                    <option value="corretiva">Corretiva</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Data Programada</label>
                    <input type="date" name="dataProgramada" class="border rounded px-2 py-1 w-full text-black">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Data Execução</label>
                    <input type="date" name="dataExecucao" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Fornecedor</label>
                    <select name="fornecedorId" class="border rounded px-2 py-1 w-full">
                        <option value="">Selecione</option>
                        @foreach($fornecedores as $f)
                            <option value="{{ $f->idFornecedor }}">{{ $f->razaoSocial }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Custo</label>
                    <input type="number" step="0.01" name="custo" class="border rounded px-2 py-1 w-full text-black">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Observações</label>
                <input type="text" name="observacoes" class="border rounded px-2 py-1 w-full text-black">
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Salvar</button>
                <a href="{{ route('manutencoes.index') }}" class="text-blue-600">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

