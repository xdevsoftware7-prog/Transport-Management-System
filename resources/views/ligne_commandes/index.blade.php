{{--
|--------------------------------------------------------------------------
| PAGE : LISTE DES LIGNES DE COMMANDE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $lignes    : LengthAwarePaginator (avec filtres)
|   - $stats     : ['total', 'ce_mois', 'poids_total', 'qte_totale']
|   - $commandes : Collection (id, code_commande) pour filtre
|   - $articles  : Collection (id, designation, unite) pour filtre
|
| QUERY PARAMS :
|   ?search=      filtrage désignation article ou code commande
|   ?commande_id= filtrage par commande
|   ?article_id=  filtrage par article
|   ?date_from=   date début (Y-m-d)
|   ?date_to=     date fin   (Y-m-d)
|   ?page=        pagination
|
| ROUTE : GET /ligne_commandes → LigneCommandeController@index
--}}

@extends('layouts.app')

@section('title', 'Lignes de commande')
@section('page-title', 'Lignes de commande')
@section('page-subtitle', 'Détail des articles par commande')

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
            max-height: 520px;
        }

        .table-scroll-wrap::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .table-scroll-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-scroll-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
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

        .commande-cell {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .commande-code {
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--color-primary);
        }

        .commande-client {
            font-size: 11px;
            color: var(--text-muted);
        }

        .article-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .article-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--color-primary);
            flex-shrink: 0;
        }

        .article-name {
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .article-unite {
            font-size: 10px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .td-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 600;
        }

        .td-num .unit-label {
            font-size: 10px;
            color: var(--text-muted);
            font-weight: 400;
            margin-left: 2px;
        }

        .statut-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .statut-badge.en_attente {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .25);
        }

        .statut-badge.planifiée {
            background: rgba(59, 130, 246, .1);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, .25);
        }

        .statut-badge.en_cours {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .25);
        }

        .statut-badge.exécuté {
            background: rgba(107, 114, 128, .1);
            color: #4b5563;
            border: 1px solid rgba(107, 114, 128, .25);
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

        @media (max-width: 1100px) {
            .filters-grid {
                grid-template-columns: 1fr 1fr 1fr;
            }

            .filter-actions {
                grid-column: 1/-1;
                justify-content: flex-start;
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
        <span>Lignes de commande</span>
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
            <div class="kpi-label">Total lignes</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['ce_mois']) }}</div>
            <div class="kpi-label">Ajoutées ce mois</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['qte_totale']) }}</div>
            <div class="kpi-label">Quantité totale</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value">{{ number_format($stats['poids_total'], 2) }}<small
                    style="font-size:14px;font-weight:400"> kg</small></div>
            <div class="kpi-label">Poids total</div>
        </div>
    </div>

    {{-- ── FILTRES ── --}}
    @php
        $activeFilters = collect([
            'search' => request('search'),
            'commande_id' => request('commande_id'),
            'article_id' => request('article_id'),
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

        <form method="GET" action="{{ route('ligne_commandes.index') }}" id="filterForm">
            <div class="filters-grid">

                {{-- Recherche --}}
                <div class="filter-field">
                    <label>Recherche</label>
                    <div class="filter-input-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="search" class="filter-input has-icon"
                            placeholder="Code commande ou article…" value="{{ request('search') }}" autocomplete="off">
                    </div>
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

                {{-- Article --}}
                <div class="filter-field">
                    <label>Article</label>
                    <select name="article_id" class="filter-select">
                        <option value="">Tous les articles</option>
                        @foreach ($articles as $art)
                            <option value="{{ $art->id }}" {{ request('article_id') == $art->id ? 'selected' : '' }}>
                                {{ $art->designation }}
                            </option>
                        @endforeach
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
                        <a href="{{ route('ligne_commandes.index') }}" class="btn-reset">
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
                            "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithoutQuery(['search', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('commande_id'))
                        @php $cmd = $commandes->firstWhere('id', request('commande_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-file-invoice" style="font-size:9px"></i>
                            Commande : {{ $cmd?->code_commande ?? request('commande_id') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['commande_id', 'page']) }}"><i
                                    class="fa-solid fa-xmark"></i></a>
                        </span>
                    @endif
                    @if (request('article_id'))
                        @php $art = $articles->firstWhere('id', request('article_id')); @endphp
                        <span class="active-filter-tag">
                            <i class="fa-solid fa-box" style="font-size:9px"></i>
                            Article : {{ $art?->designation ?? request('article_id') }}
                            <a href="{{ request()->fullUrlWithoutQuery(['article_id', 'page']) }}"><i
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
                <h2 class="section-title">Liste des lignes de commande</h2>
                <span class="result-count">
                    <strong>{{ $lignes->total() }}</strong> résultat(s)
                </span>
            </div>
            <a href="{{ route('ligne_commandes.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        {{-- Scroll vertical + sticky header --}}
        <div class="table-scroll-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="width:160px">Commande</th>
                        <th>Article</th>
                        <th style="width:110px">Quantité</th>
                        <th style="width:120px">Poids (kg)</th>
                        <th style="width:120px">Statut cmd</th>
                        <th style="width:150px">Créé le</th>
                        <th style="width:150px">Modifié le</th>
                        <th style="width:90px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lignes as $ligne)
                        <tr>
                            <td class="td-id">#{{ $ligne->id }}</td>

                            {{-- Commande --}}
                            <td>
                                <div class="commande-cell">
                                    <span class="commande-code">{{ $ligne->commande->code_commande }}</span>
                                    <span class="commande-client">{{ $ligne->commande->client->nom ?? '—' }}</span>
                                </div>
                            </td>

                            {{-- Article --}}
                            <td>
                                <div class="article-cell">
                                    <div class="article-icon"><i class="fa-solid fa-box"></i></div>
                                    <div>
                                        <div class="article-name">
                                            @if (request('search'))
                                                {!! preg_replace(
                                                    '/(' . preg_quote(request('search'), '/') . ')/i',
                                                    '<mark style="background:rgba(224,32,32,.12);color:var(--color-primary);border-radius:2px;padding:0 2px">$1</mark>',
                                                    e($ligne->article->designation),
                                                ) !!}
                                            @else
                                                {{ $ligne->article->designation }}
                                            @endif
                                        </div>
                                        <div class="article-unite">{{ strtoupper($ligne->article->unite) }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Quantité --}}
                            <td>
                                <span class="td-num">
                                    {{ number_format($ligne->quantite) }}
                                    <span class="unit-label">{{ strtoupper($ligne->article->unite) }}</span>
                                </span>
                            </td>

                            {{-- Poids --}}
                            <td>
                                <span class="td-num">
                                    {{ number_format($ligne->poids_kg, 3) }}
                                    <span class="unit-label">kg</span>
                                </span>
                            </td>

                            {{-- Statut commande --}}
                            <td>
                                <span class="statut-badge {{ $ligne->commande->statut }}">
                                    {{ str_replace('_', ' ', $ligne->commande->statut) }}
                                </span>
                            </td>

                            {{-- created_at --}}
                            <td>
                                <div class="td-date">{{ $ligne->created_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $ligne->created_at->format('H:i') }}</div>
                            </td>

                            {{-- updated_at --}}
                            <td>
                                <div class="td-date">{{ $ligne->updated_at->format('d/m/Y') }}</div>
                                <div class="td-date-sub">{{ $ligne->updated_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex;gap:4px">
                                    <a href="{{ route('ligne_commandes.edit', $ligne) }}" class="btn-icon"
                                        title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('ligne_commandes.destroy', $ligne) }}"
                                        id="delete-form-{{ $ligne->id }}" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn-icon btn-icon--danger" title="Supprimer"
                                        onclick="handleDeleteLigne({{ $ligne->id }}, '{{ $ligne->commande->code_commande }}', '{{ addslashes($ligne->article->designation) }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding:0;border:none">
                                <div class="empty-state">
                                    <div class="empty-state-icon"><i class="fa-solid fa-list-check"></i></div>
                                    <h3>
                                        @if ($activeFilters > 0)
                                            Aucune ligne ne correspond aux filtres
                                        @else
                                            Aucune ligne de commande enregistrée
                                        @endif
                                    </h3>
                                    <p>
                                        @if ($activeFilters > 0)
                                            Essayez de modifier ou d'effacer vos critères.
                                        @else
                                            Commencez par ajouter votre première ligne.
                                        @endif
                                    </p>
                                    @if ($activeFilters > 0)
                                        <a href="{{ route('ligne_commandes.index') }}" class="btn btn-primary"
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
        @if ($lignes->hasPages())
            <div class="pagination-wrap">
                <span class="pagination-info">
                    Affichage <strong>{{ $lignes->firstItem() }}–{{ $lignes->lastItem() }}</strong>
                    sur <strong>{{ $lignes->total() }}</strong>
                </span>
                <div class="pagination-links">
                    {{-- Précédent --}}
                    <a href="{{ $lignes->previousPageUrl() ?? '#' }}"
                        class="page-btn {{ $lignes->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fa-solid fa-chevron-left" style="font-size:10px"></i>
                    </a>

                    {{-- Pages numérotées --}}
                    @foreach ($lignes->getUrlRange(max(1, $lignes->currentPage() - 2), min($lignes->lastPage(), $lignes->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                            class="page-btn {{ $page == $lignes->currentPage() ? 'active' : '' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Suivant --}}
                    <a href="{{ $lignes->nextPageUrl() ?? '#' }}"
                        class="page-btn {{ !$lignes->hasMorePages() ? 'disabled' : '' }}">
                        <i class="fa-solid fa-chevron-right" style="font-size:10px"></i>
                    </a>
                </div>
            </div>
        @endif

    </div>{{-- /section-card --}}

@endsection

    @push('scripts')
        <script>
            function handleDeleteLigne(id, code) {
                Swal.fire({
                    title: 'Supprimer la ligne de commande ?',
                    text: `Êtes-vous sûr de vouloir supprimer la ligne de commande "${code}" ? Cette action est irréversible.`,
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
