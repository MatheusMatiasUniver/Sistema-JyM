@extends('layouts.app')

@section('title', 'Detalhes da Manutenção - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Detalhes da Manutenção</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-3xl">
        <!-- Status Badge -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Manutenção #{{ $manutencao->idManutencao }}</h2>
            @php
                $statusValue = $manutencao->status instanceof \App\Models\StatusManutencao ? $manutencao->status->value : $manutencao->status;
            @endphp
            <span class="px-3 py-1 rounded text-sm font-semibold {{ $statusValue === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                {{ $statusValue }}
            </span>
        </div>

        <!-- Informações do Equipamento -->
        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-600 mb-3">Equipamento</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-500 text-sm">Descrição:</span>
                    <p class="font-medium">{{ $manutencao->equipamento?->descricao ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Fabricante/Modelo:</span>
                    <p class="font-medium">{{ $manutencao->equipamento?->fabricante ?? '-' }} {{ $manutencao->equipamento?->modelo ?? '' }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Número de Série:</span>
                    <p class="font-medium">{{ $manutencao->equipamento?->numeroSerie ?? '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Status Atual:</span>
                    @if($manutencao->equipamento)
                        <p class="font-medium">{{ $manutencao->equipamento->status->value ?? $manutencao->equipamento->status }}</p>
                    @else
                        <p class="font-medium text-gray-400">-</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informações da Solicitação -->
        <div class="border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-600 mb-3">Solicitação</h3>
            <div class="grid grid-cols-2 gap-4">
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
                    <p class="font-medium">{{ $manutencao->dataSolicitacao ? $manutencao->dataSolicitacao->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Data Programada:</span>
                    <p class="font-medium">{{ $manutencao->dataProgramada ? $manutencao->dataProgramada->format('d/m/Y') : '-' }}</p>
                </div>
                <div>
                    <span class="text-gray-500 text-sm">Fornecedor:</span>
                    <p class="font-medium">{{ $manutencao->fornecedor->razaoSocial ?? '-' }}</p>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-500 text-sm">Problema Reportado:</span>
                    <p class="font-medium bg-gray-50 p-3 rounded border mt-1">{{ $manutencao->descricao }}</p>
                </div>
            </div>
        </div>

        <!-- Informações da Execução (se concluída) -->
        @if($statusValue === 'Concluída')
            <div class="border-b pb-4 mb-4">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">Execução</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-500 text-sm">Data de Execução:</span>
                        <p class="font-medium">{{ $manutencao->dataExecucao ? $manutencao->dataExecucao->format('d/m/Y') : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Responsável Técnico:</span>
                        <p class="font-medium">{{ $manutencao->responsavel ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Custo:</span>
                        <p class="font-medium text-lg">{{ $manutencao->custo ? 'R$ ' . number_format($manutencao->custo, 2, ',', '.') : '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Tempo de Resolução:</span>
                        @php
                            $dias = ($manutencao->dataSolicitacao && $manutencao->dataExecucao) 
                                ? $manutencao->dataSolicitacao->diffInDays($manutencao->dataExecucao) 
                                : null;
                        @endphp
                        <p class="font-medium">{{ $dias !== null ? $dias . ' dia(s)' : '-' }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500 text-sm">Serviço Realizado:</span>
                        <p class="font-medium bg-green-50 p-3 rounded border border-green-200 mt-1">{{ $manutencao->servicoRealizado ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Botões de Ação -->
        <div class="flex gap-4 mt-6">
            <a href="{{ route('manutencoes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded">
                Voltar
            </a>
            <a href="{{ route('equipamentos.index') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-6 rounded">
                Ver Equipamentos
            </a>
        </div>
    </div>
@endsection
