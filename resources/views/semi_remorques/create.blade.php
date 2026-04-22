{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE SEMI-REMORQUE — OBTRANS TMS
|--------------------------------------------------------------------------
| ROUTE : GET  /semi-remorques/create → SemiRemorqueController@create
|         POST /semi-remorques         → SemiRemorqueController@store
--}}

@extends('layouts.app')

@section('title', 'Nouvelle Semi-Remorque')
@section('page-title', 'Nouvelle Semi-Remorque')
@section('page-subtitle', 'Ajouter une remorque au parc')

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
            pointer-events: none
        }

        .field input[type=text],
        .field input[type=number],
        .field select {
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
        .field select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .field input.no-icon,
        .field select.no-icon {
            padding-left: 14px
        }

        .field input::placeholder {
            color: var(--text-muted)
        }

        .field input.is-invalid,
        .field select.is-invalid {
            border-color: var(--color-primary)
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px
        }

        .mono-input {
            font-family: 'JetBrains Mono', monospace !important;
            letter-spacing: .5px
        }

        /* TOGGLE ACTIF */
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: #fafafa;
            cursor: pointer;
            transition: border-color var(--transition)
        }

        .toggle-row:hover {
            border-color: var(--color-primary)
        }

        .toggle-label-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary)
        }

        .toggle-label-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px
        }

        .toggle-switch {
            width: 40px;
            height: 22px;
            border-radius: 20px;
            background: var(--border);
            position: relative;
            transition: background .2s;
            flex-shrink: 0
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            top: 3px;
            left: 3px;
            transition: transform .2s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2)
        }

        .toggle-switch.on {
            background: #10b981
        }

        .toggle-switch.on::after {
            transform: translateX(18px)
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
            font-size: 20px;
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

        .prev-mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: .5px
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
            .fields-grid {
                grid-template-columns: 1fr
            }
        }
    </style>

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('semi_remorques.index') }}">Semi-Remorques</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle remorque</span>
    </div>

    <form method="POST" action="{{ route('semi_remorques.store') }}" id="remorqueForm" novalidate>
        @csrf
        <input type="hidden" id="isActiveHidden" name="is_active" value="{{ old('is_active', '1') }}">

        <div class="form-layout">

            {{-- COLONNE PRINCIPALE --}}
            <div class="form-section">

                {{-- IDENTIFICATION --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-id-card"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Identification
                        </h2>
                    </div>
                    <div style="padding:20px">
                        <div class="fields-grid">

                            {{-- Matricule --}}
                            <div class="field">
                                <label>Matricule <span class="required-star">*</span></label>
                                <p class="field-hint">Unique · Converti automatiquement en majuscules</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-hashtag field-prefix"></i>
                                    <input type="text" id="matricule" name="matricule" value="{{ old('matricule') }}"
                                        placeholder="Ex : 12345-A-67" required maxlength="50" autocomplete="off" autofocus
                                        class="mono-input {{ $errors->has('matricule') ? 'is-invalid' : '' }}"
                                        oninput="this.value=this.value.toUpperCase();updatePreview()">
                                </div>
                                @error('matricule')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- VIN --}}
                            <div class="field">
                                <label>Numéro VIN</label>
                                <p class="field-hint">Identifiant véhicule · 17 caractères standard</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-barcode field-prefix"></i>
                                    <input type="text" name="vin" value="{{ old('vin') }}"
                                        placeholder="Ex : WDB9634031L123456" maxlength="255" autocomplete="off"
                                        class="mono-input {{ $errors->has('vin') ? 'is-invalid' : '' }}"
                                        oninput="this.value=this.value.toUpperCase()">
                                </div>
                                @error('vin')
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
                            <i class="fa-solid fa-trailer"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Caractéristiques techniques
                        </h2>
                    </div>
                    <div style="padding:20px">
                        <div class="fields-grid">

                            {{-- Marque --}}
                            <div class="field">
                                <label>Marque <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-industry field-prefix"></i>
                                    <input type="text" id="marque" name="marque" value="{{ old('marque') }}"
                                        placeholder="Ex : Schmitz, Krone, Chereau…" required maxlength="100"
                                        autocomplete="off" class="{{ $errors->has('marque') ? 'is-invalid' : '' }}"
                                        oninput="updatePreview()">
                                </div>
                                @error('marque')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Type remorque --}}
                            <div class="field">
                                <label>Type de remorque <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-tag field-prefix"></i>
                                    {{-- <input type="text" id="typeRemorque" name="type_remorque"
                                        value="{{ old('type_remorque') }}"
                                        placeholder="Ex : Frigo, Bâché, Plateau, Citerne…" required maxlength="100"
                                        autocomplete="off" class="{{ $errors->has('type_remorque') ? 'is-invalid' : '' }}"
                                        oninput="updatePreview()"> --}}
                                    <select name="type_remorque" id="type_remorque">
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
                                @error('type_remorque')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- PTAC --}}
                            <div class="field">
                                <label>PTAC <span style="color:var(--text-muted);font-weight:400;text-transform:none">(en
                                        tonnes)</span></label>
                                <p class="field-hint">Poids Total Autorisé en Charge</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-weight-scale field-prefix"></i>
                                    <input type="number" id="ptac" name="ptac" value="{{ old('ptac') }}"
                                        placeholder="Ex : 39.00" step="0.01" min="0" max="999999"
                                        class="{{ $errors->has('ptac') ? 'is-invalid' : '' }}" oninput="updatePreview()">
                                </div>
                                @error('ptac')
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
                            <i class="fa-solid fa-toggle-on"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Statut de la remorque
                        </h2>
                    </div>
                    <div style="padding:16px 20px">
                        <div class="toggle-row" onclick="toggleActive()">
                            <div>
                                <div class="toggle-label-text">Remorque active</div>
                                <div class="toggle-label-sub" id="toggleSub">La remorque est disponible et opérationnelle
                                </div>
                            </div>
                            <div class="toggle-switch on" id="toggleSwitch"></div>
                        </div>
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- SIDEBAR --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-icon"><i class="fa-solid fa-trailer"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu remorque</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Matricule</div>
                            <div id="prevMatricule" class="prev-mono"
                                style="color:var(--text-muted);font-style:italic;font-size:13px">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Marque</div>
                            <div class="prev-value primary" id="prevMarque">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div class="prev-value muted" id="prevType">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">PTAC</div>
                            <div class="prev-value muted" id="prevPtac">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                <span
                                    style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i
                                        class="fa-solid fa-circle" style="font-size:6px"></i> Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="remorqueForm">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer la remorque
                    </button>
                    <a href="{{ route('semi_remorques.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Unicité :</strong> Le matricule et le VIN doivent être uniques dans le système.<br>
                    Le matricule est automatiquement converti en majuscules.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();
        });

        function updatePreview() {
            var mat = (document.getElementById('matricule').value || '').trim();
            var marq = (document.getElementById('marque').value || '').trim();
            var typ = (document.getElementById('typeRemorque').value || '').trim();
            var ptac = (document.getElementById('ptac').value || '').trim();

            // Matricule
            var pm = document.getElementById('prevMatricule');
            if (mat) {
                pm.textContent = mat;
                pm.className = 'prev-mono';
            } else {
                pm.textContent = '—';
                pm.className = 'prev-mono';
                pm.style.color = 'var(--text-muted)';
                pm.style.fontStyle = 'italic';
            }
            if (mat) {
                pm.style.color = '';
                pm.style.fontStyle = '';
            }

            // Marque
            var pma = document.getElementById('prevMarque');
            pma.textContent = marq || '—';
            pma.className = 'prev-value' + (marq ? ' primary' : ' muted');

            // Type
            var pt = document.getElementById('prevType');
            pt.textContent = typ || '—';
            pt.className = 'prev-value' + (typ ? '' : ' muted');

            // PTAC
            var pp = document.getElementById('prevPtac');
            if (ptac) {
                pp.textContent = parseFloat(ptac).toFixed(2).replace('.', ',') + ' T';
                pp.className = 'prev-value';
            } else {
                pp.textContent = '—';
                pp.className = 'prev-value muted';
            }
        }

        function toggleActive() {
            var hidden = document.getElementById('isActiveHidden');
            var sw = document.getElementById('toggleSwitch');
            var sub = document.getElementById('toggleSub');
            var wrap = document.getElementById('prevStatutWrap');
            var isOn = hidden.value === '1';
            hidden.value = isOn ? '0' : '1';
            sw.classList.toggle('on', !isOn);
            sub.textContent = !isOn ? 'La remorque est disponible et opérationnelle' :
                'La remorque est hors service ou inactive';
            wrap.innerHTML = !isOn ?
                '<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i class="fa-solid fa-circle" style="font-size:6px"></i> Active</span>' :
                '<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i class="fa-solid fa-circle" style="font-size:6px"></i> Inactive</span>';
        }
    </script>
@endpush
