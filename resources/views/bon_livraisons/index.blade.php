{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES BONS DE LIVRAISON — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $bons       : LengthAwarePaginator
|   - $stats      : ['total', 'ce_mois', 'livres', 'en_cours']
|   - $commandes  : Collection (id, code_commande)
|   - $chauffeurs : Collection (id, code_drv, nom, prenom)
|
| ROUTES :
|   GET  /bon_livraisons                          → index
|   GET  /bon_livraisons/{bonLivraison}/pdf       → downloadPdf
--}}

@extends('layouts.app')

@section('title', 'Bons de livraison')
@section('page-title', 'Bons de livraison')
@section('page-subtitle', 'Suivi et édition des bons de livraison')

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

        /* ── TOOLBAR / TABLE ── */
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

        .table-scroll-wrap {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 540px
        }

        .table-scroll-wrap::-webkit-scrollbar {
            width: 5px;
            height: 5px
        }

        .table-scroll-wrap::-webkit-scrollbar-track {
            background: transparent
        }

        .table-scroll-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px
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

        /* BL number cell */
        .bl-cell {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .bl-icon {
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

        .bl-num {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--text-primary)
        }

        .bl-date {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 1px
        }

        /* Commande cell */
        .cmd-cell {
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .cmd-code {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--color-primary)
        }

        .cmd-client {
            font-size: 11px;
            color: var(--text-muted)
        }

        /* Vehicule cell */
        .veh-cell {
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .veh-matricule {
            font-weight: 700;
            font-size: 13px
        }

        .veh-info {
            font-size: 11px;
            color: var(--text-muted)
        }

        /* Chauffeur cell */
        .drv-cell {
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .drv-name {
            font-weight: 600;
            font-size: 13px
        }

        .drv-code {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace
        }

        /* Date livraison */
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

        .td-date-null {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic
        }

        /* Statut badges */
        .statut-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .3px;
            white-space: nowrap
        }

        .statut-badge.brouillon {
            background: rgba(107, 114, 128, .1);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .25)
        }

        .statut-badge.émis {
            background: rgba(59, 130, 246, .1);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, .25)
        }

        .statut-badge.livré {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .25)
        }

        .statut-badge.partiel {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .25)
        }

        .statut-badge.annulé {
            background: rgba(224, 32, 32, .08);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2)
        }

        /* Action buttons */
        .actions-cell {
            display: flex;
            gap: 4px
        }

        .btn-pdf {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 5px 10px;
            background: rgba(220, 38, 38, .08);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .25);
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: background var(--transition), border-color var(--transition);
            white-space: nowrap
        }

        .btn-pdf:hover {
            background: rgba(224, 32, 32, .15);
            border-color: var(--color-primary)
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

        @media(max-width:1100px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr
            }

            .filter-actions {
                grid-column: 1/-1;
                justify-content: flex-start
            }
        }

        @media(max-width:640px) {
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
        <span>Bons de livraison</span>
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
            <div class="kpi-value">{{ number_format($stats['total']) }}</div>
            <div class="kpi-label">Total BL</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['ce_mois']) }}</div>
            <div class="kpi-label">Ce mois</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['livres']) }}</div>
            <div class="kpi-label">Livrés</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['en_cours']) }}</div>
            <div class="kpi-label">Émis / En cours</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'statut' => request('statut'),
            'commande_id' => request('commande_id'),
            'chauffeur_id' => request('chauffeur_id'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
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

        <form method="GET" action="{{ route('bon_livraisons.index') }}">
            <div class="filters-grid">

                {{-- Recherche libre --}}
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="N° BL, code commande, matricule, chauffeur…" value="{{ request('search') }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        @foreach (\App\Models\BonLivraison::STATUTS as $key => $label)
                            <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Commande --}}
                <div class="filter-field">
                    <label>Commande</label>
                    <select name="commande_id" class="filter-select">
                        <option value="">Toutes les commandes</option>
                        @foreach ($commandes as $cmd)
                            <option value="{{ $cmd->id }}"
                                {{ request('commande_id') == $cmd->id ? 'selected' : '' }}>
                                {{ $cmd->code_commande }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Chauffeur --}}
                <div class="filter-field">
                    <label>Chauffeur</label>
                    <select name="chauffeur_id" class="filter-select">
                        <option value="">Tous les chauffeurs</option>
                        @foreach ($chauffeurs as $drv)
                            <option value="{{ $drv->id }}"
                                {{ request('chauffeur_id') == $drv->id ? 'selected' : '' }}>
                                {{ $drv->prenom }} {{ $drv->nom }} ({{ $drv->code_drv }})
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
                        <a href="{{ route('bon_livraisons.index') }}" class="btn-reset">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

            </div>

            {{-- Période livraison réelle (ligne 2) --}}
            <div style="display:grid;grid-template-columns:2fr auto;gap:10px;margin-top:10px;align-items:end">
                <div class="filter-field">
                    <label>Période de livraison réelle</label>
                    <div class="date-range-wrap">
                        <input type="date" name="date_from" class="filter-date-input"
                            value="{{ request('date_from') }}">
                        <span class="date-range-sep">→</span>
                        <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}">
                    </div>
                </div>
            </div>

            {{-- Badges actifs --}}
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
                    @if (request('statut'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-circle-dot" style="font-size:9px"></i>
                            Statut : {{ \App\Models\BonLivraison::STATUTS[request('statut')] ?? request('statut') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['statut', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('commande_id'))
                        @php $fc = $commandes->firstWhere('id', request('commande_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-file-invoice" style="font-size:9px"></i>
                            Commande : {{ $fc?->code_commande ?? '—' }}
                            <a href="{{ request()->fullUrlWithoutQuery(['commande_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('chauffeur_id'))
                        @php $fd = $chauffeurs->firstWhere('id', request('chauffeur_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-id-card" style="font-size:9px"></i>
                            Chauffeur : {{ $fd ? $fd->prenom . ' ' . $fd->nom : '—' }}
                            <a href="{{ request()->fullUrlWithoutQuery(['chauffeur_id', 'page']) }}"><i
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
                <h2 class="section-title">Liste des bons de livraison</h2>
                <span class="result-count">
                    <strong>{{ $bons->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('bon_livraisons.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Nouveau BL
            </a>
        </div>

        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:55px">#</th>
                        <th style="width:180px">N° BL</th>
                        <th style="width:170px">Commande</th>
                        <th style="width:160px">Véhicule</th>
                        <th style="width:160px">Chauffeur</th>
                        <th style="width:155px">Livraison réelle</th>
                        <th style="width:130px">Statut</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:130px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bons as $bon)
                        <tr>
                            <td class="td-id">#{{ $bon->id }}</td>

                            {{-- N° BL --}}
                            <td>
                                <div class="bl-cell">
                                    <div class="bl-icon"><i class="fa-solid fa-file-lines"></i></div>
                                    <div>
                                        <div class="bl-num">
                                            @if (request('search'))
                                                {!! preg_replace(
                                                    '/(' . preg_quote(request('search'), '/') . ')/i',
                                                    '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                    e($bon->num_bl),
                                                ) !!}
                                            @else
                                                {{ $bon->num_bl }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Commande --}}
                            <td>
                                <div class="cmd-cell">
                                    <span class="cmd-code">{{ $bon->commande->code_commande }}</span>
                                    <span class="cmd-client">{{ $bon->commande->client->nom ?? '—' }}</span>
                                </div>
                            </td>

                            {{-- Véhicule --}}
                            <td>
                                <div class="veh-cell">
                                    <span class="veh-matricule">{{ $bon->vehicule->matricule }}</span>
                                    <span class="veh-info">{{ $bon->vehicule->marque }} ·
                                        {{ $bon->vehicule->type_vehicule }}</span>
                                </div>
                            </td>

                            {{-- Chauffeur --}}
                            <td>
                                <div class="drv-cell">
                                    <span class="drv-name">{{ $bon->chauffeur->prenom }}
                                        {{ $bon->chauffeur->nom }}</span>
                                    <span class="drv-code">{{ $bon->chauffeur->code_drv }}</span>
                                </div>
                            </td>

                            {{-- Date livraison réelle --}}
                            <td>
                                @if ($bon->date_livraison_reelle)
                                    <div class="td-date">{{ $bon->date_livraison_reelle->format('d/m/Y') }}</div>
                                    <div class="td-date-sub">{{ $bon->date_livraison_reelle->format('H:i') }}</div>
                                @else
                                    <span class="td-date-null">Non renseignée</span>
                                @endif
                            </td>

                            {{-- Statut --}}
                            <td>
                                <span class="statut-badge {{ $bon->statut }}">
                                    @switch($bon->statut)
                                        @case('brouillon')
                                            <i class="fa-solid fa-pencil" style="font-size:8px"></i>
                                        @break

                                        @case('émis')
                                            <i class="fa-solid fa-paper-plane" style="font-size:8px"></i>
                                        @break

                                        @case('livré')
                                            <i class="fa-solid fa-circle-check" style="font-size:8px"></i>
                                        @break

                                        @case('partiel')
                                            <i class="fa-solid fa-circle-half-stroke" style="font-size:8px"></i>
                                        @break

                                        @case('annulé')
                                            <i class="fa-solid fa-ban" style="font-size:8px"></i>
                                        @break
                                    @endswitch
                                    {{ \App\Models\BonLivraison::STATUTS[$bon->statut] ?? $bon->statut }}
                                </span>
                            </td>

                            {{-- Créé le --}}
                            <td>
                                <div class="td-date">{{ $bon->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $bon->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="actions-cell">
                                    {{-- Télécharger PDF --}}
                                    <a href="{{ route('bon_livraisons.pdf', $bon->id) }}" class="btn-pdf"
                                        title="Télécharger le PDF" target="_blank">
                                        <i class="fa-solid fa-file-pdf"></i> PDF
                                    </a>

                                    {{-- Modifier --}}
                                    <a href="{{ route('bon_livraisons.edit', $bon) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>

                                    {{-- Supprimer --}}
                                    <form method="POST" action="{{ route('bon_livraisons.destroy', $bon) }}"
                                        id="delete-form-{{ $bon->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteBl({{ $bon->id }}, '{{ $bon->num_bl }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding:0;border:none">
                                    <div class="empty-state">
                                        <div class="empty-state-icon"><i class="fa-solid fa-file-circle-xmark"></i></div>
                                        <h3>
                                            @if ($activeFilters > 0)
                                                Aucun bon ne correspond aux filtres
                                            @else
                                                Aucun bon de livraison enregistré
                                            @endif
                                        </h3>
                                        <p>
                                            @if ($activeFilters > 0)
                                                Essayez de modifier ou d'effacer vos critères.
                                            @else
                                                Commencez par créer votre premier BL.
                                            @endif
                                        </p>
                                        @if ($activeFilters > 0)
                                            <a href="{{ route('bon_livraisons.index') }}" class="btn btn-primary"
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
            @if ($bons->hasPages())
                @php
                    $current = $bons->currentPage();
                    $last = $bons->lastPage();
                    $window = 2;
                    $from = max(1, $current - $window);
                    $to = min($last, $current + $window);
                    $qs = http_build_query(request()->except('page'));
                    $sep = $qs ? '&' : '';
                @endphp
                <div class="pagination-wrap">
                    <span class="pagination-info">
                        <strong>{{ $bons->firstItem() }}</strong>–<strong>{{ $bons->lastItem() }}</strong>
                        sur <strong>{{ $bons->total() }}</strong> bon(s)
                    </span>
                    <div class="pagination-links">
                        @if ($current > 1)
                            <a class="page-btn" href="{{ $bons->url(1) . $sep . $qs }}">
                                <i class="fa-solid fa-angles-left"></i>
                            </a>
                        @endif
                        <a class="page-btn {{ $bons->onFirstPage() ? 'disabled' : '' }}"
                            href="{{ $bons->previousPageUrl() ? $bons->previousPageUrl() . $sep . $qs : '#' }}">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                        @if ($from > 1)
                            <a class="page-btn" href="{{ $bons->url(1) . $sep . $qs }}">1</a>
                            @if ($from > 2)
                                <span class="page-btn disabled">…</span>
                            @endif
                        @endif

                        @for ($p = $from; $p <= $to; $p++)
                            <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                                href="{{ $bons->url($p) . $sep . $qs }}">{{ $p }}</a>
                        @endfor

                        @if ($to < $last)
                            @if ($to < $last - 1)
                                <span class="page-btn disabled">…</span>
                            @endif
                            <a class="page-btn" href="{{ $bons->url($last) . $sep . $qs }}">{{ $last }}</a>
                        @endif

                        <a class="page-btn {{ !$bons->hasMorePages() ? 'disabled' : '' }}"
                            href="{{ $bons->nextPageUrl() ? $bons->nextPageUrl() . $sep . $qs : '#' }}">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        @if ($current < $last)
                            <a class="page-btn" href="{{ $bons->url($last) . $sep . $qs }}">
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
            function handleDeleteBl(id, num) {
                Swal.fire({
                    title: 'Supprimer ce bon ?',
                    text: `Supprimer le BL "${num}" ? Cette action est irréversible.`,
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
