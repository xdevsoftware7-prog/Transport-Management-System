{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UNE SEMI-REMORQUE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $semiRemorque : App\Models\SemiRemorque
|
| ROUTE : GET  /semi-remorques/{semiRemorque}/edit → edit
|         PUT  /semi-remorques/{semiRemorque}       → update
--}}

@extends('layouts.app')

@section('title', 'Modifier · ' . $semiRemorque->matricule)
@section('page-title', 'Modifier la Semi-Remorque')
@section('page-subtitle', $semiRemorque->matricule . ' — ' . $semiRemorque->marque)

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
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: .5px
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

        .edit-banner-marque {
            font-size: 12px;
            color: var(--color-primary);
            background: rgba(224, 32, 32, .1);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 6px;
            padding: 5px 12px;
            font-weight: 700
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

        .field input.is-invalid,
        .field select.is-invalid {
            border-color: var(--color-primary)
        }

        .field input::placeholder {
            color: var(--text-muted)
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

        /* CHANGE INDICATOR */
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

        /* COMPARE */
        .compare-box {
            display: grid;
            grid-template-columns: 1fr 24px 1fr;
            gap: 0;
            margin-bottom: 20px;
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

        .compare-val {
            font-size: 13px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: .4px
        }

        .compare-side.before .compare-val {
            color: var(--text-muted)
        }

        .compare-side.after .compare-val {
            color: var(--color-primary)
        }

        /* TOGGLE */
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

        @media(max-width:480px) {
            .meta-grid {
                grid-template-columns: 1fr
            }

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
        <span>Modifier · #{{ $semiRemorque->id }}</span>
    </div>

    {{-- BANDEAU --}}
    <div class="edit-banner">
        <div class="edit-banner-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="edit-banner-text">
            <strong>{{ $semiRemorque->matricule }}</strong>
            <span>{{ $semiRemorque->marque }} · {{ $semiRemorque->type_remorque }} · Modifié
                {{ $semiRemorque->updated_at->diffForHumans() }}</span>
        </div>
        <div class="edit-banner-right">
            <span class="edit-banner-marque">{{ strtoupper($semiRemorque->marque) }}</span>
            <span class="edit-banner-id">#{{ $semiRemorque->id }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('semi_remorques.update', $semiRemorque) }}" id="remorqueForm" novalidate>
        @csrf @method('PUT')
        <input type="hidden" id="isActiveHidden" name="is_active"
            value="{{ old('is_active', $semiRemorque->is_active ? '1' : '0') }}">

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
                        <div class="change-indicator" id="changeIndicator">
                            <i class="fa-solid fa-circle-exclamation"></i> Modifications non sauvegardées
                        </div>
                    </div>
                    <div style="padding:20px">

                        {{-- Comparaison matricule --}}
                        <div class="compare-box">
                            <div class="compare-side before">
                                <div class="compare-side-label"><i class="fa-solid fa-clock-rotate-left"></i> Actuel</div>
                                <div class="compare-val" id="compareOrigMat">{{ $semiRemorque->matricule }}</div>
                            </div>
                            <div class="compare-sep"><i class="fa-solid fa-arrow-right"></i></div>
                            <div class="compare-side after">
                                <div class="compare-side-label"><i class="fa-solid fa-pen"></i> Nouveau</div>
                                <div class="compare-val" id="compareNewMat">
                                    {{ old('matricule', $semiRemorque->matricule) }}</div>
                            </div>
                        </div>

                        <div class="fields-grid">
                            <div class="field">
                                <label>Matricule <span class="required-star">*</span></label>
                                <p class="field-hint">Unique · Converti automatiquement en majuscules</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-hashtag field-prefix"></i>
                                    <input type="text" id="matricule" name="matricule"
                                        value="{{ old('matricule', $semiRemorque->matricule) }}" required maxlength="50"
                                        autocomplete="off"
                                        class="mono-input {{ $errors->has('matricule') ? 'is-invalid' : '' }}"
                                        oninput="this.value=this.value.toUpperCase();onFieldChange();updatePreview()">
                                </div>
                                @error('matricule')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Numéro VIN</label>
                                <p class="field-hint">Identifiant véhicule · Unique</p>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-barcode field-prefix"></i>
                                    <input type="text" name="vin" value="{{ old('vin', $semiRemorque->vin) }}"
                                        maxlength="255" autocomplete="off"
                                        class="mono-input {{ $errors->has('vin') ? 'is-invalid' : '' }}"
                                        oninput="this.value=this.value.toUpperCase();onFieldChange()">
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
                            <div class="field">
                                <label>Marque <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-industry field-prefix"></i>
                                    <input type="text" id="marque" name="marque"
                                        value="{{ old('marque', $semiRemorque->marque) }}" required maxlength="100"
                                        autocomplete="off" class="{{ $errors->has('marque') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()">
                                </div>
                                @error('marque')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Type de remorque <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-tag field-prefix"></i>
                                    {{-- <input type="text" id="typeRemorque" name="type_remorque"
                                        value="{{ old('type_remorque', $semiRemorque->type_remorque) }}" required
                                        maxlength="100" autocomplete="off"
                                        class="{{ $errors->has('type_remorque') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()"> --}}
                                    <select name="type_remorque" id="typeRemorque"
                                        class="{{ $errors->has('type_remorque') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()">
                                        <option value="tracteur"
                                            {{ old('type_vehicule',$semiRemorque->type_remorque) === 'tracteur' ? 'selected' : '' }}>Tracteur</option>
                                        <option value="semi-remorque"
                                            {{ old('type_vehicule',$semiRemorque->type_remorque) === 'semi-remorque' ? 'selected' : '' }}>Semi-remorque
                                        </option>
                                        <option value="camion" {{ old('type_vehicule',$semiRemorque->type_remorque) === 'camion' ? 'selected' : '' }}>
                                            Camion</option>
                                        <option value="fourgon"
                                            {{ old('type_vehicule',$semiRemorque->type_remorque) === 'fourgon' ? 'selected' : '' }}>
                                            Fourgon</option>
                                        <option value="benne" {{ old('type_vehicule',$semiRemorque->type_remorque) === 'benne' ? 'selected' : '' }}>
                                            Benne</option>
                                        <option value="citerne"
                                            {{ old('type_vehicule',$semiRemorque->type_remorque) === 'citerne' ? 'selected' : '' }}>
                                            Citerne</option>
                                        <option value="frigo" {{ old('type_vehicule',$semiRemorque->type_remorque) === 'frigo' ? 'selected' : '' }}>
                                            Frigo</option>
                                        <option value="plateau"
                                            {{ old('type_vehicule',$semiRemorque->type_remorque) === 'plateau' ? 'selected' : '' }}>
                                            Plateau</option>
                                    </select>
                                </div>
                                @error('type_remorque')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>PTAC <span style="color:var(--text-muted);font-weight:400;text-transform:none">(en
                                        tonnes)</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-weight-scale field-prefix"></i>
                                    <input type="number" id="ptac" name="ptac"
                                        value="{{ old('ptac', $semiRemorque->ptac) }}" step="0.01" min="0"
                                        max="999999" class="{{ $errors->has('ptac') ? 'is-invalid' : '' }}"
                                        oninput="onFieldChange();updatePreview()">
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
                        @php $initActive = old('is_active', $semiRemorque->is_active ? '1' : '0'); @endphp
                        <div class="toggle-row" onclick="toggleActive()">
                            <div>
                                <div class="toggle-label-text">Remorque active</div>
                                <div class="toggle-label-sub" id="toggleSub">
                                    {{ $initActive === '1' ? 'La remorque est disponible et opérationnelle' : 'La remorque est hors service ou inactive' }}
                                </div>
                            </div>
                            <div class="toggle-switch {{ $initActive === '1' ? 'on' : '' }}" id="toggleSwitch"></div>
                        </div>
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
                                #{{ $semiRemorque->id }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-plus"></i> Créé le</div>
                            <div class="meta-item-value">{{ $semiRemorque->created_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $semiRemorque->created_at->format('H:i') }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-pen"></i> Modifié le</div>
                            <div class="meta-item-value">{{ $semiRemorque->updated_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $semiRemorque->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- SIDEBAR --}}
            <div class="sidebar-col">
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
                            <div id="prevMatricule" class="prev-mono">{{ old('matricule', $semiRemorque->matricule) }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Marque</div>
                            <div class="prev-value primary" id="prevMarque">{{ old('marque', $semiRemorque->marque) }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div class="prev-value" id="prevType">
                                {{ old('type_remorque', $semiRemorque->type_remorque) }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">PTAC</div>
                            <div class="prev-value" id="prevPtac">
                                @if ($semiRemorque->ptac)
                                    {{ number_format($semiRemorque->ptac, 2, ',', ' ') }} T
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                @if ($initActive === '1')
                                    <span
                                        style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i
                                            class="fa-solid fa-circle" style="font-size:6px"></i> Active</span>
                                @else
                                    <span
                                        style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i
                                            class="fa-solid fa-circle" style="font-size:6px"></i> Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-card">
                    <button type="submit" class="btn-submit" form="remorqueForm">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('semi_remorques.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </a>
                    <button type="button" class="btn-delete"
                        onclick="handleDeleteRemorque({{ $semiRemorque->id }},'{{ addslashes($semiRemorque->matricule) }}')">
                        <i class="fa-solid fa-trash"></i> Supprimer
                    </button>
                </div>

                <div class="info-box">
                    <strong>Unicité :</strong> Le matricule et le VIN doivent rester uniques.<br>
                    <strong>Note :</strong> Les modifications sont enregistrées immédiatement après validation.
                </div>
            </div>

        </div>
    </form>

    <form method="POST" action="{{ route('semi_remorques.destroy', $semiRemorque) }}" id="deleteForm">
        @csrf @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        var _origMat = '{{ addslashes($semiRemorque->matricule) }}';
        var _origMarq = '{{ addslashes($semiRemorque->marque) }}';
        var _origType = '{{ addslashes($semiRemorque->type_remorque) }}';
        var _origPtac = '{{ $semiRemorque->ptac ?? '' }}';
        var _origActif = '{{ $semiRemorque->is_active ? '1' : '0' }}';

        function onFieldChange() {
            var mat = document.getElementById('matricule').value;
            var marq = document.getElementById('marque').value;
            var typ = document.getElementById('typeRemorque').value;
            var ptac = document.getElementById('ptac').value;
            var act = document.getElementById('isActiveHidden').value;
            var changed = mat !== _origMat || marq !== _origMarq || typ !== _origType || ptac !== _origPtac || act !==
                _origActif;
            document.getElementById('changeIndicator').classList.toggle('visible', changed);
            document.getElementById('compareNewMat').textContent = mat || '—';
        }

        function updatePreview() {
            var mat = (document.getElementById('matricule').value || '').trim();
            var marq = (document.getElementById('marque').value || '').trim();
            var typ = (document.getElementById('typeRemorque').value || '').trim();
            var ptac = (document.getElementById('ptac').value || '').trim();

            var pm = document.getElementById('prevMatricule');
            pm.textContent = mat || '—';

            var pma = document.getElementById('prevMarque');
            pma.textContent = marq || '—';
            pma.className = 'prev-value' + (marq ? ' primary' : ' muted');

            var pt = document.getElementById('prevType');
            pt.textContent = typ || '—';
            pt.className = 'prev-value' + (typ ? '' : ' muted');

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
            onFieldChange();
        }

        function handleDeleteRemorque(id, mat) {
            Swal.fire({
                title: 'Supprimer la remorque ?',
                text: `Êtes-vous sûr de vouloir supprimer "${mat}" ? Cette action est irréversible.`,
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
