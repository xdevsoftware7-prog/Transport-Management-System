{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE MAINTENANCE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $vehicules : Collection (id, matricule, marque, type_vehicule)
|
| ROUTE : POST /maintenances → MaintenanceController@store
--}}

@extends('layouts.app')

@section('title', 'Nouvelle Maintenance')
@section('page-title', 'Nouvelle Maintenance')
@section('page-subtitle', 'Enregistrer une intervention sur un véhicule')

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
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition)
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted)
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px
        }

        /* ── GRILLE 2 COL ── */
        .fields-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        /* ── BADGE STATUT SELECT ── */
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

        .statut-pill:hover {
            border-color: var(--border);
            filter: brightness(.97)
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
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('maintenances.index') }}">Maintenances</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle</span>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('maintenances.store') }}" id="maintenanceForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

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
                        <p class="field-hint">Sélectionnez le véhicule à entretenir</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-truck field-prefix"></i>
                            <select id="vehicule_id" name="vehicule_id" required onchange="updatePreview()">
                                <option value="">— Choisir un véhicule —</option>
                                @foreach ($vehicules as $v)
                                    <option value="{{ $v->id }}" data-marque="{{ $v->marque }}"
                                        data-type="{{ $v->type_vehicule }}"
                                        {{ old('vehicule_id') == $v->id ? 'selected' : '' }}>
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
                        <p class="field-hint">Description de l'opération réalisée · 255 caractères max</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-wrench field-prefix"></i>
                            <input type="text" id="type_intervention" name="type_intervention"
                                value="{{ old('type_intervention') }}"
                                placeholder="Ex : Vidange moteur, Changement pneus avant…" required maxlength="255"
                                autocomplete="off" autofocus oninput="updatePreview()">
                        </div>
                        <div class="char-counter" id="interventionCounter">0 / 255</div>
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
                            <p class="field-hint">Laisser vide si non connu</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-coins field-prefix"></i>
                                <input type="number" id="cout_total" name="cout_total" value="{{ old('cout_total') }}"
                                    placeholder="0.00" step="0.01" min="0" oninput="updatePreview()">
                            </div>
                            @error('cout_total')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label>Statut <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">État actuel de l'intervention</p>
                            <div class="statut-pills" id="statutPills">
                                @foreach ([
            'en_attente' => ['fa-clock', 'En attente', 'pill-en_attente'],
            'en_cours' => ['fa-spinner', 'En cours', 'pill-en_cours'],
            'terminée' => ['fa-circle-check', 'Terminée', 'pill-terminee'],
        ] as $val => [$icon, $label, $cls])
                                    <label
                                        class="statut-pill {{ $cls }} {{ old('statut', 'en_attente') === $val ? 'selected' : '' }}"
                                        onclick="selectStatut('{{ $val }}', this)">
                                        <input type="radio" name="statut" value="{{ $val }}"
                                            {{ old('statut', 'en_attente') === $val ? 'checked' : '' }}>
                                        <i class="fa-solid {{ $icon }}"></i> {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                            @error('statut')
                                <span class="field-error" style="margin-top:6px"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
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
                                <input type="date" id="date_debut" name="date_debut" value="{{ old('date_debut') }}"
                                    required oninput="updatePreview()">
                            </div>
                            @error('date_debut')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="date_fin">Date de fin</label>
                            <p class="field-hint">Optionnelle — postérieure à la date début</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar-check field-prefix"></i>
                                <input type="date" id="date_fin" name="date_fin" value="{{ old('date_fin') }}"
                                    oninput="updatePreview()">
                            </div>
                            @error('date_fin')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
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
                            <div class="prev-value primary" id="prevVehicule">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Intervention</div>
                            <div class="prev-value" id="prevIntervention"><span class="muted">Non renseignée</span>
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Coût</div>
                            <div class="prev-value" id="prevCout"><span class="muted">—</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Période</div>
                            <div class="prev-value" id="prevPeriode"><span class="muted">—</span></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="maintenanceForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer la maintenance
                    </button>
                    <a href="{{ route('maintenances.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Astuce :</strong> La date de fin est optionnelle pour les interventions en attente ou en cours.
                    Elle peut être renseignée lors de la clôture.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        // ── Compteur désignation ────────────────────────────────────
        var ti = document.getElementById('type_intervention');
        var counter = document.getElementById('interventionCounter');
        if (ti) {
            ti.addEventListener('input', function() {
                var len = this.value.length;
                counter.textContent = len + ' / 255';
                counter.className = 'char-counter' + (len >= 255 ? ' over' : len >= 204 ? ' warn' : '');
            });
        }

        // ── Sélection statut pills ──────────────────────────────────
        function selectStatut(val, el) {
            document.querySelectorAll('.statut-pill').forEach(p => p.classList.remove('selected'));
            el.classList.add('selected');
            updatePreview();
        }

        // ── Aperçu ──────────────────────────────────────────────────
        function updatePreview() {
            var sel = document.getElementById('vehicule_id');
            var opt = sel ? sel.options[sel.selectedIndex] : null;

            // Véhicule
            var pv = document.getElementById('prevVehicule');
            if (opt && opt.value) {
                pv.textContent = sel.options[sel.selectedIndex].text;
                pv.className = 'prev-value primary';
            } else {
                pv.innerHTML = '<span class="muted">—</span>';
                pv.className = 'prev-value';
            }

            // Intervention
            var ti = (document.getElementById('type_intervention') || {}).value || '';
            var pi = document.getElementById('prevIntervention');
            pi.textContent = ti || '';
            pi.innerHTML = ti ? ti : '<span class="muted">Non renseignée</span>';

            // Coût
            var cout = parseFloat((document.getElementById('cout_total') || {}).value);
            var pc = document.getElementById('prevCout');
            pc.innerHTML = !isNaN(cout) && cout >= 0 ?
                '<strong>' + cout.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2
                }) + ' MAD</strong>' :
                '<span class="muted">—</span>';

            // Période
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
        }

        document.addEventListener('DOMContentLoaded', updatePreview);
    </script>
@endpush
