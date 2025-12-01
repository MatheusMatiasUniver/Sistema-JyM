@extends('layouts.app')

@section('title', 'Cadastrar Fornecedor - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Cadastrar Novo Fornecedor</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-xl mx-auto">
        <form action="{{ route('fornecedores.store') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-4">
                <label for="razaoSocial" class="block text-gray-700 text-sm font-bold mb-2">Razão Social:</label>
                <input type="text" id="razaoSocial" name="razaoSocial" value="{{ old('razaoSocial') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('razaoSocial') border-red-500 @enderror">
                @error('razaoSocial')<p class="text-red-500 text-xs italic">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="cnpjCpf" class="block text-gray-700 text-sm font-bold mb-2">CNPJ/CPF:</label>
                    <input type="text" id="cnpjCpf" name="cnpjCpf" value="{{ old('cnpjCpf') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="inscricaoEstadual" class="block text-gray-700 text-sm font-bold mb-2">Inscrição Estadual:</label>
                    <input type="text" id="inscricaoEstadual" name="inscricaoEstadual" value="{{ old('inscricaoEstadual') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="contato" class="block text-gray-700 text-sm font-bold mb-2">Contato:</label>
                    <input type="text" id="contato" name="contato" value="{{ old('contato') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label for="telefone" class="block text-gray-700 text-sm font-bold mb-2">Telefone:</label>
                    <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-mail:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="endereco" class="block text-gray-700 text-sm font-bold mb-2">Endereço:</label>
                <input type="text" id="endereco" name="endereco" value="{{ old('endereco') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label for="condicaoPagamentoPadrao" class="block text-gray-700 text-sm font-bold mb-2">Condição de Pagamento Padrão:</label>
                <input type="text" id="condicaoPagamentoPadrao" name="condicaoPagamentoPadrao" value="{{ old('condicaoPagamentoPadrao') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="ativo" value="1" {{ old('ativo', true) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-green-600 text-black">
                    <span class="ml-2 text-gray-700">Fornecedor ativo</span>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Salvar</button>
                <a href="{{ route('fornecedores.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

