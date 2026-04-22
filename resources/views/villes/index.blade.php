{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES VILLES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $villes : LengthAwarePaginator  (paginate avec filtre nom)
|   - $stats  : ['total' => int, 'ce_mois' => int]
|
| ROUTE : GET /villes?search=&page= → VilleController@index
| --}}

@extends('layouts.app')

@section('title', 'Villes')
@section('page-title', 'Gestion des Villes')
@section('page-subtitle', 'Référentiel géographique des villes de transport')

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

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 220px;
        }

        .search-wrap {
            position: relative;
            flex: 1;
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
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .search-input:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
            background: #fff;
        }

        .btn-search {
            padding: 9px 16px;
            background: var(--color-dark);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background var(--transition);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-search:hover {
            background: var(--color-primary);
        }

        .btn-reset {
            padding: 9px 12px;
            background: transparent;
            color: var(--text-muted);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: border-color var(--transition), color var(--transition);
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .btn-reset:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* Filtre actif */
        .search-active-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 20px;
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
            padding: 10px 16px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
        }

        .data-table th:hover {
            color: var(--text-primary);
        }

        .data-table th .th-inner {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .data-table th .sort-icon {
            font-size: 9px;
            color: var(--border);
        }

        .data-table th.sorted .sort-icon {
            color: var(--color-primary);
        }

        .data-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr {
            transition: background var(--transition);
        }

        .data-table tbody tr:hover td {
            background: #fafafa;
        }

        /* Colonne ID */
        .td-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
        }

        /* Nom avec icône */
        .ville-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ville-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--color-primary);
            flex-shrink: 0;
        }

        .ville-name {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Dates */
        .td-date {
            font-size: 12px;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .td-date-sub {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ── PAGINATION ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 12px;
        }

        .pagination-info {
            font-size: 12px;
            color: var(--text-muted);
        }

        .pagination-info strong {
            color: var(--text-primary);
        }

        .pagination-links {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }

        .page-btn {
            min-width: 32px;
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
            padding: 0 8px;
            text-decoration: none;
            transition: border-color var(--transition), background var(--transition), color var(--transition);
            white-space: nowrap;
        }

        .page-btn:hover:not(.disabled) {
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
            cursor: default;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 52px 24px;
        }

        .empty-state-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: var(--border);
            margin: 0 auto 14px;
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* ── FLASH ── */
        .flash {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 13px;
        }

        .flash-success {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            border-left: 3px solid #10b981;
            color: #059669;
        }

        .flash-error {
            background: rgba(224, 32, 32, .06);
            border: 1px solid rgba(224, 32, 32, .2);
            border-left: 3px solid var(--color-primary);
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
        <span>Villes</span>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="flash flash-success">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- ── KPI ── --}}
    <div class="kpi-grid" style="grid-template-columns: repeat(2, 1fr)">

        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total villes</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['ce_mois'] }}</div>
            <div class="kpi-label">Ajoutées ce mois</div>
        </div>

    </div>

    {{-- ── TABLEAU ── --}}
    <div class="section-card">

        {{-- Header --}}
        <div class="section-header"
            style="flex-wrap:wrap;gap:12px;margin-bottom:0;padding-bottom:16px;border-bottom:1px solid var(--border)">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
                <h2 class="section-title">Liste des villes</h2>
                @if (request('search'))
                    <span class="search-active-badge">
                        <i class="fa-solid fa-filter"></i>
                        Filtre : "{{ request('search') }}"
                    </span>
                @endif
            </div>

            <div class="toolbar">
                {{-- Formulaire de recherche (GET → conserve la pagination) --}}
                <form method="GET" action="{{ route('villes.index') }}" class="search-form">
                    <div class="search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="search-input" placeholder="Rechercher par nom…"
                            value="{{ request('search') }}" autocomplete="off" autofocus>
                    </div>
                    <button type="submit" class="btn-search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Rechercher
                    </button>
                    @if (request('search'))
                        <a href="{{ route('villes.index') }}" class="btn-reset">
                            <i class="fa-solid fa-xmark"></i>
                            Effacer
                        </a>
                    @endif
                </form>

                <a href="{{ route('villes.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="table-wrap" style="margin-top:0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">
                            <div class="th-inner">
                                #<i class="fa-solid fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                Nom de la ville
                                <i class="fa-solid fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                Créée le
                                <i class="fa-solid fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th>
                            <div class="th-inner">
                                Modifiée le
                                <i class="fa-solid fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th style="width:110px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($villes as $ville)
                        <tr>
                            {{-- ID --}}
                            <td class="td-id">#{{ $ville->id }}</td>

                            {{-- Nom --}}
                            <td>
                                <div class="ville-cell">
                                    <div class="ville-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <span class="ville-name">
                                        {{-- Highlight du terme recherché --}}
                                        @if (request('search'))
                                            {!! preg_replace(
                                                '/(' . preg_quote(request('search'), '/') . ')/i',
                                                '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                e($ville->nom),
                                            ) !!}
                                        @else
                                            {{ $ville->nom }}
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date">{{ $ville->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $ville->created_at->format('H:i') }}</div>
                            </td>

                            {{-- updated_at --}}
                            <td>
                                <div class="td-date">{{ $ville->updated_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $ville->updated_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('villes.edit', $ville) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('villes.destroy', $ville) }}"
                                        id="delete-form-{{ $ville->id }}" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteVille({{ $ville->id }}, '{{ $ville->nom }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </div>
                                    <h3>
                                        @if (request('search'))
                                            Aucune ville trouvée pour "{{ request('search') }}"
                                        @else
                                            Aucune ville enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if (request('search'))
                                            Essayez avec un autre terme de recherche.
                                        @else
                                            Commencez par ajouter votre première ville.
                                        @endif
                                    </p>
                                    @if (request('search'))
                                        <a href="{{ route('villes.index') }}" class="btn btn-primary"
                                            style="display:inline-flex">
                                            <i class="fa-solid fa-xmark"></i> Effacer le filtre
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── PAGINATION ── --}}
        @if ($villes->hasPages())
            <div class="pagination-wrap">
                <span class="pagination-info">
                    Affichage de <strong>{{ $villes->firstItem() }}</strong>
                    à <strong>{{ $villes->lastItem() }}</strong>
                    sur <strong>{{ $villes->total() }}</strong> ville(s)
                    @if (request('search'))
                        · filtre : "{{ request('search') }}"
                    @endif
                </span>

                <div class="pagination-links">

                    {{-- Première page --}}
                    @if ($villes->currentPage() > 1)
                        <a class="page-btn"
                            href="{{ $villes->url(1) . (request('search') ? '&search=' . urlencode(request('search')) : '') }}"
                            title="Première page">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    @endif

                    {{-- Précédent --}}
                    <a class="page-btn {{ $villes->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $villes->previousPageUrl() ? $villes->previousPageUrl() . (request('search') ? '&search=' . urlencode(request('search')) : '') : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    {{-- Pages numérotées --}}
                    @php
                        $current = $villes->currentPage();
                        $last = $villes->lastPage();
                        $window = 2; // pages de chaque côté
                        $from = max(1, $current - $window);
                        $to = min($last, $current + $window);
                        $search = request('search') ? '&search=' . urlencode(request('search')) : '';
                    @endphp

                    @if ($from > 1)
                        <a class="page-btn" href="{{ $villes->url(1) . $search }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif

                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $villes->url($p) . $search }}">{{ $p }}</a>
                    @endfor

                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $villes->url($last) . $search }}">{{ $last }}</a>
                    @endif

                    {{-- Suivant --}}
                    <a class="page-btn {{ !$villes->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $villes->nextPageUrl() ? $villes->nextPageUrl() . $search : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    {{-- Dernière page --}}
                    @if ($villes->currentPage() < $last)
                        <a class="page-btn" href="{{ $villes->url($last) . $search }}" title="Dernière page">
                            <i class="fa-solid fa-angles-right"></i>
                        </a>
                    @endif

                </div>
            </div>
        @endif

    </div>{{-- /section-card --}}

@endsection

@push('scripts')
    <script>
        function handleDeleteVille(id, name) {
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
