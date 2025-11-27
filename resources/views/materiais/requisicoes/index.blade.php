@extends('layouts.app')

@section('title', 'Requisições de Materiais - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Requisições de Materiais</h1>

    <div class="mb-4">
        <a href="{{ route('materiais.requisicoes.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Nova Requisição</a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Material</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantidade</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Centro de Custo</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisicoes as $r)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $r->material->descricao }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $r->quantidade }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $r->centroCusto }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $r->data->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma requisição encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($requisicoes->hasPages())
        <div class="mt-6">{{ $requisicoes->links() }}</div>
    @endif
@endsection

