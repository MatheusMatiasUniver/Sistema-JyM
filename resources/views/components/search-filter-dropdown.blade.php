@props(['placeholder' => 'Pesquisar...', 'filters' => [], 'sortOptions' => []])

<div class="mb-6 bg-white p-4 rounded-lg shadow-md">
    <form method="GET" action="{{ request()->url() }}" class="space-y-4">
        <!-- Barra de Pesquisa e Botão de Filtros -->
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Campo de Pesquisa -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                <input 
                    type="text" 
                    name="search" 
                    id="search"
                    value="{{ request('search') }}" 
                    placeholder="{{ $placeholder }}"
                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
            
            <!-- Botão de Filtros -->
            @if(count($filters) > 0 || count($sortOptions) > 0)
                <div class="flex items-end gap-2">
                    <button 
                        type="button" 
                        id="toggleFilters"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 flex items-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                        </svg>
                        Filtros
                        <svg id="filterArrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    
                    <!-- Botão de Pesquisar -->
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        Pesquisar
                    </button>
                </div>
            @else
                <!-- Botão de Pesquisar quando não há filtros -->
                <div class="flex items-end">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        Pesquisar
                    </button>
                </div>
            @endif
        </div>

        <!-- Dropdown de Filtros -->
        @if(count($filters) > 0 || count($sortOptions) > 0)
            <div id="filtersDropdown" class="hidden border-t pt-4 mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Ordenação -->
                    @if(count($sortOptions) > 0)
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                            <select 
                                name="sort" 
                                id="sort"
                                class="w-full px-3 py-2  text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
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

                    <!-- Filtros Adicionais -->
                    @foreach($filters as $filter)
                        <div>
                            <label for="{{ $filter['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $filter['label'] }}
                            </label>
                            
                            @if($filter['type'] === 'select')
                                <select 
                                    name="{{ $filter['name'] }}" 
                                    id="{{ $filter['name'] }}"
                                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                                    <option value="">Todos</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}" {{ request($filter['name']) == $value ? 'selected' : '' }}>
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
                                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                            @elseif($filter['type'] === 'number')
                                <input 
                                    type="number" 
                                    name="{{ $filter['name'] }}" 
                                    id="{{ $filter['name'] }}"
                                    value="{{ request($filter['name']) }}"
                                    placeholder="{{ $filter['placeholder'] ?? '' }}"
                                    step="{{ $filter['step'] ?? 'any' }}"
                                    min="{{ $filter['min'] ?? '' }}"
                                    max="{{ $filter['max'] ?? '' }}"
                                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                >
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Botões de Ação do Dropdown -->
                <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t">
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        Aplicar Filtros
                    </button>
                    <a 
                        href="{{ request()->url() }}" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        Limpar Filtros
                    </a>
                </div>
            </div>
        @endif
    </form>
</div>

<!-- JavaScript para controlar o dropdown -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleFilters');
    const dropdown = document.getElementById('filtersDropdown');
    const arrow = document.getElementById('filterArrow');
    
    if (toggleButton && dropdown && arrow) {
        toggleButton.addEventListener('click', function() {
            const isHidden = dropdown.classList.contains('hidden');
            
            if (isHidden) {
                dropdown.classList.remove('hidden');
                arrow.style.transform = 'rotate(180deg)';
            } else {
                dropdown.classList.add('hidden');
                arrow.style.transform = 'rotate(0deg)';
            }
        });

        // Verificar se há filtros ativos e mostrar o dropdown automaticamente
        const hasActiveFilters = Array.from(dropdown.querySelectorAll('input, select')).some(input => {
            if (input.type === 'select-one') {
                return input.value !== '';
            }
            return input.value !== '';
        });

        if (hasActiveFilters) {
            dropdown.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        }
    }
});
</script>