{{--
|--------------------------------------------------------------------------
| PAGE : DÉTAIL D'UN RÔLE — OBTRANS TMS
|--------------------------------------------------------------------------
| Vue en lecture seule d'un rôle : infos, permissions et utilisateurs.
|
| VARIABLES ATTENDUES DU CONTROLLER :
|   - $role            : App\Models\Role
|   - $rolePermissions : array  (ex: ['vehicules.voir', 'commandes.creer', ...])
|   - $role->users     : Collection des utilisateurs ayant ce rôle
|
| ROUTE : GET /roles/{role} → RoleController@show
| --}}

@extends('layouts.app')

@section('title', 'Détail — ' . $role->name)
@section('page-title', $role->name)
@section('page-subtitle', 'Détail du rôle et permissions associées')

@section('content')

    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .breadcrumb a {
            color: var(--text-muted);
            transition: color var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--color-primary);
        }

        .breadcrumb-sep {
            font-size: 10px;
        }

        /* ── HERO DU RÔLE ── */
        .role-hero {
            background: var(--color-dark);
            border-radius: var(--border-radius);
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .role-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(224, 32, 32, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(224, 32, 32, .05) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
        }

        .role-hero::after {
            content: '';
            position: absolute;
            right: -80px;
            top: -80px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(224, 32, 32, .2) 0%, transparent 70%);
            pointer-events: none;
        }

        .role-hero-icon {
            width: 60px;
            height: 60px;
            background: rgba(224, 32, 32, .15);
            border: 1px solid rgba(224, 32, 32, .25);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: var(--color-primary);
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        .role-hero-info {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .role-hero-name {
            font-size: 24px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .role-hero-desc {
            font-size: 13px;
            color: #777;
            line-height: 1.5;
            max-width: 500px;
        }

        .role-hero-stats {
            display: flex;
            gap: 28px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat strong {
            display: block;
            font-size: 28px;
            font-weight: 900;
            font-family: 'JetBrains Mono', monospace;
            color: #fff;
            line-height: 1;
        }

        .hero-stat span {
            font-size: 11px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-hero-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        /* ── LAYOUT DÉTAIL ── */
        .detail-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }

        .detail-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .detail-aside {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* ── GRILLE PERMISSIONS (lecture seule) ── */
        .perm-groups-grid {
            display: flex;
            flex-direction: column;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .perm-group-row {
            border-bottom: 1px solid var(--border);
        }

        .perm-group-row:last-child {
            border-bottom: none;
        }

        .perm-group-row-header {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            gap: 12px;
            background: var(--bg-card);
        }

        .perm-group-row-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .perm-group-row-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            flex: 1;
        }

        .perm-group-row-summary {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* Badge permission accordée */
        .perm-badge-granted {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 9px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .perm-badge-granted i {
            font-size: 9px;
        }

        /* Badge permission refusée */
        .perm-badge-denied {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 500;
            padding: 4px 9px;
            background: var(--bg-body);
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-decoration: line-through;
        }

        /* ── COUVERTURE (%) ── */
        .coverage-bar-wrap {
            margin-top: 10px;
        }

        .coverage-label {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .coverage-bar {
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }

        .coverage-fill {
            height: 100%;
            background: var(--color-primary);
            border-radius: 2px;
            transition: width .6s ease;
        }

        /* ── UTILISATEURS ── */
        .users-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .user-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: var(--bg-body);
            transition: border-color var(--transition);
        }

        .user-row:hover {
            border-color: var(--color-primary);
        }

        .user-row-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--color-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            font-family: 'JetBrains Mono', monospace;
        }

        .user-row-info {
            flex: 1;
        }

        .user-row-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-row-email {
            font-size: 11px;
            color: var(--text-muted);
        }

        .user-row-status {
            font-size: 10px;
        }

        .users-empty {
            text-align: center;
            padding: 24px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .users-empty i {
            display: block;
            font-size: 24px;
            margin-bottom: 8px;
            color: var(--border);
        }

        /* ── ASIDE : fiche résumé ── */
        .aside-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .aside-card-header {
            padding: 13px 18px;
            border-bottom: 1px solid var(--border);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            background: var(--bg-body);
        }

        .aside-card-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .aside-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .aside-row-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .aside-row-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .aside-row-value.primary {
            color: var(--color-primary);
        }

        /* Barre de progression aside */
        .aside-progress {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .aside-progress-bar {
            height: 6px;
            background: var(--border);
            border-radius: 3px;
            overflow: hidden;
        }

        .aside-progress-fill {
            height: 100%;
            background: var(--color-primary);
            border-radius: 3px;
        }

        .aside-progress-label {
            font-size: 11px;
            color: var(--text-muted);
            text-align: right;
        }

        /* Boutons aside */
        .aside-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 18px;
            border-top: 1px solid var(--border);
        }

        .btn-edit-role {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px;
            background: var(--color-dark);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: background var(--transition), transform var(--transition);
        }

        .btn-edit-role:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-back:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* Dates */
        .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--text-muted);
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 3px 8px;
        }

        @media (max-width: 960px) {
            .detail-layout {
                grid-template-columns: 1fr;
            }

            .detail-aside {
                order: -1;
            }

            .role-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .role-hero-stats {
                border-top: 1px solid #222;
                padding-top: 16px;
                width: 100%;
                justify-content: space-around;
            }
        }
    </style>

    {{-- ── BREADCRUMB ── --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('roles.index') }}">Rôles</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>{{ $role->name }}</span>
    </div>

    {{-- ── HERO ── --}}
    <div class="role-hero">
        <div class="role-hero-icon">
            <i class="fa-solid fa-shield-halved"></i>
        </div>

        <div class="role-hero-info">
            <div class="role-hero-name">{{ $role->name }}</div>
            <div class="role-hero-desc">{{ $role->description ?: 'Aucune description renseignée pour ce rôle.' }}</div>
            <div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap">
                <span class="date-chip"><i class="fa-solid fa-calendar-plus"></i> Créé
                    {{ $role->created_at->diffForHumans() }}</span>
                <span class="date-chip"><i class="fa-solid fa-calendar-pen"></i> Modifié
                    {{ $role->updated_at->diffForHumans() }}</span>
            </div>
        </div>

        @php
            $rp = $rolePermissions ?? [];
            $totalPerms = $groupedPermissions->count();
            $grantedCount = count($rp);
            $usersCount = $role->users_count ?? ($role->users ? $role->users->count() : 0);
            $coverage = $totalPerms > 0 ? round(($grantedCount / $totalPerms) * 100) : 0;
        @endphp

        <div class="role-hero-stats">
            <div class="hero-stat">
                <strong>{{ $grantedCount }}</strong>
                <span>Permissions</span>
            </div>
            <div class="hero-stat">
                <strong>{{ $usersCount }}</strong>
                <span>Utilisateurs</span>
            </div>
            <div class="hero-stat">
                <strong>{{ $coverage }}%</strong>
                <span>Couverture</span>
            </div>
        </div>

        <div class="role-hero-actions">
            <a href="{{ route('roles.edit', $role) }}" class="btn-edit-role">
                <i class="fa-solid fa-pen-to-square"></i> Modifier
            </a>
            <a href="{{ route('roles.index') }}" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    {{-- ── LAYOUT PRINCIPAL ── --}}
    <div class="detail-layout">

        {{-- ══ COLONNE PRINCIPALE ══ --}}
        <div class="detail-main">

            {{-- Permissions --}}
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fa-solid fa-shield-halved"
                            style="color:var(--color-primary);margin-right:6px;font-size:15px"></i>
                        Permissions accordées
                    </h2>
                    <span class="tag {{ $grantedCount > 0 ? 'tag-active' : 'tag-expired' }}">
                        {{ $grantedCount }} / {{ $totalPerms }}
                    </span>
                </div>

                {{-- Barre de couverture globale --}}
                <div class="coverage-bar-wrap" style="margin-bottom:20px">
                    <div class="coverage-label">
                        <span>Couverture des permissions</span>
                        <span>{{ $coverage }}%</span>
                    </div>
                    <div class="coverage-bar">
                        <div class="coverage-fill" style="width:{{ $coverage }}%"></div>
                    </div>
                </div>

                {{--
            ── POUR CHAQUE GROUPE :
               Copier un bloc .perm-group-row, adapter le titre, l'icône et la liste des permissions.
               in_array($perm, $rp) retourne true si la permission est accordée.
            --}}
                <div class="perm-groups-grid">
                    @foreach ($groupedPermissions as $module => $permissions)
                        <div class="perm-group-row">
                            <div class="perm-group-row-header">
                                {{-- Icône dynamique basée sur le nom du module --}}
                                <div class="perm-group-row-icon">
                                    <i
                                        class="fa-solid {{ match ($module) {
                                            'vehicules' => 'fa-truck',
                                            'chauffeurs' => 'fa-id-card',
                                            'commandes' => 'fa-plus',
                                            'facturation' => 'fa-file-invoice-dollar',
                                            'admin' => 'fa-user-gear',
                                            default => 'fa-shield-halved',
                                        } }}"></i>
                                </div>

                                <span class="perm-group-row-name">{{ ucfirst($module) }}</span>

                                @php
                                    // On compte combien de permissions de ce module le rôle possède
                                    $modulePermsNames = $permissions->pluck('name')->toArray();
                                    $grantedInModule = count(array_intersect($modulePermsNames, $rolePermissions));
                                @endphp

                                <span class="tag {{ $grantedInModule > 0 ? 'tag-active' : 'tag-expired' }}"
                                    style="font-size:10px">
                                    {{ $grantedInModule }} / {{ $permissions->count() }}
                                </span>
                            </div>

                            <div style="padding:0 20px 14px; display:flex; flex-wrap:wrap; gap:6px">
                                @foreach ($permissions as $permission)
                                    @php
                                        $isGranted = in_array($permission->name, $rolePermissions);
                                        // Nettoyage du label (ex: "vehicules.creer" -> "creer")
                                        $label = str_contains($permission->name, '.')
                                            ? explode('.', $permission->name)[1]
                                            : $permission->name;
                                    @endphp

                                    @if ($isGranted)
                                        <span class="perm-badge-granted">
                                            <i class="fa-solid fa-check"></i> {{ ucfirst($label) }}
                                        </span>
                                    @else
                                        <span class="perm-badge-denied">
                                            {{ ucfirst($label) }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>{{-- /perm-groups-grid --}}
            </div>

            {{-- Utilisateurs ayant ce rôle --}}
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fa-solid fa-users" style="color:var(--color-primary);margin-right:6px;font-size:15px"></i>
                        Utilisateurs avec ce rôle
                    </h2>
                    <span class="tag tag-active">{{ $usersCount }}</span>
                </div>

                @if ($role->users && $role->users->count() > 0)
                    <div class="users-list">
                        @foreach ($role->users as $user)
                            <div class="user-row">
                                <div class="user-row-avatar">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="user-row-info">
                                    <div class="user-row-name">{{ $user->name }}</div>
                                    <div class="user-row-email">{{ $user->email }}</div>
                                </div>
                                <span class="tag tag-active user-row-status">Actif</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="users-empty">
                        <i class="fa-solid fa-users-slash"></i>
                        Aucun utilisateur n'est assigné à ce rôle.
                    </div>
                @endif
            </div>

        </div>{{-- /detail-main --}}

        {{-- ══ COLONNE ASIDE ══ --}}
        <div class="detail-aside">

            {{-- Fiche résumé --}}
            <div class="aside-card">
                <div class="aside-card-header">Résumé</div>
                <div class="aside-card-body">
                    <div class="aside-row">
                        <div class="aside-row-label">Nom du rôle</div>
                        <div class="aside-row-value primary">{{ $role->name }}</div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Utilisateurs</div>
                        <div class="aside-row-value">{{ $usersCount }}</div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Permissions accordées</div>
                        <div class="aside-row-value">{{ $grantedCount }} / {{ $totalPerms }}</div>
                    </div>
                    <div class="aside-progress">
                        <div class="aside-progress-bar">
                            <div class="aside-progress-fill" style="width:{{ $coverage }}%"></div>
                        </div>
                        <div class="aside-progress-label">{{ $coverage }}% de couverture</div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Créé le</div>
                        <div class="aside-row-value" style="font-size:12px;font-weight:500">
                            {{ $role->created_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Dernière modification</div>
                        <div class="aside-row-value" style="font-size:12px;font-weight:500">
                            {{ $role->updated_at->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                </div>
                <div class="aside-actions">
                    <a href="{{ route('roles.edit', $role) }}" class="btn-edit-role">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Modifier ce rôle
                    </a>
                    <a href="{{ route('roles.index') }}" class="btn-back">
                        <i class="fa-solid fa-list"></i>
                        Tous les rôles
                    </a>
                    <a href="{{ route('permissions.index', $role) }}" class="btn-edit-role">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Gérer les permissions
                    </a>
                </div>
            </div>

            {{-- Permissions accordées (liste compacte) --}}
            <div class="aside-card">
                <div class="aside-card-header">Permissions actives</div>
                <div class="aside-card-body" style="gap:6px">
                    @forelse($rp as $perm)
                        <span class="perm-badge-granted" style="width:fit-content">
                            <i class="fa-solid fa-check"></i>
                            {{ str_replace('.', ' › ', $perm) }}
                        </span>
                    @empty
                        <span style="font-size:12px;color:var(--text-muted);font-style:italic">
                            Aucune permission accordée.
                        </span>
                    @endforelse
                </div>
            </div>

        </div>{{-- /detail-aside --}}

    </div>{{-- /detail-layout --}}

@endsection
