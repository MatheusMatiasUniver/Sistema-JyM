<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema JyM')</title>
    {{-- Inclua seu CSS de dashboard --}}
    <link rel="stylesheet" href="{{ asset('css/style-dashboard.css') }}">
</head>
<body>
    {{-- Inclui a sidebar --}}
    @include('partials.sidebar')

    <div class="main">
        {{-- Área para mensagens de sucesso ou erro --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- O conteúdo específico de cada página será injetado aqui --}}
        @yield('content')
    </div>
</body>
</html>