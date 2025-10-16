@extends('layouts.app')

@section('title', 'Cadastrar Cliente - Sistema JyM')

@section('content')
        <h1 class="text-3xl font-bold mb-6 text-accent-blue">Cadastrar Novo Cliente</h1>
    
        @if ($errors->any())
            <div class="alert alert-danger mb-4 p-4 rounded-lg bg-red-100 text-red-700">
                <strong class="font-bold">Opa!</strong> Algo deu errado. Verifique os erros abaixo:
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('clientes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror" value="{{ old('nome') }}" required>
                @error('nome')
                    <div class="text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="cpf" class="block text-gray-700 text-sm font-bold mb-2">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="{{ old('cpf') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                @error('cpf')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="telefone" class="block text-gray-700 text-sm font-bold mb-2">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                @error('telefone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="codigo_acesso" class="block text-gray-700 text-sm font-bold mb-2">Código de Acesso (6 dígitos, opcional):</label>
                <input type="number" id="codigo_acesso" name="codigo_acesso" placeholder="Apenas números"
                    value="" {{-- Não pré-preencha o valor do hash por segurança --}}
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('codigo_acesso') border-red-500 @enderror"
                    maxlength="6" inputmode="numeric" pattern="\d*" oninput="this.value=this.value.slice(0,this.maxLength)">
                @error('codigo_acesso')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Será utilizado como fallback no Kiosk. Se deixado em branco, não será alterado.</p>
            </div>

            <div class="mb-4">
                <label for="dataNascimento" class="block text-gray-700 text-sm font-bold mb-2">Data de Nascimento:</label>
                <input type="date" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('dataNascimento') border-red-500 @enderror">
                @error('dataNascimento')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select name="status" required class="seu-estilo-aqui">
                    <option value="Pendente" {{ old('status', $cliente->status ?? '') == 'Pendente' ? 'selected' : '' }}>
                        Pendente
                    </option>
                    <option value="Ativo" {{ old('status', $cliente->status ?? '') == 'Ativo' ? 'selected' : '' }}>
                        Ativo
                    </option>
                    <option value="Suspenso" {{ old('status', $cliente->status ?? '') == 'Suspenso' ? 'selected' : '' }}>
                        Suspenso
                    </option>
                    <option value="Inadimplente" {{ old('status', $cliente->status ?? '') == 'Inadimplente' ? 'selected' : '' }}>
                        Inadimplente
                    </option>
                    <option value="Inativo" {{ old('status', $cliente->status ?? '') == 'Inativo' ? 'selected' : '' }}>
                        Inativo
                    </option>
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
                    @foreach($planos as $plano)
                        <option value="{{ $plano->idPlano }}" {{ old('idPlano') == $plano->idPlano ? 'selected' : '' }}>
                            {{ $plano->nome }} (R\$ {{ number_format($plano->valor, 2, ',', '.') }})
                        </option>
                    @endforeach
                </select>
                @error('idPlano')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="foto" class="block text-gray-700 text-sm font-bold mb-2">Foto (Opcional):</label>
                <input type="file" id="foto" name="foto"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('foto') border-red-500 @enderror">
                @error('foto')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cadastrar Cliente
            </button>
        </form>    
@endsection