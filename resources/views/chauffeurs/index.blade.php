{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES CHAUFFEURS — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $chauffeurs : LengthAwarePaginator (paginate avec filtres)
|   - $stats      : ['total' => int, 'actifs' => int, 'ce_mois' => int]
|
| QUERY PARAMS :
|   ?search=    filtrage nom / prénom / code / CIN / tél
|   ?statut=    actif | inactif | suspendu
|   ?date_from= date début (Y-m-d)
|   ?date_to=   date fin   (Y-m-d)
|   ?page=      pagination
|
| ROUTE : GET /chauffeurs → ChauffeurController@index
| --}}

@extends('layouts.app')

@section('title', 'Chauffeurs')
@section('page-title', 'Gestion des Chauffeurs')
@section('page-subtitle', 'Référentiel des chauffeurs et leurs informations')

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
            gap: 5px;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
            white-space: nowrap;
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

        .drv-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .drv-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--color-primary);
            font-weight: 700;
            flex-shrink: 0;
        }

        .drv-name {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .drv-code {
            font-size: 11px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
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

        /* Statut badges */
        .statut-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
        }

        .statut-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .statut-actif {
            background: rgba(16, 185, 129, .1);
            color: #059669;
        }

        .statut-actif .dot {
            background: #10b981;
        }

        .statut-inactif {
            background: rgba(107, 114, 128, .1);
            color: #6b7280;
        }

        .statut-inactif .dot {
            background: #9ca3af;
        }

        .statut-suspendu {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
        }

        .statut-suspendu .dot {
            background: #f59e0b;
        }

        /* Expiration */
        .exp-warn {
            color: #d97706;
            font-weight: 600;
        }

        .exp-danger {
            color: var(--color-primary);
            font-weight: 700;
        }

        /* Salaire */
        .td-salary {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 600;
        }

        /* Pagination */
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

        /* Empty */
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

        @media (max-width:1100px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1/-1;
                justify-content: flex-start;
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

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Chauffeurs</span>
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
            <div class="kpi-label">Total chauffeurs</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['actifs'] }}</div>
            <div class="kpi-label">Actifs</div>
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
                <i class="fa-solid fa-filter"></i>
                Filtres
                @if ($activeFilters > 0)
                    <span class="filters-active-count">{{ $activeFilters }}</span>
                @endif
            </span>
        </div>

        <form method="GET" action="{{ route('chauffeurs.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Recherche --}}
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Nom, prénom, code, CIN, téléphone…" value="{{ request('search') }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Statut --}}
                <div class="filter-field">
                    <label>Statut</label>
                    <select name="statut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspendu" {{ request('statut') == 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>

                {{-- Période de création --}}
                <div class="filter-field" style="grid-column:span 2">
                    <label>Période de création</label>
                    <div class="date-range-wrap">
                        <input type="date" name="date_from" class="filter-date-input" value="{{ request('date_from') }}"
                            placeholder="Date début">
                        <span class="date-range-sep">→</span>
                        <input type="date" name="date_to" class="filter-date-input" value="{{ request('date_to') }}"
                            placeholder="Date fin">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="filter-actions">
                    <button type="submit" class="btn-filter">
                        <i class="fa-solid fa-filter"></i> Filtrer
                    </button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('chauffeurs.index') }}" class="btn-reset">
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
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        </span>
                    @endif
                    @if (request('statut'))
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-circle-dot" style="font-size:9px"></i>
                            Statut : {{ ucfirst(request('statut')) }}
                            <a href="{{ request()->fullUrlWithoutQuery(['statut', 'page']) }}">
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
                </div>
            @endif
        </form>
    </div>

    {{-- ── TABLEAU ── --}}
    <div class="section-card">

        <div class="toolbar-row">
            <div class="toolbar-left">
                <h2 class="section-title">Liste des chauffeurs</h2>
                <span class="result-count">
                    <strong>{{ $chauffeurs->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('chauffeurs.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:55px">#</th>
                        <th style="width:90px">Code</th>
                        <th>Chauffeur</th>
                        <th style="width:130px">Téléphone</th>
                        <th style="width:110px">CIN</th>
                        <th style="width:120px">Exp. CIN</th>
                        <th style="width:120px">Exp. Permis</th>
                        <th style="width:120px">Salaire Net</th>
                        <th style="width:120px">Salaire Brut</th>
                        <th style="width:110px">Statut</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:130px">Modifié le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chauffeurs as $drv)
                        @php
                            $expCin = \Carbon\Carbon::parse($drv->date_exp_cin);
                            $expPermis = \Carbon\Carbon::parse($drv->date_exp_permis);
                            $cinExpClass = $expCin->isPast()
                                ? 'exp-danger'
                                : ($expCin->diffInDays(now()) <= 30
                                    ? 'exp-warn'
                                    : '');
                            $permisExpClass = $expPermis->isPast()
                                ? 'exp-danger'
                                : ($expPermis->diffInDays(now()) <= 30
                                    ? 'exp-warn'
                                    : '');
                            $initials = strtoupper(substr($drv->prenom, 0, 1) . substr($drv->nom, 0, 1));
                        @endphp
                        <tr>
                            <td class="td-id">#{{ $drv->id }}</td>

                            {{-- Code --}}
                            <td>
                                <span
                                    style="font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;
                                             padding:2px 7px;background:var(--bg-body);border:1px solid var(--border);
                                             border-radius:5px;color:var(--text-secondary);">
                                    {{ $drv->code_drv }}
                                </span>
                            </td>

                            {{-- Nom / Prénom --}}
                            <td>
                                <div class="drv-cell">
                                    <div class="drv-avatar">{{ $initials }}</div>
                                    <div>
                                        <div class="drv-name">
                                            @if (request('search'))
                                                {!! preg_replace(
                                                    '/(' . preg_quote(request('search'), '/') . ')/i',
                                                    '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                    e($drv->prenom . ' ' . $drv->nom),
                                                ) !!}
                                            @else
                                                {{ $drv->prenom }} {{ $drv->nom }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Téléphone --}}
                            <td style="font-family:'JetBrains Mono',monospace;font-size:12px;">
                                {{ $drv->telephone }}
                            </td>

                            {{-- CIN --}}
                            <td style="font-family:'JetBrains Mono',monospace;font-size:12px;">
                                {{ $drv->cin }}
                                @if ($drv->cin_path)
                                    <a href="{{ Storage::url($drv->cin_path) }}" target="_blank" title="Voir scan CIN"
                                        style="margin-left:5px;color:var(--color-primary);font-size:11px;">
                                        <i class="fa-solid fa-file-image"></i>
                                    </a>
                                @endif
                            </td>

                            {{-- Exp. CIN --}}
                            <td class="td-date {{ $cinExpClass }}">
                                {{ $expCin->format('d/m/Y') }}
                                @if ($expCin->isPast())
                                    <div style="font-size:10px;">Expirée</div>
                                @elseif ($expCin->diffInDays(now()) <= 30)
                                    <div style="font-size:10px;">{{ $expCin->diffInDays(now()) }}j restant(s)</div>
                                @endif
                            </td>

                            {{-- Exp. Permis --}}
                            <td class="td-date {{ $permisExpClass }}">
                                {{ $expPermis->format('d/m/Y') }}
                                @if ($expPermis->isPast())
                                    <div style="font-size:10px;">Expiré</div>
                                @elseif ($expPermis->diffInDays(now()) <= 30)
                                    <div style="font-size:10px;">{{ $expPermis->diffInDays(now()) }}j restant(s)</div>
                                @endif
                            </td>

                            {{-- Salaire net --}}
                            <td class="td-salary">
                                {{ number_format($drv->salaire_net, 2, ',', ' ') }}
                                <span style="font-size:10px;font-weight:400;color:var(--text-muted);">MAD</span>
                            </td>

                            {{-- Salaire brut --}}
                            <td class="td-salary">
                                {{ number_format($drv->salaire_brut, 2, ',', ' ') }}
                                <span style="font-size:10px;font-weight:400;color:var(--text-muted);">MAD</span>
                            </td>

                            {{-- Statut --}}
                            <td>
                                <span class="statut-badge statut-{{ $drv->statut }}">
                                    <span class="dot"></span>
                                    {{ ucfirst($drv->statut) }}
                                </span>
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date">{{ $drv->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $drv->created_at->format('H:i') }}</div>
                            </td>

                            {{-- updated_at --}}
                            <td>
                                <div class="td-date">{{ $drv->updated_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $drv->updated_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('chauffeurs.edit', $drv) }}" class="btn-icon" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('chauffeurs.destroy', $drv) }}"
                                        id="delete-form-{{ $drv->id }}" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteChauffeur({{ $drv->id }}, '{{ addslashes($drv->prenom . ' ' . $drv->nom) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-id-card"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucun chauffeur ne correspond aux filtres
                                        @else
                                            Aucun chauffeur enregistré
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères.
                                        @else
                                            Commencez par ajouter votre premier chauffeur.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('chauffeurs.index') }}" class="btn btn-primary"
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
        @if ($chauffeurs->hasPages())
            @php
                $current = $chauffeurs->currentPage();
                $last = $chauffeurs->lastPage();
                $window = 2;
                $from = max(1, $current - $window);
                $to = min($last, $current + $window);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $chauffeurs->firstItem() }}</strong>–<strong>{{ $chauffeurs->lastItem() }}</strong>
                    sur <strong>{{ $chauffeurs->total() }}</strong> chauffeur(s)
                </span>

                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $chauffeurs->url(1) . $sep . $qs }}">
                            <i class="fa-solid fa-angles-left"></i>
                        </a>
                    @endif

                    <a class="page-btn {{ $chauffeurs->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $chauffeurs->previousPageUrl() ? $chauffeurs->previousPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>

                    @if ($from > 1)
                        <a class="page-btn" href="{{ $chauffeurs->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif

                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $chauffeurs->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor

                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $chauffeurs->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif

                    <a class="page-btn {{ !$chauffeurs->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $chauffeurs->nextPageUrl() ? $chauffeurs->nextPageUrl() . $sep . $qs : '#' }}">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>

                    @if ($current < $last)
                        <a class="page-btn" href="{{ $chauffeurs->url($last) . $sep . $qs }}">
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
        function handleDeleteChauffeur(id, name) {
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
