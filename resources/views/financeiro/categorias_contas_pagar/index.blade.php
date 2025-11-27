@extends('layouts.app')

@section('title', 'Categorias de Contas a Pagar - Sistema JyM')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-grip-6">Categorias de Contas a Pagar</h1>
        <a href="{{ route('financeiro.categorias_contas_pagar.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">Nova Categoria</a>
    </div>

    @if(session('success'))
        <div class="bg-grip-4 border border-border-light text-grip-3 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome</th>
                    <th class="px-5 py-3 border-b-2 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ativa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                    <tr>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $categoria->nome }}</td>
                        <td class="px-5 py-5 border-b bg-white text-sm">{{ $categoria->ativa ? 'Sim' : 'Não' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-5 py-5 border-b bg-white text-sm text-center text-gray-500">Nenhuma categoria cadastrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection