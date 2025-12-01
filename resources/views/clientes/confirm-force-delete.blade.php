@extends('layouts.app')

@section('title', 'Confirmar Exclusão Definitiva - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-red-700">Excluir Definitivamente</h1>

    @if(session('warning'))
        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4">{{ session('warning') }}</div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <p class="mb-4">Você está prestes a excluir definitivamente o cliente <strong>{{ $cliente->nome }}</strong> (CPF: {{ $cliente->cpf }}).</p>
        <p class="mb-4">Há {{ $mensalidadesCount }} mensalidades, {{ $entradasCount }} entradas e {{ $vendasCount }} vendas vinculadas.</p>
        <p class="mb-6 text-red-700">Se prosseguir, o histórico pode ficar inconsistente. Os registros vinculados serão mantidos e exibidos como "Cliente Deletado".</p>

        <div class="flex space-x-3">
            <form action="{{ route('clientes.forceDelete', $cliente->idCliente) }}" method="POST" autocomplete="off">
                @csrf
                @method('DELETE')
                <input type="hidden" name="confirm" value="yes">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Sim, Excluir</button>
            </form>
            <a href="{{ route('clientes.excluidos.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">Não, Cancelar</a>
        </div>
    </div>
@endsection