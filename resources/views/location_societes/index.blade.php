{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES SOCIÉTÉS DE LOCATION — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $societes : LengthAwarePaginator
|   - $stats    : ['total', 'actifs', 'en_attente', 'termines']
| ROUTE : GET /location-societes → LocationSocieteController@index
--}}

@extends('layouts.app')

@section('title', 'Sociétés de location')
@section('page-title', 'Sociétés de Location')
@section('page-subtitle', 'Gestion des contrats et sociétés partenaires')

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

        /* FILTRES */
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
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
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

        /* TABLE */
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
            max-height: 540px;
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

        .societe-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .societe-icon {
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

        .societe-nom {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .societe-email {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .badge-statut {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }

        .badge-statut.actif {
            background: rgba(16, 185, 129, .08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .badge-statut.en_attente {
            background: rgba(245, 158, 11, .08);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .2);
        }

        .badge-statut.termine {
            background: rgba(107, 114, 128, .08);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .2);
        }

        .contrat-dates {
            font-size: 12px;
            white-space: nowrap;
        }

        .contrat-date-row {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--text-secondary);
            margin-bottom: 2px;
        }

        .contrat-date-row i {
            font-size: 10px;
            color: var(--text-muted);
        }

        .expiry-warn {
            color: #d97706;
            font-weight: 600;
        }

        .expiry-ok {
            color: #059669;
        }

        .pdf-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            background: rgba(239, 68, 68, .06);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, .15);
            border-radius: 6px;
            text-decoration: none;
            transition: background var(--transition);
        }

        .pdf-badge:hover {
            background: rgba(239, 68, 68, .12);
        }

        .no-pdf {
            font-size: 11px;
            color: var(--text-muted);
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

        /* PAGINATION */
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

        /* EMPTY */
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

        /* FLASH */
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

        @media (max-width:1100px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1/-1;
            }
        }

        @media (max-width:640px) {
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

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Sociétés de location</span>
    </div>

    @if (session('success'))
        <div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    {{-- KPI --}}
    <div class="kpi-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] }}</div>
            <div class="kpi-label">Total sociétés</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#059669">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Contrats actifs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#d97706">{{ $stats['en_attente'] }}</div>
            <div class="kpi-label">En attente</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#6b7280">{{ $stats['termines'] }}</div>
            <div class="kpi-label">Terminés</div>
        </div>
    </div>

    {{-- FILTRES --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'statut' => request('statut'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
        ])
            ->filter()
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
        <form method="GET" action="{{ route('location_societes.index') }}">
            <div class="filters-grid">
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Nom de société, e-mail, téléphone…" value="{{ request('search') }}"
                            autocomplete="off">
                    </div>
                </div>
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>En attente
                        </option>
                        <option value="terminé" {{ request('statut') === 'terminé' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>
                <div class="filter-field" style="grid-column:span 2">
                    <label>Période du contrat (début → fin)</label>
                    <div class="date-range-wrap">
                        <input type="date" name="date_from" class="filter-date-input"
                            value="{{ request('date_from') }}">
                        <span class="date-range-sep">→</span>
                        <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filtrer</button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('location_societes.index') }}" class="btn-reset"><i
                                class="fa-solid fa-xmark"></i></a>
                    @endif
                </div>
            </div>
            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('search'))
                        <span class="active-filter-tag"><i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                            "{{ request('search') }}" <a
                                href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('statut'))
                        <span class="active-filter-tag"><i class="fa-solid fa-circle" style="font-size:9px"></i>
                            {{ ucfirst(str_replace('_', ' ', request('statut'))) }} <a
                                href="{{ request()->fullUrlWithoutQuery(['statut', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('date_from') || request('date_to'))
                        <span class="active-filter-tag"><i class="fa-solid fa-calendar" style="font-size:9px"></i>
                            @if (request('date_from') && request('date_to'))
                                Du {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }} au
                                {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                            @elseif (request('date_from'))
                                Début ≥ {{ \Carbon\Carbon::parse(request('date_from'))->format('d/m/Y') }}
                            @else
                                Fin ≤ {{ \Carbon\Carbon::parse(request('date_to'))->format('d/m/Y') }}
                            @endif
                            <a href="{{ request()->fullUrlWithoutQuery(['date_from', 'date_to', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="section-card">
        <div class="toolbar-row">
            <div class="toolbar-left">
                <h2 class="section-title">Liste des sociétés</h2>
                <span class="result-count"><strong>{{ $societes->total() }}</strong> résultat(s)</span>
            </div>
            <a href="{{ route('location_societes.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                Nouvelle société</a>
        </div>

        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Société</th>
                        <th style="width:130px">Téléphone</th>
                        <th style="width:185px">Durée du contrat</th>
                        <th style="width:105px">Contrat PDF</th>
                        <th style="width:115px">Statut</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($societes as $s)
                        @php
                            $today = \Carbon\Carbon::today();
                            $finDate = $s->date_fin_contrat ? \Carbon\Carbon::parse($s->date_fin_contrat) : null;
                            $expSoon = $finDate && $finDate->isFuture() && $finDate->diffInDays($today) <= 30;
                        @endphp
                        <tr>
                            <td class="td-id">#{{ $s->id }}</td>
                            <td>
                                <div class="societe-cell">
                                    <div class="societe-icon"><i class="fa-solid fa-building"></i></div>
                                    <div>
                                        <div class="societe-nom">
                                            @if (request('search'))
                                                {!! preg_replace(
                                                    '/(' . preg_quote(request('search'), '/') . ')/i',
                                                    '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                    e($s->nom_societe),
                                                ) !!}
                                            @else
                                                {{ $s->nom_societe }}
                                            @endif
                                        </div>
                                        @if ($s->email)
                                            <div class="societe-email">{{ $s->email }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $s->telephone ?? '—' }}</td>
                            <td>
                                <div class="contrat-dates">
                                    @if ($s->date_debut_contrat)
                                        <div class="contrat-date-row"><i class="fa-solid fa-play"></i>
                                            {{ \Carbon\Carbon::parse($s->date_debut_contrat)->format('d/m/Y') }}</div>
                                    @endif
                                    @if ($finDate)
                                        <div
                                            class="contrat-date-row {{ $expSoon ? 'expiry-warn' : ($finDate->isPast() ? '' : 'expiry-ok') }}">
                                            <i class="fa-solid fa-flag-checkered"></i> {{ $finDate->format('d/m/Y') }}
                                            @if ($expSoon)
                                                <span style="font-size:10px;font-style:italic">(expire bientôt)</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if (!$s->date_debut_contrat && !$finDate)
                                        <span style="color:var(--text-muted)">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($s->contrat_pdf_path)
                                    <a href="{{ Storage::url($s->contrat_pdf_path) }}" target="_blank"
                                        class="pdf-badge"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                                @else
                                    <span class="no-pdf">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $sc = match ($s->statut) {
                                        'actif' => 'actif',
                                        'en_attente' => 'en_attente',
                                        default => 'termine',
                                    };
                                    $si = match ($s->statut) {
                                        'actif' => 'fa-circle-check',
                                        'en_attente' => 'fa-clock',
                                        default => 'fa-circle-xmark',
                                    };
                                @endphp
                                <span class="badge-statut {{ $sc }}"><i class="fa-solid {{ $si }}"
                                        style="font-size:9px"></i> {{ ucfirst(str_replace('_', ' ', $s->statut)) }}</span>
                            </td>
                            <td>
                                <div class="td-date">{{ $s->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $s->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('location_societes.edit', $s) }}" class="btn-icon"
                                        title="Modifier"><i class="fa-solid fa-pen"></i></a>
                                    <form method="POST" action="{{ route('location_societes.destroy', $s) }}"
                                        id="delete-form-{{ $s->id }}" style="display:none">@csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDelete({{ $s->id }},'{{ addslashes($s->nom_societe) }}')"><i
                                            class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-building-circle-xmark"></i></div>
                                    <h3>{{ $activeFilters > 0 ? 'Aucune société ne correspond aux filtres' : 'Aucune société enregistrée' }}
                                    </h3>
                                    <p>{{ $activeFilters > 0 ? "Essayez de modifier ou d'effacer vos critères." : 'Commencez par ajouter votre première société de location.' }}
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('location_societes.index') }}" class="btn btn-primary"
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

        {{-- PAGINATION --}}
        @if ($societes->hasPages())
            @php
                $current = $societes->currentPage();
                $last = $societes->lastPage();
                $w = 2;
                $from = max(1, $current - $w);
                $to = min($last, $current + $w);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span
                    class="pagination-info"><strong>{{ $societes->firstItem() }}</strong>–<strong>{{ $societes->lastItem() }}</strong>
                    sur <strong>{{ $societes->total() }}</strong> société(s)</span>
                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $societes->url(1) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-left"></i></a>
                    @endif
                    <a class="page-btn {{ $societes->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $societes->previousPageUrl() ? $societes->previousPageUrl() . $sep . $qs : '#' }}"><i
                            class="fa-solid fa-chevron-left"></i></a>
                    @if ($from > 1)<a class="page-btn"
                            href="{{ $societes->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif
                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $societes->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor
                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $societes->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif
                    <a class="page-btn {{ !$societes->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $societes->nextPageUrl() ? $societes->nextPageUrl() . $sep . $qs : '#' }}"><i
                            class="fa-solid fa-chevron-right"></i></a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $societes->url($last) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-right"></i></a>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function handleDelete(id, name) {
            Swal.fire({
                title: 'Supprimer la société ?',
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
            }).then(r => {
                if (r.isConfirmed) document.getElementById('delete-form-' + id).submit()
            });
        }
    </script>
@endpush
