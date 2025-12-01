@extends('layouts.app')

@section('title', 'Manutenções de Equipamentos - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Manutenções de Equipamentos</h1>

    <div class="mb-4 flex flex-wrap gap-4 items-center justify-between">
        <a href="{{ route('manutencoes.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">
            Nova Manutenção
        </a>

        <form action="{{ route('manutencoes.index') }}" method="GET" class="flex flex-wrap gap-2 items-center" autocomplete="off">
            <select name="status" class="select text-sm" style="width: auto; min-width: 150px;">
                <option value="">Todos os Status</option>
                <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Concluída" {{ request('status') == 'Concluída' ? 'selected' : '' }}>Concluída</option>
            </select>

            <select name="tipo" class="select text-sm" style="width: auto; min-width: 150px;">
                <option value="">Todos os Tipos</option>
                <option value="preventiva" {{ request('tipo') == 'preventiva' ? 'selected' : '' }}>Preventiva</option>
                <option value="corretiva" {{ request('tipo') == 'corretiva' ? 'selected' : '' }}>Corretiva</option>
            </select>

            <select name="idEquipamento" class="select text-sm" style="width: auto; min-width: 200px;">
                <option value="">Todos os Equipamentos</option>
                @foreach($equipamentos as $eq)
                    <option value="{{ $eq->idEquipamento }}" {{ request('idEquipamento') == $eq->idEquipamento ? 'selected' : '' }}>
                        {{ $eq->descricao }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white px-4 py-2 rounded text-sm">
                Filtrar
            </button>

            @if(request()->hasAny(['status', 'tipo', 'idEquipamento']))
                <a href="{{ route('manutencoes.index') }}" class="text-grip-1 hover:text-grip-2 text-sm underline">
                    Limpar
                </a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Equipamento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Solicitação</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Execução</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Custo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($manutencoes as $m)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <div class="font-medium text-gray-900">{{ $m->equipamento?->descricao ?? 'N/A' }}</div>
                            <div class="text-gray-500 text-xs">{{ $m->equipamento?->fabricante ?? '' }} {{ $m->equipamento?->modelo ?? '' }}</div>
                        </td>
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
                            @php
                                $statusValue = $m->status instanceof \App\Models\StatusManutencao ? $m->status->value : $m->status;
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusValue === 'Pendente' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $statusValue }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            {{ $m->custo ? 'R$ ' . number_format($m->custo, 2, ',', '.') : '-' }}
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <div class="flex gap-2">
                                @php
                                    $isPendente = ($m->status instanceof \App\Models\StatusManutencao) 
                                        ? $m->status === \App\Models\StatusManutencao::PENDENTE 
                                        : $m->status === 'Pendente';
                                @endphp
                                
                                @if($isPendente)
                                    <a href="{{ route('manutencoes.edit', $m->idManutencao) }}" 
                                       class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                        Finalizar
                                    </a>
                                    <form action="{{ route('manutencoes.destroy', $m->idManutencao) }}" method="POST" class="inline" autocomplete="off"
                                          data-confirm="Tem certeza que deseja excluir esta manutenção?"
                                          data-confirm-title="Excluir Manutenção"
                                          data-confirm-icon="danger"
                                          data-confirm-text="Excluir"
                                          data-cancel-text="Cancelar">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                            Excluir
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('manutencoes.show', $m->idManutencao) }}" 
                                       class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs">
                                        Ver Detalhes
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">
                            Nenhuma manutenção encontrada.
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
