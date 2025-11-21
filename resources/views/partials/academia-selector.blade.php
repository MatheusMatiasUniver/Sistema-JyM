@if(Auth::check() && Auth::user()->isAdministrador())
<div x-data="{ open: false }" class="fixed top-4 right-4 z-[9999]">
    <button @click="open = !open" 
            type="button"
            class="flex items-center gap-2 px-4 py-2 bg-grip-1 hover:bg-grip-2 text-white rounded-lg shadow-lg transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span class="text-sm font-medium">
            @php
                $academiaSelecionadaId = session('academia_selecionada');
                $academiaAtual = $academiaSelecionadaId ? Auth::user()->academias()->where('academias.idAcademia', $academiaSelecionadaId)->first() : null;
            @endphp
            {{ $academiaAtual ? $academiaAtual->nome : 'Selecione Academia' }}
        </span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         x-transition
         class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl max-h-96 overflow-y-auto">
        @foreach(Auth::user()->academias as $academia)
            <button type="button"
                    data-academia-id="{{ $academia->idAcademia }}"
                    onclick="trocarAcademia(this.getAttribute('data-academia-id'))"
                    class="w-full text-left px-4 py-3 hover:bg-grip-4 transition-colors {{ session('academia_selecionada') == $academia->idAcademia ? 'bg-grip-4 text-grip-3 font-semibold' : 'text-grip-3' }}">
                <div class="flex items-center justify-between">
                    <span>{{ $academia->nome }}</span>
                    @if(session('academia_selecionada') == $academia->idAcademia)
                        <svg class="w-5 h-5 text-grip-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            </button>
        @endforeach
    </div>
</div>

<script>
    function trocarAcademia(academiaId) {
        const url = '{{ route("academia.trocar") }}';
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ idAcademia: academiaId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
            }
        })
        .catch(error => {
            alert('Erro ao trocar academia');
        });
    }
</script>
@endif