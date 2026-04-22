{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN VÉHICULE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $chauffeurs : Collection<Chauffeur> (statut actif)
|
| ROUTE : POST /vehicules → VehiculeController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouveau Véhicule')
@section('page-title', 'Nouveau Véhicule')
@section('page-subtitle', 'Enregistrer un nouveau véhicule dans le parc')

@section('content')

    <style>
        /* ── BREADCRUMB ── */
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

        /* ── LAYOUT ── */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── SECTION CARDS ── */
        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .section-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        .section-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .fields-grid {
            display: grid;
            gap: 14px;
        }

        .fields-grid-2 {
            grid-template-columns: 1fr 1fr;
        }

        .fields-grid-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        /* ── CHAMPS ── */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-primary);
        }

        .field .field-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: -2px;
        }

        .field-input-wrap {
            position: relative;
        }

        .field-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .field input[type="text"],
        .field input[type="number"],
        .field input[type="date"],
        .field select,
        .field textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: #fafafa;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field input.has-prefix,
        .field select.has-prefix {
            padding-left: 34px;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted);
        }

        .field select {
            cursor: pointer;
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Suffix (unité) */
        .field-suffix {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            pointer-events: none;
            font-family: 'JetBrains Mono', monospace;
        }

        .field input.has-suffix {
            padding-right: 46px;
        }

        /* ── SIDEBAR ── */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .preview-header {
            padding: 14px 16px;
            background: var(--color-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-truck-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(224, 32, 32, .15);
            border: 1px solid rgba(224, 32, 32, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 16px;
            flex-shrink: 0;
        }

        .preview-header-text .preview-header-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .preview-header-text .preview-header-sub {
            font-size: 11px;
            color: #666;
        }

        .preview-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .prev-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .prev-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .prev-value.primary {
            color: var(--text-primary);
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-weight: 400;
            font-style: italic;
        }

        .prev-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 5px;
        }

        .prev-badge-achat {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .prev-badge-location {
            background: rgba(99, 102, 241, .1);
            color: #4f46e5;
            border: 1px solid rgba(99, 102, 241, .2);
        }

        /* Action card */
        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 8px;
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
            transition: background var(--transition), transform .1s;
        }

        .btn-submit:hover {
            background: #c01a1a;
        }

        .btn-submit:active {
            transform: scale(.98);
        }

        .btn-cancel {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-cancel:hover {
            border-color: var(--text-secondary);
            color: var(--text-primary);
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ── FLASH ── */
        .flash-error {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 13px;
            background: rgba(224, 32, 32, .06);
            border: 1px solid rgba(224, 32, 32, .2);
            border-left: 3px solid var(--color-primary);
            color: var(--color-primary);
            margin-bottom: 4px;
        }

        @media (max-width: 1024px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .fields-grid-3 {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {

            .fields-grid-2,
            .fields-grid-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('vehicules.index') }}">Véhicules</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouveau</span>
    </div>

    {{-- Erreurs globales --}}
    @if ($errors->any())
        <div class="flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->count() }} erreur(s) à corriger avant d'enregistrer.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('vehicules.store') }}" id="vehiculeForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- ── Identification ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-id-card" style="color:var(--color-primary);font-size:13px"></i>
                            Identification
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid fields-grid-2">

                            {{-- Matricule --}}
                            <div class="field">
                                <label for="matricule">Matricule <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-hashtag field-prefix"></i>
                                    <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}"
                                        placeholder="Ex : 123456-A-1" required maxlength="30" autocomplete="off" autofocus
                                        class="has-prefix" oninput="updatePreview()">
                                </div>
                                @error('matricule')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Marque --}}
                            <div class="field">
                                <label for="marque">Marque <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-building field-prefix"></i>
                                    <input type="text" id="marque" name="marque" value="{{ old('marque') }}"
                                        placeholder="Ex : Mercedes, Scania, Volvo…" required maxlength="100"
                                        autocomplete="off" class="has-prefix" oninput="updatePreview()">
                                </div>
                                @error('marque')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <div class="fields-grid fields-grid-2">

                            {{-- Type véhicule --}}
                            <div class="field">
                                <label for="type_vehicule">Type de véhicule <span
                                        style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-truck field-prefix"></i>
                                    <select id="type_vehicule" name="type_vehicule" required class="has-prefix"
                                        onchange="updatePreview()">
                                        <option value="">— Sélectionner un type —</option>
                                        <option value="tracteur"
                                            {{ old('type_vehicule') === 'tracteur' ? 'selected' : '' }}>Tracteur</option>
                                        <option value="semi-remorque"
                                            {{ old('type_vehicule') === 'semi-remorque' ? 'selected' : '' }}>Semi-remorque
                                        </option>
                                        <option value="camion" {{ old('type_vehicule') === 'camion' ? 'selected' : '' }}>
                                            Camion</option>
                                        <option value="fourgon" {{ old('type_vehicule') === 'fourgon' ? 'selected' : '' }}>
                                            Fourgon</option>
                                        <option value="benne" {{ old('type_vehicule') === 'benne' ? 'selected' : '' }}>
                                            Benne</option>
                                        <option value="citerne" {{ old('type_vehicule') === 'citerne' ? 'selected' : '' }}>
                                            Citerne</option>
                                        <option value="frigo" {{ old('type_vehicule') === 'frigo' ? 'selected' : '' }}>
                                            Frigo</option>
                                        <option value="plateau" {{ old('type_vehicule') === 'plateau' ? 'selected' : '' }}>
                                            Plateau</option>
                                    </select>
                                </div>
                                @error('type_vehicule')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Numéro de châssis --}}
                            <div class="field">
                                <label for="num_chassis">N° de châssis (VIN)</label>
                                <p class="field-hint">17 caractères alphanumériques</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-barcode field-prefix"></i>
                                    <input type="text" id="num_chassis" name="num_chassis"
                                        value="{{ old('num_chassis') }}" placeholder="Ex : WDB9630331L123456"
                                        maxlength="17" autocomplete="off" class="has-prefix"
                                        style="font-family:'JetBrains Mono',monospace;text-transform:uppercase"
                                        oninput="this.value=this.value.toUpperCase()">
                                </div>
                                @error('num_chassis')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Acquisition & Circulation ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-file-contract" style="color:var(--color-primary);font-size:13px"></i>
                            Acquisition & Mise en circulation
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid fields-grid-2">

                            {{-- Acquisition --}}
                            <div class="field">
                                <label for="acquisition">Mode d'acquisition <span
                                        style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-tag field-prefix"></i>
                                    <select id="acquisition" name="acquisition" required class="has-prefix"
                                        onchange="updatePreview()">
                                        <option value="">— Sélectionner —</option>
                                        <option value="achat" {{ old('acquisition') === 'achat' ? 'selected' : '' }}>
                                            Achat</option>
                                        <option value="location"
                                            {{ old('acquisition') === 'location' ? 'selected' : '' }}>Location</option>
                                    </select>
                                </div>
                                @error('acquisition')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Date circulation --}}
                            <div class="field">
                                <label for="date_circulation">Date de mise en circulation</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-calendar field-prefix"></i>
                                    <input type="date" id="date_circulation" name="date_circulation"
                                        value="{{ old('date_circulation') }}" class="has-prefix">
                                </div>
                                @error('date_circulation')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Caractéristiques techniques ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-gears" style="color:var(--color-primary);font-size:13px"></i>
                            Caractéristiques techniques
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid fields-grid-3">

                            {{-- Poids à vide --}}
                            <div class="field">
                                <label for="poids_a_vide">Poids à vide</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-weight-scale field-prefix"></i>
                                    <input type="number" id="poids_a_vide" name="poids_a_vide"
                                        value="{{ old('poids_a_vide') }}" placeholder="0" min="0" step="0.01"
                                        class="has-prefix has-suffix">
                                    <span class="field-suffix">kg</span>
                                </div>
                                @error('poids_a_vide')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- PTAC --}}
                            <div class="field">
                                <label for="ptac">PTAC</label>
                                <p class="field-hint">Poids total autorisé en charge</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-weight-hanging field-prefix"></i>
                                    <input type="number" id="ptac" name="ptac" value="{{ old('ptac') }}"
                                        placeholder="0" min="0" step="0.01" class="has-prefix has-suffix">
                                    <span class="field-suffix">kg</span>
                                </div>
                                @error('ptac')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- KM initial --}}
                            <div class="field">
                                <label for="km_initial">Kilométrage initial</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-road field-prefix"></i>
                                    <input type="number" id="km_initial" name="km_initial"
                                        value="{{ old('km_initial', 0) }}" placeholder="0" min="0"
                                        step="1" class="has-prefix has-suffix">
                                    <span class="field-suffix">km</span>
                                </div>
                                @error('km_initial')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Statut & Chauffeur ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-user-tie" style="color:var(--color-primary);font-size:13px"></i>
                            Statut & Affectation
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid fields-grid-2">

                            {{-- Statut --}}
                            <div class="field">
                                <label for="statut">Statut <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-circle-dot field-prefix"></i>
                                    <select id="statut" name="statut" required class="has-prefix"
                                        onchange="updatePreview()">
                                        <option value="">— Sélectionner —</option>
                                        <option value="actif" {{ old('statut', 'actif') === 'actif' ? 'selected' : '' }}>
                                            Actif</option>
                                        <option value="inactif" {{ old('statut') === 'inactif' ? 'selected' : '' }}>
                                            Inactif</option>
                                        <option value="maintenance"
                                            {{ old('statut') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                </div>
                                @error('statut')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Chauffeur --}}
                            <div class="field">
                                <label for="chauffeur_id">Chauffeur affecté</label>
                                <p class="field-hint">Optionnel — peut être assigné ultérieurement</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-user field-prefix"></i>
                                    <select id="chauffeur_id" name="chauffeur_id" class="has-prefix"
                                        onchange="updatePreview()">
                                        <option value="">— Aucun chauffeur —</option>
                                        @foreach ($chauffeurs as $c)
                                            <option value="{{ $c->id }}"
                                                {{ old('chauffeur_id') == $c->id ? 'selected' : '' }}>
                                                {{ $c->prenom }} {{ $c->nom }} ({{ $c->code_drv }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('chauffeur_id')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card" style="margin-bottom:16px">
                    <div class="preview-header">
                        <div class="preview-truck-icon">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div class="preview-header-text">
                            <div class="preview-header-label">Aperçu véhicule</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Matricule</div>
                            <div class="prev-value primary" id="prevMatricule"
                                style="font-family:'JetBrains Mono',monospace">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Marque</div>
                            <div class="prev-value" id="prevMarque">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Acquisition</div>
                            <div id="prevAcquisitionWrap"><span class="prev-value muted">Non sélectionné</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div class="prev-value" id="prevStatut">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Chauffeur</div>
                            <div class="prev-value" id="prevChauffeur">Non affecté</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="vehiculeForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer le véhicule
                    </button>
                    <a href="{{ route('vehicules.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box" style="margin-top:16px">
                    <strong>Matricule :</strong> doit être unique dans le système.<br>
                    <strong>N° châssis :</strong> identifiant VIN (optionnel mais recommandé).<br>
                    <strong>KM initial :</strong> relevé kilométrique à l'entrée en flotte.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        function updatePreview() {
            var matricule = document.getElementById('matricule').value.trim();
            var marque = document.getElementById('marque').value.trim();
            var acq = document.getElementById('acquisition').value;
            var statut = document.getElementById('statut').value;
            var chauffeurSel = document.getElementById('chauffeur_id');
            var chauffeurTxt = chauffeurSel.options[chauffeurSel.selectedIndex].text;

            document.getElementById('prevMatricule').textContent = matricule || '—';
            document.getElementById('prevMarque').textContent = marque || '—';
            document.getElementById('prevStatut').textContent = statut ? statut.charAt(0).toUpperCase() + statut.slice(1) :
                '—';

            // Chauffeur
            document.getElementById('prevChauffeur').textContent =
                (chauffeurSel.value ? chauffeurTxt : 'Non affecté');

            // Acquisition badge
            var wrap = document.getElementById('prevAcquisitionWrap');
            if (acq === 'achat') {
                wrap.innerHTML =
                    '<span class="prev-badge prev-badge-achat"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Achat</span>';
            } else if (acq === 'location') {
                wrap.innerHTML =
                    '<span class="prev-badge prev-badge-location"><i class="fa-solid fa-file-contract" style="font-size:9px"></i> Location</span>';
            } else {
                wrap.innerHTML = '<span class="prev-value muted">Non sélectionné</span>';
            }
        }

        // Init depuis old() si présent
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });
    </script>
@endpush
