@extends('layouts.app')

@section('title', 'Histórico de Manutenções - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-2 text-grip-6">Histórico de Manutenções</h1>
    <p class="text-gray-600 mb-6">
        Equipamento: <strong>{{ $equipamento->descricao }}</strong>
        @if($equipamento->fabricante || $equipamento->modelo)
            ({{ $equipamento->fabricante }} {{ $equipamento->modelo }})
        @endif
    </p>

    <div class="mb-4 flex gap-4">
        <a href="{{ route('equipamentos.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
            Voltar para Equipamentos
        </a>
        @if($equipamento->status->value === 'Ativo' || $equipamento->status === 'Ativo')
            <a href="{{ route('manutencoes.create', ['idEquipamento' => $equipamento->idEquipamento]) }}" 
               class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">
                Nova Manutenção
            </a>
        @endif
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-4 gap-4 mb-6">
        @php
            $totalManutencoes = $manutencoes->total();
            $custoTotal = $manutencoes->sum('custo');
            $preventivas = $manutencoes->where('tipo', 'preventiva')->count();
            $corretivas = $manutencoes->where('tipo', 'corretiva')->count();
        @endphp
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $totalManutencoes }}</p>
            <p class="text-gray-500 text-sm">Total de Manutenções</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-green-600">R$ {{ number_format($custoTotal, 2, ',', '.') }}</p>
            <p class="text-gray-500 text-sm">Custo Total</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $preventivas }}</p>
            <p class="text-gray-500 text-sm">Preventivas</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ $corretivas }}</p>
            <p class="text-gray-500 text-sm">Corretivas</p>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Solicitação</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Execução</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Problema</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Serviço</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Custo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($manutencoes as $m)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $m->tipo === 'preventiva' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ ucfirst($m->tipo) }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            {{ $m->dataSolicitacao ? $m->dataSolicitacao->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            {{ $m->dataExecucao ? $m->dataExecucao->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <span title="{{ $m->descricao }}">
                                {{ Str::limit($m->descricao, 40) }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <span title="{{ $m->servicoRealizado }}">
                                {{ $m->servicoRealizado ? Str::limit($m->servicoRealizado, 40) : '-' }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            {{ $m->custo ? 'R$ ' . number_format($m->custo, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            @php
                                $statusValue = $m->status instanceof \App\Models\StatusManutencao ? $m->status->value : $m->status;
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusValue === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $statusValue }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">
                            Nenhuma manutenção registrada para este equipamento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($manutencoes->hasPages())
        <div class="mt-6">{{ $manutencoes->links() }}</div>
    @endif
@endsection
