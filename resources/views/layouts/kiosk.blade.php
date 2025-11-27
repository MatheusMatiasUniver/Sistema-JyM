<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>@yield('title', 'Reconhecimento Facial - JyM Kiosk')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
<body class="bg-primary-dark text-text-white flex flex-col items-center justify-center min-h-screen p-4">
    @yield('content')

    @stack('body_scripts')
</body>
</html>