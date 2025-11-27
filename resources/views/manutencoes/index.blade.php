@extends('layouts.app')

@section('title', 'Manutenções - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Manutenções de Equipamentos</h1>

    <div class="mb-4">
        <a href="{{ route('manutencoes.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Nova Manutenção</a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Equipamento</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Programada</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Execução</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Custo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($manutencoes as $m)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->equipamento->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ ucfirst($m->tipo) }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($m->dataProgramada)->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ optional($m->dataExecucao)->format('d/m/Y') }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->custo ? 'R$ '.number_format($m->custo,2,',','.') : '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma manutenção encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($manutencoes->hasPages())
        <div class="mt-6">{{ $manutencoes->links() }}</div>
    @endif
@endsection

