{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE INFRACTION — OBTRANS TMS
|--------------------------------------------------------------------------
| CHAMPS :
|   - vehicule_id     required|exists:vehicules,id
|   - chauffeur_id    required|exists:chauffeurs,id
|   - date_infraction required|date
|   - type_infraction required|string|max:255
|   - montant         required|numeric|min:0
|   - description     nullable|string|max:1000
|
| VARIABLES ATTENDUES :
|   - $vehicules  : Collection (id, matricule, marque)
|   - $chauffeurs : Collection (id, code_drv, nom, prenom)
|
| ROUTE : POST /infractions → InfractionController@store
--}}

@extends('layouts.app')

@section('title', 'Nouvelle Infraction')
@section('page-title', 'Nouvelle Infraction')
@section('page-subtitle', 'Enregistrer une infraction liée à un véhicule ou un chauffeur')

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

        /* ── FIELDS ── */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
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

        .field-input-wrap input,
        .field-input-wrap select,
        .field-input-wrap textarea {
            padding-left: 34px;
        }

        .field input[type="text"],
        .field input[type="date"],
        .field input[type="number"],
        .field select,
        .field textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: #fafafa;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field select {
            cursor: pointer;
        }

        .field textarea {
            resize: vertical;
            min-height: 100px;
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted);
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .char-counter {
            font-size: 11px;
            color: var(--text-muted);
            text-align: right;
            margin-top: -2px;
            transition: color .2s;
        }

        .char-counter.warn {
            color: #f59e0b;
        }

        .char-counter.over {
            color: var(--color-primary);
        }

        /* Grille 2 colonnes pour les fields */
        .fields-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
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
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: var(--color-dark);
            border-bottom: 1px solid #222;
        }

        .preview-infraction-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 16px;
            flex-shrink: 0;
        }

        .preview-header-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .preview-header-sub {
            font-size: 11px;
            color: #555;
            margin-top: 1px;
        }

        .preview-body {
            padding: 14px 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .prev-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
        }

        .prev-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-weight: 400;
            font-style: italic;
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-montant-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 800;
            color: var(--color-primary);
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 8px;
            padding: 6px 12px;
        }

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
            background: #c01b1b;
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
            font-weight: 600;
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
            border-color: var(--color-primary);
            color: var(--color-primary);
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

        .info-box strong {
            color: var(--text-secondary);
        }

        /* Type infraction quick tabs */
        .type-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
        }

        .type-tab {
            padding: 5px 12px;
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            background: var(--bg-body);
            cursor: pointer;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
        }

        .type-tab:hover,
        .type-tab.selected {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim);
        }
    </style>

    {{-- ── BREADCRUMB ── --}}
    <nav class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('infractions.index') }}">Infractions</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle infraction</span>
    </nav>

    <form method="POST" action="{{ route('infractions.store') }}" id="infractionForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Véhicule & Chauffeur --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-truck"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Véhicule & Chauffeur
                        </h2>
                    </div>

                    <div class="fields-2col">
                        {{-- Véhicule --}}
                        <div class="field">
                            <label for="vehicule_id">Véhicule <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-truck field-prefix"></i>
                                <select id="vehicule_id" name="vehicule_id" onchange="updatePreview()">
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($vehicules as $v)
                                        <option value="{{ $v->id }}"
                                            {{ old('vehicule_id') == $v->id ? 'selected' : '' }}>
                                            {{ $v->matricule }} — {{ $v->marque }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vehicule_id')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Chauffeur --}}
                        <div class="field">
                            <label for="chauffeur_id">Chauffeur <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-user field-prefix"></i>
                                <select id="chauffeur_id" name="chauffeur_id" onchange="updatePreview()">
                                    <option value="">— Sélectionner —</option>
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

                {{-- Détails de l'infraction --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-triangle-exclamation"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Détails de l'infraction
                        </h2>
                    </div>

                    <div class="fields-2col" style="margin-bottom: 14px">
                        {{-- Date --}}
                        <div class="field">
                            <label for="date_infraction">Date de l'infraction <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar field-prefix"></i>
                                <input type="date" id="date_infraction" name="date_infraction"
                                    value="{{ old('date_infraction', date('Y-m-d')) }}" onchange="updatePreview()">
                            </div>
                            @error('date_infraction')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Montant --}}
                        <div class="field">
                            <label for="montant">Montant (MAD) <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-coins field-prefix"></i>
                                <input type="number" id="montant" name="montant" value="{{ old('montant') }}"
                                    min="0" step="0.01" placeholder="0.00" oninput="updatePreview()">
                            </div>
                            @error('montant')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Type d'infraction --}}
                    <div class="field" style="margin-bottom: 14px">
                        <label>Type d'infraction <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Choisissez parmi les types courants ou saisissez librement</p>

                        <div class="type-tabs" id="typeTabs">
                            @foreach (['Excès de vitesse', 'Stationnement interdit', 'Feu rouge grillé', 'Défaut de documents', 'Surcharge', 'Téléphone au volant', 'Défaut d\'assurance', 'Alcool au volant'] as $t)
                                <button type="button" class="type-tab"
                                    onclick="selectType('{{ $t }}')">{{ $t }}</button>
                            @endforeach
                        </div>

                        <div class="field-input-wrap">
                            <i class="fa-solid fa-tag field-prefix"></i>
                            <input type="text" id="type_infraction" name="type_infraction"
                                value="{{ old('type_infraction') }}" placeholder="Type d'infraction…" maxlength="255"
                                autocomplete="off" oninput="syncTypeInput()" onchange="updatePreview()">
                        </div>
                        <div class="char-counter" id="typeCounter">0 / 255</div>
                        @error('type_infraction')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="field">
                        <label for="description">Description</label>
                        <p class="field-hint">Détails complémentaires sur l'infraction · 1000 caractères max</p>
                        <textarea id="description" name="description" placeholder="Circonstances, lieu, références du PV…" maxlength="1000"
                            oninput="updateDescCounter()">{{ old('description') }}</textarea>
                        <div class="char-counter" id="descCounter">0 / 1000</div>
                        @error('description')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col" style="display:flex;flex-direction:column;gap:16px">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-infraction-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="preview-header-text">
                            <div class="preview-header-label">Aperçu infraction</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Véhicule</div>
                            <div class="prev-value muted" id="prevVehicule">Non sélectionné</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Chauffeur</div>
                            <div class="prev-value muted" id="prevChauffeur">Non sélectionné</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Date</div>
                            <div class="prev-value" id="prevDate">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div class="prev-value muted" id="prevType">Non renseigné</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Montant</div>
                            <div id="prevMontantWrap">
                                <span class="prev-value muted">Non renseigné</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="infractionForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer l'infraction
                    </button>
                    <a href="{{ route('infractions.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Remarque :</strong> Tous les champs marqués d'un
                    <span style="color:var(--color-primary)">*</span> sont obligatoires.
                    Le montant doit être en dirhams marocains (MAD).
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        // ── DONNÉES POUR L'APERÇU ─────────────────────────────
        const vehiculesData = @json($vehicules->map(fn($v) => ['id' => $v->id, 'label' => $v->matricule . ' — ' . $v->marque]));
        const chauffeursData = @json($chauffeurs->map(fn($c) => ['id' => $c->id, 'label' => $c->prenom . ' ' . $c->nom]));

        document.addEventListener('DOMContentLoaded', function() {
            // Init compteurs
            updateTypeCounter();
            updateDescCounter();
            updatePreview();

            // Sync onglets si old() présent
            const oldType = document.getElementById('type_infraction').value;
            if (oldType) highlightTypeTab(oldType);
        });

        // ── TYPE INFRACTION ───────────────────────────────────
        function selectType(val) {
            document.getElementById('type_infraction').value = val;
            highlightTypeTab(val);
            updateTypeCounter();
            updatePreview();
        }

        function syncTypeInput() {
            const val = document.getElementById('type_infraction').value;
            highlightTypeTab(val);
            updateTypeCounter();
            updatePreview();
        }

        function highlightTypeTab(val) {
            document.querySelectorAll('.type-tab').forEach(function(t) {
                t.classList.toggle('selected', t.textContent.trim() === val);
            });
        }

        function updateTypeCounter() {
            const len = document.getElementById('type_infraction').value.length;
            const el = document.getElementById('typeCounter');
            el.textContent = len + ' / 255';
            el.className = 'char-counter' + (len >= 255 ? ' over' : len >= 204 ? ' warn' : '');
        }

        function updateDescCounter() {
            const len = document.getElementById('description').value.length;
            const el = document.getElementById('descCounter');
            el.textContent = len + ' / 1000';
            el.className = 'char-counter' + (len >= 1000 ? ' over' : len >= 800 ? ' warn' : '');
        }

        // ── APERÇU TEMPS RÉEL ─────────────────────────────────
        function updatePreview() {
            // Véhicule
            const vId = document.getElementById('vehicule_id').value;
            const vObj = vehiculesData.find(v => String(v.id) === vId);
            const prevV = document.getElementById('prevVehicule');
            if (vObj) {
                prevV.textContent = vObj.label;
                prevV.className = 'prev-value primary';
            } else {
                prevV.textContent = 'Non sélectionné';
                prevV.className = 'prev-value muted';
            }

            // Chauffeur
            const cId = document.getElementById('chauffeur_id').value;
            const cObj = chauffeursData.find(c => String(c.id) === cId);
            const prevC = document.getElementById('prevChauffeur');
            if (cObj) {
                prevC.textContent = cObj.label;
                prevC.className = 'prev-value primary';
            } else {
                prevC.textContent = 'Non sélectionné';
                prevC.className = 'prev-value muted';
            }

            // Date
            const d = document.getElementById('date_infraction').value;
            document.getElementById('prevDate').textContent = d ?
                new Date(d).toLocaleDateString('fr-FR') : '—';

            // Type
            const t = document.getElementById('type_infraction').value.trim();
            const prevT = document.getElementById('prevType');
            if (t) {
                prevT.textContent = t;
                prevT.className = 'prev-value';
            } else {
                prevT.textContent = 'Non renseigné';
                prevT.className = 'prev-value muted';
            }

            // Montant
            const m = parseFloat(document.getElementById('montant').value);
            const mWrap = document.getElementById('prevMontantWrap');
            if (!isNaN(m) && m >= 0) {
                mWrap.innerHTML =
                    '<span class="prev-montant-badge"><i class="fa-solid fa-coins" style="font-size:11px"></i>' +
                    m.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2
                    }) + ' MAD</span>';
            } else {
                mWrap.innerHTML = '<span class="prev-value muted">Non renseigné</span>';
            }
        }
    </script>
@endpush
