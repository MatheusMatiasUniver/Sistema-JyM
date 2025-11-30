@extends('layouts.app')

@section('title', 'Registrar Manutenção - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Registrar Manutenção</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl">
        <form action="{{ route('manutencoes.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="idEquipamento" class="block text-gray-700 text-sm font-bold mb-2">Equipamento *</label>
                <select name="idEquipamento" id="idEquipamento" required
                    class="select @error('idEquipamento') border-red-500 @enderror">
                    <option value="">Selecione o equipamento...</option>
                    @foreach($equipamentos as $eq)
                        <option value="{{ $eq->idEquipamento }}" 
                            {{ (old('idEquipamento', $equipamentoSelecionado?->idEquipamento) == $eq->idEquipamento) ? 'selected' : '' }}>
                            {{ $eq->descricao }} 
                            @if($eq->fabricante || $eq->modelo)
                                ({{ $eq->fabricante }} {{ $eq->modelo }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('idEquipamento')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="tipo" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Manutenção *</label>
                <select name="tipo" id="tipo" required
                    class="select @error('tipo') border-red-500 @enderror">
                    <option value="">Selecione o tipo...</option>
                    <option value="corretiva" {{ old('tipo') === 'corretiva' ? 'selected' : '' }}>
                        Corretiva (reparo de defeito)
                    </option>
                    <option value="preventiva" {{ old('tipo') === 'preventiva' ? 'selected' : '' }}>
                        Preventiva (manutenção programada)
                    </option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="descricao" class="block text-gray-700 text-sm font-bold mb-2">Descrição do Problema *</label>
                <textarea name="descricao" id="descricao" rows="4" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descricao') border-red-500 @enderror"
                    placeholder="Descreva o problema ou defeito encontrado...">{{ old('descricao') }}</textarea>
                @error('descricao')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="dataProgramada" class="block text-gray-700 text-sm font-bold mb-2">Data Programada</label>
                <input type="date" name="dataProgramada" id="dataProgramada" 
                    value="{{ old('dataProgramada') }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('dataProgramada') border-red-500 @enderror">
                <p class="text-gray-500 text-sm mt-1">Opcional. Para manutenções preventivas agendadas.</p>
                @error('dataProgramada')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="fornecedorId" class="block text-gray-700 text-sm font-bold mb-2">Fornecedor/Prestador</label>
                <select name="fornecedorId" id="fornecedorId"
                    class="select @error('fornecedorId') border-red-500 @enderror">
                    <option value="">Selecione o fornecedor (opcional)...</option>
                    @foreach($fornecedores as $f)
                        <option value="{{ $f->idFornecedor }}" {{ old('fornecedorId') == $f->idFornecedor ? 'selected' : '' }}>
                            {{ $f->razaoSocial }}
                        </option>
                    @endforeach
                </select>
                @error('fornecedorId')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Atenção:</strong> Ao registrar esta manutenção, o status do equipamento será 
                            automaticamente alterado para <strong>"Em Manutenção"</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-6 rounded">
                    Registrar Manutenção
                </button>
                <a href="{{ route('manutencoes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
