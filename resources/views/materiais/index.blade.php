@extends('layouts.app')

@section('title', 'Materiais - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Materiais de Consumo</h1>

    <div class="mb-4 flex space-x-2">
        <a href="{{ route('materiais.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Novo Material</a>
        <a href="{{ route('materiais.requisicoes.index') }}" class="bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded">Requisições</a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descrição</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estoque</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unidade</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mínimo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materiais as $m)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->estoque }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->unidadeMedida ?? '-' }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $m->estoqueMinimo }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">
                            <a href="{{ route('materiais.edit', $m->idMaterial) }}" class="text-yellow-600">Editar</a>
                            <form action="{{ route('materiais.destroy', $m->idMaterial) }}" method="POST" class="inline" onsubmit="return confirm('Excluir material?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhum material encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($materiais->hasPages())
        <div class="mt-6">{{ $materiais->links() }}</div>
    @endif
@endsection

