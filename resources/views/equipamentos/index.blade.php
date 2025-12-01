@extends('layouts.app')

@section('title', 'Equipamentos - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Equipamentos</h1>

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

    <div class="mb-4 flex gap-4">
        <a href="{{ route('equipamentos.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Novo Equipamento</a>
        <a href="{{ route('manutencoes.index') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">Ver Manutenções</a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fabricante</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Modelo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipamentos as $e)
                    @php
                        $statusValue = $e->status instanceof \App\Models\StatusEquipamento ? $e->status->value : $e->status;
                        $isAtivo = $statusValue === 'Ativo';
                        $isEmManutencao = $statusValue === 'Em Manutenção';
                    @endphp
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->fabricante }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->modelo }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $isAtivo ? 'bg-green-100 text-green-800' : '' }}
                                {{ $isEmManutencao ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $statusValue === 'Desativado' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $statusValue }}
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('equipamentos.edit', $e->idEquipamento) }}" class="text-yellow-600 hover:text-yellow-800">Editar</a>
                                
                                @if($isAtivo)
                                    <a href="{{ route('manutencoes.create', ['idEquipamento' => $e->idEquipamento]) }}" 
                                       class="text-orange-600 hover:text-orange-800 font-medium">
                                        Registrar Manutenção
                                    </a>
                                @endif
                                
                                <a href="{{ route('manutencoes.historico', $e->idEquipamento) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    Histórico
                                </a>
                                
                                <form action="{{ route('equipamentos.destroy', $e->idEquipamento) }}" method="POST" class="inline" autocomplete="off"
                                      data-confirm="Tem certeza que deseja excluir este equipamento?"
                                      data-confirm-title="Excluir Equipamento"
                                      data-confirm-icon="danger"
                                      data-confirm-text="Excluir"
                                      data-cancel-text="Cancelar">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhum equipamento encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($equipamentos->hasPages())
        <div class="mt-6">{{ $equipamentos->links() }}</div>
    @endif
@endsection
