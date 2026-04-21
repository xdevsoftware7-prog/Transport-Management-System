@extends('layouts.app')

@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')
@section('page-subtitle', 'Gérer les comptes et les accès de votre organisation')

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

        .filter-select {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            cursor: pointer;
            transition: border-color var(--transition);
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

        /* Cellule utilisateur (avatar + nom + email) */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--color-dark);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
        }

        .user-cell-info {}

        .user-cell-name {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 13px;
        }

        .user-cell-email {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* Rôles */
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

        .role-chip-more {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 7px;
            background: var(--bg-body);
            color: var(--text-muted);
            border: 1px solid var(--border);
            border-radius: 4px;
        }

        .roles-none {
            font-size: 11px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Date */
        .date-text {
            font-size: 11px;
            color: var(--text-muted);
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
            text-decoration: none;
            transition: border-color var(--transition), background var(--transition), color var(--transition);
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

        .page-btn.disabled {
            opacity: .35;
            pointer-events: none;
        }

        /* ── EMPTY ── */
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

        /* ── FLASH ── */
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

        /* btn-icon warning */
        .btn-icon--warning:hover {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
        }

        /* Badge "Moi" */
        .me-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 5px;
            background: var(--color-dark);
            color: #fff;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            vertical-align: middle;
            margin-left: 4px;
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Utilisateurs</span>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="flash-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flash-error">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ── KPI ── --}}
    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total utilisateurs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Actifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['inactifs'] }}</div>
            <div class="kpi-label">Inactifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['admins'] }}</div>
            <div class="kpi-label">Administrateurs</div>
        </div>
    </div>

    {{-- ── TABLEAU ── --}}
    <div class="section-card">

        <div class="section-header" style="flex-wrap:wrap;gap:12px">
            <h2 class="section-title">Liste des utilisateurs</h2>
            <div class="toolbar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="search-input" id="searchInput" placeholder="Nom, email…">
                </div>
                <select class="filter-select" id="filterRole">
                    <option value="">Tous les rôles</option>
                    {{-- Généré dynamiquement par le controller --}}
                    @foreach ($allRoles ?? [] as $role)
                        <option value="{{ strtolower($role->name) }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </a>
            </div>
        </div>

        <div class="table-wrap">
            <table class="data-table" id="userTable">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôles</th>
                        <th>Créé le</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $initials = strtoupper(
                                substr($user->name, 0, 1) .
                                    (strpos($user->name, ' ') !== false
                                        ? substr($user->name, strpos($user->name, ' ') + 1, 1)
                                        : ''),
                            );
                            $roleNames = $user->roles->pluck('name')->map(fn($n) => strtolower($n))->implode(' ');
                            $isMe = auth()->id() === $user->id;
                        @endphp
                        <tr data-search="{{ strtolower($user->name . ' ' . $user->email) }}"
                            data-roles="{{ $roleNames }}">

                            {{-- Utilisateur --}}
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">{{ $initials }}</div>
                                    <div class="user-cell-info">
                                        <div class="user-cell-name">
                                            {{ $user->name }}
                                            @if ($isMe)
                                                <span class="me-badge">Moi</span>
                                            @endif
                                        </div>
                                        <div class="user-cell-email">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Rôles --}}
                            <td>
                                @if ($user->roles && $user->roles->count() > 0)
                                    <div class="roles-chips">
                                        @foreach ($user->roles->take(2) as $role)
                                            <span class="role-chip">{{ $role->name }}</span>
                                        @endforeach
                                        @if ($user->roles->count() > 2)
                                            <span class="role-chip-more">+{{ $user->roles->count() - 2 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="roles-none">Aucun rôle</span>
                                @endif
                            </td>

                            {{-- Date --}}
                            <td>
                                <span class="date-text" title="{{ $user->created_at->format('d/m/Y H:i') }}">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                <span
                                    class="tag {{ $user->email_verified_at || !isset($user->email_verified_at) ? 'tag-active' : 'tag-pending' }}">
                                    {{ $user->email_verified_at || !isset($user->email_verified_at) ? 'Actif' : 'En attente' }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn-icon btn-icon--warning"
                                        title="Détail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if (!$isMe)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                                            id="delete-form-{{ $user->id }}" style="display: none;">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                            onclick="handleDeleteUser({{ $user->id }}, '{{ $user->name }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-users-slash"></i>
                                    <p>Aucun utilisateur trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="pagination-wrap">
                <span class="pagination-info">
                    {{ $users->firstItem() }}–{{ $users->lastItem() }} sur {{ $users->total() }} utilisateur(s)
                </span>
                <div class="pagination-links">
                    <a class="page-btn {{ $users->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $users->previousPageUrl() ?? '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        <a class="page-btn {{ $page == $users->currentPage() ? 'active' : '' }}"
                            href="{{ $url }}">{{ $page }}</a>
                    @endforeach
                    <a class="page-btn {{ !$users->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $users->nextPageUrl() ?? '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var searchInput = document.getElementById('searchInput');
            var filterRole = document.getElementById('filterRole');
            var rows = document.querySelectorAll('#userTable tbody tr[data-search]');

            function applyFilter() {
                var search = searchInput.value.toLowerCase().trim();
                var role = filterRole.value.toLowerCase().trim();
                rows.forEach(function(row) {
                    var matchSearch = !search || row.dataset.search.includes(search);
                    var matchRole = !role || row.dataset.roles.includes(role);
                    row.style.display = (matchSearch && matchRole) ? '' : 'none';
                });
            }



            searchInput.addEventListener('input', applyFilter);
            filterRole.addEventListener('change', applyFilter);
        });

        function handleDeleteUser(id, name) {
            Swal.fire({
                title: 'Supprimer l\'utilisateur ?',
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
