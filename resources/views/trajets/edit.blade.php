{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UN TRAJET — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $trajet : App\Models\Trajet (with villeDepart, villeDestination)
|   - $villes : Collection<Ville>
|
| ROUTE : GET /trajets/{trajet}/edit → TrajetController@edit
|         PUT /trajets/{trajet}       → TrajetController@update
--}}

@extends('layouts.app')

@section('title', 'Modifier · Trajet #' . $trajet->id)
@section('page-title', 'Modifier le Trajet')
@section('page-subtitle', $trajet->villeDepart->nom . ' → ' . $trajet->villeDestination->nom)

@section('content')

    <style>
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

        /* BANDEAU */
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
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3
        }

        .edit-banner-text span {
            font-size: 12px;
            color: #666;
            margin-top: 2px;
            display: block
        }

        .edit-banner-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0
        }

        .edit-banner-badge {
            font-size: 11px;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: 700
        }

        .edit-banner-badge.actif {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, .25)
        }

        .edit-banner-badge.inactif {
            background: rgba(107, 114, 128, .15);
            color: #9ca3af;
            border: 1px solid rgba(107, 114, 128, .25)
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

        /* LAYOUT */
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

        /* FIELDS */
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

        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        .fields-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px
        }

        .required-star {
            color: var(--color-primary)
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
            pointer-events: none;
            z-index: 1
        }

        .field input[type=text],
        .field input[type=number],
        .field select,
        .field textarea {
            width: 100%;
            padding: 11px 14px 11px 36px;
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
        .field select:focus,
        .field textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .field input.is-invalid,
        .field select.is-invalid,
        .field textarea.is-invalid {
            border-color: var(--color-primary)
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted)
        }

        .field select {
            cursor: pointer
        }

        .field textarea {
            resize: vertical;
            min-height: 70px;
            padding-top: 11px;
            padding-left: 36px
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px
        }

        /* Change indicator */
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

        /* Compare box */
        .compare-box {
            display: grid;
            grid-template-columns: 1fr 24px 1fr;
            gap: 0;
            margin-bottom: 16px;
            padding: 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm)
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
            gap: 5px;
            margin-bottom: 4px
        }

        .compare-side.before .compare-side-label {
            color: var(--text-muted)
        }

        .compare-side.after .compare-side-label {
            color: var(--color-primary)
        }

        .compare-sep {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--text-muted);
            align-self: center
        }

        .compare-route {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap
        }

        .compare-ville {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 7px;
            border-radius: 4px
        }

        .compare-ville.dep {
            background: rgba(224, 32, 32, .08);
            color: var(--color-primary)
        }

        .compare-ville.dest {
            background: rgba(59, 130, 246, .08);
            color: #3b82f6
        }

        .compare-arrow {
            font-size: 10px;
            color: var(--text-muted)
        }

        /* Route viz */
        .route-viz {
            display: flex;
            align-items: center;
            gap: 0;
            padding: 14px 18px;
            background: var(--bg-body);
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border);
            margin-bottom: 4px
        }

        .route-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            flex-shrink: 0
        }

        .route-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid
        }

        .route-dot.depart {
            border-color: var(--color-primary);
            background: var(--color-primary)
        }

        .route-dot.destination {
            border-color: #3b82f6;
            background: #3b82f6
        }

        .route-node-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: var(--text-muted)
        }

        .route-node-val {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            max-width: 110px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .route-line {
            flex: 1;
            height: 2px;
            background: linear-gradient(90deg, var(--color-primary), #3b82f6);
            border-radius: 2px;
            position: relative;
            margin: 0 8px;
            margin-bottom: 18px
        }

        .route-line::after {
            content: '▶';
            position: absolute;
            right: -6px;
            top: -7px;
            font-size: 12px;
            color: #3b82f6
        }

        /* Statut tabs */
        .statut-tabs {
            display: flex;
            gap: 8px
        }

        .statut-tab {
            flex: 1;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: #fafafa;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-secondary);
            transition: all var(--transition)
        }

        .statut-tab.sel-actif {
            border-color: #10b981;
            background: rgba(16, 185, 129, .06);
            color: #059669
        }

        .statut-tab.sel-inactif {
            border-color: #9ca3af;
            background: rgba(107, 114, 128, .06);
            color: #6b7280
        }

        .statut-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            flex-shrink: 0
        }

        .dot-actif {
            background: #10b981
        }

        .dot-inactif {
            background: #9ca3af
        }

        /* META */
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

        /* SIDEBAR */
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
            gap: 14px
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
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary)
        }

        .prev-value.primary {
            color: var(--color-primary)
        }

        .prev-value.muted {
            font-weight: 400;
            color: var(--text-muted);
            font-style: italic;
            font-size: 13px
        }

        .prev-route {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap
        }

        .prev-ville {
            font-size: 12px;
            font-weight: 700;
            padding: 4px 9px;
            border-radius: 5px
        }

        .prev-ville.dep {
            background: rgba(224, 32, 32, .08);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2)
        }

        .prev-ville.dest {
            background: rgba(59, 130, 246, .08);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, .2)
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--shadow-sm)
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--color-dark);
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
            transition: background var(--transition), transform var(--transition)
        }

        .btn-submit:hover {
            background: var(--color-primary);
            transform: translateY(-1px)
        }

        .btn-cancel {
            width: 100%;
            padding: 11px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition)
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .btn-delete {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: var(--color-primary);
            border: 1.5px solid rgba(224, 32, 32, .25);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background var(--transition), border-color var(--transition)
        }

        .btn-delete:hover {
            background: rgba(224, 32, 32, .06);
            border-color: var(--color-primary)
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.6
        }

        .info-box strong {
            color: var(--text-primary)
        }

        #deleteForm {
            display: none
        }

        @media(max-width:960px) {
            .form-layout {
                grid-template-columns: 1fr
            }

            .sidebar-col {
                order: -1
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

        @media(max-width:640px) {

            .fields-grid,
            .fields-grid-3 {
                grid-template-columns: 1fr
            }

            .statut-tabs {
                flex-direction: column
            }

            .meta-grid {
                grid-template-columns: 1fr
            }
        }
    </style>

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('trajets.index') }}">Trajets</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Modifier · #{{ $trajet->id }}</span>
    </div>

    {{-- BANDEAU --}}
    <div class="edit-banner">
        <div class="edit-banner-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="edit-banner-text">
            <strong>
                <span style="color:var(--color-primary)">{{ $trajet->villeDepart->nom }}</span>
                <i class="fa-solid fa-arrow-right" style="font-size:12px;color:#555"></i>
                <span style="color:#3b82f6">{{ $trajet->villeDestination->nom }}</span>
            </strong>
            <span>Modifié {{ $trajet->updated_at->diffForHumans() }} · Créé
                {{ $trajet->created_at->diffForHumans() }}</span>
        </div>
        <div class="edit-banner-right">
            <span class="edit-banner-badge {{ $trajet->statut }}">{{ ucfirst($trajet->statut) }}</span>
            <span class="edit-banner-id">#{{ $trajet->id }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('trajets.update', $trajet) }}" id="trajetForm" novalidate>
        @csrf @method('PUT')
        <input type="hidden" id="statutHidden" name="statut" value="{{ old('statut', $trajet->statut) }}">

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- VILLES --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-route"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Liaison entre villes
                        </h2>
                        <div class="change-indicator" id="changeIndicator">
                            <i class="fa-solid fa-circle-exclamation"></i> Modifications non sauvegardées
                        </div>
                    </div>
                    <div style="padding:20px;display:flex;flex-direction:column;gap:16px">

                        {{-- Compare box --}}
                        <div class="compare-box">
                            <div class="compare-side before">
                                <div class="compare-side-label"><i class="fa-solid fa-clock-rotate-left"></i> Actuel</div>
                                <div class="compare-route">
                                    <span class="compare-ville dep">{{ $trajet->villeDepart->nom }}</span>
                                    <span class="compare-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                                    <span class="compare-ville dest">{{ $trajet->villeDestination->nom }}</span>
                                </div>
                            </div>
                            <div class="compare-sep"><i class="fa-solid fa-arrow-right"></i></div>
                            <div class="compare-side after">
                                <div class="compare-side-label"><i class="fa-solid fa-pen"></i> Nouveau</div>
                                <div class="compare-route" id="compareNewRoute">
                                    <span class="compare-ville dep" id="cmpDep">{{ $trajet->villeDepart->nom }}</span>
                                    <span class="compare-arrow"><i class="fa-solid fa-arrow-right"></i></span>
                                    <span class="compare-ville dest"
                                        id="cmpDest">{{ $trajet->villeDestination->nom }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Route viz --}}
                        <div class="route-viz">
                            <div class="route-node">
                                <div class="route-dot depart"></div>
                                <div class="route-node-label">Départ</div>
                                <div class="route-node-val" id="vizDepart">{{ $trajet->villeDepart->nom }}</div>
                            </div>
                            <div class="route-line"></div>
                            <div class="route-node">
                                <div class="route-dot destination"></div>
                                <div class="route-node-label">Destination</div>
                                <div class="route-node-val" id="vizDest">{{ $trajet->villeDestination->nom }}</div>
                            </div>
                        </div>

                        <div class="fields-grid">
                            <div class="field">
                                <label>Ville de départ <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-location-dot field-prefix" style="color:var(--color-primary)"></i>
                                    <select name="ville_depart_id" id="villeDepart" required
                                        class="{{ $errors->has('ville_depart_id') ? 'is-invalid' : '' }}"
                                        onchange="onFieldChange();updatePreview()">
                                        <option value="">— Choisir —</option>
                                        @foreach ($villes as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('ville_depart_id', $trajet->ville_depart_id) == $v->id ? 'selected' : '' }}>
                                                {{ $v->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('ville_depart_id')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Ville de destination <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-flag-checkered field-prefix" style="color:#3b82f6"></i>
                                    <select name="ville_destination_id" id="villeDest" required
                                        class="{{ $errors->has('ville_destination_id') ? 'is-invalid' : '' }}"
                                        onchange="onFieldChange();updatePreview()">
                                        <option value="">— Choisir —</option>
                                        @foreach ($villes as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('ville_destination_id', $trajet->ville_destination_id) == $v->id ? 'selected' : '' }}>
                                                {{ $v->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('ville_destination_id')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Adresse de départ</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-map-pin field-prefix"></i>
                                    <textarea name="adresse_depart" oninput="onFieldChange()"
                                        class="{{ $errors->has('adresse_depart') ? 'is-invalid' : '' }}">{{ old('adresse_depart', $trajet->adresse_depart) }}</textarea>
                                </div>
                                @error('adresse_depart')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Adresse de destination</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-map-pin field-prefix" style="color:#3b82f6"></i>
                                    <textarea name="adresse_destination" oninput="onFieldChange()"
                                        class="{{ $errors->has('adresse_destination') ? 'is-invalid' : '' }}">{{ old('adresse_destination', $trajet->adresse_destination) }}</textarea>
                                </div>
                                @error('adresse_destination')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARACTÉRISTIQUES --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-gauge-high"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Caractéristiques du trajet
                        </h2>
                    </div>
                    <div style="padding:20px">
                        <div class="fields-grid-3">
                            <div class="field">
                                <label>Distance <span
                                        style="color:var(--text-muted);font-weight:400;text-transform:none">(km)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-road field-prefix" style="color:#d97706"></i>
                                    <input type="number" id="distanceKm" name="distance_km"
                                        value="{{ old('distance_km', $trajet->distance_km) }}" step="0.01"
                                        min="0" class="{{ $errors->has('distance_km') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()">
                                </div>
                                @error('distance_km')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Prix autoroute <span
                                        style="color:var(--text-muted);font-weight:400;text-transform:none">(MAD)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-money-bill field-prefix" style="color:#059669"></i>
                                    <input type="number" id="prixAutoroute" name="prix_autoroute"
                                        value="{{ old('prix_autoroute', $trajet->prix_autoroute) }}" step="0.01"
                                        min="0" class="{{ $errors->has('prix_autoroute') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()">
                                </div>
                                @error('prix_autoroute')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Durée <span
                                        style="color:var(--text-muted);font-weight:400;text-transform:none">(minutes)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-clock field-prefix" style="color:#7c3aed"></i>
                                    <input type="number" id="dureeMinutes" name="duree_minutes"
                                        value="{{ old('duree_minutes', $trajet->duree_minutes) }}" step="1"
                                        min="0" class="{{ $errors->has('duree_minutes') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()">
                                </div>
                                @error('duree_minutes')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATUT --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-circle-half-stroke"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Statut du trajet <span class="required-star">*</span>
                        </h2>
                    </div>
                    <div style="padding:16px 20px">
                        @php $curStatut = old('statut', $trajet->statut); @endphp
                        <div class="statut-tabs">
                            <button type="button" class="statut-tab {{ $curStatut === 'actif' ? 'sel-actif' : '' }}"
                                onclick="selectStatut('actif',this)">
                                <span class="statut-dot dot-actif"></span> Actif
                            </button>
                            <button type="button" class="statut-tab {{ $curStatut === 'inactif' ? 'sel-inactif' : '' }}"
                                onclick="selectStatut('inactif',this)">
                                <span class="statut-dot dot-inactif"></span> Inactif
                            </button>
                        </div>
                        @error('statut')
                            <span class="field-error" style="margin-top:8px"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- MÉTADONNÉES --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title" style="font-size:13px">
                            <i class="fa-solid fa-circle-info"
                                style="color:var(--color-primary);margin-right:5px;font-size:12px"></i>
                            Informations système
                        </h2>
                    </div>
                    <div class="meta-grid" style="padding:16px 20px 20px">
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-hashtag"></i> ID</div>
                            <div class="meta-item-value" style="font-family:'JetBrains Mono',monospace">
                                #{{ $trajet->id }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-plus"></i> Créé le</div>
                            <div class="meta-item-value">{{ $trajet->created_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $trajet->created_at->format('H:i') }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-pen"></i> Modifié le</div>
                            <div class="meta-item-value">{{ $trajet->updated_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $trajet->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-icon"><i class="fa-solid fa-route"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu trajet</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Liaison</div>
                            <div class="prev-route">
                                <span class="prev-ville dep" id="prevDep">{{ $trajet->villeDepart->nom }}</span>
                                <i class="fa-solid fa-arrow-right" style="font-size:10px;color:var(--text-muted)"></i>
                                <span class="prev-ville dest" id="prevDest">{{ $trajet->villeDestination->nom }}</span>
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Distance</div>
                            <div class="prev-value {{ $trajet->distance_km ? '' : 'muted' }}" id="prevDist">
                                {{ $trajet->distance_km ? number_format($trajet->distance_km, 1, ',', ' ') . ' km' : '—' }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Prix autoroute</div>
                            <div class="prev-value {{ $trajet->prix_autoroute ? '' : 'muted' }}" id="prevPrix">
                                {{ $trajet->prix_autoroute ? number_format($trajet->prix_autoroute, 2, ',', ' ') . ' MAD' : '—' }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Durée</div>
                            <div class="prev-value {{ $trajet->duree_minutes ? '' : 'muted' }}" id="prevDuree">
                                @if ($trajet->duree_minutes)
                                    @php
                                        $h = intdiv($trajet->duree_minutes, 60);
                                        $m = $trajet->duree_minutes % 60;
                                    @endphp
                                    {{ ($h > 0 ? $h . 'h ' : '') . $m . 'min' }}
                                @else
                                    —
                                @endif
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                @if ($curStatut === 'actif')
                                    <span
                                        style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i
                                            class="fa-solid fa-circle-check" style="font-size:9px"></i> Actif</span>
                                @else
                                    <span
                                        style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i
                                            class="fa-solid fa-circle-xmark" style="font-size:9px"></i> Inactif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-card">
                    <button type="submit" class="btn-submit" form="trajetForm">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('trajets.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </a>
                    <button type="button" class="btn-delete"
                        onclick="handleDeleteTrajet({{ $trajet->id }},'{{ addslashes($trajet->villeDepart->nom) }} → {{ addslashes($trajet->villeDestination->nom) }}')">
                        <i class="fa-solid fa-trash"></i> Supprimer ce trajet
                    </button>
                </div>

                <div class="info-box">
                    <strong>Rappel :</strong> La ville de départ et la destination doivent être différentes.
                </div>

            </div>

        </div>
    </form>

    <form method="POST" action="{{ route('trajets.destroy', $trajet) }}" id="deleteForm">
        @csrf @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        var villesMap = {
            @foreach ($villes as $v)
                {{ $v->id }}: "{{ addslashes($v->nom) }}",
            @endforeach
        };

        var _origDep = {{ $trajet->ville_depart_id }};
        var _origDest = {{ $trajet->ville_destination_id }};
        var _origDist = '{{ $trajet->distance_km ?? '' }}';
        var _origPrix = '{{ $trajet->prix_autoroute ?? '' }}';
        var _origDuree = '{{ $trajet->duree_minutes ?? '' }}';
        var _origStat = '{{ $trajet->statut }}';

        function onFieldChange() {
            var depId = parseInt(document.getElementById('villeDepart').value) || 0;
            var destId = parseInt(document.getElementById('villeDest').value) || 0;
            var dist = document.getElementById('distanceKm').value;
            var prix = document.getElementById('prixAutoroute').value;
            var duree = document.getElementById('dureeMinutes').value;
            var stat = document.getElementById('statutHidden').value;

            var changed = depId !== _origDep || destId !== _origDest || dist !== _origDist || prix !== _origPrix ||
                duree !== _origDuree || stat !== _origStat;
            document.getElementById('changeIndicator').classList.toggle('visible', changed);

            // Mise à jour compare box
            var cDep = document.getElementById('cmpDep');
            var cDest = document.getElementById('cmpDest');
            cDep.textContent = depId && villesMap[depId] ? villesMap[depId] : '?';
            cDest.textContent = destId && villesMap[destId] ? villesMap[destId] : '?';
        }

        function updatePreview() {
            var depId = document.getElementById('villeDepart').value;
            var destId = document.getElementById('villeDest').value;
            var dist = (document.getElementById('distanceKm').value || '').trim();
            var prix = (document.getElementById('prixAutoroute').value || '').trim();
            var duree = (document.getElementById('dureeMinutes').value || '').trim();

            var depNom = depId ? villesMap[depId] : null;
            var destNom = destId ? villesMap[destId] : null;

            document.getElementById('vizDepart').textContent = depNom || '—';
            document.getElementById('vizDest').textContent = destNom || '—';
            document.getElementById('prevDep').textContent = depNom || '?';
            document.getElementById('prevDest').textContent = destNom || '?';

            var pd = document.getElementById('prevDist');
            if (dist) {
                pd.textContent = parseFloat(dist).toFixed(1).replace('.', ',') + ' km';
                pd.className = 'prev-value';
            } else {
                pd.textContent = '—';
                pd.className = 'prev-value muted';
            }

            var pp = document.getElementById('prevPrix');
            if (prix) {
                pp.textContent = parseFloat(prix).toFixed(2).replace('.', ',') + ' MAD';
                pp.className = 'prev-value';
            } else {
                pp.textContent = '—';
                pp.className = 'prev-value muted';
            }

            var pdu = document.getElementById('prevDuree');
            if (duree) {
                var mins = parseInt(duree);
                var h = Math.floor(mins / 60);
                var m = mins % 60;
                pdu.textContent = (h > 0 ? h + 'h ' : '') + m + 'min';
                pdu.className = 'prev-value';
            } else {
                pdu.textContent = '—';
                pdu.className = 'prev-value muted';
            }
        }

        function selectStatut(val, el) {
            document.getElementById('statutHidden').value = val;
            document.querySelectorAll('.statut-tab').forEach(function(t) {
                t.className = 'statut-tab';
            });
            el.classList.add(val === 'actif' ? 'sel-actif' : 'sel-inactif');
            var badges = {
                'actif': '<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Actif</span>',
                'inactif': '<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i class="fa-solid fa-circle-xmark" style="font-size:9px"></i> Inactif</span>'
            };
            document.getElementById('prevStatutWrap').innerHTML = badges[val];
            onFieldChange();
        }

        function handleDeleteTrajet(id, label) {
            Swal.fire({
                title: 'Supprimer le trajet ?',
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
            }).then(r => {
                if (r.isConfirmed) document.getElementById('deleteForm').submit();
            });
        }
    </script>
@endpush
