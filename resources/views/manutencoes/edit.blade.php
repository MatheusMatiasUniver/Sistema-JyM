@extends('layouts.app')

@section('title', 'Finalizar Manutenção - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Finalizar Manutenção</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Informações da Manutenção -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6 max-w-2xl">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Informações da Solicitação</h2>
        </div>
        
        <div class="p-6 grid grid-cols-2 gap-4">
            <div>
                <span class="text-gray-500 text-sm">Equipamento:</span>
                <p class="font-medium text-gray-900">{{ $manutencao->equipamento?->descricao ?? 'N/A' }}</p>
                <p class="text-gray-500 text-sm">{{ $manutencao->equipamento?->fabricante ?? '' }} {{ $manutencao->equipamento?->modelo ?? '' }}</p>
            </div>
            <div>
                <span class="text-gray-500 text-sm">Tipo:</span>
                <p class="font-medium">
                    <span class="px-2 py-1 rounded text-xs {{ $manutencao->tipo === 'preventiva' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                        {{ ucfirst($manutencao->tipo) }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-gray-500 text-sm">Data de Solicitação:</span>
                <p class="font-medium text-gray-900">{{ $manutencao->dataSolicitacao ? $manutencao->dataSolicitacao->format('d/m/Y') : '-' }}</p>
            </div>
            <div>
                <span class="text-gray-500 text-sm">Data Programada:</span>
                <p class="font-medium text-gray-900">{{ $manutencao->dataProgramada ? $manutencao->dataProgramada->format('d/m/Y') : '-' }}</p>
            </div>
            <div class="col-span-2">
                <span class="text-gray-500 text-sm">Problema Reportado:</span>
                <p class="font-medium text-gray-900 bg-gray-50 p-3 rounded border border-gray-200 mt-1">{{ $manutencao->descricao }}</p>
            </div>
        </div>
    </div>

    <!-- Formulário de Finalização -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden max-w-2xl">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Dados da Execução</h2>
        </div>

        <form action="{{ route('manutencoes.update', $manutencao->idManutencao) }}" method="POST" class="p-6" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="dataExecucao" class="block text-gray-700 text-sm font-bold mb-2">Data de Execução *</label>
                <input type="date" name="dataExecucao" id="dataExecucao" required
                    value="{{ old('dataExecucao', now()->format('Y-m-d')) }}"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('dataExecucao') border-red-500 @enderror">
                @error('dataExecucao')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="servicoRealizado" class="block text-gray-700 text-sm font-bold mb-2">Serviço Realizado *</label>
                <textarea name="servicoRealizado" id="servicoRealizado" rows="4" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('servicoRealizado') border-red-500 @enderror"
                    placeholder="Descreva detalhadamente o serviço executado...">{{ old('servicoRealizado') }}</textarea>
                @error('servicoRealizado')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="custo" class="block text-gray-700 text-sm font-bold mb-2">Custo (R$)</label>
                    <input type="number" name="custo" id="custo" step="0.01" min="0"
                        value="{{ old('custo') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('custo') border-red-500 @enderror"
                        placeholder="0,00">
                    @error('custo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="responsavel" class="block text-gray-700 text-sm font-bold mb-2">Responsável Técnico *</label>
                    <input type="text" name="responsavel" id="responsavel" required
                        value="{{ old('responsavel') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('responsavel') border-red-500 @enderror"
                        placeholder="Nome do técnico">
                    @error('responsavel')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="fornecedorId" class="block text-gray-700 text-sm font-bold mb-2">Fornecedor/Prestador</label>
                <select name="fornecedorId" id="fornecedorId"
                    class="select @error('fornecedorId') border-red-500 @enderror">
                    <option value="">Selecione o fornecedor (opcional)...</option>
                    @foreach($fornecedores as $f)
                        <option value="{{ $f->idFornecedor }}" 
                            {{ old('fornecedorId', $manutencao->fornecedorId) == $f->idFornecedor ? 'selected' : '' }}>
                            {{ $f->razaoSocial }}
                        </option>
                    @endforeach
                </select>
                @error('fornecedorId')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            <strong>Atenção:</strong> Ao finalizar esta manutenção, o status do equipamento será 
                            automaticamente alterado para <strong>"Ativo"</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                    Finalizar Manutenção
                </button>
                <a href="{{ route('manutencoes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
