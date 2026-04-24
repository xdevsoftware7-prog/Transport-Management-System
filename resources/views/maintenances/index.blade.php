{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES MAINTENANCES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $maintenances  : LengthAwarePaginator
|   - $stats         : ['total', 'en_attente', 'en_cours', 'terminees']
|   - $vehicules     : Collection (filtre)
|   - $activeFilters : int
|
| QUERY PARAMS :
|   ?search=      type_intervention
|   ?vehicule_id= filtrage par véhicule
|   ?statut=      en_attente|en_cours|terminée
|   ?date_from=   date début
|   ?date_to=     date fin
|   ?page=        pagination
|
| ROUTE : GET /maintenances → MaintenanceController@index
--}}

@extends('layouts.app')

@section('title', 'Maintenances')
@section('page-title', 'Gestion des Maintenances')
@section('page-subtitle', 'Suivi des interventions et entretiens des véhicules')

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
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
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

        /* Cellule véhicule */
        .vehicule-cell {
            display: flex;
            align-items: center;
            gap: 10px
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
            flex-shrink: 0
        }

        .vehicule-matricule {
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px
        }

        .vehicule-marque {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px
        }

        /* Badge statut */
        .statut-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            white-space: nowrap
        }

        .statut-badge i {
            font-size: 7px
        }

        .statut-en_attente {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .25)
        }

        .statut-en_cours {
            background: rgba(59, 130, 246, .1);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, .25)
        }

        .statut-terminee {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .25)
        }

        /* Coût */
        .cout-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary)
        }

        .cout-null {
            color: var(--text-muted);
            font-style: italic;
            font-size: 12px
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

        @media (max-width:1100px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr
            }

            .filter-actions {
                grid-column: 1/-1;
                justify-content: flex-start
            }
        }

        @media (max-width:640px) {
            .filters-grid {
                grid-template-columns: 1fr
            }

            .date-range-wrap {
                flex-direction: column;
                gap: 8px
            }

            .date-range-sep {
                display: none
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Maintenances</span>
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
            <div class="kpi-label">Total interventions</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#d97706">{{ $stats['en_attente'] }}</div>
            <div class="kpi-label">En attente</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#2563eb">{{ $stats['en_cours'] }}</div>
            <div class="kpi-label">En cours</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#059669">{{ $stats['terminees'] }}</div>
            <div class="kpi-label">Terminées</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
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

        <form method="GET" action="{{ route('maintenances.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Recherche intervention --}}
                <div class="filter-field">
                    <label>Type d'intervention</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Rechercher une intervention…" value="{{ request('search') }}" autocomplete="off">
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

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente
                        </option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours
                        </option>
                        <option value="terminée" {{ request('statut') == 'terminée' ? 'selected' : '' }}>Terminée
                        </option>
                    </select>
                </div>

                {{-- Période --}}
                <div class="filter-field">
                    <label>Période (date début)</label>
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
                        <a href="{{ route('maintenances.index') }}" class="btn-reset">
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
                            Intervention : "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('vehicule_id'))
                        @php $vLabel = $vehicules->firstWhere('id', request('vehicule_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-truck" style="font-size:9px"></i>
                            Véhicule : {{ $vLabel ? $vLabel->matricule : request('vehicule_id') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['vehicule_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('statut'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-circle-half-stroke" style="font-size:9px"></i>
                            Statut :
                            {{ ['en_attente' => 'En attente', 'en_cours' => 'En cours', 'terminée' => 'Terminée'][request('statut')] ?? request('statut') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['statut', 'page']) }}"><i
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
                <h2 class="section-title">Liste des maintenances</h2>
                <span class="result-count">
                    <strong>{{ $maintenances->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('maintenances.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Véhicule</th>
                        <th>Type d'intervention</th>
                        <th style="width:130px">Coût total</th>
                        <th style="width:130px">Statut</th>
                        <th style="width:120px">Date début</th>
                        <th style="width:120px">Date fin</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenances as $m)
                        <tr>
                            <td class="td-id">#{{ $m->id }}</td>

                            {{-- Véhicule --}}
                            <td>
                                <div class="vehicule-cell">
                                    <div class="vehicule-icon"><i class="fa-solid fa-truck"></i></div>
                                    <div>
                                        <div class="vehicule-matricule">{{ $m->vehicule->matricule }}</div>
                                        <div class="vehicule-marque">{{ $m->vehicule->marque }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type intervention --}}
                            <td>
                                @if (request('search'))
                                    {!! preg_replace(
                                        '/(' . preg_quote(request('search'), '/') . ')/i',
                                        '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                        e($m->type_intervention),
                                    ) !!}
                                @else
                                    {{ $m->type_intervention }}
                                @endif
                            </td>

                            {{-- Coût --}}
                            <td>
                                @if ($m->cout_total !== null)
                                    <span class="cout-value">{{ number_format($m->cout_total, 2, ',', ' ') }} MAD</span>
                                @else
                                    <span class="cout-null">—</span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td>
                                @php
                                    $sc = match ($m->statut) {
                                        'en_attente' => 'statut-en_attente',
                                        'en_cours' => 'statut-en_cours',
                                        'terminée' => 'statut-terminee',
                                        default => '',
                                    };
                                    $si = match ($m->statut) {
                                        'en_attente' => 'fa-clock',
                                        'en_cours' => 'fa-spinner',
                                        'terminée' => 'fa-circle-check',
                                        default => 'fa-circle',
                                    };
                                @endphp
                                <span class="statut-badge {{ $sc }}">
                                    <i class="fa-solid {{ $si }}"></i>
                                    {{ $m->statut_label }}
                                </span>
                            </td>

                            {{-- Date début --}}
                            <td>
                                <div class="td-date">{{ $m->date_debut->format('d/m/Y') }}</div>
                            </td>

                            {{-- Date fin --}}
                            <td>
                                @if ($m->date_fin)
                                    <div class="td-date">{{ $m->date_fin->format('d/m/Y') }}</div>
                                @else
                                    <span class="cout-null">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('maintenances.edit', $m) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('maintenances.destroy', $m) }}"
                                        id="delete-form-{{ $m->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteMaintenance({{ $m->id }}, '{{ addslashes($m->type_intervention) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-wrench"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucune maintenance ne correspond aux filtres
                                        @else
                                            Aucune maintenance enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères.
                                        @else
                                            Commencez par enregistrer votre première intervention.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('maintenances.index') }}" class="btn btn-primary"
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
        @if ($maintenances->hasPages())
            @php
                $current = $maintenances->currentPage();
                $last = $maintenances->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $maintenances->firstItem() }}</strong>–<strong>{{ $maintenances->lastItem() }}</strong>
                    sur <strong>{{ $maintenances->total() }}</strong> maintenance(s)
                </span>
                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $maintenances->url(1) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-left"></i></a>
                    @endif
                    <a class="page-btn {{ $maintenances->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $maintenances->previousPageUrl() ? $maintenances->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    @if ($from > 1)
                        <a class="page-btn" href="{{ $maintenances->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif
                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $maintenances->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor
                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $maintenances->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif
                    <a class="page-btn {{ !$maintenances->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $maintenances->nextPageUrl() ? $maintenances->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $maintenances->url($last) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-right"></i></a>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function handleDeleteMaintenance(id, label) {
            Swal.fire({
                title: 'Supprimer la maintenance ?',
                text: `Êtes-vous sûr de vouloir supprimer "${label}" ? Cette action est irréversible.`,
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
            }).then(result => {
                if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
            });
        }
    </script>
@endpush
