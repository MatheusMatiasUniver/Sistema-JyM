@extends('layouts.app')

@section('title', 'Ajustes do Sistema - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Ajustes do Sistema</h1>

    @if(session('success'))
        <div class="bg-grip-4 border border-border-light text-grip-3 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-xl">
        <form method="POST" action="{{ route('ajustes.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="diaVencimentoSalarios" class="block text-gray-700 text-sm font-bold mb-2">Dia de vencimento dos salários</label>
                <input type="number" min="1" max="31" id="diaVencimentoSalarios" name="diaVencimentoSalarios" value="{{ old('diaVencimentoSalarios', $ajuste->diaVencimentoSalarios) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('diaVencimentoSalarios') border-red-500 @enderror">
                @error('diaVencimentoSalarios')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Salvar Ajustes
                </button>
                <a href="{{ route('dashboard') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection