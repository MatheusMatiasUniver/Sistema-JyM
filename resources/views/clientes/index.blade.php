@extends('layouts.app')

@section('title', 'Clientes - Sistema JyM') 

@section('content')
    <div class="main">
        <h1>Lista de Clientes</h1>

        {{-- Mensagens de sucesso ou erro, se houverem --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- Botão para adicionar novo cliente --}}
        <a href="{{ route('clientes.create') }}">
            <button>Adicionar Novo Cliente</button>
        </a>

        {{-- Tabela de Clientes --}}
        <table>
            <thead>
                <tr>
                    <th>CPF</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Data de Nascimento</th>
                    <th>Plano</th>
                    <th>Status</th> {{-- Adicionando a coluna de status --}}
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                {{-- Verifica se existem clientes para exibir --}}
                @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->cpf }}</td>
                        <td>{{ $cliente->nome }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>{{ $cliente->telefone }}</td>
                        <td>{{ $cliente->dataNascimento }}</td>
                        <td>{{ $cliente->plano }}</td>
                        <td>{{ $cliente->status }}</td> {{-- Exibindo o status --}}
                        <td>
                            {{-- Links para Editar e Excluir --}}
                            <a href="{{ route('clientes.edit', $cliente->idCliente) }}">Editar</a> |
                            <a href="{{ route('clientes.destroy', $cliente->idCliente) }}"
                               onclick="event.preventDefault(); document.getElementById('delete-form-{{ $cliente->idCliente }}').submit();">Excluir</a>

                            {{-- Formulário oculto para o método DELETE --}}
                            <form id="delete-form-{{ $cliente->idCliente }}" action="{{ route('clientes.destroy', $cliente->idCliente) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @empty
                    {{-- Mensagem se não houver clientes --}}
                    <tr>
                        <td colspan="8">Nenhum cliente cadastrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection 
