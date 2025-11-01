@extends('layouts.app')

@section('title', 'Editar Cliente - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Editar Cliente: {{ $cliente->nome }}</h1>

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
                <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $cliente->cpf) }}" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cpf') border-red-500 @enderror"
                       placeholder="000.000.000-00">
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
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $cliente->telefone) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('telefone') border-red-500 @enderror"
                       placeholder="(00) 00000-0000">
                @error('telefone')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="codigo_acesso_antigo" class="block text-gray-700 text-sm font-bold mb-2">Código de Acesso Atual (para verificação):</label>
                <input type="password" id="codigo_acesso_antigo" name="codigo_acesso_antigo" placeholder="Digite o código atual"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('codigo_acesso_antigo') border-red-500 @enderror"
                    maxlength="6" inputmode="numeric" pattern="\d*" oninput="this.value=this.value.slice(0,this.maxLength)"
                    oncopy="return false" onpaste="return false" oncut="return false" oncontextmenu="return false" 
                    onselectstart="return false" ondragstart="return false">
                @error('codigo_acesso_antigo')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Digite o código atual para poder alterá-lo.</p>
            </div>

            <div class="mb-4">
                <label for="codigo_acesso" class="block text-gray-700 text-sm font-bold mb-2">Novo Código de Acesso (6 dígitos, opcional):</label>
                <input type="password" id="codigo_acesso" name="codigo_acesso" placeholder="Novo código de acesso"
                    value=""
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('codigo_acesso') border-red-500 @enderror"
                    maxlength="6" inputmode="numeric" pattern="\d*" oninput="this.value=this.value.slice(0,this.maxLength)"
                    oncopy="return false" onpaste="return false" oncut="return false" oncontextmenu="return false" 
                    onselectstart="return false" ondragstart="return false">
                @error('codigo_acesso')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-xs mt-1">Será utilizado como fallback no Kiosk. Se deixado em branco, não será alterado.</p>
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
                <select id="status" name="status" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-white @error('status') border-red-500 @enderror">
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
                <input type="file" id="foto" name="foto" accept="image/*"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('foto') border-red-500 @enderror"
                       onchange="previewImage(this)">
                @error('foto')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                
                <!-- Preview da nova imagem -->
                <div id="imagePreview" class="mt-3 hidden">
                    <p class="text-sm text-gray-600 mb-2">Preview da nova imagem:</p>
                    <img id="preview" src="" alt="Preview" class="max-w-xs max-h-48 rounded border shadow">
                    <button type="button" onclick="removeImage()" class="ml-3 text-red-500 hover:text-red-700 text-sm">
                        Remover nova imagem
                    </button>
                </div>
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

        <hr class="my-8 border-gray-300">

        <h2 class="text-xl font-bold mb-4 text-gray-800">Informações do Plano</h2>
        @if($cliente->plano)
            <div class="mb-4">
                <p class="text-gray-700">Plano Atual: <strong class="text-gray-900">{{ $cliente->plano->nome }}</strong></p>
                <p class="text-gray-700">Valor: <strong class="text-gray-900">R\$ {{ number_format($cliente->plano->valor, 2, ',', '.') }}</strong></p>
                <p class="text-gray-700">Duração: <strong class="text-gray-900">{{ $cliente->plano->duracaoDias }} dias</strong></p>

                @php
                    $proximoVencimentoExibicao = 'N/A';
                    $ultimaMensalidadePaga = $cliente->mensalidades()->where('status', 'Paga')->latest('dataVencimento')->first();
                    $classeCorVencimento = 'text-gray-500';

                    if ($ultimaMensalidadePaga) {
                        $dataVencimentoCalculada = \Carbon\Carbon::parse($ultimaMensalidadePaga->dataVencimento)->addDays($cliente->plano->duracaoDias ?? 0);
                        if ($dataVencimentoCalculada->isPast()) {
                            $classeCorVencimento = 'text-red-600';
                        } elseif ($dataVencimentoCalculada->diffInDays(\Carbon\Carbon::now()) <= 7) {
                            $classeCorVencimento = 'text-orange-600';
                        } else {
                            $classeCorVencimento = 'text-green-600';
                        }
                        $proximoVencimentoExibicao = $dataVencimentoCalculada->format('d/m/Y');
                    }
                @endphp
                <p class="text-gray-700">Próximo vencimento: <strong class="{{ $classeCorVencimento }}">{{ $proximoVencimentoExibicao }}</strong></p>
            </div>
        @else
            <p class="text-gray-600 mb-4">Este cliente não possui um plano de assinatura associado.</p>
            <p class="text-gray-600">Atribua um plano utilizando o formulário acima.</p>
        @endif

    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
            }
        }
        
        function removeImage() {
            const input = document.getElementById('foto');
            const previewContainer = document.getElementById('imagePreview');
            
            input.value = '';
            previewContainer.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof applyFormatting === 'function') {
                applyFormatting();
            }
        });
    </script>
@endsection