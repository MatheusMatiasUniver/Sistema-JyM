@extends('layouts.app')

@section('title', 'Clientes Excluídos - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Clientes Excluídos</h1>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">CPF</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nome</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $cliente->cpf }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $cliente->nome }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">Deletado</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <form action="{{ route('clientes.restore', $cliente->idCliente) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900">Restaurar</button>
                                </form>
                                <a href="{{ route('clientes.confirmForceDelete', $cliente->idCliente) }}" class="text-red-600 hover:text-red-900">Excluir Definitivamente</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">Nenhum cliente excluído.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6">{{ $clientes->links() }}</div>
    </div>
@endsection