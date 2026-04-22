{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES TARIFS CLIENTS — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $tarifs        : LengthAwarePaginator
|   - $stats         : ['total', 'ce_mois', 'prix_moyen']
|   - $clients       : Collection
|   - $trajets       : Collection (avec villeDepart, villeDestination)
|   - $typesVehicule : Collection (valeurs distinctes)
|
| QUERY PARAMS : ?client_id= ?trajet_id= ?type_vehicule= ?prix_min= ?prix_max= ?page=
| ROUTE : GET /tarif-clients → TarifClientController@index
| --}}

@extends('layouts.app')

@section('title', 'Tarifs Clients')
@section('page-title', 'Tarifs Clients')
@section('page-subtitle', 'Grille tarifaire par client et par trajet')

@section('content')

    <style>
        /* ── BREADCRUMB ── */
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
            grid-template-columns: 1.5fr 1.5fr 1fr 0.7fr 0.7fr auto;
            gap: 10px;
            align-items: end
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
        .filter-select,
        .filter-number {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition);
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
        .filter-select:focus,
        .filter-number:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .price-range-wrap {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .price-range-sep {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0
        }

        .price-range-wrap .filter-number {
            flex: 1;
            min-width: 0
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
            gap: 5px;
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

        /* ── TOOLBAR ── */
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

        /* ── TABLE SCROLL ── */
        .table-scroll-wrap {
            overflow-x: auto;
            max-height: 530px;
            overflow-y: auto
        }

        .table-scroll-wrap::-webkit-scrollbar {
            width: 5px;
            height: 5px
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

        .data-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            text-align: left;
            padding: 10px 16px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            white-space: nowrap
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

        /* Client cell */
        .client-cell {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .client-avatar {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--color-primary);
            flex-shrink: 0
        }

        .client-name {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3
        }

        .client-type {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-top: 1px
        }

        /* Trajet cell */
        .trajet-cell {
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .trajet-route {
            font-weight: 600;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px
        }

        .trajet-arrow {
            color: var(--text-muted);
            font-size: 10px
        }

        .trajet-meta {
            font-size: 10px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace
        }

        /* Badges */
        .badge-vehicule {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text-secondary)
        }

        .badge-tonnage {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 8px;
            background: rgba(59, 130, 246, .08);
            border: 1px solid rgba(59, 130, 246, .2);
            border-radius: 5px;
            color: #2563eb;
            font-family: 'JetBrains Mono', monospace
        }

        .badge-prix {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
            font-weight: 700;
            padding: 4px 10px;
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            border-radius: 6px;
            color: #059669;
            font-family: 'JetBrains Mono', monospace
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

        /* Flash */
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

        @media(max-width:1300px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1/-1
            }
        }

        @media(max-width:640px) {
            .filters-grid {
                grid-template-columns: 1fr
            }

            .price-range-wrap {
                flex-direction: column
            }

            .price-range-sep {
                display: none
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Tarifs Clients</span>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    {{-- ── KPI ── --}}
    <div class="kpi-grid" style="grid-template-columns:repeat(3,1fr)">
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total tarifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['ce_mois'] }}</div>
            <div class="kpi-label">Ajoutés ce mois</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['prix_moyen'], 2) }} MAD</div>
            <div class="kpi-label">Prix moyen</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'client_id' => request('client_id'),
            'trajet_id' => request('trajet_id'),
            'type_vehicule' => request('type_vehicule'),
            'prix_min' => request('prix_min'),
            'prix_max' => request('prix_max'),
        ])
            ->filter()
            ->count();
    @endphp

    <div class="filters-card">
        <div class="filters-header">
            <span class="filters-title">
                <i class="fa-solid fa-filter"></i>
                Filtres
                @if ($activeFilters > 0)
                    <span class="filters-active-count">{{ $activeFilters }}</span>
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('tarif_clients.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Client --}}
                <div class="filter-field">
                    <label>Client</label>
                    <select name="client_id" class="filter-select">
                        <option value="">Tous les clients</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Trajet --}}
                <div class="filter-field">
                    <label>Trajet</label>
                    <select name="trajet_id" class="filter-select">
                        <option value="">Tous les trajets</option>
                        @foreach ($trajets as $trajet)
                            <option value="{{ $trajet->id }}"
                                {{ request('trajet_id') == $trajet->id ? 'selected' : '' }}>
                                {{ $trajet->villeDepart?->nom }} → {{ $trajet->villeDestination?->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type véhicule --}}
                <div class="filter-field">
                    <label>Type véhicule</label>
                    <select name="type_vehicule" class="filter-select">
                        <option value="">Tous types</option>
                        @foreach ($typesVehicule as $tv)
                            <option value="{{ $tv }}" {{ request('type_vehicule') == $tv ? 'selected' : '' }}>
                                {{ $tv }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Prix (min → max) --}}
                <div class="filter-field" style="grid-column:span 2">
                    <label>Fourchette de prix (MAD)</label>
                    <div class="price-range-wrap">
                        <input type="number" name="prix_min" class="filter-number" placeholder="Min"
                            value="{{ request('prix_min') }}" min="0" step="0.01">
                        <span class="price-range-sep">→</span>
                        <input type="number" name="prix_max" class="filter-number" placeholder="Max"
                            value="{{ request('prix_max') }}" min="0" step="0.01">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('tarif_clients.index') }}" class="btn-reset">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Badges filtres actifs --}}
            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('client_id'))
                        @php $cl = $clients->firstWhere('id', request('client_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-building" style="font-size:9px"></i>
                            {{ $cl?->nom ?? '—' }}
                            <a href="{{ request()->fullUrlWithoutQuery(['client_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('trajet_id'))
                        @php $tr = $trajets->firstWhere('id', request('trajet_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-route" style="font-size:9px"></i>
                            {{ $tr?->villeDepart?->nom }} → {{ $tr?->villeDestination?->nom }}
                            <a href="{{ request()->fullUrlWithoutQuery(['trajet_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('type_vehicule'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-truck" style="font-size:9px"></i>
                            {{ request('type_vehicule') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['type_vehicule', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('prix_min') || request('prix_max'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-coins" style="font-size:9px"></i>
                            @if (request('prix_min') && request('prix_max'))
                                {{ number_format(request('prix_min'), 2) }} – {{ number_format(request('prix_max'), 2) }}
                                MAD
                            @elseif (request('prix_min'))
                                ≥ {{ number_format(request('prix_min'), 2) }} MAD
                            @else
                                ≤ {{ number_format(request('prix_max'), 2) }} MAD
                            @endif
                            <a href="{{ request()->fullUrlWithoutQuery(['prix_min', 'prix_max', 'page']) }}"><i
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
                <h2 class="section-title">Grille tarifaire</h2>
                <span class="result-count"><strong>{{ $tarifs->total() }}</strong> résultat(s)</span>
            </div>
            <a href="{{ route('tarif_clients.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter un tarif
            </a>
        </div>

        {{-- Scroll vertical + horizontal, headers sticky --}}
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="min-width:200px">Client</th>
                        <th style="min-width:200px">Trajet</th>
                        <th style="width:150px">Type véhicule</th>
                        <th style="width:110px">Tonnage</th>
                        <th style="width:140px">Prix vente</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tarifs as $tarif)
                        <tr>
                            <td class="td-id">#{{ $tarif->id }}</td>

                            {{-- Client --}}
                            <td>
                                <div class="client-cell">
                                    <div class="client-avatar">
                                        <i
                                            class="fa-solid fa-{{ $tarif->client->type === 'entreprise' ? 'building' : 'user' }}"></i>
                                    </div>
                                    <div>
                                        <div class="client-name">{{ $tarif->client->nom }}</div>
                                        <div class="client-type">{{ $tarif->client->type }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Trajet --}}
                            <td>
                                <div class="trajet-cell">
                                    <div class="trajet-route">
                                        <span>{{ $tarif->trajet->villeDepart?->nom ?? '?' }}</span>
                                        <span class="trajet-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                                        <span>{{ $tarif->trajet->villeDestination?->nom ?? '?' }}</span>
                                    </div>
                                    <div class="trajet-meta">
                                        {{ number_format($tarif->trajet->distance_km, 1) }} km
                                        · {{ $tarif->trajet->duree_minutes }} min
                                    </div>
                                </div>
                            </td>

                            {{-- Type véhicule --}}
                            <td>
                                <span class="badge-vehicule">
                                    <i class="fa-solid fa-truck" style="font-size:9px"></i>
                                    {{ $tarif->type_vehicule }}
                                </span>
                            </td>

                            {{-- Tonnage --}}
                            <td>
                                <span class="badge-tonnage">
                                    {{ number_format($tarif->tonnage, 2) }} T
                                </span>
                            </td>

                            {{-- Prix vente --}}
                            <td>
                                <span class="badge-prix">
                                    {{ number_format($tarif->prix_vente, 2) }}
                                    <span style="font-size:10px;opacity:.7">MAD</span>
                                </span>
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date">{{ $tarif->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $tarif->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('tarif_clients.edit', $tarif) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('tarif_clients.destroy', $tarif) }}"
                                        id="delete-form-{{ $tarif->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteTarif({{ $tarif->id }}, '{{ addslashes($tarif->client->nom) }}', '{{ addslashes($tarif->trajet->villeDepart?->nom ?? '') }} → {{ addslashes($tarif->trajet->villeDestination?->nom ?? '') }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucun tarif ne correspond aux filtres
                                        @else
                                            Aucun tarif enregistré
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Modifiez ou effacez vos critères de recherche.
                                        @else
                                            Commencez par ajouter votre première grille tarifaire.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('tarif_clients.index') }}" class="btn btn-primary"
                                            style="display:inline-flex">
                                            <i class="fa-solid fa-xmark"></i> Effacer les filtres
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── PAGINATION (en dehors du scroll) ── --}}
        @if ($tarifs->hasPages())
            @php
                $current = $tarifs->currentPage();
                $last = $tarifs->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $tarifs->firstItem() }}</strong>–<strong>{{ $tarifs->lastItem() }}</strong>
                    sur <strong>{{ $tarifs->total() }}</strong> tarif(s)
                </span>
                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $tarifs->url(1) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-left"></i></a>
                    @endif
                    <a class="page-btn {{ $tarifs->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $tarifs->previousPageUrl() ? $tarifs->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    @if ($from > 1)
                        <a class="page-btn" href="{{ $tarifs->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif
                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $tarifs->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor
                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $tarifs->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif
                    <a class="page-btn {{ !$tarifs->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $tarifs->nextPageUrl() ? $tarifs->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $tarifs->url($last) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-right"></i></a>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function handleDeleteTarif(id, client, trajet) {
            Swal.fire({
                title: 'Supprimer ce tarif ?',
                text: `Tarif de ${client} pour le trajet ${trajet} — action irréversible.`,
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
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
