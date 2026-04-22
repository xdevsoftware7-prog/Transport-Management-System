{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES SEMI-REMORQUES — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $remorques     : LengthAwarePaginator
|   - $stats         : ['total','actives','inactives','ce_mois']
|   - $marques       : Collection des marques distinctes
|   - $typesRemorque : Collection des types distincts
|
| QUERY PARAMS :
|   ?search=        matricule / marque / vin / type
|   ?marque=        filtrer par marque
|   ?type_remorque= filtrer par type
|   ?is_active=     1 | 0
|   ?page=          pagination
|
| ROUTE : GET /semi-remorques → SemiRemorqueController@index
--}}

@extends('layouts.app')

@section('title', 'Semi-Remorques')
@section('page-title', 'Semi-Remorques')
@section('page-subtitle', 'Gestion du parc de semi-remorques')

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
        .filter-select {
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
        .filter-select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .filter-select {
            cursor: pointer
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

        /* Scroll vertical + horizontal EN DESSOUS du tableau */
        .table-scroll-wrap {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 520px
        }

        .table-scroll-wrap::-webkit-scrollbar {
            height: 6px;
            width: 6px
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
            position: sticky;
            top: 0;
            z-index: 2
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

        .td-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: .5px
        }

        .td-mono-sm {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--text-secondary)
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

        .remorque-cell {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .remorque-icon {
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

        .remorque-matricule {
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            letter-spacing: .5px
        }

        .remorque-marque {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px
        }

        .badge-type {
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
            letter-spacing: .3px
        }

        .ptac-cell {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary)
        }

        .ptac-unit {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 400;
            font-family: 'DM Sans', sans-serif;
            margin-left: 2px
        }

        .badge-active {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px
        }

        .badge-active.on {
            background: rgba(16, 185, 129, .08);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2)
        }

        .badge-active.off {
            background: rgba(107, 114, 128, .08);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .2)
        }

        .actions-cell {
            display: flex;
            gap: 4px;
            align-items: center
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
            transition: border-color var(--transition), color var(--transition), background var(--transition)
        }

        .btn-icon:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim)
        }

        .btn-icon--danger:hover {
            border-color: #ef4444;
            color: #ef4444;
            background: rgba(239, 68, 68, .06)
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

        @media(max-width:1100px) {
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
        }
    </style>

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Semi-Remorques</span>
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
            <div class="kpi-label">Total remorques</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#059669">{{ $stats['actives'] }}</div>
            <div class="kpi-label">Actives</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#6b7280">{{ $stats['inactives'] }}</div>
            <div class="kpi-label">Inactives</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['ce_mois'] }}</div>
            <div class="kpi-label">Ajoutées ce mois</div>
        </div>
    </div>

    {{-- FILTRES --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'marque' => request('marque'),
            'type_remorque' => request('type_remorque'),
            'is_active' => request()->has('is_active') && request('is_active') !== '' ? request('is_active') : null,
        ])
            ->filter(fn($v) => $v !== null && $v !== '')
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
        <form method="GET" action="{{ route('semi_remorques.index') }}">
            <div class="filters-grid">
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Matricule, marque, VIN, type…" value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                <div class="filter-field">
                    <label>Marque</label>
                    <select name="marque" class="filter-select">
                        <option value="">Toutes les marques</option>
                        @foreach ($marques as $m)
                            <option value="{{ $m }}" {{ request('marque') === $m ? 'selected' : '' }}>
                                {{ $m }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label>Type remorque</label>
                    <select name="type_remorque" class="filter-select">
                        <option value="">Tous les types</option>
                        @foreach ($typesRemorque as $t)
                            <option value="{{ $t }}" {{ request('type_remorque') === $t ? 'selected' : '' }}>
                                {{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-field">
                    <label>Statut</label>
                    <select name="is_active" class="filter-select">
                        <option value="">Tous</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Filtrer</button>
                    @if ($activeFilters > 0)
                        <a href="{{ route('semi_remorques.index') }}" class="btn-reset"><i
                                class="fa-solid fa-xmark"></i></a>
                    @endif
                </div>
            </div>

            @if ($activeFilters > 0)
                <div class="active-filters">
                    @if (request('search'))
                        <span class="active-filter-tag"><i class="fa-solid fa-magnifying-glass" style="font-size:9px"></i>
                            "{{ request('search') }}" <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('marque'))
                        <span class="active-filter-tag"><i class="fa-solid fa-industry" style="font-size:9px"></i>
                            {{ request('marque') }} <a href="{{ request()->fullUrlWithoutQuery(['marque', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('type_remorque'))
                        <span class="active-filter-tag"><i class="fa-solid fa-trailer" style="font-size:9px"></i>
                            {{ request('type_remorque') }} <a
                                href="{{ request()->fullUrlWithoutQuery(['type_remorque', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                    @if (request('is_active') !== null && request('is_active') !== '')
                        <span class="active-filter-tag"><i class="fa-solid fa-circle" style="font-size:9px"></i>
                            {{ request('is_active') == '1' ? 'Active' : 'Inactive' }} <a
                                href="{{ request()->fullUrlWithoutQuery(['is_active', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a></span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    {{-- TABLEAU --}}
    <div class="section-card">
        <div class="toolbar-row">
            <div class="toolbar-left">
                <h2 class="section-title">Liste des semi-remorques</h2>
                <span class="result-count"><strong>{{ $remorques->total() }}</strong> résultat(s)</span>
            </div>
            <a href="{{ route('semi_remorques.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>
                Nouvelle remorque</a>
        </div>

        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Matricule / Marque</th>
                        <th style="width:160px">Type remorque</th>
                        <th style="width:130px">PTAC</th>
                        <th style="width:200px">VIN</th>
                        <th style="width:100px">Statut</th>
                        <th style="width:130px">Créé le</th>
                        <th style="width:80px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($remorques as $r)
                        <tr>
                            <td class="td-id">#{{ $r->id }}</td>
                            <td>
                                <div class="remorque-cell">
                                    <div class="remorque-icon"><i class="fa-solid fa-trailer"></i></div>
                                    <div>
                                        <div class="remorque-matricule">
                                            @if (request('search'))
                                                {!! preg_replace(
                                                    '/(' . preg_quote(request('search'), '/') . ')/i',
                                                    '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                    e($r->matricule),
                                                ) !!}
                                            @else
                                                {{ $r->matricule }}
                                            @endif
                                        </div>
                                        <div class="remorque-marque">{{ $r->marque }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge-type"><i class="fa-solid fa-tag" style="font-size:9px"></i>
                                    {{ $r->type_remorque }}</span></td>
                            <td>
                                @if ($r->ptac)
                                    <span class="ptac-cell">{{ number_format($r->ptac, 2, ',', ' ') }}<span
                                            class="ptac-unit">T</span></span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($r->vin)
                                    <span class="td-mono-sm">{{ $r->vin }}</span>
                                @else
                                    <span style="color:var(--text-muted)">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-active {{ $r->is_active ? 'on' : 'off' }}">
                                    <i class="fa-solid fa-circle" style="font-size:6px"></i>
                                    {{ $r->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="td-date">{{ $r->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $r->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="actions-cell">
                                    <a href="{{ route('semi_remorques.edit', $r) }}" class="btn-icon"
                                        title="Modifier"><i class="fa-solid fa-pen"></i></a>
                                    <form method="POST" action="{{ route('semi_remorques.destroy', $r) }}"
                                        id="delete-form-{{ $r->id }}" style="display:none">@csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDelete({{ $r->id }},'{{ addslashes($r->matricule) }}')"><i
                                            class="fa-solid fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-trailer"></i></div>
                                    <h3>{{ $activeFilters > 0 ? 'Aucune remorque ne correspond aux filtres' : 'Aucune semi-remorque enregistrée' }}
                                    </h3>
                                    <p>{{ $activeFilters > 0 ? "Essayez de modifier ou d'effacer vos critères." : 'Commencez par ajouter votre première semi-remorque.' }}
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('semi_remorques.index') }}" class="btn btn-primary"
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
        @if ($remorques->hasPages())
            @php
                $current = $remorques->currentPage();
                $last = $remorques->lastPage();
                $w = 2;
                $from = max(1, $current - $w);
                $to = min($last, $current + $w);
                $qs = http_build_query(request()->except('page'));
                $sep = $qs ? '&' : '';
            @endphp
            <div class="pagination-wrap">
                <span class="pagination-info">
                    <strong>{{ $remorques->firstItem() }}</strong>–<strong>{{ $remorques->lastItem() }}</strong> sur
                    <strong>{{ $remorques->total() }}</strong> remorque(s)
                </span>
                <div class="pagination-links">
                    @if ($current > 1)
                        <a class="page-btn" href="{{ $remorques->url(1) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-left"></i></a>
                    @endif
                    <a class="page-btn {{ $remorques->onFirstPage() ? 'disabled' : '' }}"
                        href="{{ $remorques->previousPageUrl() ? $remorques->previousPageUrl() . $sep . $qs : '#' }}"><i
                            class="fa-solid fa-chevron-left"></i></a>
                    @if ($from > 1)<a class="page-btn"
                            href="{{ $remorques->url(1) . $sep . $qs }}">1</a>
                        @if ($from > 2)
                            <span class="page-btn disabled">…</span>
                        @endif
                    @endif
                    @for ($p = $from; $p <= $to; $p++)
                        <a class="page-btn {{ $p == $current ? 'active' : '' }}"
                            href="{{ $remorques->url($p) . $sep . $qs }}">{{ $p }}</a>
                    @endfor
                    @if ($to < $last)
                        @if ($to < $last - 1)
                            <span class="page-btn disabled">…</span>
                        @endif
                        <a class="page-btn" href="{{ $remorques->url($last) . $sep . $qs }}">{{ $last }}</a>
                    @endif
                    <a class="page-btn {{ !$remorques->hasMorePages() ? 'disabled' : '' }}"
                        href="{{ $remorques->nextPageUrl() ? $remorques->nextPageUrl() . $sep . $qs : '#' }}"><i
                            class="fa-solid fa-chevron-right"></i></a>
                    @if ($current < $last)
                        <a class="page-btn" href="{{ $remorques->url($last) . $sep . $qs }}"><i
                                class="fa-solid fa-angles-right"></i></a>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function handleDelete(id, mat) {
            Swal.fire({
                title: 'Supprimer la remorque ?',
                text: `Êtes-vous sûr de vouloir supprimer "${mat}" ? Cette action est irréversible.`,
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
