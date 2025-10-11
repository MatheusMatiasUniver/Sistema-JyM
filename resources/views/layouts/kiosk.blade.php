<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Reconhecimento Facial - JyM Kiosk')</title>
    @vite(['resources/css/app.css']) 
    @stack('head_styles') 
</head>
<body class="bg-primary-dark text-text-white flex flex-col items-center justify-center min-h-screen p-4">
    @yield('content')

    @stack('body_scripts')
</body>
</html>