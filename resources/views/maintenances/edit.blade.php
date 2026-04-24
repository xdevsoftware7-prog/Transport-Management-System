{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UNE MAINTENANCE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $maintenance : App\Models\Maintenance (avec vehicule.chauffeur chargés)
|   - $vehicules   : Collection (id, matricule, marque, type_vehicule)
|
| ROUTE : PUT /maintenances/{maintenance} → MaintenanceController@update
--}}

@extends('layouts.app')

@section('title', 'Modifier — ' . $maintenance->type_intervention)
@section('page-title', 'Modifier la Maintenance')
@section('page-subtitle', 'Mettre à jour les informations d\'une intervention')

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

        /* ── EDIT BANNER ── */
        .edit-banner {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--color-dark);
            border-radius: var(--border-radius);
            padding: 14px 22px;
            margin-bottom: 4px
        }

        .edit-banner-icon {
            width: 40px;
            height: 40px;
            background: rgba(224, 32, 32, .15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 18px;
            flex-shrink: 0
        }

        .edit-banner-text strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3
        }

        .edit-banner-text span {
            font-size: 12px;
            color: #666
        }

        .edit-banner-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0
        }

        .edit-banner-statut {
            font-size: 11px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 6px
        }

        .banner-en_attente {
            background: rgba(245, 158, 11, .15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, .3)
        }

        .banner-en_cours {
            background: rgba(59, 130, 246, .15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, .3)
        }

        .banner-terminee {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, .3)
        }

        .edit-banner-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #555;
            background: rgba(255, 255, 255, .05);
            border: 1px solid #222;
            border-radius: 6px;
            padding: 5px 10px
        }

        /* ── LAYOUT ── */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        /* ── CHAMPS ── */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-primary)
        }

        .field .field-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: -2px
        }

        .field-input-wrap {
            position: relative
        }

        .field-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--text-muted);
            pointer-events: none
        }

        .field-input-wrap input,
        .field-input-wrap select {
            padding-left: 34px
        }

        .field input[type="text"],
        .field input[type="date"],
        .field input[type="number"],
        .field select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: #fafafa;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition)
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px
        }

        .char-counter {
            font-size: 11px;
            color: var(--text-muted);
            text-align: right;
            margin-top: -2px;
            transition: color .2s
        }

        .char-counter.warn {
            color: #f59e0b
        }

        .char-counter.over {
            color: var(--color-primary)
        }

        /* ── GRILLE 2 COL ── */
        .fields-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        /* ── INDICATEUR CHANGEMENTS ── */
        .change-indicator {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #d97706;
            background: rgba(245, 158, 11, .08);
            border: 1px solid rgba(245, 158, 11, .2);
            border-radius: var(--border-radius-sm);
            padding: 8px 12px
        }

        .change-indicator.visible {
            display: flex
        }

        /* ── STATUT PILLS ── */
        .statut-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 4px
        }

        .statut-pill {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            background: var(--bg-body);
            transition: all .15s;
            user-select: none
        }

        .statut-pill input[type="radio"] {
            display: none
        }

        .statut-pill.pill-en_attente.selected {
            border-color: #f59e0b;
            background: rgba(245, 158, 11, .1);
            color: #d97706
        }

        .statut-pill.pill-en_cours.selected {
            border-color: #3b82f6;
            background: rgba(59, 130, 246, .1);
            color: #2563eb
        }

        .statut-pill.pill-terminee.selected {
            border-color: #10b981;
            background: rgba(16, 185, 129, .1);
            color: #059669
        }

        /* ── COMPARAISON AVANT / APRÈS ── */
        .compare-box {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 10px;
            margin-bottom: 20px;
            padding: 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            align-items: center
        }

        .compare-side {
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        .compare-side-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            display: flex;
            align-items: center;
            gap: 5px
        }

        .compare-side.before .compare-side-label {
            color: var(--text-muted)
        }

        .compare-side.after .compare-side-label {
            color: var(--color-primary)
        }

        .compare-sep {
            font-size: 16px;
            color: var(--text-muted);
            display: flex;
            justify-content: center
        }

        .compare-val {
            font-size: 13px;
            font-weight: 600
        }

        .compare-side.before .compare-val {
            color: var(--text-muted)
        }

        .compare-side.after .compare-val {
            color: var(--color-primary)
        }

        /* ── METADATA ── */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px
        }

        .meta-item {
            padding: 12px 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm)
        }

        .meta-item-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 5px
        }

        .meta-item-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary)
        }

        .meta-item-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px
        }

        /* ── SIDEBAR ── */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px
        }

        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm)
        }

        .preview-header {
            background: var(--color-dark);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px
        }

        .preview-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(224, 32, 32, .15);
            border: 1px solid rgba(224, 32, 32, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--color-primary);
            flex-shrink: 0
        }

        .preview-header-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff
        }

        .preview-header-sub {
            font-size: 11px;
            color: #666;
            margin-top: 1px
        }

        .preview-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px
        }

        .prev-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            font-weight: 700
        }

        .prev-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary)
        }

        .prev-value.primary {
            color: var(--color-primary)
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-style: italic;
            font-weight: 400
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: filter .15s
        }

        .btn-submit:hover {
            filter: brightness(1.1)
        }

        .btn-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px;
            background: transparent;
            color: var(--text-muted);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition)
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6
        }

        .info-box strong {
            color: var(--text-secondary)
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

        .flash-error {
            background: rgba(224, 32, 32, .06);
            border: 1px solid rgba(224, 32, 32, .2);
            border-left: 3px solid var(--color-primary);
            color: var(--color-primary)
        }

        @media (max-width:900px) {
            .form-layout {
                grid-template-columns: 1fr
            }

            .fields-grid-2 {
                grid-template-columns: 1fr
            }

            .meta-grid {
                grid-template-columns: 1fr 1fr
            }

            .compare-box {
                grid-template-columns: 1fr
            }

            .compare-sep {
                display: none
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('maintenances.index') }}">Maintenances</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Modifier #{{ $maintenance->id }}</span>
    </div>

    {{-- Edit Banner --}}
    @php
        $bannerCls = match ($maintenance->statut) {
            'en_attente' => 'banner-en_attente',
            'en_cours' => 'banner-en_cours',
            'terminée' => 'banner-terminee',
            default => '',
        };
    @endphp
    <div class="edit-banner">
        <div class="edit-banner-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="edit-banner-text">
            <strong>{{ $maintenance->type_intervention }}</strong>
            <span>{{ $maintenance->vehicule->matricule }} — {{ $maintenance->vehicule->marque }}</span>
        </div>
        <div class="edit-banner-right">
            <span class="edit-banner-statut {{ $bannerCls }}">{{ $maintenance->statut_label }}</span>
            <span class="edit-banner-id">#{{ $maintenance->id }}</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- Indicateur changements --}}
    <div class="change-indicator" id="changeIndicator">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Des modifications non sauvegardées sont en cours.
    </div>

    <form method="POST" action="{{ route('maintenances.update', $maintenance) }}" id="maintenanceForm">
        @csrf @method('PUT')

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Comparaison avant/après --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-code-compare"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Avant / Après
                        </h2>
                    </div>
                    <div class="compare-box">
                        <div class="compare-side before">
                            <div class="compare-side-label"><i class="fa-solid fa-clock-rotate-left"></i> Avant</div>
                            <div class="compare-val">{{ $maintenance->type_intervention }}</div>
                            <small style="font-size:11px;color:var(--text-muted);margin-top:2px">
                                {{ $maintenance->vehicule->matricule }} · {{ $maintenance->statut_label }}
                            </small>
                        </div>
                        <div class="compare-sep"><i class="fa-solid fa-arrow-right"></i></div>
                        <div class="compare-side after">
                            <div class="compare-side-label"><i class="fa-solid fa-pen"></i> Après (aperçu)</div>
                            <div class="compare-val" id="cmpIntervention">{{ $maintenance->type_intervention }}</div>
                            <small style="font-size:11px;color:var(--color-primary);margin-top:2px" id="cmpMeta">
                                — · —
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Véhicule --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-truck"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Véhicule concerné
                        </h2>
                    </div>
                    <div class="field">
                        <label for="vehicule_id">Véhicule <span style="color:var(--color-primary)">*</span></label>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-truck field-prefix"></i>
                            <select id="vehicule_id" name="vehicule_id" required onchange="markChanged();updateCompare()">
                                <option value="">— Choisir un véhicule —</option>
                                @foreach ($vehicules as $v)
                                    <option value="{{ $v->id }}"
                                        {{ old('vehicule_id', $maintenance->vehicule_id) == $v->id ? 'selected' : '' }}>
                                        {{ $v->matricule }} — {{ $v->marque }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('vehicule_id')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Intervention --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-wrench"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Type d'intervention
                        </h2>
                    </div>
                    <div class="field">
                        <label for="type_intervention">Intervention <span
                                style="color:var(--color-primary)">*</span></label>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-wrench field-prefix"></i>
                            <input type="text" id="type_intervention" name="type_intervention"
                                value="{{ old('type_intervention', $maintenance->type_intervention) }}" required
                                maxlength="255" autocomplete="off" autofocus
                                oninput="markChanged();updateCounter(this);updateCompare()">
                        </div>
                        <div class="char-counter" id="interventionCounter">
                            {{ strlen(old('type_intervention', $maintenance->type_intervention)) }} / 255
                        </div>
                        @error('type_intervention')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Coût & Statut --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-coins"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Coût & Statut
                        </h2>
                    </div>
                    <div class="fields-grid-2">
                        <div class="field">
                            <label for="cout_total">Coût total (MAD)</label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-coins field-prefix"></i>
                                <input type="number" id="cout_total" name="cout_total"
                                    value="{{ old('cout_total', $maintenance->cout_total) }}" step="0.01"
                                    min="0" placeholder="0.00" oninput="markChanged();updateCompare()">
                            </div>
                            @error('cout_total')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label>Statut <span style="color:var(--color-primary)">*</span></label>
                            <div class="statut-pills">
                                @foreach ([
            'en_attente' => ['fa-clock', 'En attente', 'pill-en_attente'],
            'en_cours' => ['fa-spinner', 'En cours', 'pill-en_cours'],
            'terminée' => ['fa-circle-check', 'Terminée', 'pill-terminee'],
        ] as $val => [$icon, $label, $cls])
                                    <label
                                        class="statut-pill {{ $cls }} {{ old('statut', $maintenance->statut) === $val ? 'selected' : '' }}"
                                        onclick="selectStatut('{{ $val }}', this)">
                                        <input type="radio" name="statut" value="{{ $val }}"
                                            {{ old('statut', $maintenance->statut) === $val ? 'checked' : '' }}>
                                        <i class="fa-solid {{ $icon }}"></i> {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @error('statut')
                                <span class="field-error" style="margin-top:6px"><i
                                        class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Dates --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-calendar-days"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Planification
                        </h2>
                    </div>
                    <div class="fields-grid-2">
                        <div class="field">
                            <label for="date_debut">Date de début <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar-day field-prefix"></i>
                                <input type="date" id="date_debut" name="date_debut"
                                    value="{{ old('date_debut', optional($maintenance->date_debut)->format('Y-m-d')) }}"
                                    required oninput="markChanged();updateCompare()">
                            </div>
                            @error('date_debut')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="date_fin">Date de fin</label>
                            <p class="field-hint">Optionnelle</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar-check field-prefix"></i>
                                <input type="date" id="date_fin" name="date_fin"
                                    value="{{ old('date_fin', optional($maintenance->date_fin)->format('Y-m-d')) }}"
                                    oninput="markChanged();updateCompare()">
                            </div>
                            @error('date_fin')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Métadonnées --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-circle-info"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Informations
                        </h2>
                    </div>
                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-hashtag"></i> ID</div>
                            <div class="meta-item-value">#{{ $maintenance->id }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-plus"></i> Créée le</div>
                            <div class="meta-item-value">{{ $maintenance->created_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $maintenance->created_at->format('H:i') }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-clock-rotate-left"></i> Modifiée</div>
                            <div class="meta-item-value">{{ $maintenance->updated_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $maintenance->updated_at->diffForHumans() }}</div>
                        </div>
                        @if ($maintenance->vehicule->chauffeur)
                            <div class="meta-item" style="grid-column:span 3">
                                <div class="meta-item-label"><i class="fa-solid fa-id-card"></i> Chauffeur associé</div>
                                <div class="meta-item-value">
                                    {{ $maintenance->vehicule->chauffeur->prenom }}
                                    {{ $maintenance->vehicule->chauffeur->nom }}
                                </div>
                                <div class="meta-item-sub">{{ $maintenance->vehicule->chauffeur->code_drv }}</div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-icon"><i class="fa-solid fa-wrench"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu maintenance</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Véhicule</div>
                            <div class="prev-value primary" id="prevVehicule">
                                {{ $maintenance->vehicule->matricule }} — {{ $maintenance->vehicule->marque }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Intervention</div>
                            <div class="prev-value" id="prevIntervention">{{ $maintenance->type_intervention }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Coût</div>
                            <div class="prev-value" id="prevCout">
                                @if ($maintenance->cout_total !== null)
                                    {{ number_format($maintenance->cout_total, 2, ',', ' ') }} MAD
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Période</div>
                            <div class="prev-value" id="prevPeriode">
                                {{ optional($maintenance->date_debut)->format('d/m/Y') }}
                                @if ($maintenance->date_fin)
                                    → {{ $maintenance->date_fin->format('d/m/Y') }}
                                @else
                                    → en cours
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="maintenanceForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('maintenances.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Note :</strong> Modifier le statut en
                    <em>Terminée</em> sans date de fin renseignée est autorisé — pensez à la compléter.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        var originalValues = {};

        document.addEventListener('DOMContentLoaded', function() {
            // Snapshot initial
            ['vehicule_id', 'type_intervention', 'cout_total', 'statut', 'date_debut', 'date_fin'].forEach(function(
                id) {
                var el = document.getElementById(id) || document.querySelector('[name="' + id +
                    '"]:checked');
                if (el) originalValues[id] = el.value;
            });

            updateCounter(document.getElementById('type_intervention'));
            updateCompare();
        });

        function markChanged() {
            document.getElementById('changeIndicator').classList.add('visible');
        }

        function updateCounter(input) {
            var counter = document.getElementById('interventionCounter');
            var len = input ? input.value.length : 0;
            counter.textContent = len + ' / 255';
            counter.className = 'char-counter' + (len >= 255 ? ' over' : len >= 204 ? ' warn' : '');
        }

        function selectStatut(val, el) {
            document.querySelectorAll('.statut-pill').forEach(p => p.classList.remove('selected'));
            el.classList.add('selected');
            markChanged();
            updateCompare();
        }

        function updateCompare() {
            // Aperçu sidebar
            var sel = document.getElementById('vehicule_id');
            var opt = sel ? sel.options[sel.selectedIndex] : null;
            var pv = document.getElementById('prevVehicule');
            if (opt && opt.value) {
                pv.textContent = opt.text;
                pv.className = 'prev-value primary';
            } else {
                pv.innerHTML = '<span class="muted">—</span>';
                pv.className = 'prev-value';
            }

            var ti = (document.getElementById('type_intervention') || {}).value || '';
            var pi = document.getElementById('prevIntervention');
            pi.textContent = ti || '';
            if (!ti) pi.innerHTML = '<span class="muted">—</span>';

            var cout = parseFloat((document.getElementById('cout_total') || {}).value);
            var pc = document.getElementById('prevCout');
            pc.innerHTML = !isNaN(cout) && cout >= 0 ?
                cout.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2
                }) + ' MAD' :
                '<span class="muted">—</span>';

            var dd = (document.getElementById('date_debut') || {}).value;
            var df = (document.getElementById('date_fin') || {}).value;
            var pp = document.getElementById('prevPeriode');
            if (dd) {
                var fmt = d => {
                    var p = d.split('-');
                    return p[2] + '/' + p[1] + '/' + p[0];
                };
                pp.textContent = fmt(dd) + (df ? ' → ' + fmt(df) : ' → en cours');
                pp.className = 'prev-value';
            } else {
                pp.innerHTML = '<span class="muted">—</span>';
                pp.className = 'prev-value';
            }

            // Comparaison avant/après
            var ci = document.getElementById('cmpIntervention');
            if (ci) ci.textContent = ti || '—';
            var cm = document.getElementById('cmpMeta');
            if (cm) {
                var mat = opt && opt.value ? opt.text.split('—')[0].trim() : '—';
                var statEl = document.querySelector('.statut-pill.selected');
                var statLabel = statEl ? statEl.textContent.trim() : '—';
                cm.textContent = mat + ' · ' + statLabel;
            }
        }
    </script>
@endpush
