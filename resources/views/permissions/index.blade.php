{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES PERMISSIONS — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $permissions : Collection paginée des permissions
|   - $stats       : ['total' => int, 'groupes' => int, 'utilisees' => int, 'libres' => int]
|
| ROUTE : GET /permissions → PermissionController@index
| --}}

@extends('layouts.app')

@section('title', 'Permissions')
@section('page-title', 'Gestion des Permissions')
@section('page-subtitle', 'Définir et organiser les permissions de votre système')

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

        /* ── TOOLBAR ── */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            min-width: 220px;
        }

        .search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 9px 12px 9px 34px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .search-input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
            background: #fff;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-select {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition);
            cursor: pointer;
        }

        .filter-select:focus {
            border-color: var(--color-primary);
        }

        /* ── TABLE ── */
        .table-wrap {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .data-table th {
            text-align: left;
            padding: 10px 14px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            white-space: nowrap;
        }

        .data-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover td {
            background: #fafafa;
        }

        /* Nom de la permission avec icône groupe */
        .perm-name-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .perm-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--color-primary);
            flex-shrink: 0;
        }

        .perm-name-text {
            font-weight: 600;
            color: var(--text-primary);
        }

        .perm-slug {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: 2px 6px;
        }

        /* Groupe badge */
        .group-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 9px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .group-chip i {
            font-size: 10px;
            color: var(--text-muted);
        }

        /* Rôles assignés */
        .roles-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .role-chip {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 7px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .roles-none {
            font-size: 11px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* ── PAGINATION ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 10px;
        }

        .pagination-info {
            font-size: 12px;
            color: var(--text-muted);
        }

        .pagination-links {
            display: flex;
            gap: 4px;
        }

        .page-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            background: transparent;
            font-size: 12px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color var(--transition), background var(--transition), color var(--transition);
            text-decoration: none;
        }

        .page-btn:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .page-btn.active {
            background: var(--color-dark);
            color: #fff;
            border-color: var(--color-dark);
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 36px;
            color: var(--border);
            margin-bottom: 12px;
            display: block;
        }

        .empty-state p {
            font-size: 14px;
        }

        /* ── FLASH MESSAGE ── */
        .flash-success {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            border-left: 3px solid #10b981;
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 13px;
            color: #059669;
        }

        .flash-error {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(224, 32, 32, .06);
            border: 1px solid rgba(224, 32, 32, .2);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 13px;
            color: var(--color-primary);
        }

        /* btn-icon--warning */
        .btn-icon--warning:hover {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Permissions</span>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="flash-success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── KPI CARDS ── --}}
    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">

        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total permissions</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['moyenne_par_role'] }}</div>
            <div class="kpi-label">Moyenne d'assignation par role</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['utilisees'] }}</div>
            <div class="kpi-label">Utiliser</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['orphelines'] }}</div>
            <div class="kpi-label">Non assignées</div>
        </div>

    </div>

    {{-- ── TABLEAU ── --}}
    <div class="section-card">

        {{-- Header + Toolbar --}}
        <div class="section-header" style="flex-wrap:wrap;gap:12px">
            <h2 class="section-title">Liste des permissions</h2>
            <div class="toolbar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Rechercher une permission…">
                </div>
                <form action="{{ route('permissions.index') }}" method="GET" id="filterForm">
                    <div class="filter-group" style="display: flex; gap: 10px; align-items: center;">

                        {{-- Ajout du onchange pour soumettre dès la sélection --}}
                        <select name="module" class="filter-select" id="filterGroup" onchange="this.form.submit()">
                            <option value="">Tous les groupes</option>
                            @foreach ($modules as $module)
                                {{-- On compare avec la variable $selectedModule envoyée par le contrôleur --}}
                                <option value="{{ $module }}"
                                    {{ isset($selectedModule) && $selectedModule == $module ? 'selected' : '' }}>
                                    {{ ucfirst($module) }}
                                </option>
                            @endforeach
                        </select>

                        @if (request('module'))
                            <a href="{{ route('permissions.index') }}" class="btn-back"
                                style="padding: 9px 15px; background: var(--bg-body); border: 1px solid var(--border); border-radius: var(--border-radius-sm); color: var(--text-muted); text-decoration: none; font-size: 13px;">
                                <i class="fa-solid fa-xmark"></i> Effacer
                            </a>
                        @endif

                    </div>
                </form>
                <a href="{{ route('permissions.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            <table class="data-table" id="permTable">
                <thead>
                    <tr>
                        <th>Permission</th>
                        <th>Slug</th>
                        <th>Groupe</th>
                        <th>Description</th>
                        <th>Rôles assignés</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        @php
                            // On extrait le groupe dynamiquement pour le JS
                            $dynamicGroup = explode('.', $permission->name)[0];
                        @endphp
                        <tr data-name="{{ strtolower($permission->name) }}"
                            data-group="{{ strtolower($dynamicGroup ?? '') }}">

                            {{-- Nom --}}
                            <td>
                                <div class="perm-name-cell">
                                    <span class="perm-dot"></span>
                                    <span class="perm-name-text">{{ $permission->name }}</span>
                                </div>
                            </td>

                            {{-- Slug --}}
                            <td>
                                <span class="perm-slug">{{ $permission->slug }}</span>
                            </td>

                            {{-- Groupe --}}
                            <td>
                                @php
                                    $icons = [
                                        'vehicules' => 'fa-truck',
                                        'chauffeurs' => 'fa-id-card',
                                        'commandes' => 'fa-plus',
                                        'facturation' => 'fa-file-invoice-dollar',
                                        'administration' => 'fa-user-gear',
                                    ];
                                    $icon = $icons[strtolower($permission->group ?? '')] ?? 'fa-shield-halved';
                                @endphp
                                <span class="group-chip">
                                    <i class="fa-solid {{ $icon }}"></i>
                                    {{ $permission->group ?? '—' }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td style="color:var(--text-muted);font-size:12px;max-width:200px">
                                {{ $permission->description ?: '—' }}
                            </td>

                            {{-- Rôles --}}
                            <td>
                                @if ($permission->roles && $permission->roles->count() > 0)
                                    <div class="roles-chips">
                                        @foreach ($permission->roles->take(3) as $role)
                                            <span class="role-chip">{{ $role->name }}</span>
                                        @endforeach
                                        @if ($permission->roles->count() > 3)
                                            <span class="role-chip"
                                                style="background:var(--bg-body);color:var(--text-muted);border:1px solid var(--border)">
                                                +{{ $permission->roles->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="roles-none">Aucun rôle</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('permissions.show', $permission) }}"
                                        class="btn-icon btn-icon--warning" title="Détail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form id="delete-form-{{ $permission->id }}"
                                        action="{{ route('permissions.destroy', $permission) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeletePermission({{ $permission->id }}, '{{ $permission->name }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <p>Aucune permission trouvée.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($permissions->hasPages())
            <div class="pagination-wrap">
                <span class="pagination-info">
                    {{ $permissions->firstItem() }}–{{ $permissions->lastItem() }} sur {{ $permissions->total() }}
                    permission(s)
                </span>
                <div class="pagination-links">
                    {{-- Précédent --}}
                    @if ($permissions->onFirstPage())
                        <span class="page-btn" style="opacity:.35;pointer-events:none">
                            <i class="fa-solid fa-chevron-left"></i>
                        </span>
                    @else
                        <a class="page-btn" href="{{ $permissions->previousPageUrl() }}">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pages --}}
                    @foreach ($permissions->getUrlRange(1, $permissions->lastPage()) as $page => $url)
                        <a class="page-btn {{ $page == $permissions->currentPage() ? 'active' : '' }}"
                            href="{{ $url }}">{{ $page }}</a>
                    @endforeach

                    {{-- Suivant --}}
                    @if ($permissions->hasMorePages())
                        <a class="page-btn" href="{{ $permissions->nextPageUrl() }}">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="page-btn" style="opacity:.35;pointer-events:none">
                            <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        // Recherche + filtre groupe côté client (sans requête serveur)
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.getElementById('searchInput');
            var filterGroup = document.getElementById('filterGroup');
            var rows = document.querySelectorAll('#permTable tbody tr[data-name]');

            function applyFilter() {
                var search = searchInput.value.toLowerCase().trim();
                var group = filterGroup.value.toLowerCase().trim();
                rows.forEach(function(row) {
                    var matchSearch = !search || row.dataset.name.includes(search);
                    var matchGroup = !group || row.dataset.group === group;
                    row.style.display = (matchSearch && matchGroup) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', applyFilter);
            filterGroup.addEventListener('change', applyFilter);
        });

        function handleDeletePermission(id, name) {
            Swal.fire({
                title: 'Supprimer la permission ?',
                text: `Êtes-vous sûr de vouloir supprimer cette permission "${name}" ? Cette action est irréversible.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e02020', // Ton rouge OBTRANS
                cancelButtonColor: '#1a1a1a', // Ton gris foncé/noir
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                background: '#111', // Fond sombre pour matcher ton thème
                color: '#fff', // Texte blanc
                customClass: {
                    popup: 'swal-custom-radius'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // On soumet le formulaire correspondant
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endpush
