@props(['placeholder' => 'Pesquisar...', 'filters' => [], 'sortOptions' => []])

<div class="mb-6 bg-white p-4 rounded-lg shadow-md">
    <form method="GET" action="{{ request()->url() }}" class="space-y-4">
        <!-- Barra de Pesquisa -->
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                <input 
                    type="text" 
                    name="search" 
                    id="search"
                    value="{{ request('search') }}" 
                    placeholder="{{ $placeholder }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            
            <!-- Ordenação -->
            @if(count($sortOptions) > 0)
                <div class="md:w-48">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                    <select 
                        name="sort" 
                        id="sort"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Padrão</option>
                        @foreach($sortOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('sort') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- Filtros Adicionais -->
        @if(count($filters) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($filters as $filter)
                    <div>
                        <label for="{{ $filter['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $filter['label'] }}
                        </label>
                        
                        @if($filter['type'] === 'select')
                            <select 
                                name="{{ $filter['name'] }}" 
                                id="{{ $filter['name'] }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                                <option value="">Todos</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}" {{ request($filter['name']) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif($filter['type'] === 'date')
                            <input 
                                type="date" 
                                name="{{ $filter['name'] }}" 
                                id="{{ $filter['name'] }}"
                                value="{{ request($filter['name']) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                        @elseif($filter['type'] === 'number')
                            <input 
                                type="number" 
                                name="{{ $filter['name'] }}" 
                                id="{{ $filter['name'] }}"
                                value="{{ request($filter['name']) }}"
                                placeholder="{{ $filter['placeholder'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            >
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Botões de Ação -->
        <div class="flex flex-wrap gap-2">
            <button 
                type="submit" 
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            >
                Filtrar
            </button>
            
            <a 
                href="{{ request()->url() }}" 
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
            >
                Limpar
            </a>
        </div>
    </form>
</div>