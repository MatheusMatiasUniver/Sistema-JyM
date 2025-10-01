@extends('layouts.app')

@section('title', 'Cadastro de Usuário - Sistema JyM')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="mx-auto mb-4" style="width: 120px;">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Cadastro de Usuário</h2>

        <form id="cadastroForm" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Nome" value="{{ old('nome') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('nome') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="usuario" class="block text-gray-700 text-sm font-bold mb-2">Usuário para Login:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Usuário" value="{{ old('usuario') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('usuario') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email (Opcional):</label>
                <input type="email" id="email" name="email" placeholder="Email (Opcional)" value="{{ old('email') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('email') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label for="senha" class="block text-gray-700 text-sm font-bold mb-2">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Senha" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                @error('senha') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label for="nivelAcesso" class="block text-gray-700 text-sm font-bold mb-2">Nível de Acesso:</label>
                <select id="nivelAcesso" name="nivelAcesso" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Selecione o nível de acesso</option>
                    <option value="Funcionario" {{ old('nivelAcesso') == 'Funcionario' ? 'selected' : '' }}>Funcionário</option>
                    <option value="Administrador" {{ old('nivelAcesso') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                </select>
                @error('nivelAcesso') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>
@endsection