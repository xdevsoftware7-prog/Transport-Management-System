{{--
|--------------------------------------------------------------------------
| SIDEBAR PARTIAL
|--------------------------------------------------------------------------
| Pour ajouter un nouveau lien :
|   1. Copier un bloc <li class="nav-item"> existant
|   2. Changer : href, l'icône (fa-*), et le texte du label
|   3. Si c'est un nouveau groupe, copier un bloc <div class="nav-group">
|
| Icônes disponibles : https://fontawesome.com/icons
| --}}

<aside class="sidebar" id="sidebar">

    {{-- ── LOGO ── --}}
    <div class="sidebar-header">
        <div class="sidebar-logo">
            {{-- Remplacer src par votre vrai logo --}}
            <img src="{{ asset('images/logo.png') }}" alt="OBTRANS" class="logo-img">
            <span class="logo-text">OBTRANS</span>
        </div>

        {{-- Bouton collapse / expand --}}
        <button class="collapse-btn" id="collapseBtn" title="Réduire le menu">
            <i class="fa-solid fa-chevron-left" id="collapseIcon"></i>
        </button>
    </div>

    {{-- ── NAVIGATION ── --}}
    <nav class="sidebar-nav">

        {{-- ════ GROUPE : PRINCIPAL ════ --}}
        <div class="nav-group">
            <span class="nav-group-label">Principal</span>

            <ul class="nav-list">
                {{-- Dashboard --}}
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </li>

                {{-- Commandes --}}
                <li class="nav-item {{ request()->routeIs('commandes.*') ? 'active' : '' }}">
                    <a href="{{ route('commandes.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-plus"></i></span>
                        <span class="nav-label">Commandes</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- ════ GROUPE : FLOTTE ════ --}}
        <div class="nav-group">
            <span class="nav-group-label">Flotte</span>

            <ul class="nav-list">
                {{-- Véhicules --}}
                <li class="nav-item {{ request()->routeIs('vehicules.*') ? 'active' : '' }}">
                    <a href="{{ route('vehicules.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-truck"></i></span>
                        <span class="nav-label">Véhicules</span>
                    </a>
                </li>

                {{-- Chauffeurs --}}
                <li class="nav-item {{ request()->routeIs('chauffeurs.*') ? 'active' : '' }}">
                    <a href="{{ route('chauffeurs.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-id-card"></i></span>
                        <span class="nav-label">Chauffeurs</span>
                    </a>
                </li>

                {{-- Gestion des Voyages --}}
                <li class="nav-item {{ request()->routeIs('voyages.*') ? 'active' : '' }}">
                    <a href="{{ route('trajets.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-route"></i></span>
                        <span class="nav-label">Gestion des Voyages</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- ════ GROUPE : COMMERCIAL ════ --}}
        <div class="nav-group">
            <span class="nav-group-label">Commercial</span>

            <ul class="nav-list">
                {{-- Clients --}}
                <li class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <a href="{{ route('clients.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-users"></i></span>
                        <span class="nav-label">Clients</span>
                    </a>
                </li>

                {{-- Factures --}}
                <li class="nav-item {{ request()->routeIs('factures.*') ? 'active' : '' }}">
                    <a href="#" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-chart-bar"></i></span>
                        <span class="nav-label">Factures</span>
                    </a>
                </li>

                {{-- Bons de Livraison --}}
                <li class="nav-item {{ request()->routeIs('bons-livraison.*') ? 'active' : '' }}">
                    <a href="{{ route('bon_livraisons.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-file-invoice"></i></span>
                        <span class="nav-label">Bons de Livraison</span>
                    </a>
                </li>

                {{-- Primes de Déplacement --}}
                <li class="nav-item {{ request()->routeIs('primes.*') ? 'active' : '' }}">
                    <a href="{{ route('prime_deplacements.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></span>
                        <span class="nav-label">Primes de Déplacement</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- ════ GROUPE : ADMINISTRATION ════ --}}
        <div class="nav-group">
            <span class="nav-group-label">Administration</span>

            <ul class="nav-list">
                {{-- Utilisateurs --}}
                <li class="nav-item {{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-user-gear"></i></span>
                        <span class="nav-label">Utilisateurs</span>
                    </a>
                </li>

                {{-- Rôles --}}
                <li class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <a href="{{ route('roles.index') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-shield-halved"></i></span>
                        <span class="nav-label">Rôles</span>
                    </a>
                </li>

                {{-- AutresLink: des liens pour gerer les tables indepedantsm utilitaires pour les autres tables --}}
                <li class="nav-item {{ request()->routeIs('autresLink') ? 'active' : '' }}">
                    <a href="{{ route('autresLink') }}" class="nav-link">
                        <span class="nav-icon"><i class="fa-solid fa-circle-dollar-to-slot"></i></span>
                        <span class="nav-label">Autres Liens</span>
                    </a>
                </li>
            </ul>
        </div>

    </nav>

    {{-- ── PROFIL UTILISATEUR (bas de sidebar) ── --}}
    <div class="sidebar-footer">
        <div class="user-avatar">
            <i class="fa-solid fa-circle-user"></i>
        </div>
        <div class="user-info">
            <span class="user-name">{{ auth()->user()->name ?? 'Utilisateur' }}</span>
            <span class="user-email">{{ auth()->user()->email ?? 'email@example.com' }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn" title="Déconnexion">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </button>
        </form>
    </div>

</aside>
