{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN TRAJET — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $villes : Collection<Ville>
|
| ROUTE : GET  /trajets/create → TrajetController@create
|         POST /trajets         → TrajetController@store
--}}

@extends('layouts.app')

@section('title', 'Nouveau Trajet')
@section('page-title', 'Nouveau Trajet')
@section('page-subtitle', 'Créer une liaison entre deux villes')

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

        .field-full {
            grid-column: 1/-1
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

        /* SELECT2 / Select villes stylé */
        .ville-select-wrap {
            position: relative
        }

        .ville-select-wrap select {
            padding-left: 36px
        }

        .ville-select-wrap .field-prefix {
            pointer-events: none
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

        .statut-tab:hover {
            border-color: var(--text-muted)
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

        /* Visualisation trajet */
        .route-viz {
            display: flex;
            align-items: center;
            gap: 0;
            padding: 16px 20px;
            background: var(--bg-body);
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border)
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

        @media(max-width:960px) {
            .form-layout {
                grid-template-columns: 1fr
            }

            .sidebar-col {
                order: -1
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
        }
    </style>

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('trajets.index') }}">Trajets</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouveau trajet</span>
    </div>

    <form method="POST" action="{{ route('trajets.store') }}" id="trajetForm" novalidate>
        @csrf
        <input type="hidden" id="statutHidden" name="statut" value="{{ old('statut', 'actif') }}">

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
                    </div>
                    <div style="padding:20px;display:flex;flex-direction:column;gap:16px">

                        {{-- Visualisation --}}
                        <div class="route-viz" id="routeViz">
                            <div class="route-node">
                                <div class="route-dot depart"></div>
                                <div class="route-node-label">Départ</div>
                                <div class="route-node-val" id="vizDepart"
                                    style="color:var(--text-muted);font-style:italic;font-weight:400;font-size:12px">—</div>
                            </div>
                            <div class="route-line"></div>
                            <div class="route-node">
                                <div class="route-dot destination"></div>
                                <div class="route-node-label">Destination</div>
                                <div class="route-node-val" id="vizDest"
                                    style="color:var(--text-muted);font-style:italic;font-weight:400;font-size:12px">—</div>
                            </div>
                        </div>

                        <div class="fields-grid">
                            {{-- Ville départ --}}
                            <div class="field">
                                <label>Ville de départ <span class="required-star">*</span></label>
                                <div class="field-input-wrap ville-select-wrap">
                                    <i class="fa-solid fa-location-dot field-prefix" style="color:var(--color-primary)"></i>
                                    <select name="ville_depart_id" id="villeDepart" required
                                        class="{{ $errors->has('ville_depart_id') ? 'is-invalid' : '' }}"
                                        onchange="updatePreview()">
                                        <option value="">— Choisir une ville —</option>
                                        @foreach ($villes as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('ville_depart_id') == $v->id ? 'selected' : '' }}>{{ $v->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('ville_depart_id')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Ville destination --}}
                            <div class="field">
                                <label>Ville de destination <span class="required-star">*</span></label>
                                <div class="field-input-wrap ville-select-wrap">
                                    <i class="fa-solid fa-flag-checkered field-prefix" style="color:#3b82f6"></i>
                                    <select name="ville_destination_id" id="villeDest" required
                                        class="{{ $errors->has('ville_destination_id') ? 'is-invalid' : '' }}"
                                        onchange="updatePreview()">
                                        <option value="">— Choisir une ville —</option>
                                        @foreach ($villes as $v)
                                            <option value="{{ $v->id }}"
                                                {{ old('ville_destination_id') == $v->id ? 'selected' : '' }}>
                                                {{ $v->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('ville_destination_id')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Adresse départ --}}
                            <div class="field">
                                <label>Adresse de départ</label>
                                <p class="field-hint">Point de départ précis (optionnel)</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-map-pin field-prefix"></i>
                                    <textarea name="adresse_depart" placeholder="Ex : Zone industrielle Sidi Bernoussi, Rue 12…"
                                        class="{{ $errors->has('adresse_depart') ? 'is-invalid' : '' }}">{{ old('adresse_depart') }}</textarea>
                                </div>
                                @error('adresse_depart')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Adresse destination --}}
                            <div class="field">
                                <label>Adresse de destination</label>
                                <p class="field-hint">Point d'arrivée précis (optionnel)</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-map-pin field-prefix" style="color:#3b82f6"></i>
                                    <textarea name="adresse_destination" placeholder="Ex : Port de Casablanca, Quai 7…"
                                        class="{{ $errors->has('adresse_destination') ? 'is-invalid' : '' }}">{{ old('adresse_destination') }}</textarea>
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

                            {{-- Distance --}}
                            <div class="field">
                                <label>Distance <span
                                        style="color:var(--text-muted);font-weight:400;text-transform:none">(km)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-road field-prefix" style="color:#d97706"></i>
                                    <input type="number" id="distanceKm" name="distance_km"
                                        value="{{ old('distance_km') }}" placeholder="Ex : 354.50" step="0.01"
                                        min="0" class="{{ $errors->has('distance_km') ? 'is-invalid' : '' }}"
                                        oninput="updatePreview()">
                                </div>
                                @error('distance_km')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Prix autoroute --}}
                            <div class="field">
                                <label>Prix autoroute <span
                                        style="color:var(--text-muted);font-weight:400;text-transform:none">(MAD)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-money-bill field-prefix" style="color:#059669"></i>
                                    <input type="number" id="prixAutoroute" name="prix_autoroute"
                                        value="{{ old('prix_autoroute') }}" placeholder="Ex : 145.00" step="0.01"
                                        min="0" class="{{ $errors->has('prix_autoroute') ? 'is-invalid' : '' }}"
                                        oninput="updatePreview()">
                                </div>
                                @error('prix_autoroute')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Durée --}}
                            <div class="field">
                                <label>Durée <span
                                        style="color:var(--text-muted);font-weight:400;text-transform:none">(minutes)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-clock field-prefix" style="color:#7c3aed"></i>
                                    <input type="number" id="dureeMinutes" name="duree_minutes"
                                        value="{{ old('duree_minutes') }}" placeholder="Ex : 240" step="1"
                                        min="0" class="{{ $errors->has('duree_minutes') ? 'is-invalid' : '' }}"
                                        oninput="updatePreview()">
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
                        <div class="statut-tabs">
                            <button type="button"
                                class="statut-tab {{ old('statut', 'actif') === 'actif' ? 'sel-actif' : '' }}"
                                onclick="selectStatut('actif',this)">
                                <span class="statut-dot dot-actif"></span> Actif
                            </button>
                            <button type="button"
                                class="statut-tab {{ old('statut') === 'inactif' ? 'sel-inactif' : '' }}"
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
                            <div class="prev-route" id="prevRoute">
                                <span class="prev-ville dep" id="prevDep" style="opacity:.4">Départ ?</span>
                                <i class="fa-solid fa-arrow-right" style="font-size:10px;color:var(--text-muted)"></i>
                                <span class="prev-ville dest" id="prevDest" style="opacity:.4">Destination ?</span>
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Distance</div>
                            <div class="prev-value muted" id="prevDist">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Prix autoroute</div>
                            <div class="prev-value muted" id="prevPrix">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Durée</div>
                            <div class="prev-value muted" id="prevDuree">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                <span
                                    style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i
                                        class="fa-solid fa-circle-check" style="font-size:9px"></i> Actif</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-card">
                    <button type="submit" class="btn-submit" form="trajetForm">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer le trajet
                    </button>
                    <a href="{{ route('trajets.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Note :</strong> La ville de départ et la ville de destination doivent être différentes.<br>
                    Les champs <span style="color:var(--color-primary)">*</span> sont obligatoires.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        // Map villes pour lookup rapide
        var villesMap = {
            @foreach ($villes as $v)
                {{ $v->id }}: "{{ addslashes($v->nom) }}",
            @endforeach
        };

        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
            window.updatePreview = updatePreview;
        });

        function updatePreview() {
            var depId = document.getElementById('villeDepart').value;
            var destId = document.getElementById('villeDest').value;
            var dist = (document.getElementById('distanceKm').value || '').trim();
            var prix = (document.getElementById('prixAutoroute').value || '').trim();
            var duree = (document.getElementById('dureeMinutes').value || '').trim();

            var depNom = depId ? villesMap[depId] : null;
            var destNom = destId ? villesMap[destId] : null;

            // Viz route
            document.getElementById('vizDepart').textContent = depNom || '—';
            document.getElementById('vizDest').textContent = destNom || '—';
            if (depNom) {
                document.getElementById('vizDepart').style.color = '';
                document.getElementById('vizDepart').style.fontStyle = '';
                document.getElementById('vizDepart').style.fontWeight = '700';
            }
            if (destNom) {
                document.getElementById('vizDest').style.color = '';
                document.getElementById('vizDest').style.fontStyle = '';
                document.getElementById('vizDest').style.fontWeight = '700';
            }

            // Sidebar preview
            var pdep = document.getElementById('prevDep');
            var pdest = document.getElementById('prevDest');
            pdep.textContent = depNom || 'Départ ?';
            pdest.textContent = destNom || 'Destination ?';
            pdep.style.opacity = depNom ? '1' : '.4';
            pdest.style.opacity = destNom ? '1' : '.4';

            // Distance
            var pd = document.getElementById('prevDist');
            if (dist) {
                pd.textContent = parseFloat(dist).toFixed(1).replace('.', ',') + ' km';
                pd.className = 'prev-value';
            } else {
                pd.textContent = '—';
                pd.className = 'prev-value muted';
            }

            // Prix
            var pp = document.getElementById('prevPrix');
            if (prix) {
                pp.textContent = parseFloat(prix).toFixed(2).replace('.', ',') + ' MAD';
                pp.className = 'prev-value';
            } else {
                pp.textContent = '—';
                pp.className = 'prev-value muted';
            }

            // Durée
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
        }
    </script>
@endpush
