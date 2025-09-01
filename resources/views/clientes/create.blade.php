@extends('layouts.app') 

@section('title', 'Cadastrar Cliente - Sistema JyM') {{-- Define o título da página --}}

@section('content') {{-- Início da seção de conteúdo --}}
    <div class="main"> {{-- Mantendo a classe 'main' para usar seu estilo CSS existente --}}
        <h1>Cadastrar Novo Cliente</h1>

        {{-- Mensagens de validação de erro --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulário de Cadastro de Cliente --}}
        <form action="{{ route('clientes.store') }}" method="POST">
            @csrf {{-- Token CSRF para segurança --}}

            <input type="text" name="cpf" placeholder="CPF" maxlength="11" required value="{{ old('cpf') }}">
            <input type="text" name="nome" placeholder="Nome" required value="{{ old('nome') }}">
            <input type="email" name="email" placeholder="Email" required value="{{ old('email') }}">
            <input type="text" name="telefone" placeholder="Telefone" maxlength="11" value="{{ old('telefone') }}">
            <input type="date" name="dataNascimento" placeholder="Data de Nascimento" value="{{ old('dataNascimento') }}">
            
            {{-- Adicionando a seleção de Status, conforme o DB --}}
            <select name="status" required>
                <option value="Ativo" {{ old('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ old('status') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>

            <select name="plano" required>
                <option value="Assinante" {{ old('plano') == 'Assinante' ? 'selected' : '' }}>Assinante</option>
                <option value="Não Assinante" {{ old('plano') == 'Não Assinante' ? 'selected' : '' }}>Não Assinante</option>
            </select>
            
            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <script>
        // Bloquear letras enquanto digita no CPF e Telefone
        document.querySelector('input[name="cpf"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        document.querySelector('input[name="telefone"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Validação final antes de enviar (JavaScript no cliente)
        document.querySelector("form").addEventListener("submit", function(e) {
            const cpf = document.querySelector('input[name="cpf"]').value.trim();
            const telefone = document.querySelector('input[name="telefone"]').value.trim();

            if (cpf.length !== 11) {
                alert("CPF inválido! Deve conter exatamente 11 dígitos numéricos.");
                e.preventDefault();
                return;
            }

            if (telefone.length < 8 || telefone.length > 11) {
                alert("Telefone inválido! Deve conter entre 8 e 11 dígitos numéricos.");
                e.preventDefault();
                return;
            }
        });
    </script>
@endsection {{-- Fim da seção de conteúdo --}}
