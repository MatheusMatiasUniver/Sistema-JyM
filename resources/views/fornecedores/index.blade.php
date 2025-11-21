@extends('layouts.app')

@section('title', 'Gerenciar Fornecedores - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Gerenciar Fornecedores</h1>

    <div class="mb-4">
        <a href="{{ route('fornecedores.create') }}" class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded">
            Cadastrar Novo Fornecedor
        </a>
    </div>

    <x-search-filter 
        placeholder="Razão social, CNPJ/CPF ou e-mail..."
        action="{{ route('fornecedores.index') }}"
    />

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

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Razão Social</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">CNPJ/CPF</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contato</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Telefone</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">E-mail</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fornecedores as $fornecedor)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="font-medium text-gray-900">{{ $fornecedor->razaoSocial }}</div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $fornecedor->cnpjCpf ?? '-' }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $fornecedor->contato ?? '-' }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $fornecedor->telefone ?? '-' }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">{{ $fornecedor->email ?? '-' }}</td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('fornecedores.show', $fornecedor) }}" class="text-blue-600 hover:text-blue-900 text-sm">Ver</a>
                                <a href="{{ route('fornecedores.edit', $fornecedor) }}" class="text-yellow-600 hover:text-yellow-900 text-sm">Editar</a>
                                <form action="{{ route('fornecedores.destroy', $fornecedor) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja excluir este fornecedor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">Nenhum fornecedor encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fornecedores->hasPages())
        <div class="mt-6">
            {{ $fornecedores->links() }}
        </div>
    @endif
@endsection

