{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES VÉHICULES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $vehicules : LengthAwarePaginator
|   - $stats     : ['total' => int, 'achats' => int, 'locations' => int, 'actifs' => int]
|   - $chauffeurs: Collection (pour filtre)
|
| QUERY PARAMS :
|   ?search=       matricule / marque / num_chassis
|   ?type=         type_vehicule
|   ?acquisition=  achat|location
|   ?statut=       statut
|   ?chauffeur_id= id chauffeur
|   ?page=         pagination
|
| ROUTE : GET /vehicules → VehiculeController@index
| --}}

@extends('layouts.app')

@section('title', 'Véhicules')
@section('page-title', 'Gestion des Véhicules')
@section('page-subtitle', 'Parc automobile · immatriculations, types et affectations')

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

        /* ── FILTRES ── */
        .filters-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
        }

        .filters-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .filters-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filters-active-count {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            background: var(--color-primary);
            color: #fff;
            border-radius: 20px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .filter-field label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
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
            transition: border-color var(--transition), box-shadow var(--transition);
            width: 100%;
        }

        .filter-input.has-icon {
            padding-left: 32px;
        }

        .filter-input-wrap {
            position: relative;
        }

        .filter-input-wrap i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .filter-actions {
            display: flex;
            gap: 6px;
            align-items: flex-end;
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
            white-space: nowrap;
        }

        .btn-filter:hover {
            background: var(--color-primary);
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
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-reset:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
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
            border-radius: 20px;
        }

        .active-filter-tag a {
            color: var(--color-primary);
            opacity: .7;
            font-size: 10px;
            transition: opacity .15s;
        }

        .active-filter-tag a:hover {
            opacity: 1;
        }

        /* ── TABLE ── */
        .toolbar-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .result-count {
            font-size: 13px;
            color: var(--text-muted);
        }

        .result-count strong {
            color: var(--text-primary);
        }

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

        .td-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .vehicule-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vehicule-icon {
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
            flex-shrink: 0;
        }

        .vehicule-matricule {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .vehicule-marque {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 5px;
            white-space: nowrap;
        }

        .badge-achat {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .badge-location {
            background: rgba(99, 102, 241, .1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, .2);
        }

        .badge-actif {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .badge-inactif {
            background: rgba(107, 114, 128, .1);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .2);
        }

        .badge-maintenance {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .2);
        }

        .td-date {
            font-size: 12px;
            color: var(--text-secondary);
            white-space: nowrap;
        }

        .td-date-sub {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .td-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* Chauffeur cell */
        .chauffeur-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chauffeur-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--color-dark);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .chauffeur-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Boutons d'action */
        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            background: transparent;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
            text-decoration: none;
        }

        .btn-icon:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .btn-icon--danger:hover {
            border-color: var(--color-primary);
            background: rgba(224, 32, 32, .08);
            color: var(--color-primary);
        }

        .btn-icon--warning:hover {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
            border-color: #d97706;
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
        }

        /* ── EMPTY ── */
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

        @media (max-width: 1200px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1/-1;
                justify-content: flex-start;
            }
        }

        @media (max-width: 640px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Véhicules</span>
    </div>

    {{-- Flash --}}
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
            <div class="kpi-label">Total véhicules</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Actifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['achats'] }}</div>
            <div class="kpi-label">Achat</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['locations'] }}</div>
            <div class="kpi-label">Location</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'type' => request('type'),
            'acquisition' => request('acquisition'),
            'statut' => request('statut'),
            'chauffeur_id' => request('chauffeur_id'),
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

        <form method="GET" action="{{ route('vehicules.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Recherche --}}
                <div class="filter-field">
                    <label>Matricule / Marque / Châssis</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon" placeholder="Rechercher…"
                            value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                {{-- Type véhicule --}}
                <div class="filter-field">
                    <label>Type</label>
                    {{-- <input type="text" name="type" class="filter-input" placeholder="Camion, Remorque…"
                         autocomplete="off"> --}}
                    <select id="type_vehicule" name="type" class="filter-select">
                        <option value="">Tous</option>
                        <option value="tracteur" {{ request('type') === 'tracteur' ? 'selected' : '' }}>Tracteur
                        </option>
                        <option value="semi-remorque" {{ request('type') === 'semi-remorque' ? 'selected' : '' }}>
                            Semi-remorque
                        </option>
                        <option value="camion" {{ request('type') === 'camion' ? 'selected' : '' }}>
                            Camion</option>
                        <option value="fourgon" {{ request('type') === 'fourgon' ? 'selected' : '' }}>
                            Fourgon</option>
                        <option value="benne" {{ request('type') === 'benne' ? 'selected' : '' }}>
                            Benne</option>
                        <option value="citerne" {{ request('type') === 'citerne' ? 'selected' : '' }}>
                            Citerne</option>
                        <option value="frigo" {{ request('type') === 'frigo' ? 'selected' : '' }}>
                            Frigo</option>
                        <option value="plateau" {{ request('type') === 'plateau' ? 'selected' : '' }}>
                            Plateau</option>
                    </select>
                </div>

                {{-- Acquisition --}}
                <div class="filter-field">
                    <label>Acquisition</label>
                    <select name="acquisition" class="filter-select">
                        <option value="">Tous</option>
                        <option value="achat" {{ request('acquisition') === 'achat' ? 'selected' : '' }}>Achat</option>
                        <option value="location" {{ request('acquisition') === 'location' ? 'selected' : '' }}>Location
                        </option>
                    </select>
                </div>

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous</option>
                        <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif
                        </option>
                        <option value="maintenance" {{ request('statut') === 'maintenance' ? 'selected' : '' }}>Maintenance
                        </option>
                    </select>
                </div>

                {{-- Chauffeur --}}
                <div class="filter-field">
                    <label>Chauffeur</label>
                    <select name="chauffeur_id" class="filter-select">
                        <option value="">Tous les chauffeurs</option>
                        @foreach ($chauffeurs as $c)
                            <option value="{{ $c->id }}" {{ request('chauffeur_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->prenom }} {{ $c->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('vehicules.index') }}" class="btn-reset">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Badges filtres actifs --}}
            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('search'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                            "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('acquisition'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-tag" style="font-size:9px"></i>
                            {{ ucfirst(request('acquisition')) }}
                            <a href="{{ request()->fullUrlWithoutQuery(['acquisition', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('statut'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-circle-dot" style="font-size:9px"></i>
                            {{ ucfirst(request('statut')) }}
                            <a href="{{ request()->fullUrlWithoutQuery(['statut', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('type'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-truck" style="font-size:9px"></i>
                            {{ request('type') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['type', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('chauffeur_id'))
                        @php $drv = $chauffeurs->firstWhere('id', request('chauffeur_id')); @endphp
                        @if ($drv)
                            <span class="active-filter-tag">
                                <i class="fa-solid fa-user" style="font-size:9px"></i>
                                {{ $drv->prenom }} {{ $drv->nom }}
                                <a href="{{ request()->fullUrlWithoutQuery(['chauffeur_id', 'page']) }}"><i
                                        class="fa-solid fa-xmark"></i></a>
                            </span>
                        @endif
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- ── TABLEAU ── --}}
    <div class="section-card">

        <div class="toolbar-row">
            <div class="toolbar-left">
                <span class="result-count">
                    <strong>{{ $vehicules->total() }}</strong> véhicule(s) trouvé(s)
                </span>
            </div>
            <a href="{{ route('vehicules.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouveau véhicule
            </a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Matricule / Marque</th>
                        <th>Type</th>
                        <th>Acquisition</th>
                        <th>Mise en circ.</th>
                        <th>PTAC</th>
                        <th>Km initial</th>
                        <th>Statut</th>
                        <th>Chauffeur</th>
                        <th>Créé le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicules as $v)
                        <tr>
                            {{-- ID --}}
                            <td><span class="td-id">#{{ $v->id }}</span></td>

                            {{-- Matricule / Marque --}}
                            <td>
                                <div class="vehicule-cell">
                                    <div class="vehicule-icon">
                                        <i class="fa-solid fa-truck"></i>
                                    </div>
                                    <div>
                                        <div class="vehicule-matricule">{{ $v->matricule }}</div>
                                        <div class="vehicule-marque">{{ $v->marque }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td>
                                <span class="badge"
                                    style="background:var(--bg-body);border:1px solid var(--border);color:var(--text-secondary)">
                                    {{ $v->type_vehicule ?? '—' }}
                                </span>
                            </td>

                            {{-- Acquisition --}}
                            <td>
                                @if ($v->acquisition === 'achat')
                                    <span class="badge badge-achat"><i class="fa-solid fa-circle-check"
                                            style="font-size:9px"></i> Achat</span>
                                @else
                                    <span class="badge badge-location"><i class="fa-solid fa-file-contract"
                                            style="font-size:9px"></i> Location</span>
                                @endif
                            </td>

                            {{-- Date circulation --}}
                            <td>
                                <div class="td-date">
                                    {{ $v->date_circulation ? \Carbon\Carbon::parse($v->date_circulation)->format('d/m/Y') : '—' }}
                                </div>
                            </td>

                            {{-- PTAC --}}
                            <td>
                                <span
                                    class="td-mono">{{ $v->ptac ? number_format($v->ptac, 0, ',', ' ') . ' kg' : '—' }}</span>
                            </td>

                            {{-- KM initial --}}
                            <td>
                                <span
                                    class="td-mono">{{ $v->km_initial ? number_format($v->km_initial, 0, ',', ' ') . ' km' : '—' }}</span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                @php
                                    $s = strtolower($v->statut ?? '');
                                    $cls = match ($s) {
                                        'actif' => 'badge-actif',
                                        'inactif' => 'badge-inactif',
                                        'maintenance' => 'badge-maintenance',
                                        default => '',
                                    };
                                    $ico = match ($s) {
                                        'actif' => 'fa-circle-check',
                                        'inactif' => 'fa-circle-xmark',
                                        'maintenance' => 'fa-wrench',
                                        default => 'fa-circle',
                                    };
                                @endphp
                                <span class="badge {{ $cls }}">
                                    <i class="fa-solid {{ $ico }}" style="font-size:9px"></i>
                                    {{ ucfirst($s ?: '—') }}
                                </span>
                            </td>

                            {{-- Chauffeur --}}
                            <td>
                                @if ($v->chauffeur)
                                    <div class="chauffeur-cell">
                                        <div class="chauffeur-avatar">
                                            {{ strtoupper(substr($v->chauffeur->prenom, 0, 1) . substr($v->chauffeur->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="chauffeur-name">{{ $v->chauffeur->prenom }}
                                                {{ $v->chauffeur->nom }}</div>
                                            <div style="font-size:10px;color:var(--text-muted)">
                                                {{ $v->chauffeur->code_drv }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span style="font-size:12px;color:var(--text-muted)">Non affecté</span>
                                @endif
                            </td>

                            {{-- Créé le --}}
                            <td>
                                <div class="td-date">{{ $v->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $v->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('vehicules.edit', $v) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('vehicules.destroy', $v) }}"
                                        id="delete-form-{{ $v->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteVehicule({{ $v->id }}, '{{ addslashes($v->matricule) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-truck"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucun véhicule ne correspond aux filtres
                                        @else
                                            Aucun véhicule enregistré
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères.
                                        @else
                                            Commencez par ajouter votre premier véhicule.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('vehicules.index') }}" class="btn btn-primary"
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

        {{-- ── PAGINATION ── --}}
        @if ($vehicules->hasPages())
            @php
                $current = $vehicules->currentPage();
                $last = $vehicules->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $vehicules->firstItem() }}</strong>–<strong>{{ $vehicules->lastItem() }}</strong>
                    sur <strong>{{ $vehicules->total() }}</strong> véhicule(s)
                </span>

                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $vehicules->url(1) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    @endif

                    <a class="page-btn {{ $vehicules->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $vehicules->previousPageUrl() ? $vehicules->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    @if ($from > 1)
                        <a class="page-btn" href="{{ $vehicules->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif

                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $vehicules->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor

                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $vehicules->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif

                    <a class="page-btn {{ !$vehicules->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $vehicules->nextPageUrl() ? $vehicules->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    @if ($current < $last)
                        <a class="page-btn" href="{{ $vehicules->url($last) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function handleDeleteVehicule(id, matricule) {
            Swal.fire({
                title: 'Supprimer le véhicule ?',
                text: `Êtes-vous sûr de vouloir supprimer le véhicule "${matricule}" ? Cette action est irréversible.`,
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
