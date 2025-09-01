{{-- resources/views/clientes/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Editar Cliente - Sistema JyM')

@section('content') 
    <div class="main"> 
        <h1>Editar Cliente</h1>

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

        {{-- Formulário de Edição de Cliente --}}
        <form action="{{ route('clientes.update', $cliente->idCliente) }}" method="POST">
            @csrf {{-- Token CSRF para segurança --}}
            @method('PUT') {{-- Indica que esta é uma requisição PUT (para atualização) --}}

            {{-- O CPF geralmente não é editável, mas pode ser um hidden input se for chave primária --}}
            <input type="hidden" name="cpf" value="{{ $cliente->cpf }}"> 
            
            <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" placeholder="Nome" required>
            <input type="email" name="email" value="{{ old('email', $cliente->email) }}" placeholder="Email" required>
            <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" placeholder="Telefone" maxlength="11">
            <input type="date" name="dataNascimento" value="{{ old('dataNascimento', $cliente->dataNascimento) }}" placeholder="Data de Nascimento">
            
            {{-- Campo de Status --}}
            <select name="status" required>
                <option value="Ativo" {{ old('status', $cliente->status) == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                <option value="Inativo" {{ old('status', $cliente->status) == 'Inativo' ? 'selected' : '' }}>Inativo</option>
            </select>

            <select name="plano" required>
                <option value="Assinante" {{ old('plano', $cliente->plano) == 'Assinante' ? 'selected' : '' }}>Assinante</option>
                <option value="Não Assinante" {{ old('plano', $cliente->plano) == 'Não Assinante' ? 'selected' : '' }}>Não Assinante</option>
            </select>
            
            <button type="submit">Salvar Alterações</button>
        </form>
    </div>

    <script>
        // Bloquear letras enquanto digita no Telefone
        document.querySelector('input[name="telefone"]').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Validação final antes de enviar (JavaScript no cliente)
        document.querySelector("form").addEventListener("submit", function(e) {
            const telefone = document.querySelector('input[name="telefone"]').value.trim();

            if (telefone.length < 8 || telefone.length > 11) {
                alert("Telefone inválido! Deve conter entre 8 e 11 dígitos numéricos.");
                e.preventDefault();
                return;
            }
        });
    </script>
@endsection