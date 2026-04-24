{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES INFRACTIONS — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $infractions   : LengthAwarePaginator
|   - $stats         : ['total', 'ce_mois', 'montant_total']
|   - $vehicules     : Collection (id, matricule, marque)
|   - $chauffeurs    : Collection (id, code_drv, nom, prenom)
|   - $types         : Collection des types distincts
|   - $activeFilters : int
|
| QUERY PARAMS :
|   ?search=          filtrage type/description
|   ?vehicule_id=     filtrage par véhicule
|   ?chauffeur_id=    filtrage par chauffeur
|   ?type_infraction= filtrage par type
|   ?date_from=       date début
|   ?date_to=         date fin
|   ?montant_min=     montant minimum
|   ?montant_max=     montant maximum
|   ?page=            pagination
|
| ROUTE : GET /infractions → InfractionController@index
--}}

@extends('layouts.app')

@section('title', 'Infractions')
@section('page-title', 'Gestion des Infractions')
@section('page-subtitle', 'Suivi des infractions liées aux véhicules et chauffeurs')

@section('content')

    <style>
        /* ── BREADCRUMB ── */
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

        /* ── STATS CARDS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        .stat-icon--red {
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .15);
        }

        .stat-icon--orange {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .2);
        }

        .stat-icon--green {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .stat-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-top: 3px;
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
            letter-spacing: .6px;
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
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
        }

        .filters-grid-row2 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr auto;
            gap: 10px;
            align-items: end;
            margin-top: 10px;
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
            letter-spacing: .5px;
            color: var(--text-muted);
        }

        .filter-input,
        .filter-select,
        .filter-date-input,
        .filter-number-input {
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
        .filter-select:focus,
        .filter-date-input:focus,
        .filter-number-input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .date-range-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .date-range-sep {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .date-range-wrap .filter-date-input {
            flex: 1;
            min-width: 0;
        }

        .montant-range-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .montant-range-wrap .filter-number-input {
            flex: 1;
            min-width: 0;
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
            white-space: nowrap;
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
            letter-spacing: .6px;
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

        /* Cellule véhicule */
        .vehicule-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .vehicule-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(59, 130, 246, .1);
            border: 1px solid rgba(59, 130, 246, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #3b82f6;
            flex-shrink: 0;
        }

        .vehicule-matricule {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-primary);
        }

        .vehicule-marque {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* Cellule chauffeur */
        .chauffeur-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chauffeur-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            color: var(--color-primary);
            flex-shrink: 0;
        }

        .chauffeur-nom {
            font-weight: 600;
            font-size: 13px;
        }

        .chauffeur-code {
            font-size: 10px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Badge type infraction */
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .2);
            border-radius: 20px;
            white-space: nowrap;
        }

        /* Montant */
        .td-montant {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            font-size: 13px;
            color: var(--color-primary);
        }

        /* Date */
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

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 40px;
            color: var(--text-muted);
            margin-bottom: 16px;
            opacity: .4;
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        /* Btn icon */
        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--text-secondary);
            cursor: pointer;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
        }

        .btn-icon:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .btn-icon--danger:hover {
            border-color: var(--color-primary);
            color: #fff;
            background: var(--color-primary);
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
            padding: 0 8px;
        }

        .page-btn:hover:not(.disabled):not(.active) {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .page-btn.active {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: #fff;
            font-weight: 700;
        }

        .page-btn.disabled {
            opacity: .4;
            pointer-events: none;
        }

        /* ── SCROLL INDICATOR ── */
        .scroll-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            font-size: 11px;
            color: var(--text-muted);
            border-top: 1px solid var(--border);
        }

        .scroll-indicator i {
            animation: bounceDown .8s infinite alternate;
        }

        @keyframes bounceDown {
            from {
                transform: translateY(0);
            }

            to {
                transform: translateY(4px);
            }
        }
    </style>

    {{-- ── BREADCRUMB ── --}}
    <nav class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Infractions</span>
    </nav>

    {{-- ── STATS ── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon--red"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total infractions</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--orange"><i class="fa-solid fa-calendar-day"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['ce_mois']) }}</div>
                <div class="stat-label">Ce mois-ci</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--green"><i class="fa-solid fa-coins"></i></div>
            <div>
                <div class="stat-value">{{ number_format($stats['montant_total'], 0, ',', ' ') }} MAD</div>
                <div class="stat-label">Montant total</div>
            </div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    <div class="filters-card" style="margin-bottom: 20px">
        <div class="filters-header">
            <span class="filters-title">
                <i class="fa-solid fa-filter"></i>
                Filtres
                @if ($activeFilters > 0)
                    <span class="filters-active-count">{{ $activeFilters }}</span>
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('infractions.index') }}">
            <div class="filters-grid">
                {{-- Recherche --}}
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Type d'infraction, description…" value="{{ request('search') }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Véhicule --}}
                <div class="filter-field">
                    <label>Véhicule</label>
                    <select name="vehicule_id" class="filter-select">
                        <option value="">Tous les véhicules</option>
                        @foreach ($vehicules as $v)
                            <option value="{{ $v->id }}" {{ request('vehicule_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->matricule }} — {{ $v->marque }}
                            </option>
                        @endforeach
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

                {{-- Type --}}
                <div class="filter-field">
                    <label>Type d'infraction</label>
                    <select name="type_infraction" class="filter-select">
                        <option value="">Tous les types</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}"
                                {{ request('type_infraction') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions row 1 --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('infractions.index') }}" class="btn-reset">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Ligne 2 : dates + montants --}}
            <div class="filters-grid-row2">
                {{-- Période --}}
                <div class="filter-field" style="grid-column: span 2">
                    <label>Période d'infraction</label>
                    <div class="date-range-wrap">
                        <input type="date" name="date_from" class="filter-date-input"
                            value="{{ request('date_from') }}">
                        <span class="date-range-sep">→</span>
                        <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}">
                    </div>
                </div>

                {{-- Montant --}}
                <div class="filter-field" style="grid-column: span 2">
                    <label>Montant (MAD)</label>
                    <div class="montant-range-wrap">
                        <input type="number" name="montant_min" class="filter-number-input" placeholder="Min"
                            min="0" step="0.01" value="{{ request('montant_min') }}">
                        <span class="date-range-sep">→</span>
                        <input type="number" name="montant_max" class="filter-number-input" placeholder="Max"
                            min="0" step="0.01" value="{{ request('montant_max') }}">
                    </div>
                </div>

                <div></div>{{-- spacer --}}
            </div>

            {{-- Badges filtres actifs --}}
            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('search'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                            Recherche : "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('vehicule_id'))
                        @php $vLabel = $vehicules->firstWhere('id', request('vehicule_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-truck" style="font-size:9px"></i>
                            Véhicule : {{ $vLabel?->matricule ?? request('vehicule_id') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['vehicule_id', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('chauffeur_id'))
                        @php $cLabel = $chauffeurs->firstWhere('id', request('chauffeur_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-user" style="font-size:9px"></i>
                            Chauffeur : {{ $cLabel ? $cLabel->prenom . ' ' . $cLabel->nom : request('chauffeur_id') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['chauffeur_id', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('type_infraction'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-tag" style="font-size:9px"></i>
                            Type : {{ request('type_infraction') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['type_infraction', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('date_from') || request('date_to'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-calendar" style="font-size:9px"></i>
                            @if (request('date_from') && request('date_to'))
                                Du {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                                au {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                            @elseif (request('date_from'))
                                Depuis le {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                            @else
                                Jusqu'au {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                            @endif
                            <a href="{{ request()->fullUrlWithoutQuery(['date_from', 'date_to', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('montant_min') || request('montant_max'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-coins" style="font-size:9px"></i>
                            Montant :
                            @if (request('montant_min') && request('montant_max'))
                                {{ request('montant_min') }} → {{ request('montant_max') }} MAD
                            @elseif (request('montant_min'))
                                ≥ {{ request('montant_min') }} MAD
                            @else
                                ≤ {{ request('montant_max') }} MAD
                            @endif
                            <a href="{{ request()->fullUrlWithoutQuery(['montant_min', 'montant_max', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
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
                <h2 class="section-title">Liste des infractions</h2>
                <span class="result-count">
                    <strong>{{ $infractions->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('infractions.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:55px">#</th>
                        <th>Véhicule</th>
                        <th>Chauffeur</th>
                        <th>Date</th>
                        <th>Type d'infraction</th>
                        <th style="width:130px">Montant</th>
                        <th>Description</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($infractions as $infraction)
                        <tr>
                            {{-- ID --}}
                            <td class="td-id">#{{ $infraction->id }}</td>

                            {{-- Véhicule --}}
                            <td>
                                @if ($infraction->vehicule)
                                    <div class="vehicule-cell">
                                        <div class="vehicule-icon">
                                            <i class="fa-solid fa-truck"></i>
                                        </div>
                                        <div>
                                            <div class="vehicule-matricule">
                                                @if (request('search'))
                                                    {!! preg_replace(
                                                        '/(' . preg_quote(request('search'), '/') . ')/i',
                                                        '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                        e($infraction->vehicule->matricule),
                                                    ) !!}
                                                @else
                                                    {{ $infraction->vehicule->matricule }}
                                                @endif
                                            </div>
                                            <div class="vehicule-marque">{{ $infraction->vehicule->marque }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span style="color:var(--text-muted);font-size:12px">—</span>
                                @endif
                            </td>

                            {{-- Chauffeur --}}
                            <td>
                                @if ($infraction->chauffeur)
                                    <div class="chauffeur-cell">
                                        <div class="chauffeur-avatar">
                                            {{ strtoupper(substr($infraction->chauffeur->prenom, 0, 1)) }}{{ strtoupper(substr($infraction->chauffeur->nom, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="chauffeur-nom">
                                                {{ $infraction->chauffeur->prenom }} {{ $infraction->chauffeur->nom }}
                                            </div>
                                            <div class="chauffeur-code">{{ $infraction->chauffeur->code_drv }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span style="color:var(--text-muted);font-size:12px">—</span>
                                @endif
                            </td>

                            {{-- Date infraction --}}
                            <td>
                                <div class="td-date">
                                    {{ $infraction->date_infraction->format('d/m/Y') }}
                                </div>
                                <div class="td-date-sub">
                                    {{ $infraction->date_infraction->diffForHumans() }}
                                </div>
                            </td>

                            {{-- Type --}}
                            <td>
                                <span class="type-badge">
                                    <i class="fa-solid fa-tag" style="font-size:9px"></i>
                                    @if (request('search'))
                                        {!! preg_replace(
                                            '/(' . preg_quote(request('search'), '/') . ')/i',
                                            '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                            e($infraction->type_infraction),
                                        ) !!}
                                    @else
                                        {{ $infraction->type_infraction }}
                                    @endif
                                </span>
                            </td>

                            {{-- Montant --}}
                            <td>
                                <span class="td-montant">
                                    {{ number_format($infraction->montant, 2, ',', ' ') }} MAD
                                </span>
                            </td>

                            {{-- Description --}}
                            <td style="max-width:200px">
                                @if ($infraction->description)
                                    <span style="font-size:12px;color:var(--text-secondary)"
                                        title="{{ $infraction->description }}">
                                        {{ Str::limit($infraction->description, 60) }}
                                    </span>
                                @else
                                    <span style="color:var(--text-muted);font-size:12px">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('infractions.edit', $infraction) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('infractions.destroy', $infraction) }}"
                                        id="delete-form-{{ $infraction->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteInfraction({{ $infraction->id }}, '{{ addslashes($infraction->type_infraction) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucune infraction ne correspond aux filtres
                                        @else
                                            Aucune infraction enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères.
                                        @else
                                            Commencez par enregistrer la première infraction.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('infractions.index') }}" class="btn btn-primary"
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
        @if ($infractions->hasPages())
            @php
                $current = $infractions->currentPage();
                $last = $infractions->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $infractions->firstItem() }}</strong>–<strong>{{ $infractions->lastItem() }}</strong>
                    sur <strong>{{ $infractions->total() }}</strong> infraction(s)
                </span>

                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $infractions->url(1) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    @endif

                    <a class="page-btn {{ $infractions->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $infractions->previousPageUrl() ? $infractions->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    @if ($from > 1)
                        <a class="page-btn" href="{{ $infractions->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif

                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $infractions->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor

                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $infractions->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif

                    <a class="page-btn {{ !$infractions->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $infractions->nextPageUrl() ? $infractions->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    @if ($current < $last)
                        <a class="page-btn" href="{{ $infractions->url($last) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── SCROLL INDICATOR ── --}}
        @if ($infractions->count() > 5)
            <div class="scroll-indicator">
                <i class="fa-solid fa-chevron-down"></i>
                <span>Faites défiler pour voir plus de résultats</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function handleDeleteInfraction(id, type) {
            Swal.fire({
                title: 'Supprimer l\'infraction ?',
                text: `Êtes-vous sûr de vouloir supprimer l'infraction "${type}" ? Cette action est irréversible.`,
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
