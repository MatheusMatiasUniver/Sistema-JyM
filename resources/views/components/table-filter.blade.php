@props([
    'action' => '',
    'searchPlaceholder' => 'Pesquisar...',
    'searchValue' => '',
    'filters' => [],
    'sortOptions' => []
])

<div class="bg-white shadow-md rounded-lg p-4 mb-4">
    <form action="{{ $action }}" method="GET" class="flex flex-wrap items-end gap-4" autocomplete="off">
        <!-- Campo de Pesquisa -->
        <div class="flex-grow min-w-64">
            <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Pesquisar:</label>
            <input type="text" 
                   id="search" 
                   name="search" 
                   placeholder="{{ $searchPlaceholder }}"
                   value="{{ $searchValue }}"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-black">
        </div>

        <!-- Filtros Dinâmicos -->
        @foreach($filters as $filter)
            <div class="min-w-32">
                <label for="{{ $filter['name'] }}" class="block text-gray-700 text-sm font-bold mb-2">{{ $filter['label'] }}:</label>
                
                @if($filter['type'] === 'select')
                    <select id="{{ $filter['name'] }}" 
                            name="{{ $filter['name'] }}"
                            class="select">
                        <option value="">{{ $filter['placeholder'] ?? 'Todos' }}</option>
                        @foreach($filter['options'] as $value => $label)
                            <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                @elseif($filter['type'] === 'date')
                    <input type="date" 
                           id="{{ $filter['name'] }}" 
                           name="{{ $filter['name'] }}"
                           value="{{ request($filter['name']) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-black">
                @elseif($filter['type'] === 'number')
                    <input type="number" 
                           id="{{ $filter['name'] }}" 
                           name="{{ $filter['name'] }}"
                           placeholder="{{ $filter['placeholder'] ?? '' }}"
                           value="{{ request($filter['name']) }}"
                           step="{{ $filter['step'] ?? '0.01' }}"
                           min="{{ $filter['min'] ?? '0' }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-black">
                @endif
            </div>
        @endforeach

        <!-- Ordenação (se fornecida) -->
        @if(!empty($sortOptions))
            <div class="min-w-32">
                <label for="sort" class="block text-gray-700 text-sm font-bold mb-2">Ordenar:</label>
                <select id="sort" 
                        name="sort"
                        class="select">
                    <option value="">Padrão</option>
                    @foreach($sortOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('sort') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Botões de Ação -->
        <div class="flex gap-2">
            <button type="submit" 
                    class="bg-grip-1 hover:bg-grip-2 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Filtrar
            </button>
            <a href="{{ $action }}" 
               class="bg-grip-1 hover:bg-grip-2 hover:text-white text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Limpar
            </a>
        </div>
    </form>
</div>
