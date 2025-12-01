@extends('layouts.app')

@section('title', 'Gerenciar Usuários - Sistema JyM')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-grip-6">Gerenciar Usuários</h1>
        <a href="{{ route('register') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Cadastrar Novo Usuário
        </a>
    </div>

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

    <x-table-filter 
        :action="route('users.index')"
        search-placeholder="Nome, usuário ou email..."
        :search-value="request('search')"
        :filters="[
            [
                'type' => 'select',
                'name' => 'nivel_acesso',
                'label' => 'Nível de Acesso',
                'options' => [
                    'Administrador' => 'Administrador',
                    'Funcionário' => 'Funcionário'
                ]
            ]
        ]"
        :sort-options="[
            'nome_asc' => 'Nome (A-Z)',
            'nome_desc' => 'Nome (Z-A)',
            'usuario_asc' => 'Usuário (A-Z)',
            'usuario_desc' => 'Usuário (Z-A)',
            'email_asc' => 'Email (A-Z)',
            'email_desc' => 'Email (Z-A)',
            'nivelAcesso_asc' => 'Nível (A-Z)',
            'nivelAcesso_desc' => 'Nível (Z-A)'
        ]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <table class="min-w-full leading-normal">
        <thead>
            <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nome
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Usuário
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nível de Acesso
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
            </tr>
        </thead>
        <tbody>
                @forelse ($users as $user)
                <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $user->nome }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $user->usuario }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $user->email ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $user->nivelAcesso }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('users.edit', $user->idUsuario) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                @if($user->podeDeletar() && Auth::id() !== $user->idUsuario)
                                    <form action="{{ route('users.destroy', $user->idUsuario) }}" method="POST" autocomplete="off"
                                          data-confirm="Tem certeza que deseja excluir este usuário?"
                                          data-confirm-title="Excluir Usuário"
                                          data-confirm-icon="danger"
                                          data-confirm-text="Excluir"
                                          data-cancel-text="Cancelar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" 
                                          title="{{ Auth::id() === $user->idUsuario ? 'Não é possível excluir seu próprio usuário' : 'Não é possível excluir usuário com clientes associados' }}">
                                        Excluir
                                    </span>
                                @endif
                            </div>
                    </td>
                </tr>
            @empty
                <tr>
                        <td colspan="5" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Nenhum usuário cadastrado.
                        </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
@endsection
