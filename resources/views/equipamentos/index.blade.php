@extends('layouts.app')

@section('title', 'Equipamentos - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Equipamentos</h1>

    <div class="mb-4">
        <a href="{{ route('equipamentos.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Novo Equipamento</a>
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
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->fabricante }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->modelo }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $e->status }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <a href="{{ route('equipamentos.edit', $e->idEquipamento) }}" class="text-yellow-600">Editar</a>
                            <form action="{{ route('equipamentos.destroy', $e->idEquipamento) }}" method="POST" class="inline" onsubmit="return confirm('Excluir equipamento?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">Excluir</button>
                            </form>
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
