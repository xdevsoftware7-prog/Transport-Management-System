{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE ABSENCE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $chauffeurs : Collection des chauffeurs
|
| ROUTE : POST /absences → AbsenceController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouvelle Absence')
@section('page-title', 'Nouvelle Absence')
@section('page-subtitle', 'Enregistrer une absence chauffeur')

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

        /* Layout */
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

        /* Champs */
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

        .field-input-wrap input,
        .field-input-wrap select {
            padding-left: 34px;
        }

        .field input[type="text"],
        .field input[type="date"],
        .field input[type="time"],
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

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted);
        }

        .field textarea {
            resize: vertical;
            min-height: 80px;
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Grid 2 colonnes pour les champs heures */
        .field-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        /* Sidebar */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .preview-header {
            background: var(--color-dark);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-absence-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--color-primary);
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
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .prev-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
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

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-weight: 400;
            font-style: italic;
        }

        .prev-badge-time {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 700;
            padding: 3px 9px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace;
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px;
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
            transition: background var(--transition);
        }

        .btn-submit:hover {
            background: #c01010;
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
            gap: 8px;
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
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .info-box strong {
            color: var(--text-primary);
        }

        @media (max-width:900px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-col {
                order: -1;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('absences.index') }}">Absences</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle absence</span>
    </div>

    <form method="POST" action="{{ route('absences.store') }}" id="absenceForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Chauffeur --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-user"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Chauffeur
                        </h2>
                    </div>
                    <div class="field">
                        <label for="chauffeur_id">Chauffeur <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Sélectionnez le chauffeur concerné par l'absence</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-user field-prefix"></i>
                            <select id="chauffeur_id" name="chauffeur_id" required>
                                <option value="">— Sélectionner un chauffeur —</option>
                                @foreach ($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->id }}"
                                        {{ old('chauffeur_id') == $chauffeur->id ? 'selected' : '' }}>
                                        {{ $chauffeur->nom }} {{ $chauffeur->prenom }} — {{ $chauffeur->code_drv }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('chauffeur_id')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Date & Heures --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-calendar-day"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Date & Horaires
                        </h2>
                    </div>

                    {{-- Date d'absence --}}
                    <div class="field" style="margin-bottom:16px">
                        <label for="date_absence">Date d'absence <span style="color:var(--color-primary)">*</span></label>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-calendar field-prefix"></i>
                            <input type="date" id="date_absence" name="date_absence" value="{{ old('date_absence') }}"
                                required autocomplete="off">
                        </div>
                        @error('date_absence')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Heure entrée / sortie --}}
                    <div class="field-row-2">
                        <div class="field">
                            <label for="heure_entree">Heure d'entrée <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-regular fa-clock field-prefix"></i>
                                <input type="time" id="heure_entree" name="heure_entree"
                                    value="{{ old('heure_entree') }}" required>
                            </div>
                            @error('heure_entree')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="heure_sortie">Heure de sortie <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-regular fa-clock field-prefix"></i>
                                <input type="time" id="heure_sortie" name="heure_sortie"
                                    value="{{ old('heure_sortie') }}" required>
                            </div>
                            @error('heure_sortie')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Heures sup & Motif --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-clock-rotate-left"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Heures supplémentaires & Motif
                        </h2>
                    </div>

                    <div class="field" style="margin-bottom:16px">
                        <label for="heures_sup">Heures supplémentaires</label>
                        <p class="field-hint">Valeur décimale · ex : 1.50 = 1h30 · max 99.99</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-plus field-prefix"></i>
                            <input type="number" id="heures_sup" name="heures_sup" value="{{ old('heures_sup', '0.00') }}"
                                step="0.01" min="0" max="99.99" placeholder="0.00" style="padding-left:34px">
                        </div>
                        @error('heures_sup')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="motif">Motif</label>
                        <p class="field-hint">Raison de l'absence · 255 caractères max</p>
                        <textarea id="motif" name="motif" maxlength="255"
                            placeholder="Ex : Congé maladie, absence injustifiée, rendez-vous médical…">{{ old('motif') }}</textarea>
                        <div class="char-counter" id="motifCounter">0 / 255</div>
                        @error('motif')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-absence-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                        <div class="preview-header-text">
                            <div class="preview-header-label">Aperçu absence</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Chauffeur</div>
                            <div class="prev-value primary" id="prevChauffeur">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Date</div>
                            <div class="prev-value" id="prevDate">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Horaires</div>
                            <div id="prevHoraires"><span class="prev-value muted">Non renseignés</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">H. Sup.</div>
                            <div class="prev-value" id="prevSup">—</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card" style="margin-top:16px">
                    <button type="submit" class="btn-submit" form="absenceForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer l'absence
                    </button>
                    <a href="{{ route('absences.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box" style="margin-top:12px">
                    <strong>Note :</strong> L'heure de sortie doit être postérieure à l'heure d'entrée.
                    Les heures supplémentaires sont facultatives.
                </div>

            </div>

        </div>
    </form>

    <style>
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
    </style>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chauffeurSel = document.getElementById('chauffeur_id');
            var dateInput = document.getElementById('date_absence');
            var entreeInput = document.getElementById('heure_entree');
            var sortieInput = document.getElementById('heure_sortie');
            var supInput = document.getElementById('heures_sup');
            var motifInput = document.getElementById('motif');
            var motifCounter = document.getElementById('motifCounter');

            // Compteur motif
            motifInput.addEventListener('input', function() {
                var len = this.value.length;
                motifCounter.textContent = len + ' / 255';
                motifCounter.className = 'char-counter' + (len >= 255 ? ' over' : len >= 204 ? ' warn' :
                '');
            });

            // Aperçu
            function updatePreview() {
                var opt = chauffeurSel.options[chauffeurSel.selectedIndex];
                document.getElementById('prevChauffeur').textContent = opt && opt.value ? opt.text : '—';

                var d = dateInput.value;
                if (d) {
                    var parts = d.split('-');
                    document.getElementById('prevDate').textContent = parts[2] + '/' + parts[1] + '/' + parts[0];
                } else {
                    document.getElementById('prevDate').textContent = '—';
                }

                var e = entreeInput.value,
                    s = sortieInput.value;
                var wrap = document.getElementById('prevHoraires');
                if (e && s) {
                    wrap.innerHTML =
                        '<span class="prev-badge-time"><i class="fa-regular fa-clock" style="font-size:9px"></i>' +
                        e + '</span>' +
                        ' <span style="font-size:11px;color:var(--text-muted)">→</span> ' +
                        '<span class="prev-badge-time"><i class="fa-regular fa-clock" style="font-size:9px"></i>' +
                        s + '</span>';
                } else {
                    wrap.innerHTML = '<span class="prev-value muted">Non renseignés</span>';
                }

                var sup = parseFloat(supInput.value);
                document.getElementById('prevSup').textContent = (!isNaN(sup) && sup > 0) ? sup.toFixed(2) + 'h' :
                    '—';
            }

            [chauffeurSel, dateInput, entreeInput, sortieInput, supInput].forEach(function(el) {
                el.addEventListener('change', updatePreview);
                el.addEventListener('input', updatePreview);
            });

            updatePreview();
        });
    </script>
@endpush
