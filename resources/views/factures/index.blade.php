{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES FACTURES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $factures     : LengthAwarePaginator (avec relation client)
|   - $stats        : ['total','reglees','non_reglees','en_retard']
|   - $clients      : Collection (id, nom) pour le filtre
|   - $activeFilters: int — nombre de filtres actifs
|
| QUERY PARAMS :
|   ?search=    filtrage par num_facture
|   ?client_id= filtrage par client
|   ?statut=    filtrage par statut
|   ?date_from= date début (Y-m-d)
|   ?date_to=   date fin   (Y-m-d)
|   ?page=      pagination
|
| ROUTE : GET /factures → FactureController@index
--}}

@extends('layouts.app')

@section('title', 'Factures')
@section('page-title', 'Gestion des Factures')
@section('page-subtitle', 'Suivi et gestion de la facturation clients')

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

        /* ── STAT CARDS ── */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px 18px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: center;
            gap: 14px
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0
        }

        .stat-icon.total {
            background: rgba(99, 102, 241, .1);
            color: #6366f1
        }

        .stat-icon.success {
            background: rgba(16, 185, 129, .1);
            color: #10b981
        }

        .stat-icon.warning {
            background: rgba(245, 158, 11, .1);
            color: #f59e0b
        }

        .stat-icon.danger {
            background: rgba(224, 32, 32, .1);
            color: var(--color-primary)
        }

        .stat-info {
            min-width: 0
        }

        .stat-value {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary);
            line-height: 1
        }

        .stat-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px
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
            grid-template-columns: 2fr 1.5fr 1fr 1.5fr auto;
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
        .filter-date-input {
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
        .filter-date-input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .date-range-wrap {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .date-range-sep {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0
        }

        .date-range-wrap .filter-date-input {
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
            transition: border-color var(--transition), color var(--transition);
            white-space: nowrap
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

        .table-wrap {
            overflow-x: auto
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

        .td-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary)
        }

        .facture-cell {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .facture-icon {
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
            color: var(--text-primary)
        }

        .client-type {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px
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

        .td-montant {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap
        }

        .td-montant-sub {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px
        }

        /* Badges statut */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px
        }

        .badge-success {
            background: rgba(16, 185, 129, .1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, .2)
        }

        .badge-warning {
            background: rgba(245, 158, 11, .1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, .2)
        }

        .badge-danger {
            background: rgba(224, 32, 32, .1);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2)
        }

        .badge-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0
        }

        /* Actions */
        .btn-icon {
            width: 30px;
            height: 30px;
            border: 1.5px solid var(--border);
            border-radius: 6px;
            background: transparent;
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
            text-decoration: none
        }

        .btn-icon:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .btn-icon--danger:hover {
            border-color: #e02020;
            color: #e02020;
            background: rgba(224, 32, 32, .06)
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            padding: 60px 30px;
            text-align: center;
            color: var(--text-muted)
        }

        .empty-state-icon {
            font-size: 40px;
            margin-bottom: 16px;
            opacity: .3
        }

        .empty-state h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px
        }

        .empty-state p {
            font-size: 13px;
            margin-bottom: 18px
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

        .page-btn:hover:not(.disabled):not(.active) {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .page-btn.active {
            background: var(--color-primary);
            border-color: var(--color-primary);
            color: #fff;
            font-weight: 700
        }

        .page-btn.disabled {
            opacity: .4;
            pointer-events: none
        }

        /* ── SCROLL SOUS LA TABLE ── */
        .table-scroll-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: var(--text-muted);
            padding: 8px 0 0;
            justify-content: flex-end
        }
    </style>

    {{-- ── BREADCRUMB ── --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Factures</span>
    </div>

    {{-- ── FLASH ── --}}
    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:16px">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── STAT CARDS ── --}}
    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-icon total"><i class="fa-solid fa-file-invoice"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total factures</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['reglees'] }}</div>
                <div class="stat-label">Réglées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon warning"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['non_reglees'] }}</div>
                <div class="stat-label">Non réglées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['en_retard'] }}</div>
                <div class="stat-label">En retard</div>
            </div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    <div class="filters-card" style="margin-bottom:20px">
        <div class="filters-header">
            <span class="filters-title">
                <i class="fa-solid fa-sliders"></i>
                Filtres
                @if ($activeFilters > 0)
                    <span class="filters-active-count">{{ $activeFilters }}</span>
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('factures.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Recherche num_facture --}}
                <div class="filter-field">
                    <label>Numéro de facture</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon" placeholder="Ex : FAC-2024-001…"
                            value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                {{-- Client --}}
                <div class="filter-field">
                    <label>Client</label>
                    <select name="client_id" class="filter-select">
                        <option value="">Tous les clients</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                {{ $client->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous</option>
                        <option value="réglée" {{ request('statut') === 'réglée' ? 'selected' : '' }}>Réglée</option>
                        <option value="non_réglée" {{ request('statut') === 'non_réglée' ? 'selected' : '' }}>Non réglée
                        </option>
                        <option value="en_retard" {{ request('statut') === 'en_retard' ? 'selected' : '' }}>En retard
                        </option>
                    </select>
                </div>

                {{-- Dates --}}
                <div class="filter-field">
                    <label>Date facture</label>
                    <div class="date-range-wrap">
                        <input type="date" name="date_from" class="filter-date-input" value="{{ request('date_from') }}"
                            title="Du">
                        <span class="date-range-sep">→</span>
                        <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}"
                            title="Au">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-magnifying-glass"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('factures.index') }}" class="btn-reset" title="Réinitialiser">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Tags filtres actifs --}}
            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('search'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                            « {{ request('search') }} »
                            <a href="{{ route('factures.index', request()->except('search', 'page')) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('client_id'))
                        @php $cl = $clients->firstWhere('id', request('client_id')) @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-user" style="font-size:9px"></i>
                            {{ $cl?->nom ?? 'Client #' . request('client_id') }}
                            <a href="{{ route('factures.index', request()->except('client_id', 'page')) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('statut'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-tag" style="font-size:9px"></i>
                            {{ ucfirst(str_replace('_', ' ', request('statut'))) }}
                            <a href="{{ route('factures.index', request()->except('statut', 'page')) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('date_from'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-calendar" style="font-size:9px"></i>
                            Depuis {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                            <a href="{{ route('factures.index', request()->except('date_from', 'page')) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('date_to'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-calendar" style="font-size:9px"></i>
                            Jusqu'au {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                            <a href="{{ route('factures.index', request()->except('date_to', 'page')) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- ── TABLE CARD ── --}}
    <div class="card"
        style="background:var(--bg-card);border:1px solid var(--border);border-radius:var(--border-radius);padding:20px;box-shadow:var(--shadow-sm)">

        {{-- Toolbar --}}
        <div class="toolbar-row">
            <div class="toolbar-left">
                <span class="result-count">
                    <strong>{{ $factures->total() }}</strong> facture(s) trouvée(s)
                    @if ($factures->total() > 0)
                        — page <strong>{{ $factures->currentPage() }}</strong> /
                        <strong>{{ $factures->lastPage() }}</strong>
                    @endif
                </span>
            </div>
            <a href="{{ route('factures.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouvelle facture
            </a>
        </div>

        {{-- Hint scroll horizontal --}}
        <div class="table-scroll-info">
            <i class="fa-solid fa-arrows-left-right"></i> Faites défiler pour voir toutes les colonnes
        </div>

        {{-- Table --}}
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:55px">#</th>
                        <th style="width:160px">N° Facture</th>
                        <th>Client</th>
                        <th style="width:130px">Date facture</th>
                        <th style="width:130px">Échéance</th>
                        <th style="width:130px">Total HT</th>
                        <th style="width:130px">TVA / TTC</th>
                        <th style="width:110px">Statut</th>
                        <th style="width:150px">Créé le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($factures as $facture)
                        <tr>
                            <td class="td-id">#{{ $facture->id }}</td>

                            {{-- N° Facture --}}
                            <td>
                                <div class="facture-cell">
                                    <div class="facture-icon"><i class="fa-solid fa-file-invoice"></i></div>
                                    <span class="td-num">
                                        @if (request('search'))
                                            {!! preg_replace(
                                                '/(' . preg_quote(request('search'), '/') . ')/i',
                                                '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                e($facture->num_facture),
                                            ) !!}
                                        @else
                                            {{ $facture->num_facture }}
                                        @endif
                                    </span>
                                </div>
                            </td>

                            {{-- Client --}}
                            <td>
                                <div class="client-name">{{ $facture->client?->nom ?? '—' }}</div>
                                @if ($facture->client)
                                    <div class="client-type">
                                        {{ $facture->client->type === 'entreprise' ? 'Entreprise' : 'Particulier' }}
                                    </div>
                                @endif
                            </td>

                            {{-- Date facture --}}
                            <td>
                                <div class="td-date">{{ $facture->date_facture->format('d/m/Y') }}</div>
                            </td>

                            {{-- Échéance --}}
                            <td>
                                <div class="td-date {{ $facture->statut === 'en_retard' ? 'text-danger' : '' }}">
                                    {{ $facture->date_echeance->format('d/m/Y') }}
                                </div>
                                @if ($facture->statut === 'en_retard')
                                    <div class="td-date-sub" style="color:var(--color-primary)">
                                        {{ $facture->date_echeance->diffForHumans() }}
                                    </div>
                                @endif
                            </td>

                            {{-- Total HT --}}
                            <td>
                                <div class="td-montant">{{ number_format($facture->total_ht, 2, ',', ' ') }} MAD</div>
                            </td>

                            {{-- TVA / TTC --}}
                            <td>
                                <div class="td-montant">{{ number_format($facture->total_tva, 2, ',', ' ') }} MAD</div>
                                <div class="td-montant-sub">TTC : {{ number_format($facture->total_ttc, 2, ',', ' ') }}
                                </div>
                            </td>

                            {{-- Statut --}}
                            <td>
                                <span class="badge {{ $facture->statut_badge_class }}">
                                    <span class="badge-dot"></span>
                                    {{ $facture->statut_label }}
                                </span>
                            </td>

                            {{-- Créé le --}}
                            <td>
                                <div class="td-date">{{ $facture->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $facture->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('factures.pdf', $facture->id) }}" class="btn-pdf"
                                        title="Télécharger le PDF" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </a>
                                    <a href="{{ route('factures.edit', $facture) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('factures.destroy', $facture) }}"
                                        id="delete-form-{{ $facture->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteFacture({{ $facture->id }}, '{{ $facture->num_facture }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-file-invoice"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucune facture ne correspond aux filtres
                                        @else
                                            Aucune facture enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères de recherche.
                                        @else
                                            Commencez par créer votre première facture.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('factures.index') }}" class="btn btn-primary"
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
        @if ($factures->hasPages())
            @php
                $current = $factures->currentPage();
                $last = $factures->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $factures->firstItem() }}</strong>–<strong>{{ $factures->lastItem() }}</strong>
                    sur <strong>{{ $factures->total() }}</strong> facture(s)
                </span>

                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $factures->url(1) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    @endif
                    <a class="page-btn {{ $factures->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $factures->previousPageUrl() ? $factures->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    @if ($from > 1)
                        <a class="page-btn" href="{{ $factures->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif

                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $factures->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor

                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $factures->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif

                    <a class="page-btn {{ !$factures->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $factures->nextPageUrl() ? $factures->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $factures->url($last) . $sep . $qs }}">
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
        function handleDeleteFacture(id, num) {
            Swal.fire({
                title: 'Supprimer la facture ?',
                text: `Êtes-vous sûr de vouloir supprimer la facture "${num}" ? Cette action est irréversible.`,
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
