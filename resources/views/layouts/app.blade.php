<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OBTRANS TMS — @yield('title', 'Dashboard')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap"
        rel="stylesheet">

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- App CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
    {{-- laravel notify css --}}
    @notifyCss
</head>

<body>

    {{-- ============================================================
     SIDEBAR
     Pour ajouter un item : copier un bloc <li> dans partials/sidebar.blade.php
     ============================================================ --}}
    @include('partials.sidebar')

    {{-- ============================================================
     MAIN CONTENT WRAPPER
     ============================================================ --}}
    <div class="main-wrapper" id="mainWrapper">

        {{-- Top bar --}}
        @include('partials.topbar')

        {{-- Page Content --}}
        <main class="page-content">
            @yield('content')
        </main>

    </div>

    {{-- appel de laravel notify --}}
    <x-notify::notify />
    {{-- App JS --}}
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
    {{-- laravel notify js --}}
    @notifyJs
</body>

</html>
