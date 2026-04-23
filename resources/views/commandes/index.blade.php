{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES COMMANDES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $commandes   : LengthAwarePaginator (paginate avec filtres)
|   - $stats       : ['total' => int, 'en_cours' => int, 'livrees' => int, 'annulees' => int]
|   - $clients     : Collection (pour le filtre select)
|   - $trajets     : Collection (pour le filtre select)
|   - $activeFilters : int (nombre de filtres actifs)
|
| QUERY PARAMS :
|   ?search=        filtrage par code_commande ou destinataire
|   ?client_id=     filtrage par client
|   ?trajet_id=     filtrage par trajet
|   ?type=          filtrage par type
|   ?statut=        filtrage par statut
|   ?date_from=     date livraison début (Y-m-d)
|   ?date_to=       date livraison fin   (Y-m-d)
|   ?page=          pagination
|
| ROUTE : GET /commandes → CommandeController@index
--}}

@extends('layouts.app')

@section('title', 'Commandes')
@section('page-title', 'Gestion des Commandes')
@section('page-subtitle', 'Suivi et administration des commandes de transport')

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

        /* ── STATS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .stat-icon--total {
            background: rgba(99, 102, 241, .12);
            color: #6366f1;
        }

        .stat-icon--encours {
            background: rgba(245, 158, 11, .12);
            color: #f59e0b;
        }

        .stat-icon--livrees {
            background: rgba(16, 185, 129, .12);
            color: #10b981;
        }

        .stat-icon--annulees {
            background: rgba(224, 32, 32, .12);
            color: var(--color-primary);
        }

        .stat-body {
            flex: 1;
            min-width: 0;
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
            letter-spacing: 0.5px;
            margin-top: 4px;
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

        .filters-grid-row2 {
            display: grid;
            grid-template-columns: 1fr 1fr 2fr auto;
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
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .filter-input,
        .filter-select,
        .filter-date-input {
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
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
        .filter-date-input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .filter-select {
            cursor: pointer;
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

        .table-scroll-wrap {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 540px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
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
            letter-spacing: 0.6px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            white-space: nowrap;
        }

        .data-table td {
            padding: 12px 16px;
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
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .code-commande {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 3px 8px;
            display: inline-block;
        }

        .client-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .client-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--color-primary);
            flex-shrink: 0;
            font-weight: 700;
        }

        .client-name {
            font-weight: 600;
            font-size: 13px;
            color: var(--text-primary);
        }

        .client-type {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .trajet-cell {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }

        .trajet-ville {
            font-weight: 600;
            color: var(--text-primary);
        }

        .trajet-sep {
            color: var(--color-primary);
            font-size: 10px;
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

        /* Badges statut */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .badge--en_attente {
            background: rgba(99, 102, 241, .12);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, .25);
        }

        .badge--en_cours {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .25);
        }

        .badge--livree {
            background: rgba(16, 185, 129, .12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .25);
        }

        .badge--annulee {
            background: rgba(224, 32, 32, .10);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2);
        }

        /* Badges type */
        .type-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 5px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        /* ── ACTIONS ── */
        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1.5px solid var(--border);
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            text-decoration: none;
            transition: all var(--transition);
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

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 18px;
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
            transition: all var(--transition);
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
            opacity: 0.4;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* scroll hint */
        .scroll-hint {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
        }
    </style>

    {{-- BREADCRUMB --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Commandes</span>
    </div>

    {{-- ── STATS ── --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon stat-icon--total"><i class="fa-solid fa-file-invoice"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ number_format($stats['total']) }}</div>
                <div class="stat-label">Total commandes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--encours"><i class="fa-solid fa-truck-moving"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ number_format($stats['en_cours']) }}</div>
                <div class="stat-label">En cours</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--livrees"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ number_format($stats['livrees']) }}</div>
                <div class="stat-label">Livrées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon--annulees"><i class="fa-solid fa-ban"></i></div>
            <div class="stat-body">
                <div class="stat-value">{{ number_format($stats['annulees']) }}</div>
                <div class="stat-label">Annulées</div>
            </div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    <div class="filters-card" style="margin-bottom:18px">
        <div class="filters-header">
            <span class="filters-title">
                <i class="fa-solid fa-sliders"></i>
                Filtres
                @if ($activeFilters > 0)
                    <span class="filters-active-count">{{ $activeFilters }}</span>
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('commandes.index') }}" id="filterForm">

            {{-- Ligne 1 : recherche, client, trajet, type, statut --}}
            <div class="filters-grid">
                {{-- Recherche --}}
                <div class="filter-field">
                    <label>Rechercher</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Code commande, destinataire…" value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                {{-- Client --}}
                <div class="filter-field">
                    <label>Client</label>
                    <select name="client_id" class="filter-select">
                        <option value="">— Tous les clients —</option>
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
                        <option value="">— Tous les trajets —</option>
                        @foreach ($trajets as $trajet)
                            <option value="{{ $trajet->id }}"
                                {{ request('trajet_id') == $trajet->id ? 'selected' : '' }}>
                                {{ $trajet->villeDepart->nom ?? '—' }} → {{ $trajet->villeDestination->nom ?? '—' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type --}}
                <div class="filter-field">
                    <label>Type</label>
                    <select name="type" class="filter-select">
                        <option value="">— Tous types —</option>
                        <option value="import" {{ request('type') === 'import' ? 'selected' : '' }}>Import</option>
                        <option value="export" {{ request('type') === 'export' ? 'selected' : '' }}>Export</option>
                        <option value="local" {{ request('type') === 'local' ? 'selected' : '' }}>Local</option>
                        <option value="transit" {{ request('type') === 'transit' ? 'selected' : '' }}>Transit</option>
                    </select>
                </div>

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">— Tous statuts —</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente
                        </option>
                        <option value="en_cours" {{ request('statut') === 'en_cours' ? 'selected' : '' }}>En cours
                        </option>
                        <option value="livree" {{ request('statut') === 'livree' ? 'selected' : '' }}>Livrée</option>
                        <option value="annulee" {{ request('statut') === 'annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-magnifying-glass"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('commandes.index') }}" class="btn-reset" title="Effacer les filtres">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Ligne 2 : plage de dates livraison --}}
            <div class="filters-grid-row2">
                <div class="filter-field">
                    <label>Date livraison — De</label>
                    <input type="date" name="date_from" class="filter-date-input" value="{{ request('date_from') }}">
                </div>
                <div class="filter-field">
                    <label>Date livraison — À</label>
                    <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}">
                </div>
                <div></div>
                <div></div>
            </div>

        </form>

        {{-- Badges filtres actifs --}}
        @if ($activeFilters > 0)
            <div class="active-filters">
                @if (request('search'))
                    <span class="active-filter-tag">
                        <i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                        {{ request('search') }}
                        <a href="{{ route('commandes.index', request()->except('search', 'page')) }}"><i
                                class="fa-solid fa-xmark"></i></a>
                    </span>
                @endif
                @if (request('client_id'))
                    <span class="active-filter-tag">
                        <i class="fa-solid fa-user" style="font-size:9px"></i>
                        {{ $clients->firstWhere('id', request('client_id'))?->nom ?? 'Client #' . request('client_id') }}
                        <a href="{{ route('commandes.index', request()->except('client_id', 'page')) }}"><i
                                class="fa-solid fa-xmark"></i></a>
                    </span>
                @endif
                @if (request('trajet_id'))
                    @php $t = $trajets->firstWhere('id', request('trajet_id')); @endphp
                    <span class="active-filter-tag">
                        <i class="fa-solid fa-route" style="font-size:9px"></i>
                        {{ $t ? ($t->villeDepart->nom ?? '?') . ' → ' . ($t->villeDestination->nom ?? '?') : 'Trajet #' . request('trajet_id') }}
                        <a href="{{ route('commandes.index', request()->except('trajet_id', 'page')) }}"><i
                                class="fa-solid fa-xmark"></i></a>
                    </span>
                @endif
                @if (request('type'))
                    <span class="active-filter-tag">
                        <i class="fa-solid fa-tag" style="font-size:9px"></i>
                        {{ ucfirst(request('type')) }}
                        <a href="{{ route('commandes.index', request()->except('type', 'page')) }}"><i
                                class="fa-solid fa-xmark"></i></a>
                    </span>
                @endif
                @if (request('statut'))
                    <span class="active-filter-tag">
                        <i class="fa-solid fa-circle-dot" style="font-size:9px"></i>
                        {{ ucfirst(str_replace('_', ' ', request('statut'))) }}
                        <a href="{{ route('commandes.index', request()->except('statut', 'page')) }}"><i
                                class="fa-solid fa-xmark"></i></a>
                    </span>
                @endif
                @if (request('date_from') || request('date_to'))
                    <span class="active-filter-tag">
                        <i class="fa-solid fa-calendar" style="font-size:9px"></i>
                        {{ request('date_from') ? \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') : '…' }}
                        →
                        {{ request('date_to') ? \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') : '…' }}
                        <a href="{{ route('commandes.index', request()->except('date_from', 'date_to', 'page')) }}"><i
                                class="fa-solid fa-xmark"></i></a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="card">
        {{-- Toolbar --}}
        <div class="toolbar-row">
            <div class="toolbar-left">
                <span class="result-count">
                    <strong>{{ $commandes->total() }}</strong> commande(s)
                    @if ($activeFilters > 0)
                        <span style="color:var(--color-primary)">filtrée(s)</span>
                    @endif
                </span>
            </div>
            <a href="{{ route('commandes.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouvelle commande
            </a>
        </div>

        {{-- Scroll hint --}}
        <div class="scroll-hint">
            <i class="fa-solid fa-arrows-left-right"></i> Faites défiler horizontalement pour voir toutes les colonnes
        </div>

        {{-- Table avec scroll vertical et horizontal --}}
        <div class="table-scroll-wrap" style="margin-top:10px">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th style="width:140px">Code</th>
                        <th style="min-width:160px">Client</th>
                        <th style="min-width:180px">Trajet</th>
                        <th style="width:130px">Livraison</th>
                        <th style="width:110px">Type</th>
                        <th style="width:120px">Statut</th>
                        <th style="min-width:140px">Destinataire</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:130px">Modifié le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                        <tr>
                            {{-- ID --}}
                            <td class="td-id">#{{ $commande->id }}</td>

                            {{-- Code commande --}}
                            <td>
                                <span class="code-commande">
                                    @if (request('search'))
                                        {!! preg_replace(
                                            '/(' . preg_quote(request('search'), '/') . ')/i',
                                            '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                            e($commande->code_commande),
                                        ) !!}
                                    @else
                                        {{ $commande->code_commande }}
                                    @endif
                                </span>
                            </td>

                            {{-- Client --}}
                            <td>
                                <div class="client-cell">
                                    <div class="client-avatar">
                                        {{ strtoupper(substr($commande->client->nom ?? '?', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="client-name">{{ $commande->client->nom ?? '—' }}</div>
                                        <div class="client-type">
                                            @if ($commande->client?->type === 'entreprise')
                                                <i class="fa-solid fa-building" style="font-size:9px"></i> Entreprise
                                            @elseif ($commande->client?->type === 'particulier')
                                                <i class="fa-solid fa-user" style="font-size:9px"></i> Particulier
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Trajet --}}
                            <td>
                                @if ($commande->trajet)
                                    <div class="trajet-cell">
                                        <span class="trajet-ville">{{ $commande->trajet->villeDepart->nom ?? '—' }}</span>
                                        <span class="trajet-sep"><i class="fa-solid fa-arrow-right"></i></span>
                                        <span
                                            class="trajet-ville">{{ $commande->trajet->villeDestination->nom ?? '—' }}</span>
                                    </div>
                                    <div style="font-size:10px;color:var(--text-muted);margin-top:3px">
                                        <i class="fa-solid fa-road" style="font-size:9px"></i>
                                        {{ number_format($commande->trajet->distance_km, 0, ',', ' ') }} km
                                    </div>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>

                            {{-- Date livraison --}}
                            <td>
                                @if ($commande->date_livraison)
                                    <div class="td-date">
                                        {{ \Carbon\Carbon::parse($commande->date_livraison)->format('d/m/Y') }}</div>
                                    <div class="td-date-sub">
                                        {{ \Carbon\Carbon::parse($commande->date_livraison)->diffForHumans() }}
                                    </div>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>

                            {{-- Type --}}
                            <td>
                                <span class="type-badge">
                                    @switch($commande->type)
                                        @case('import')
                                            <i class="fa-solid fa-arrow-down" style="font-size:9px"></i> Import
                                        @break

                                        @case('export')
                                            <i class="fa-solid fa-arrow-up" style="font-size:9px"></i> Export
                                        @break

                                        @case('local')
                                            <i class="fa-solid fa-city" style="font-size:9px"></i> Local
                                        @break

                                        @case('transit')
                                            <i class="fa-solid fa-shuffle" style="font-size:9px"></i> Transit
                                        @break

                                        @default
                                            {{ ucfirst($commande->type ?? '—') }}
                                    @endswitch
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                @switch($commande->statut)
                                    @case('en_attente')
                                        <span class="badge badge--en_attente">
                                            <i class="fa-solid fa-clock" style="font-size:8px"></i> En attente
                                        </span>
                                    @break

                                    @case('en_cours')
                                        <span class="badge badge--en_cours">
                                            <i class="fa-solid fa-truck-moving" style="font-size:8px"></i> En cours
                                        </span>
                                    @break

                                    @case('livree')
                                        <span class="badge badge--livree">
                                            <i class="fa-solid fa-circle-check" style="font-size:8px"></i> Livrée
                                        </span>
                                    @break

                                    @case('annulee')
                                        <span class="badge badge--annulee">
                                            <i class="fa-solid fa-ban" style="font-size:8px"></i> Annulée
                                        </span>
                                    @break

                                    @default
                                        <span class="badge">{{ $commande->statut ?? '—' }}</span>
                                @endswitch
                            </td>

                            {{-- Destinataire --}}
                            <td>
                                @if (request('search') && $commande->destinataire)
                                    {!! preg_replace(
                                        '/(' . preg_quote(request('search'), '/') . ')/i',
                                        '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                        e($commande->destinataire),
                                    ) !!}
                                @else
                                    {{ $commande->destinataire ?? '—' }}
                                @endif
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date">{{ $commande->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $commande->created_at->format('H:i') }}</div>
                            </td>

                            {{-- updated_at --}}
                            <td>
                                <div class="td-date">{{ $commande->updated_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $commande->updated_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('commandes.edit', $commande) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('commandes.destroy', $commande) }}"
                                        id="delete-form-{{ $commande->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteCommande({{ $commande->id }}, '{{ $commande->code_commande }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="padding:0;border:none">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fa-solid fa-file-circle-xmark"></i></div>
                                        <h3>
                                            @if ($activeFilters > 0)
                                                Aucune commande ne correspond aux filtres
                                            @else
                                                Aucune commande enregistrée
                                            @endif
                                        </h3>
                                        <p>
                                            @if ($activeFilters > 0)
                                                Essayez de modifier ou d'effacer vos critères de recherche.
                                            @else
                                                Commencez par créer votre première commande de transport.
                                            @endif
                                        </p>
                                        @if ($activeFilters > 0)
                                            <a href="{{ route('commandes.index') }}" class="btn btn-primary"
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
            @if ($commandes->hasPages())
                @php
                    $current = $commandes->currentPage();
                    $last = $commandes->lastPage();
                    $window = 2;
                    $from = max(1, $current - $window);
                    $to = min($last, $current + $window);
                    $qs = http_build_query(request()->except('page'));
                    $sep = $qs ? '&' : '';
                @endphp
                <div class="pagination-wrap">
                    <span class="pagination-info">
                        <strong>{{ $commandes->firstItem() }}</strong>–<strong>{{ $commandes->lastItem() }}</strong>
                        sur <strong>{{ $commandes->total() }}</strong> commande(s)
                    </span>

                    <div class="pagination-links">
                        @if ($current > 1)
                            <a class="page-btn" href="{{ $commandes->url(1) . $sep . $qs }}">
                                <i class="fa-solid fa-angles-left"></i>
                            </a>
                        @endif

                        <a class="page-btn {{ $commandes->onFirstPage() ? 'disabled' : '' }}"
                            href="{{ $commandes->previousPageUrl() ? $commandes->previousPageUrl() . $sep . $qs : '#' }}">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                        @if ($from > 1)
                            <a class="page-btn" href="{{ $commandes->url(1) . $sep . $qs }}">1</a>
                            @if ($from > 2)
                                <span class="page-btn disabled">…</span>
                            @endif
                        @endif

                        @for ($p = $from; $p <= $to; $p++)
                            <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                                href="{{ $commandes->url($p) . $sep . $qs }}">{{ $p }}</a>
                        @endfor

                        @if ($to < $last)
                            @if ($to < $last - 1)
                                <span class="page-btn disabled">…</span>
                            @endif
                            <a class="page-btn" href="{{ $commandes->url($last) . $sep . $qs }}">{{ $last }}</a>
                        @endif

                        <a class="page-btn {{ !$commandes->hasMorePages() ? 'disabled' : '' }}"
                            href="{{ $commandes->nextPageUrl() ? $commandes->nextPageUrl() . $sep . $qs : '#' }}">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                        @if ($current < $last)
                            <a class="page-btn" href="{{ $commandes->url($last) . $sep . $qs }}">
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
            function handleDeleteCommande(id, code) {
                Swal.fire({
                    title: 'Supprimer la commande ?',
                    text: `Êtes-vous sûr de vouloir supprimer la commande "${code}" ? Cette action est irréversible.`,
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
