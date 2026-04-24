{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UNE INFRACTION — OBTRANS TMS
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
|   - $infraction : App\Models\Infraction
|   - $vehicules  : Collection (id, matricule, marque)
|   - $chauffeurs : Collection (id, code_drv, nom, prenom)
|
| ROUTE : PUT /infractions/{infraction} → InfractionController@update
--}}

@extends('layouts.app')

@section('title', 'Modifier — Infraction #' . $infraction->id)
@section('page-title', 'Modifier l\'Infraction')
@section('page-subtitle', 'Mettre à jour les informations de l\'infraction')

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

        /* ── BANDEAU ÉDITION ── */
        .edit-banner {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--color-dark);
            border-radius: var(--border-radius);
            padding: 14px 22px;
            margin-bottom: 4px;
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
            flex-shrink: 0;
        }

        .edit-banner-text strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
        }

        .edit-banner-text span {
            font-size: 12px;
            color: #666;
        }

        .edit-banner-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .edit-banner-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #555;
            background: rgba(255, 255, 255, .05);
            border: 1px solid #222;
            border-radius: 6px;
            padding: 5px 10px;
        }

        .edit-banner-montant {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--color-primary);
            background: rgba(224, 32, 32, .1);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 6px;
            padding: 5px 12px;
            font-weight: 700;
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

        .fields-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Indicateur changements */
        .change-indicator {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #d97706;
            background: rgba(245, 158, 11, .08);
            border: 1px solid rgba(245, 158, 11, .2);
            border-radius: var(--border-radius-sm);
            padding: 8px 12px;
        }

        .change-indicator.visible {
            display: flex;
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

        /* Méta info */
        .meta-card {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 3px 0;
        }

        .meta-key {
            font-weight: 700;
            color: var(--text-secondary);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .meta-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Type tabs */
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
        <span>Modifier #{{ $infraction->id }}</span>
    </nav>

    {{-- ── BANDEAU ── --}}
    <div class="edit-banner" style="margin-bottom:20px">
        <div class="edit-banner-icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="edit-banner-text">
            <strong>{{ $infraction->type_infraction }}</strong>
            <span>
                Infraction du {{ $infraction->date_infraction->format('d/m/Y') }}
                · {{ $infraction->vehicule?->matricule ?? '—' }}
            </span>
        </div>
        <div class="edit-banner-right">
            <span class="edit-banner-montant">
                {{ number_format($infraction->montant, 2, ',', ' ') }} MAD
            </span>
            <span class="edit-banner-id">#{{ $infraction->id }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('infractions.update', $infraction) }}" id="editForm">
        @csrf @method('PUT')

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Indicateur changements --}}
                <div class="change-indicator" id="changeIndicator">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Des modifications non enregistrées sont présentes.
                </div>

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
                        <div class="field">
                            <label for="vehicule_id">Véhicule <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-truck field-prefix"></i>
                                <select id="vehicule_id" name="vehicule_id" onchange="markChanged();updatePreview()">
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($vehicules as $v)
                                        <option value="{{ $v->id }}"
                                            {{ old('vehicule_id', $infraction->vehicule_id) == $v->id ? 'selected' : '' }}>
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

                        <div class="field">
                            <label for="chauffeur_id">Chauffeur <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-user field-prefix"></i>
                                <select id="chauffeur_id" name="chauffeur_id" onchange="markChanged();updatePreview()">
                                    <option value="">— Sélectionner —</option>
                                    @foreach ($chauffeurs as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('chauffeur_id', $infraction->chauffeur_id) == $c->id ? 'selected' : '' }}>
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

                {{-- Détails --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-triangle-exclamation"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Détails de l'infraction
                        </h2>
                    </div>

                    <div class="fields-2col" style="margin-bottom: 14px">
                        <div class="field">
                            <label for="date_infraction">Date de l'infraction <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar field-prefix"></i>
                                <input type="date" id="date_infraction" name="date_infraction"
                                    value="{{ old('date_infraction', $infraction->date_infraction->format('Y-m-d')) }}"
                                    onchange="markChanged();updatePreview()">
                            </div>
                            @error('date_infraction')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="montant">Montant (MAD) <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-coins field-prefix"></i>
                                <input type="number" id="montant" name="montant"
                                    value="{{ old('montant', $infraction->montant) }}" min="0" step="0.01"
                                    placeholder="0.00" oninput="markChanged();updatePreview()">
                            </div>
                            @error('montant')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Type --}}
                    <div class="field" style="margin-bottom: 14px">
                        <label>Type d'infraction <span style="color:var(--color-primary)">*</span></label>
                        <div class="type-tabs" id="typeTabs">
                            @foreach (['Excès de vitesse', 'Stationnement interdit', 'Feu rouge grillé', 'Défaut de documents', 'Surcharge', 'Téléphone au volant', 'Défaut d\'assurance', 'Alcool au volant'] as $t)
                                <button type="button" class="type-tab"
                                    onclick="selectType('{{ $t }}')">{{ $t }}</button>
                            @endforeach
                        </div>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-tag field-prefix"></i>
                            <input type="text" id="type_infraction" name="type_infraction"
                                value="{{ old('type_infraction', $infraction->type_infraction) }}"
                                placeholder="Type d'infraction…" maxlength="255" autocomplete="off"
                                oninput="markChanged();syncTypeInput()">
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
                        <textarea id="description" name="description" placeholder="Circonstances, lieu, références du PV…" maxlength="1000"
                            oninput="markChanged();updateDescCounter()">{{ old('description', $infraction->description) }}</textarea>
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
                            <div class="prev-value muted" id="prevVehicule">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Chauffeur</div>
                            <div class="prev-value muted" id="prevChauffeur">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Date</div>
                            <div class="prev-value" id="prevDate">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div class="prev-value" id="prevType">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Montant</div>
                            <div id="prevMontantWrap"><span class="prev-value muted">—</span></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="editForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Mettre à jour
                    </button>
                    <a href="{{ route('infractions.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                {{-- Méta données --}}
                <div class="meta-card">
                    <div class="meta-row">
                        <span class="meta-key">Créé le</span>
                        <span class="meta-val">{{ $infraction->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Modifié le</span>
                        <span class="meta-val">{{ $infraction->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">ID</span>
                        <span class="meta-val">#{{ $infraction->id }}</span>
                    </div>
                </div>

                <div class="info-box">
                    <strong>Dernière modif.</strong> il y a {{ $infraction->updated_at->diffForHumans() }}.
                    Les modifications sont enregistrées immédiatement après validation.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        const vehiculesData = @json($vehicules->map(fn($v) => ['id' => $v->id, 'label' => $v->matricule . ' — ' . $v->marque]));
        const chauffeursData = @json($chauffeurs->map(fn($c) => ['id' => $c->id, 'label' => $c->prenom . ' ' . $c->nom]));

        let hasChanges = false;

        document.addEventListener('DOMContentLoaded', function() {
            updateTypeCounter();
            updateDescCounter();
            updatePreview();
            highlightTypeTab(document.getElementById('type_infraction').value);
        });

        function markChanged() {
            if (!hasChanges) {
                hasChanges = true;
                document.getElementById('changeIndicator').classList.add('visible');
            }
        }

        // ── TYPE INFRACTION ───────────────────────────────────
        function selectType(val) {
            document.getElementById('type_infraction').value = val;
            highlightTypeTab(val);
            updateTypeCounter();
            updatePreview();
            markChanged();
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

        // ── APERÇU ────────────────────────────────────────────
        function updatePreview() {
            const vId = document.getElementById('vehicule_id').value;
            const vObj = vehiculesData.find(v => String(v.id) === vId);
            const prevV = document.getElementById('prevVehicule');
            if (vObj) {
                prevV.textContent = vObj.label;
                prevV.className = 'prev-value primary';
            } else {
                prevV.textContent = '—';
                prevV.className = 'prev-value muted';
            }

            const cId = document.getElementById('chauffeur_id').value;
            const cObj = chauffeursData.find(c => String(c.id) === cId);
            const prevC = document.getElementById('prevChauffeur');
            if (cObj) {
                prevC.textContent = cObj.label;
                prevC.className = 'prev-value primary';
            } else {
                prevC.textContent = '—';
                prevC.className = 'prev-value muted';
            }

            const d = document.getElementById('date_infraction').value;
            document.getElementById('prevDate').textContent = d ?
                new Date(d).toLocaleDateString('fr-FR') : '—';

            const t = document.getElementById('type_infraction').value.trim();
            const prevT = document.getElementById('prevType');
            prevT.textContent = t || '—';
            prevT.className = 'prev-value' + (t ? '' : ' muted');

            const m = parseFloat(document.getElementById('montant').value);
            const mWrap = document.getElementById('prevMontantWrap');
            if (!isNaN(m) && m >= 0) {
                mWrap.innerHTML =
                    '<span class="prev-montant-badge"><i class="fa-solid fa-coins" style="font-size:11px"></i>' +
                    m.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2
                    }) + ' MAD</span>';
            } else {
                mWrap.innerHTML = '<span class="prev-value muted">—</span>';
            }
        }

        // ── ALERTE NAVIGATION ─────────────────────────────────
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        document.getElementById('editForm').addEventListener('submit', function() {
            hasChanges = false;
        });
    </script>
@endpush
