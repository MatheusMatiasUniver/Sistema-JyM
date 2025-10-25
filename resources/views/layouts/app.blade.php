<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>@yield('title', 'Sistema JyM')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        window.pusherConfig = {
            key: '{{ env('PUSHER_APP_KEY', 'local-key') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
            host: '{{ env('PUSHER_HOST', '127.0.0.1') }}',
            port: {{ env('PUSHER_PORT', 8080) }},
            scheme: '{{ env('PUSHER_SCHEME', 'http') }}'
        };
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head_styles')
</head>
<body>
    @include('partials.sidebar')

    <div class="main-content-area">
        @if(session('success'))
            <div class="alert-success" role="alert">
                <strong class="font-bold">Sucesso!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert-error" role="alert">
                <strong class="font-bold">Erro!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </div>
    @stack('body_scripts')
</body>
</html>