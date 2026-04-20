{{--
|--------------------------------------------------------------------------
| TOPBAR PARTIAL
|--------------------------------------------------------------------------
| Barre supérieure : titre de la page + actions globales (notifications, etc.)
|--}}

<header class="topbar">
    <div class="topbar-left">
        {{-- Titre et sous-titre de la page courante --}}
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        <p class="page-subtitle">@yield('page-subtitle', '')</p>
    </div>

    <div class="topbar-right">
        {{-- Notifications --}}
        <button class="topbar-btn" title="Notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="badge">3</span>
        </button>

        {{-- Paramètres rapides --}}
        <button class="topbar-btn" title="Paramètres">
            <i class="fa-solid fa-gear"></i>
        </button>
    </div>
</header>