@extends('layouts.app')

@section('title', 'Gerenciar Planos de Assinatura - Sistema JyM')

@section('content')
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Gerenciar Planos de Assinatura</h1>

    <div class="mb-4">
    <a href="{{ route('planos.create') }}" class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded">
            Cadastrar Novo Plano
        </a>
    </div>

    <!-- Filtros -->
    <x-search-filter-dropdown 
        placeholder="Nome ou descrição do plano..."
        :filters="[
            [
                'name' => 'academia',
                'label' => 'Academia',
                'type' => 'select',
                'options' => $academias->pluck('nome', 'idAcademia')->toArray()
            ],
            [
                'name' => 'valor_minimo',
                'label' => 'Valor Mínimo',
                'type' => 'number',
                'placeholder' => '0.00',
                'step' => '0.01',
                'min' => '0'
            ],
            [
                'name' => 'valor_maximo',
                'label' => 'Valor Máximo',
                'type' => 'number',
                'placeholder' => '999.99',
                'step' => '0.01',
                'min' => '0'
            ],
            [
                'name' => 'duracao_minima',
                'label' => 'Duração Mínima (dias)',
                'type' => 'number',
                'placeholder' => '1',
                'min' => '1'
            ],
            [
                'name' => 'duracao_maxima',
                'label' => 'Duração Máxima (dias)',
                'type' => 'number',
                'placeholder' => '365',
                'min' => '1'
            ]
        ]"
        :sort-options="[
            'nome_asc' => 'Nome (A-Z)',
            'nome_desc' => 'Nome (Z-A)',
            'valor_asc' => 'Valor (Menor)',
            'valor_desc' => 'Valor (Maior)',
            'duracao_asc' => 'Duração (Menor)',
            'duracao_desc' => 'Duração (Maior)'
        ]"
    />

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nome do Plano
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Descrição
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Valor
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Duração (dias)
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Academia
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Ações
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($planos as $plano)
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $plano->nome }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $plano->descricao ?? 'N/A' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            R$ {{ number_format($plano->valor, 2, ',', '.') }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $plano->duracaoDias }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            {{ $plano->academia->nome ?? 'Academia não encontrada' }}
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('planos.edit', $plano->idPlano) }}" class="text-grip-1 hover:text-grip-red-light">Editar</a>
                                @if($plano->podeDeletar())
                                    <form action="{{ route('planos.destroy', $plano->idPlano) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este plano?');">
                                        @csrf
                                        @method('DELETE')
                                <button type="submit" class="text-grip-2 hover:text-grip-red-dark">Excluir</button>
                                    </form>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" 
                                          title="Não é possível excluir plano com clientes ou mensalidades associadas">
                                        Excluir
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                            Nenhum plano de assinatura cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection