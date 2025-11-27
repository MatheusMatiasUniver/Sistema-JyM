@extends('layouts.app')

@section('title', 'Editar Plano de Assinatura - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Plano de Assinatura: {{ $plano->nome }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ route('planos.update', $plano->idPlano) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome do Plano:</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $plano->nome) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="descricao" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="3"
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descricao') border-red-500 @enderror">{{ old('descricao', $plano->descricao) }}</textarea>
                @error('descricao')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="valor" class="block text-gray-700 text-sm font-bold mb-2">Valor:</label>
                <input type="number" step="0.01" id="valor" name="valor" value="{{ old('valor', $plano->valor) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('valor') border-red-500 @enderror">
                @error('valor')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="duracaoDias" class="block text-gray-700 text-sm font-bold mb-2">Duração (dias):</label>
                <input type="number" id="duracaoDias" name="duracaoDias" value="{{ old('duracaoDias', $plano->duracaoDias) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('duracaoDias') border-red-500 @enderror">
                @error('duracaoDias')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="idAcademia" class="block text-gray-700 text-sm font-bold mb-2">Academia:</label>
                <select id="idAcademia" name="idAcademia" required
                        class="select @error('idAcademia') border-red-500 @enderror">
                    <option value="">Selecione uma Academia</option>
                    @foreach ($academias as $academia)
                        <option value="{{ $academia->idAcademia }}" {{ old('idAcademia', $plano->idAcademia) == $academia->idAcademia ? 'selected' : '' }}>
                            {{ $academia->nome }}
                        </option>
                    @endforeach
                </select>
                @error('idAcademia')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Atualizar Plano
                </button>
                <a href="{{ route('planos.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
