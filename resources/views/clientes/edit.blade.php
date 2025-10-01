@extends('layouts.app')

@section('title', 'Editar Cliente - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Editar Cliente: {{ $cliente->nome }}</h1>

    @if(session('success'))
        <div class="alert-success" role="alert">
            <strong class="font-bold">Sucesso!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert-error" role="alert">
            <strong class="font-bold">Erro!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ route('clientes.update', $cliente->idCliente) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $cliente->nome) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="cpf" class="block text-gray-700 text-sm font-bold mb-2">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $cliente->cpf) }}" required maxlength="11"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                @error('cpf')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $cliente->email) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefone" class="block text-gray-700 text-sm font-bold mb-2">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" maxlength="11"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                @error('telefone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="dataNascimento" class="block text-gray-700 text-sm font-bold mb-2">Data de Nascimento:</label>
                <input type="date" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento', $cliente->dataNascimento ? $cliente->dataNascimento->format('Y-m-d') : '') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('dataNascimento') border-red-500 @enderror">
                @error('dataNascimento')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select id="status" name="status" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('status') border-red-500 @enderror">
                    <option value="Ativo" {{ old('status', $cliente->status) == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="Inativo" {{ old('status', $cliente->status) == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="idPlano" class="block text-gray-700 text-sm font-bold mb-2">Plano de Assinatura:</label>
                <select id="idPlano" name="idPlano"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('idPlano') border-red-500 @enderror">
                    <option value="">Selecione um Plano (Opcional)</option>
                    @foreach ($planos as $plano)
                        <option value="{{ $plano->idPlano }}" {{ old('idPlano', $cliente->idPlano) == $plano->idPlano ? 'selected' : '' }}>
                            {{ $plano->nome }} (R\$ {{ number_format($plano->valor, 2, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @error('idPlano')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="foto" class="block text-gray-700 text-sm font-bold mb-2">Foto Atual:</label>
                @if ($cliente->foto)
                    <div class="flex items-center space-x-4 mb-2">
                        <img src="{{ asset('storage/' . $cliente->foto) }}" alt="Foto do Cliente" class="w-24 h-24 object-cover rounded-full">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="remover_foto" value="1" class="form-checkbox h-5 w-5 text-red-600">
                            <span class="ml-2 text-gray-700">Remover foto existente</span>
                        </label>
                    </div>
                @else
                    <p class="text-gray-600 mb-2">Nenhuma foto cadastrada.</p>
                @endif
                <input type="file" id="foto" name="foto"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('foto') border-red-500 @enderror">
                @error('foto')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Atualizar Cliente
                </button>
                <a href="{{ route('clientes.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection