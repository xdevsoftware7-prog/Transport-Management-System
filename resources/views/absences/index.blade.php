{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES ABSENCES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $absences  : LengthAwarePaginator (paginate avec filtres)
|   - $stats     : ['total' => int, 'ce_mois' => int, 'heures_sup' => float]
|   - $chauffeurs: Collection des chauffeurs (pour le filtre)
|
| QUERY PARAMS :
|   ?search=       filtrage par motif
|   ?chauffeur_id= filtrage par chauffeur
|   ?date_from=    date début (Y-m-d)
|   ?date_to=      date fin   (Y-m-d)
|   ?page=         pagination
|
| ROUTE : GET /absences → AbsenceController@index
| --}}

@extends('layouts.app')

@section('title', 'Absences')
@section('page-title', 'Gestion des Absences')
@section('page-subtitle', 'Suivi des absences et heures supplémentaires des chauffeurs')

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
            grid-template-columns: 2fr 1.5fr 1fr 1fr auto;
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

        /* Badges filtres actifs */
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

        /* Scroll sous la table */
        .table-scroll-wrap {
            overflow-x: auto;
            max-height: 520px;
            overflow-y: auto;
        }

        .table-scroll-wrap::-webkit-scrollbar {
            width: 5px;
            height: 5px;
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

        /* Chauffeur cell */
        .chauffeur-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chauffeur-avatar {
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

        .chauffeur-name {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .chauffeur-code {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Badges */
        .badge-time {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace;
        }

        .badge-sup {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            border-radius: 5px;
            color: #059669;
            font-family: 'JetBrains Mono', monospace;
        }

        .badge-sup-zero {
            background: var(--bg-body);
            border-color: var(--border);
            color: var(--text-muted);
        }

        .td-date {
            font-size: 13px;
            color: var(--text-primary);
            white-space: nowrap;
            font-weight: 600;
        }

        .td-date-sub {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .motif-text {
            font-size: 12px;
            color: var(--text-secondary);
            max-width: 180px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .motif-empty {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic;
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

        /* Flash */
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
            }
        }

        @media (max-width: 640px) {
            .filters-grid {
                grid-template-columns: 1fr;
            }

            .date-range-wrap {
                flex-direction: column;
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
        <span>Absences</span>
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
            <div class="kpi-label">Total absences</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['ce_mois'] }}</div>
            <div class="kpi-label">Ce mois-ci</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['heures_sup'], 2) }}h</div>
            <div class="kpi-label">Heures sup. cumulées</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
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

        <form method="GET" action="{{ route('absences.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Motif --}}
                <div class="filter-field">
                    <label>Motif</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Rechercher un motif…" value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                {{-- Chauffeur --}}
                <div class="filter-field">
                    <label>Chauffeur</label>
                    <select name="chauffeur_id" class="filter-select">
                        <option value="">Tous les chauffeurs</option>
                        @foreach ($chauffeurs as $chauffeur)
                            <option value="{{ $chauffeur->id }}"
                                {{ request('chauffeur_id') == $chauffeur->id ? 'selected' : '' }}>
                                {{ $chauffeur->nom }} {{ $chauffeur->prenom }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Période --}}
                <div class="filter-field" style="grid-column:span 2">
                    <label>Période d'absence</label>
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
                        <a href="{{ route('absences.index') }}" class="btn-reset">
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
                            Motif : "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('chauffeur_id'))
                        @php $c = $chauffeurs->firstWhere('id', request('chauffeur_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-user" style="font-size:9px"></i>
                            {{ $c ? $c->nom . ' ' . $c->prenom : '—' }}
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
                <h2 class="section-title">Liste des absences</h2>
                <span class="result-count">
                    <strong>{{ $absences->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('absences.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        {{-- Scroll conteneur (filtrage + pagination EN DESSOUS) --}}
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Chauffeur</th>
                        <th style="width:130px">Date absence</th>
                        <th style="width:110px">Entrée</th>
                        <th style="width:110px">Sortie</th>
                        <th style="width:100px">H. Sup.</th>
                        <th>Motif</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($absences as $absence)
                        <tr>
                            <td class="td-id">#{{ $absence->id }}</td>

                            {{-- Chauffeur --}}
                            <td>
                                <div class="chauffeur-cell">
                                    <div class="chauffeur-avatar">
                                        <i class="fa-solid fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="chauffeur-name">{{ $absence->chauffeur->nom }}
                                            {{ $absence->chauffeur->prenom }}</div>
                                        <div class="chauffeur-code">{{ $absence->chauffeur->code_drv }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td>
                                <div class="td-date">{{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}
                                </div>
                                <div class="td-date-sub">
                                    {{ \Carbon\Carbon::parse($absence->date_absence)->translatedFormat('l') }}</div>
                            </td>

                            {{-- Heure entrée --}}
                            <td>
                                <span class="badge-time">
                                    <i class="fa-regular fa-clock" style="font-size:9px"></i>
                                    {{ \Carbon\Carbon::parse($absence->heure_entree)->format('H:i') }}
                                </span>
                            </td>

                            {{-- Heure sortie --}}
                            <td>
                                <span class="badge-time">
                                    <i class="fa-regular fa-clock" style="font-size:9px"></i>
                                    {{ \Carbon\Carbon::parse($absence->heure_sortie)->format('H:i') }}
                                </span>
                            </td>

                            {{-- Heures sup --}}
                            <td>
                                @if ($absence->heures_sup > 0)
                                    <span class="badge-sup">
                                        <i class="fa-solid fa-plus" style="font-size:8px"></i>
                                        {{ number_format($absence->heures_sup, 2) }}h
                                    </span>
                                @else
                                    <span class="badge-sup badge-sup-zero">—</span>
                                @endif
                            </td>

                            {{-- Motif --}}
                            <td>
                                @if ($absence->motif)
                                    <span class="motif-text" title="{{ $absence->motif }}">
                                        @if (request('search'))
                                            {!! preg_replace(
                                                '/(' . preg_quote(request('search'), '/') . ')/i',
                                                '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                e($absence->motif),
                                            ) !!}
                                        @else
                                            {{ $absence->motif }}
                                        @endif
                                    </span>
                                @else
                                    <span class="motif-empty">Non renseigné</span>
                                @endif
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date" style="font-size:12px;font-weight:400">
                                    {{ $absence->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $absence->created_at->format('H:i') }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('absences.edit', $absence) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('absences.destroy', $absence) }}"
                                        id="delete-form-{{ $absence->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteAbsence({{ $absence->id }}, '{{ $absence->chauffeur->nom }} {{ $absence->chauffeur->prenom }}', '{{ \Carbon\Carbon::parse($absence->date_absence)->format('d/m/Y') }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucune absence ne correspond aux filtres
                                        @else
                                            Aucune absence enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères.
                                        @else
                                            Commencez par enregistrer la première absence.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('absences.index') }}" class="btn btn-primary"
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
        @if ($absences->hasPages())
            @php
                $current = $absences->currentPage();
                $last = $absences->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $absences->firstItem() }}</strong>–<strong>{{ $absences->lastItem() }}</strong>
                    sur <strong>{{ $absences->total() }}</strong> absence(s)
                </span>

                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $absences->url(1) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    @endif

                    <a class="page-btn {{ $absences->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $absences->previousPageUrl() ? $absences->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    @if ($from > 1)
                        <a class="page-btn" href="{{ $absences->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif

                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $absences->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor

                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $absences->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif

                    <a class="page-btn {{ !$absences->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $absences->nextPageUrl() ? $absences->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    @if ($current < $last)
                        <a class="page-btn" href="{{ $absences->url($last) . $sep . $qs }}">
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
        function handleDeleteAbsence(id, chauffeur, date) {
            Swal.fire({
                title: 'Supprimer l\'absence ?',
                text: `Absence du ${date} pour ${chauffeur} — cette action est irréversible.`,
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
