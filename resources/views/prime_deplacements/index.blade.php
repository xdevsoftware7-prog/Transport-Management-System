{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES PRIMES DE DÉPLACEMENT — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $primes        : LengthAwarePaginator
|   - $stats         : ['total', 'ce_mois', 'montant_moyen']
|   - $trajets       : Collection (statut=actif, avec villeDepart, villeDestination)
|   - $typesVehicule : array (clé => ['label', 'icon'])
|
| QUERY PARAMS : ?trajet_id= ?type_vehicule= ?montant_min= ?montant_max= ?page=
| ROUTE : GET /prime-deplacements → PrimeDeplacementController@index
| --}}

@extends('layouts.app')

@section('title', 'Primes de Déplacement')
@section('page-title', 'Primes de Déplacement')
@section('page-subtitle', 'Grille des primes par trajet et type de véhicule')

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
            grid-template-columns: 2fr 1.4fr 0.8fr 0.8fr auto;
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

        .filter-input:focus,
        .filter-select:focus,
        .filter-number:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .montant-range-wrap {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .montant-range-sep {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0
        }

        .montant-range-wrap .filter-number {
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

        /* Trajet cell */
        .trajet-cell {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .trajet-icon {
            width: 36px;
            height: 36px;
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

        .trajet-route {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 13px
        }

        .trajet-arrow {
            color: var(--text-muted);
            font-size: 10px
        }

        .trajet-meta {
            font-size: 10px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
            margin-top: 2px
        }

        /* Badge type véhicule — couleurs par type */
        .badge-veh {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            white-space: nowrap
        }

        .badge-veh.tracteur {
            background: rgba(99, 102, 241, .1);
            border: 1px solid rgba(99, 102, 241, .25);
            color: #4f46e5
        }

        .badge-veh.semi-remorque {
            background: rgba(139, 92, 246, .1);
            border: 1px solid rgba(139, 92, 246, .25);
            color: #7c3aed
        }

        .badge-veh.camion {
            background: rgba(59, 130, 246, .1);
            border: 1px solid rgba(59, 130, 246, .25);
            color: #2563eb
        }

        .badge-veh.fourgon {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            color: #059669
        }

        .badge-veh.benne {
            background: rgba(245, 158, 11, .1);
            border: 1px solid rgba(245, 158, 11, .25);
            color: #d97706
        }

        .badge-veh.citerne {
            background: rgba(6, 182, 212, .1);
            border: 1px solid rgba(6, 182, 212, .25);
            color: #0891b2
        }

        .badge-veh.frigo {
            background: rgba(147, 197, 253, .2);
            border: 1px solid rgba(96, 165, 250, .3);
            color: #1d4ed8
        }

        .badge-veh.plateau {
            background: rgba(107, 114, 128, .1);
            border: 1px solid rgba(107, 114, 128, .25);
            color: #4b5563
        }

        /* Badge montant */
        .badge-montant {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            font-weight: 800;
            padding: 5px 12px;
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            border-radius: 7px;
            color: #059669;
            font-family: 'JetBrains Mono', monospace
        }

        .badge-montant-unit {
            font-size: 10px;
            font-weight: 600;
            opacity: .7
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
        }

        @media(max-width:640px) {
            .filters-grid {
                grid-template-columns: 1fr
            }

            .montant-range-wrap {
                flex-direction: column
            }

            .montant-range-sep {
                display: none
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Primes de Déplacement</span>
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
            <div class="kpi-label">Total primes</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['ce_mois'] }}</div>
            <div class="kpi-label">Ajoutées ce mois</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['montant_moyen'], 2) }} MAD</div>
            <div class="kpi-label">Montant moyen</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'trajet_id' => request('trajet_id'),
            'type_vehicule' => request('type_vehicule'),
            'montant_min' => request('montant_min'),
            'montant_max' => request('montant_max'),
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

        <form method="GET" action="{{ route('prime_deplacements.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Trajet --}}
                <div class="filter-field">
                    <label>Trajet</label>
                    <select name="trajet_id" class="filter-select">
                        <option value="">Tous les trajets</option>
                        @foreach ($trajets as $trajet)
                            <option value="{{ $trajet->id }}" {{ request('trajet_id') == $trajet->id ? 'selected' : '' }}>
                                {{ $trajet->villeDepart?->nom }} → {{ $trajet->villeDestination?->nom }}
                                ({{ number_format($trajet->distance_km, 0) }} km)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type véhicule --}}
                <div class="filter-field">
                    <label>Type véhicule</label>
                    <select name="type_vehicule" class="filter-select">
                        <option value="">Tous les types</option>
                        @foreach ($typesVehicule as $key => $tv)
                            <option value="{{ $key }}" {{ request('type_vehicule') === $key ? 'selected' : '' }}>
                                {{ $tv['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Fourchette montant --}}
                <div class="filter-field" style="grid-column:span 2">
                    <label>Fourchette de montant (MAD)</label>
                    <div class="montant-range-wrap">
                        <input type="number" name="montant_min" class="filter-number" placeholder="Min"
                            value="{{ request('montant_min') }}" min="0" step="0.01">
                        <span class="montant-range-sep">→</span>
                        <input type="number" name="montant_max" class="filter-number" placeholder="Max"
                            value="{{ request('montant_max') }}" min="0" step="0.01">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('prime_deplacements.index') }}" class="btn-reset">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Badges filtres actifs --}}
            @if ($activeFilters > 0)
                <div class="active-filters">
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
                            {{ $typesVehicule[request('type_vehicule')]['label'] ?? request('type_vehicule') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['type_vehicule', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('montant_min') || request('montant_max'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-coins" style="font-size:9px"></i>
                            @if (request('montant_min') && request('montant_max'))
                                {{ number_format(request('montant_min'), 2) }} –
                                {{ number_format(request('montant_max'), 2) }} MAD
                            @elseif (request('montant_min'))
                                ≥ {{ number_format(request('montant_min'), 2) }} MAD
                            @else
                                ≤ {{ number_format(request('montant_max'), 2) }} MAD
                            @endif
                            <a href="{{ request()->fullUrlWithoutQuery(['montant_min', 'montant_max', 'page']) }}"><i
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
                <h2 class="section-title">Grille des primes</h2>
                <span class="result-count"><strong>{{ $primes->total() }}</strong> résultat(s)</span>
            </div>
            <a href="{{ route('prime_deplacements.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter une prime
            </a>
        </div>

        {{-- Scroll vertical + horizontal, headers sticky --}}
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="min-width:240px">Trajet</th>
                        <th style="min-width:160px">Type véhicule</th>
                        <th style="width:160px">Montant prime</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($primes as $prime)
                        <tr>
                            <td class="td-id">#{{ $prime->id }}</td>

                            {{-- Trajet --}}
                            <td>
                                <div class="trajet-cell">
                                    <div class="trajet-icon">
                                        <i class="fa-solid fa-route"></i>
                                    </div>
                                    <div>
                                        <div class="trajet-route">
                                            <span>{{ $prime->trajet->villeDepart?->nom ?? '?' }}</span>
                                            <span class="trajet-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                                            <span>{{ $prime->trajet->villeDestination?->nom ?? '?' }}</span>
                                        </div>
                                        <div class="trajet-meta">
                                            {{ number_format($prime->trajet->distance_km, 1) }} km
                                            · {{ $prime->trajet->duree_minutes }} min
                                            · Auto : {{ number_format($prime->trajet->prix_autoroute, 2) }} MAD
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Type véhicule --}}
                            <td>
                                @php
                                    $tv = $typesVehicule[$prime->type_vehicule] ?? [
                                        'label' => $prime->type_vehicule,
                                        'icon' => 'fa-truck',
                                    ];
                                    $cls = str_replace('-', '-', $prime->type_vehicule);
                                @endphp
                                <span class="badge-veh {{ $prime->type_vehicule }}">
                                    <i class="fa-solid {{ $tv['icon'] }}" style="font-size:10px"></i>
                                    {{ $tv['label'] }}
                                </span>
                            </td>

                            {{-- Montant --}}
                            <td>
                                <span class="badge-montant">
                                    {{ number_format($prime->montant_prime, 2) }}
                                    <span class="badge-montant-unit">MAD</span>
                                </span>
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date">{{ $prime->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $prime->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('prime_deplacements.edit', $prime) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('prime_deplacements.destroy', $prime) }}"
                                        id="delete-form-{{ $prime->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeletePrime({{ $prime->id }}, '{{ addslashes($prime->trajet->villeDepart?->nom ?? '') }} → {{ addslashes($prime->trajet->villeDestination?->nom ?? '') }}', '{{ addslashes($tv['label']) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-star-half-stroke"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucune prime ne correspond aux filtres
                                        @else
                                            Aucune prime enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Modifiez ou effacez vos critères.
                                        @else
                                            Commencez par créer votre première prime de déplacement.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('prime_deplacements.index') }}" class="btn btn-primary"
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

        {{-- ── PAGINATION (en dehors du scroll, sous la table) ── --}}
        @if ($primes->hasPages())
            @php
                $current = $primes->currentPage();
                $last = $primes->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $primes->firstItem() }}</strong>–<strong>{{ $primes->lastItem() }}</strong>
                    sur <strong>{{ $primes->total() }}</strong> prime(s)
                </span>
                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $primes->url(1) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-left"></i></a>
                    @endif
                    <a class="page-btn {{ $primes->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $primes->previousPageUrl() ? $primes->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    @if ($from > 1)
                        <a class="page-btn" href="{{ $primes->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif
                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $primes->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor
                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $primes->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif
                    <a class="page-btn {{ !$primes->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $primes->nextPageUrl() ? $primes->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $primes->url($last) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-right"></i></a>
                    @endif
                </div>
            </div>
        @endif

    </div>

@endsection

@push('scripts')
    <script>
        function handleDeletePrime(id, trajet, vehicule) {
            Swal.fire({
                title: 'Supprimer cette prime ?',
                text: `Prime ${vehicule} — ${trajet} · Action irréversible.`,
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
