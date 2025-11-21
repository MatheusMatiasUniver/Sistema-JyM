@extends('layouts.app')

@section('title', 'Cadastro de Usuário - Sistema JyM')

@section('content')
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

    @if($errors->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errors->first('error') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto">
        <img src="{{ asset('img/logo.png') }}" alt="Logo JyM" class="mx-auto mb-4" style="width: 120px;">
        
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Cadastro de Usuário</h2>

        <form id="cadastroForm" method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
                <input type="text" id="nome" name="nome" placeholder="Nome" value="{{ old('nome') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                @error('nome') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="usuario" class="block text-gray-700 text-sm font-bold mb-2">Usuário para Login:</label>
                <input type="text" id="usuario" name="usuario" placeholder="Usuário" value="{{ old('usuario') }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                @error('usuario') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email (Opcional):</label>
                <input type="email" id="email" name="email" placeholder="Email (Opcional)" value="{{ old('email') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black leading-tight focus:outline-none focus:shadow-outline">
                @error('email') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label for="senha" class="block text-gray-700 text-sm font-bold mb-2">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Senha" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-black mb-3 leading-tight focus:outline-none focus:shadow-outline">
                @error('senha') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label for="nivelAcesso" class="block text-gray-700 text-sm font-bold mb-2">Nível de Acesso:</label>
                <select id="nivelAcesso" name="nivelAcesso" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Selecione o nível de acesso</option>
                    <option value="Funcionário" {{ old('nivelAcesso') == 'Funcionário' ? 'selected' : '' }}>Funcionário</option>
                    <option value="Administrador" {{ old('nivelAcesso') == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                </select>
                @error('nivelAcesso') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div id="academiaField" class="mb-6" style="display: none;">
                <label for="idAcademia" class="block text-gray-700 text-sm font-bold mb-2">Academia:</label>
                <select id="idAcademia" name="idAcademia"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Selecione a academia</option>
                    @foreach(Auth::user()->academias as $academia)
                        <option value="{{ $academia->idAcademia }}" {{ old('idAcademia') == $academia->idAcademia ? 'selected' : '' }}>
                            {{ $academia->nome }}
                        </option>
                    @endforeach
                </select>
                @error('idAcademia') <span class="text-red-500 text-xs italic">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Cadastrar
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nivelAcessoSelect = document.getElementById('nivelAcesso');
            const academiaField = document.getElementById('academiaField');
            const academiaSelect = document.getElementById('idAcademia');

            function toggleAcademiaField() {
                if (nivelAcessoSelect.value === 'Funcionário') {
                    academiaField.style.display = 'block';
                    academiaSelect.required = true;
                } else {
                    academiaField.style.display = 'none';
                    academiaSelect.required = false;
                    academiaSelect.value = '';
                }
            }

            nivelAcessoSelect.addEventListener('change', toggleAcademiaField);
            
            // Verificar estado inicial
            toggleAcademiaField();
        });
    </script>
@endsection
