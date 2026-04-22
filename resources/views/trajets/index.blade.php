{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES TRAJETS — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $trajets : LengthAwarePaginator (avec villeDepart, villeDestination)
|   - $stats   : ['total','actifs','inactifs','dist_moy']
|   - $villes  : Collection<Ville>
|
| QUERY PARAMS :
|   ?search=               ville / adresse
|   ?ville_depart_id=      filtrer par ville départ
|   ?ville_destination_id= filtrer par ville destination
|   ?statut=               actif | inactif
|   ?distance_min=         distance min
|   ?distance_max=         distance max
|   ?page=                 pagination
|
| ROUTE : GET /trajets → TrajetController@index
--}}

@extends('layouts.app')

@section('title', 'Trajets')
@section('page-title', 'Trajets')
@section('page-subtitle', 'Gestion des trajets et liaisons entre villes')

@section('content')

    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px
        }

        .breadcrumb a {
            color: var(--text-muted);
            transition: color var(--transition)
        }

        .breadcrumb a:hover {
            color: var(--color-primary)
        }

        .breadcrumb-sep {
            font-size: 10px
        }

        /* ── FILTRES ── */
        .filters-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm)
        }

        .filters-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px
        }

        .filters-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px
        }

        .filters-active-count {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            background: var(--color-primary);
            color: #fff;
            border-radius: 20px
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end
        }

        .filters-grid-row2 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
            margin-top: 10px
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 5px
        }

        .filter-field label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted)
        }

        .filter-input,
        .filter-select {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
            width: 100%
        }

        .filter-input.has-icon {
            padding-left: 32px
        }

        .filter-input-wrap {
            position: relative
        }

        .filter-input-wrap i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--text-muted);
            pointer-events: none
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .filter-select {
            cursor: pointer
        }

        .dist-range {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .dist-range .filter-input {
            flex: 1;
            min-width: 0
        }

        .dist-sep {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0
        }

        .filter-actions {
            display: flex;
            gap: 6px;
            align-items: flex-end
        }

        .btn-filter {
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
            white-space: nowrap
        }

        .btn-filter:hover {
            background: var(--color-primary)
        }

        .btn-reset {
            padding: 9px 11px;
            background: transparent;
            color: var(--text-muted);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition)
        }

        .btn-reset:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border)
        }

        .active-filter-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 20px
        }

        .active-filter-tag a {
            color: var(--color-primary);
            opacity: .7;
            font-size: 10px;
            transition: opacity .15s
        }

        .active-filter-tag a:hover {
            opacity: 1
        }

        /* ── TABLE ── */
        .toolbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border)
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap
        }

        .result-count {
            font-size: 13px;
            color: var(--text-muted)
        }

        .result-count strong {
            color: var(--text-primary)
        }

        /* Scroll vertical + horizontal sous le tableau */
        .table-scroll-wrap {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 520px
        }

        .table-scroll-wrap::-webkit-scrollbar {
            height: 6px;
            width: 6px
        }

        .table-scroll-wrap::-webkit-scrollbar-track {
            background: var(--bg-body)
        }

        .table-scroll-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px
        }

        .table-scroll-wrap::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted)
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px
        }

        .data-table th {
            text-align: left;
            padding: 10px 16px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 2
        }

        .data-table td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            vertical-align: middle
        }

        .data-table tr:last-child td {
            border-bottom: none
        }

        .data-table tbody tr {
            transition: background var(--transition)
        }

        .data-table tbody tr:hover td {
            background: #fafafa
        }

        .td-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted)
        }

        .td-date {
            font-size: 12px;
            color: var(--text-secondary);
            white-space: nowrap
        }

        .td-date-sub {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px
        }

        /* Trajet arrow cell */
        .trajet-cell {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 220px
        }

        .ville-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 6px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            white-space: nowrap
        }

        .ville-pill.depart {
            background: rgba(224, 32, 32, .05);
            border-color: rgba(224, 32, 32, .2);
            color: var(--color-primary)
        }

        .ville-pill.destination {
            background: rgba(59, 130, 246, .05);
            border-color: rgba(59, 130, 246, .2);
            color: #3b82f6
        }

        .trajet-arrow {
            color: var(--text-muted);
            font-size: 12px;
            flex-shrink: 0
        }

        .adresse-cell {
            font-size: 11px;
            color: var(--text-muted);
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        /* Badges numériques */
        .num-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 5px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            color: var(--text-secondary)
        }

        .num-badge .unit {
            font-size: 10px;
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-muted);
            margin-left: 2px
        }

        .num-badge.dist {
            border-color: rgba(245, 158, 11, .25);
            color: #d97706;
            background: rgba(245, 158, 11, .05)
        }

        .num-badge.prix {
            border-color: rgba(16, 185, 129, .25);
            color: #059669;
            background: rgba(16, 185, 129, .05)
        }

        .num-badge.duree {
            border-color: rgba(139, 92, 246, .25);
            color: #7c3aed;
            background: rgba(139, 92, 246, .05)
        }

        .badge-statut {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: .3px
        }

        .badge-statut.actif {
            background: rgba(16, 185, 129, .08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2)
        }

        .badge-statut.inactif {
            background: rgba(107, 114, 128, .08);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .2)
        }

        .actions-cell {
            display: flex;
            gap: 4px;
            align-items: center
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--text-muted);
            cursor: pointer;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition), background var(--transition)
        }

        .btn-icon:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim)
        }

        .btn-icon--danger:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: rgba(239, 68, 68, .06)
        }

        /* ── PAGINATION ── */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 18px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 12px
        }

        .pagination-info {
            font-size: 12px;
            color: var(--text-muted)
        }

        .pagination-info strong {
            color: var(--text-primary)
        }

        .pagination-links {
            display: flex;
            gap: 4px;
            flex-wrap: wrap
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
            transition: border-color var(--transition), background var(--transition), color var(--transition)
        }

        .page-btn:hover:not(.disabled) {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .page-btn.active {
            background: var(--color-dark);
            color: #fff;
            border-color: var(--color-dark)
        }

        .page-btn.disabled {
            opacity: .35;
            pointer-events: none
        }

        /* ── EMPTY ── */
        .empty-state {
            text-align: center;
            padding: 52px 24px
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
            margin: 0 auto 14px
        }

        .empty-state h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px
        }

        /* ── FLASH ── */
        .flash {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 13px
        }

        .flash-success {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            border-left: 3px solid #10b981;
            color: #059669
        }

        .flash-error {
            background: rgba(224, 32, 32, .06);
            border: 1px solid rgba(224, 32, 32, .2);
            border-left: 3px solid var(--color-primary);
            color: var(--color-primary)
        }

        @media(max-width:1200px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr
            }

            .filter-actions {
                grid-column: 1/-1
            }

            .filters-grid-row2 {
                grid-template-columns: 1fr 1fr
            }
        }

        @media(max-width:640px) {

            .filters-grid,
            .filters-grid-row2 {
                grid-template-columns: 1fr
            }
        }
    </style>

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Trajets</span>
    </div>

    @if (session('success'))
        <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    {{-- ── KPI ── --}}
    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total trajets</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#059669">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Actifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#6b7280">{{ $stats['inactifs'] }}</div>
            <div class="kpi-label">Inactifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['dist_moy'] }}<span
                    style="font-size:14px;font-weight:400;color:var(--text-muted)"> km</span></div>
            <div class="kpi-label">Distance moyenne</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'ville_depart_id' => request('ville_depart_id'),
            'ville_destination_id' => request('ville_destination_id'),
            'statut' => request('statut'),
            'distance_min' => request('distance_min'),
            'distance_max' => request('distance_max'),
        ])
            ->filter(fn($v) => $v !== null && $v !== '')
            ->count();
    @endphp

    <div class="filters-card">
        <div class="filters-header">
            <span class="filters-title">
                <i class="fa-solid fa-filter"></i> Filtres
                @if ($activeFilters > 0)
                    <span class="filters-active-count">{{ $activeFilters }}</span>
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('trajets.index') }}">
            <div class="filters-grid">
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon" placeholder="Ville, adresse…"
                            value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                <div class="filter-field">
                    <label>Ville départ</label>
                    <select name="ville_depart_id" class="filter-select">
                        <option value="">Toutes</option>
                        @foreach ($villes as $v)
                            <option value="{{ $v->id }}"
                                {{ request('ville_depart_id') == $v->id ? 'selected' : '' }}>{{ $v->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label>Ville destination</label>
                    <select name="ville_destination_id" class="filter-select">
                        <option value="">Toutes</option>
                        @foreach ($villes as $v)
                            <option value="{{ $v->id }}"
                                {{ request('ville_destination_id') == $v->id ? 'selected' : '' }}>{{ $v->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filtrer</button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('trajets.index') }}" class="btn-reset"><i class="fa-solid fa-xmark"></i></a>
                    @endif
                </div>
            </div>

            {{-- Ligne 2 : plage distance --}}
            <div class="filters-grid-row2">
                <div class="filter-field">
                    <label>Distance (km)</label>
                    <div class="dist-range">
                        <input type="number" name="distance_min" class="filter-input" placeholder="Min" min="0"
                            value="{{ request('distance_min') }}" step="0.01">
                        <span class="dist-sep">–</span>
                        <input type="number" name="distance_max" class="filter-input" placeholder="Max" min="0"
                            value="{{ request('distance_max') }}" step="0.01">
                    </div>
                </div>
            </div>

            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('search'))
                        <span class="active-filter-tag"><i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                            "{{ request('search') }}" <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('ville_depart_id'))
                        @php $vd = $villes->firstWhere('id', request('ville_depart_id')); @endphp
                        <span class="active-filter-tag"><i class="fa-solid fa-location-dot" style="font-size:9px"></i>
                            Départ : {{ $vd?->nom ?? '—' }} <a
                                href="{{ request()->fullUrlWithoutQuery(['ville_depart_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('ville_destination_id'))
                        @php $vdest = $villes->firstWhere('id', request('ville_destination_id')); @endphp
                        <span class="active-filter-tag"><i class="fa-solid fa-flag-checkered" style="font-size:9px"></i>
                            Destination : {{ $vdest?->nom ?? '—' }} <a
                                href="{{ request()->fullUrlWithoutQuery(['ville_destination_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('statut'))
                        <span class="active-filter-tag"><i class="fa-solid fa-circle" style="font-size:9px"></i>
                            {{ ucfirst(request('statut')) }} <a
                                href="{{ request()->fullUrlWithoutQuery(['statut', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('distance_min') || request('distance_max'))
                        <span class="active-filter-tag"><i class="fa-solid fa-route" style="font-size:9px"></i>
                            Distance :
                            @if (request('distance_min') && request('distance_max'))
                                {{ request('distance_min') }} – {{ request('distance_max') }} km
                            @elseif(request('distance_min'))
                                ≥ {{ request('distance_min') }} km
                            @else
                                ≤ {{ request('distance_max') }} km
                            @endif
                            <a href="{{ request()->fullUrlWithoutQuery(['distance_min', 'distance_max', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- ── TABLEAU ── --}}
    <div class="section-card">
        <div class="toolbar-row">
            <div class="toolbar-left">
                <h2 class="section-title">Liste des trajets</h2>
                <span class="result-count"><strong>{{ $trajets->total() }}</strong> résultat(s)</span>
            </div>
            <a href="{{ route('trajets.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Nouveau
                trajet</a>
        </div>

        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="min-width:260px">Trajet</th>
                        <th style="min-width:160px">Adresse départ</th>
                        <th style="min-width:160px">Adresse destination</th>
                        <th style="width:110px">Distance</th>
                        <th style="width:120px">Prix autoroute</th>
                        <th style="width:110px">Durée</th>
                        <th style="width:100px">Statut</th>
                        <th style="width:120px">Créé le</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trajets as $t)
                        <tr>
                            <td class="td-id">#{{ $t->id }}</td>
                            <td>
                                <div class="trajet-cell">
                                    <span class="ville-pill depart">
                                        <i class="fa-solid fa-location-dot" style="font-size:10px"></i>
                                        {{ $t->villeDepart->nom }}
                                    </span>
                                    <span class="trajet-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                                    <span class="ville-pill destination">
                                        <i class="fa-solid fa-flag-checkered" style="font-size:10px"></i>
                                        {{ $t->villeDestination->nom }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if ($t->adresse_depart)
                                    <span class="adresse-cell"
                                        title="{{ $t->adresse_depart }}">{{ $t->adresse_depart }}</span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($t->adresse_destination)
                                    <span class="adresse-cell"
                                        title="{{ $t->adresse_destination }}">{{ $t->adresse_destination }}</span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($t->distance_km !== null)
                                    <span class="num-badge dist">{{ number_format($t->distance_km, 1, ',', ' ') }}<span
                                            class="unit">km</span></span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($t->prix_autoroute !== null)
                                    <span class="num-badge prix">{{ number_format($t->prix_autoroute, 2, ',', ' ') }}<span
                                            class="unit">MAD</span></span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($t->duree_minutes !== null)
                                    @php
                                        $h = intdiv($t->duree_minutes, 60);
                                        $m = $t->duree_minutes % 60;
                                    @endphp
                                    <span class="num-badge duree">
                                        @if ($h > 0)
                                            {{ $h }}h
                                        @endif{{ $m }}min
                                    </span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-statut {{ $t->statut }}">
                                    <i class="fa-solid {{ $t->statut === 'actif' ? 'fa-circle-check' : 'fa-circle-xmark' }}"
                                        style="font-size:9px"></i>
                                    {{ ucfirst($t->statut) }}
                                </span>
                            </td>
                            <td>
                                <div class="td-date">{{ $t->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $t->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('trajets.edit', $t) }}" class="btn-icon" title="Modifier"><i
                                            class="fa-solid fa-pen"></i></a>
                                    <form method="POST" action="{{ route('trajets.destroy', $t) }}"
                                        id="delete-form-{{ $t->id }}" style="display:none">@csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDelete({{ $t->id }},'{{ addslashes($t->villeDepart->nom) }} → {{ addslashes($t->villeDestination->nom) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-route"></i></div>
                                    <h3>{{ $activeFilters > 0 ? 'Aucun trajet ne correspond aux filtres' : 'Aucun trajet enregistré' }}
                                    </h3>
                                    <p>{{ $activeFilters > 0 ? "Essayez de modifier ou d'effacer vos critères." : 'Commencez par ajouter votre premier trajet.' }}
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('trajets.index') }}" class="btn btn-primary"
                                            style="display:inline-flex"><i class="fa-solid fa-xmark"></i> Effacer les
                                            filtres</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── PAGINATION ── --}}
        @if ($trajets->hasPages())
            @php
                $current = $trajets->currentPage();
                $last = $trajets->lastPage();
                $w = 2;
                $from = max(1, $current - $w);
                $to = min($last, $current + $w);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $trajets->firstItem() }}</strong>–<strong>{{ $trajets->lastItem() }}</strong>
                    sur <strong>{{ $trajets->total() }}</strong> trajet(s)
                </span>
                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $trajets->url(1) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-left"></i></a>
                    @endif
                    <a class="page-btn {{ $trajets->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $trajets->previousPageUrl() ? $trajets->previousPageUrl() . $sep . $qs : '#' }}"><i
                            class="fa-solid fa-chevron-left"></i></a>
                    @if ($from > 1)<a class="page-btn"
                            href="{{ $trajets->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif
                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $trajets->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor
                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $trajets->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif
                    <a class="page-btn {{ !$trajets->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $trajets->nextPageUrl() ? $trajets->nextPageUrl() . $sep . $qs : '#' }}"><i
                            class="fa-solid fa-chevron-right"></i></a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $trajets->url($last) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-right"></i></a>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function handleDelete(id, label) {
            Swal.fire({
                title: 'Supprimer le trajet ?',
                text: `Êtes-vous sûr de vouloir supprimer le trajet "${label}" ? Cette action est irréversible.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e02020',
                cancelButtonColor: '#1a1a1a',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                background: '#111',
                color: '#fff',
                customClass: {
                    popup: 'swal-custom-radius'
                }
            }).then(r => {
                if (r.isConfirmed) document.getElementById('delete-form-' + id).submit();
            });
        }
    </script>
@endpush
