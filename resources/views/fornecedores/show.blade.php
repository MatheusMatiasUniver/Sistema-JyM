@extends('layouts.app')

@section('title', 'Detalhes do Fornecedor - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-grip-6">Fornecedor: {{ $fornecedor->razaoSocial }}</h1>

    <div class="bg-white shadow-md rounded-lg p-6 max-w-2xl">
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt class="text-gray-600">CNPJ/CPF</dt>
                <dd class="font-medium">{{ $fornecedor->cnpjCpf ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">Inscrição Estadual</dt>
                <dd class="font-medium">{{ $fornecedor->inscricaoEstadual ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">Contato</dt>
                <dd class="font-medium">{{ $fornecedor->contato ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">Telefone</dt>
                <dd class="font-medium">{{ $fornecedor->telefone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">E-mail</dt>
                <dd class="font-medium">{{ $fornecedor->email ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">Endereço</dt>
                <dd class="font-medium">{{ $fornecedor->endereco ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-gray-600">Status</dt>
                <dd class="font-medium">{{ $fornecedor->ativo ? 'Ativo' : 'Inativo' }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex space-x-3">
            <a href="{{ route('fornecedores.edit', $fornecedor) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded">Editar</a>
            <a href="{{ route('fornecedores.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded">Voltar</a>
        </div>
    </div>
@endsection

