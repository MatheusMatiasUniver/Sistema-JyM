@extends('layouts.app')

@section('title', 'Gerenciar Usuários - Sistema JyM')

@section('content')
    <h1>Lista de Usuários Cadastrados</h1>

    {{-- Área para mensagens de sucesso ou erro --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Botão para adicionar novo usuário (visível apenas para administradores) --}}
    @auth
        @if(Auth::user()->nivelAcesso === 'Administrador')
            <a href="{{ route('register') }}"><button>Cadastrar Novo Usuário</button></a>
        @endif
    @endauth

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Usuário</th>
                <th>Email</th>
                <th>Nível de Acesso</th>
                <th>Ações</th> {{-- Coluna para futuros botões de Editar/Excluir --}}
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->nome }}</td>
                    <td>{{ $user->usuario }}</td>
                    <td>{{ $user->email ?? 'N/A' }}</td> {{-- Exibe 'N/A' se o email for nulo --}}
                    <td>{{ $user->nivelAcesso }}</td>
                    <td>
                        {{-- Placeholder para futuros botões de Editar/Excluir --}}
                        <a href="#">Editar</a> | <a href="#">Excluir</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum usuário cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection