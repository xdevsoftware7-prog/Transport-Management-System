{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES CLIENTS — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $clients          : LengthAwarePaginator
|   - $stats            : ['total', 'actifs', 'entreprises', 'ce_mois']
|   - $statutsJuridiques: Collection des statuts juridiques distincts
|
| QUERY PARAMS :
|   ?search=            filtrage nom / email / téléphone / ICE
|   ?type=              entreprise | particulier
|   ?modalite_paiement= comptant | 30_jours | 60_jours
|   ?is_active=         1 | 0
|   ?date_from=         date début (Y-m-d)
|   ?date_to=           date fin   (Y-m-d)
|   ?page=              pagination
|
| ROUTE : GET /clients → ClientController@index
--}}

@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Gestion des Clients')
@section('page-subtitle', 'Référentiel des clients et partenaires commerciaux')

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

        /* Scroll horizontal du tableau */
        .table-scroll-wrap {
            overflow-x: auto;
            max-height: 520px;
            overflow-y: auto;
        }

        .table-scroll-wrap::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }

        .table-scroll-wrap::-webkit-scrollbar-track {
            background: var(--bg-body);
        }

        .table-scroll-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        .table-scroll-wrap::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
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
            position: sticky;
            top: 0;
            z-index: 2;
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

        .client-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .client-icon {
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

        .client-icon.particulier {
            background: rgba(59, 130, 246, .08);
            border-color: rgba(59, 130, 246, .15);
            color: #3b82f6;
        }

        .client-nom {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .client-email {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Badge type */
        .badge-type {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .badge-type.entreprise {
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2);
        }

        .badge-type.particulier {
            background: rgba(59, 130, 246, .08);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, .2);
        }

        /* Badge paiement */
        .badge-paiement {
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
            font-family: 'JetBrains Mono', monospace;
        }

        /* Badge actif */
        .badge-active {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .badge-active.on {
            background: rgba(16, 185, 129, .08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .badge-active.off {
            background: rgba(107, 114, 128, .08);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .2);
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

        .actions-cell {
            display: flex;
            gap: 4px;
            align-items: center;
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
            transition: border-color var(--transition), color var(--transition), background var(--transition);
        }

        .btn-icon:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .btn-icon--danger:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: rgba(239, 68, 68, .06);
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
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }

            .date-range-wrap {
                flex-direction: column;
                gap: 8px;
            }

            .date-range-sep {
                display: none;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Clients</span>
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
            <div class="kpi-label">Total clients</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Clients actifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['entreprises'] }}</div>
            <div class="kpi-label">Entreprises</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['ce_mois'] }}</div>
            <div class="kpi-label">Ajoutés ce mois</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'type' => request('type'),
            'modalite_paiement' => request('modalite_paiement'),
            'is_active' => request()->has('is_active') && request('is_active') !== '' ? request('is_active') : null,
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
        ])
            ->filter(fn($v) => $v !== null && $v !== '')
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

        <form method="GET" action="{{ route('clients.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Recherche --}}
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Nom, e-mail, tél., ICE…" value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                {{-- Type --}}
                <div class="filter-field">
                    <label>Type</label>
                    <select name="type" class="filter-select">
                        <option value="">Tous les types</option>
                        <option value="entreprise" {{ request('type') === 'entreprise' ? 'selected' : '' }}>Entreprise
                        </option>
                        <option value="particulier" {{ request('type') === 'particulier' ? 'selected' : '' }}>Particulier
                        </option>
                    </select>
                </div>

                {{-- Modalité paiement --}}
                <div class="filter-field">
                    <label>Modalité paiement</label>
                    <select name="modalite_paiement" class="filter-select">
                        <option value="">Toutes</option>
                        <option value="comptant" {{ request('modalite_paiement') === 'comptant' ? 'selected' : '' }}>
                            Comptant</option>
                        <option value="30_jours" {{ request('modalite_paiement') === '30_jours' ? 'selected' : '' }}>30
                            jours</option>
                        <option value="60_jours" {{ request('modalite_paiement') === '60_jours' ? 'selected' : '' }}>60
                            jours</option>
                    </select>
                </div>

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="is_active" class="filter-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actif</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>

                {{-- Période --}}
                <div class="filter-field">
                    <label>Période de création</label>
                    <div class="date-range-wrap">
                        <input type="date" name="date_from" class="filter-date-input"
                            value="{{ request('date_from') }}">
                        <span class="date-range-sep">→</span>
                        <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('clients.index') }}" class="btn-reset">
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
                            Recherche : "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('type'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-tag" style="font-size:9px"></i>
                            Type : {{ ucfirst(request('type')) }}
                            <a href="{{ request()->fullUrlWithoutQuery(['type', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('modalite_paiement'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-credit-card" style="font-size:9px"></i>
                            Paiement : {{ str_replace('_', ' ', request('modalite_paiement')) }}
                            <a href="{{ request()->fullUrlWithoutQuery(['modalite_paiement', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('is_active') !== null && request('is_active') !== '')
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-circle" style="font-size:9px"></i>
                            Statut : {{ request('is_active') == '1' ? 'Actif' : 'Inactif' }}
                            <a href="{{ request()->fullUrlWithoutQuery(['is_active', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
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
                            <a href="{{ request()->fullUrlWithoutQuery(['date_from', 'date_to', 'page']) }}"><i
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
                <h2 class="section-title">Liste des clients</h2>
                <span class="result-count">
                    <strong>{{ $clients->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouveau client
            </a>
        </div>

        {{-- Scroll vertical + horizontal --}}
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Téléphone</th>
                        <th>ICE</th>
                        <th>Statut juridique</th>
                        <th>Modalité</th>
                        <th>Statut</th>
                        <th>Créé le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td class="td-id">#{{ $client->id }}</td>
                            <td>
                                <div class="client-cell">
                                    <div class="client-icon {{ $client->type }}">
                                        <i
                                            class="fa-solid {{ $client->type === 'entreprise' ? 'fa-building' : 'fa-user' }}"></i>
                                    </div>
                                    <div>
                                        <div class="client-nom">{{ $client->nom }}</div>
                                        @if ($client->email)
                                            <div class="client-email">{{ $client->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-type {{ $client->type }}">
                                    <i class="fa-solid {{ $client->type === 'entreprise' ? 'fa-building' : 'fa-user' }}"
                                        style="font-size:9px"></i>
                                    {{ ucfirst($client->type) }}
                                </span>
                            </td>
                            <td>{{ $client->telephone ?? '—' }}</td>
                            <td>
                                @if ($client->ice)
                                    <span
                                        style="font-family:'JetBrains Mono',monospace;font-size:12px">{{ $client->ice }}</span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>{{ $client->statut_juridique ?? '—' }}</td>
                            <td>
                                <span class="badge-paiement">
                                    {{ match ($client->modalite_paiement) {
                                        'comptant' => 'Comptant',
                                        '30_jours' => '30 j',
                                        '60_jours' => '60 j',
                                        default => $client->modalite_paiement,
                                    } }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-active {{ $client->is_active ? 'on' : 'off' }}">
                                    <i class="fa-solid fa-circle" style="font-size:6px"></i>
                                    {{ $client->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="td-date">
                                {{ $client->created_at->format('d/m/Y') }}
                                <div class="td-date-sub">{{ $client->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('clients.edit', $client) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                        id="delete-form-{{ $client->id }}" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteClient({{ $client->id }}, '{{ addslashes($client->prenom . ' ' . $client->nom) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-users-slash"></i></div>
                                    <h3>Aucun client trouvé</h3>
                                    <p>{{ $activeFilters > 0 ? 'Aucun client ne correspond aux filtres appliqués.' : 'Commencez par ajouter votre premier client.' }}
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                            <i class="fa-solid fa-xmark"></i> Réinitialiser les filtres
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
        @if ($clients->hasPages())
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Affichage de <strong>{{ $clients->firstItem() }}–{{ $clients->lastItem() }}</strong>
                    sur <strong>{{ $clients->total() }}</strong> clients
                </div>
                <div class="pagination-links">
                    {{-- Précédent --}}
                    @if ($clients->onFirstPage())
                        <span class="page-btn disabled"><i class="fa-solid fa-chevron-left"
                                style="font-size:10px"></i></span>
                    @else
                        <a href="{{ $clients->previousPageUrl() }}" class="page-btn">
                            <i class="fa-solid fa-chevron-left" style="font-size:10px"></i>
                        </a>
                    @endif

                    {{-- Numéros de pages --}}
                    @foreach ($clients->getUrlRange(max(1, $clients->currentPage() - 2), min($clients->lastPage(), $clients->currentPage() + 2)) as $page => $url)
                        @if ($page == $clients->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Suivant --}}
                    @if ($clients->hasMorePages())
                        <a href="{{ $clients->nextPageUrl() }}" class="page-btn">
                            <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                        </a>
                    @else
                        <span class="page-btn disabled"><i class="fa-solid fa-chevron-right"
                                style="font-size:10px"></i></span>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function handleDeleteClient(id, name) {
            Swal.fire({
                title: 'Supprimer le chauffeur ?',
                text: `Êtes-vous sûr de vouloir supprimer "${name}" ? Cette action est irréversible.`,
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
