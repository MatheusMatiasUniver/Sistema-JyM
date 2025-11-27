@extends('layouts.app')

@section('title', 'Editar Usuário - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Editar Usuário: {{ $user->nome }}</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
        <form action="{{ route('users.update', $user->idUsuario) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $user->nome) }}" required autofocus
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('nome') border-red-500 @enderror">
                @error('nome')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="usuario" class="block text-gray-700 text-sm font-bold mb-2">Usuário:</label>
                <input type="text" id="usuario" name="usuario" value="{{ old('usuario', $user->usuario) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('usuario') border-red-500 @enderror">
                @error('usuario')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email (Opcional):</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="senha" class="block text-gray-700 text-sm font-bold mb-2">Nova Senha (deixe em branco para não alterar):</label>
                <input type="password" id="senha" name="senha"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('senha') border-red-500 @enderror">
                @error('senha')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="senha_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Nova Senha:</label>
                <input type="password" id="senha_confirmation" name="senha_confirmation"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-6">
                <label for="nivelAcesso" class="block text-gray-700 text-sm font-bold mb-2">Nível de Acesso:</label>
                <select id="nivelAcesso" name="nivelAcesso" required
                        class="select @error('nivelAcesso') border-red-500 @enderror">
                    <option value="Administrador" {{ old('nivelAcesso', $user->nivelAcesso) == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                    <option value="Funcionário" {{ old('nivelAcesso', $user->nivelAcesso) == 'Funcionário' ? 'selected' : '' }}>Funcionário</option>
                </select>
                @error('nivelAcesso')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="salarioMensal" class="block text-gray-700 text-sm font-bold mb-2">Salário Mensal:</label>
                <input type="number" step="0.01" min="0" id="salarioMensal" name="salarioMensal" value="{{ old('salarioMensal', $user->salarioMensal) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline @error('salarioMensal') border-red-500 @enderror">
                @error('salarioMensal')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Atualizar Usuário
                </button>
                <a href="{{ route('users.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
@endsection
