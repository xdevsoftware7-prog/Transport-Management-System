{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN CHAUFFEUR — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $nextCode         : string  (code généré automatiquement, ex: DRV-00012)
|   - $categoriesPermis : array   (liste des catégories disponibles)
|
| ROUTE : GET  /chauffeurs/create → ChauffeurController@create
|         POST /chauffeurs        → ChauffeurController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouveau chauffeur')
@section('page-title', 'Nouveau chauffeur')
@section('page-subtitle', 'Renseigner les informations du chauffeur')

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

        .form-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }

        @media (max-width:960px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-col {
                order: 2;
            }

            .form-section {
                order: 1;
            }
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .section-header {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .section-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .fields-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .fields-grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
        }

        @media (max-width:640px) {

            .fields-grid-2,
            .fields-grid-3 {
                grid-template-columns: 1fr;
            }
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
        }

        .field-hint {
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
            font-size: 12px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .field input,
        .field select {
            padding: 10px 12px 10px 36px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            width: 100%;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field-error {
            font-size: 11px;
            color: var(--color-primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Code généré — lecture seule */
        .code-readonly {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: var(--bg-body);
            border: 1.5px dashed var(--border);
            border-radius: var(--border-radius-sm);
            font-family: 'JetBrains Mono', monospace;
            font-size: 15px;
            font-weight: 700;
            color: var(--color-primary);
            letter-spacing: 1px;
            width: 100%;
        }

        .code-readonly .code-auto-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted);
            font-family: 'DM Sans', sans-serif;
            padding: 2px 7px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 20px;
            margin-left: auto;
        }

        /* ── PERMIS SELECTOR ── */
        .permis-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .permis-toggle {
            position: relative;
            cursor: pointer;
        }

        .permis-toggle input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .permis-toggle-label {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border: 1.5px solid var(--border);
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-secondary);
            background: var(--bg-body);
            cursor: pointer;
            user-select: none;
            transition: border-color .15s, background .15s, color .15s, box-shadow .15s;
        }

        .permis-toggle input:checked+.permis-toggle-label {
            border-color: #2563eb;
            background: rgba(59, 130, 246, .08);
            color: #2563eb;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, .15);
        }

        .permis-toggle input:checked+.permis-toggle-label::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 10px;
        }

        .permis-toggle-label:hover {
            border-color: #2563eb;
            color: #2563eb;
        }

        .permis-selected-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-top: 8px;
            min-height: 22px;
        }

        .permis-badge-prev {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(59, 130, 246, .08);
            border: 1px solid rgba(59, 130, 246, .25);
            color: #2563eb;
            font-family: 'JetBrains Mono', monospace;
        }

        /* File drop */
        .file-drop-zone {
            border: 2px dashed var(--border);
            border-radius: var(--border-radius-sm);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color var(--transition), background var(--transition);
            background: var(--bg-body);
            position: relative;
        }

        .file-drop-zone:hover,
        .file-drop-zone.dragover {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .file-drop-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-drop-icon {
            font-size: 22px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .file-drop-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .file-drop-label strong {
            color: var(--color-primary);
        }

        .file-preview {
            display: none;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
            padding: 8px 12px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 12px;
        }

        .file-preview.show {
            display: flex;
        }

        .file-preview-name {
            font-weight: 600;
            color: var(--text-primary);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-preview-remove {
            color: var(--color-primary);
            cursor: pointer;
            font-size: 11px;
        }

        /* Sidebar */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .preview-header {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-drv-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--color-primary);
            font-weight: 700;
        }

        .preview-header-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .preview-header-sub {
            font-size: 11px;
            color: var(--text-muted);
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
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn-submit {
            width: 100%;
            padding: 11px 16px;
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
            background: #c51a1a;
        }

        .btn-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            background: transparent;
            color: var(--text-muted);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-cancel:hover {
            border-color: var(--text-muted);
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

        /* Flash */
        .flash {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: var(--border-radius-sm);
            padding: 12px 16px;
            font-size: 13px;
        }

        .flash-error {
            background: rgba(224, 32, 32, .06);
            border: 1px solid rgba(224, 32, 32, .2);
            border-left: 3px solid var(--color-primary);
            color: var(--color-primary);
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('chauffeurs.index') }}">Chauffeurs</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouveau</span>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            Veuillez corriger les {{ $errors->count() }} erreur(s) ci-dessous.
        </div>
    @endif

    <form method="POST" action="{{ route('chauffeurs.store') }}" enctype="multipart/form-data" id="chauffeurForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Identité --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-user"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Identité du chauffeur
                        </h2>
                    </div>
                    <div class="section-body">

                        {{-- Code auto-généré --}}
                        <div class="field">
                            <label>Code chauffeur</label>
                            <p class="field-hint">Généré automatiquement par le système</p>
                            <div class="code-readonly">
                                <i class="fa-solid fa-hashtag" style="font-size:13px;color:var(--text-muted)"></i>
                                {{ $nextCode }}
                                <span class="code-auto-label"><i class="fa-solid fa-wand-magic-sparkles"></i> Auto</span>
                            </div>
                        </div>

                        <div class="fields-grid-2">
                            {{-- Prénom --}}
                            <div class="field">
                                <label>Prénom <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-user field-prefix"></i>
                                    <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}"
                                        placeholder="Ex : Mohamed" required maxlength="100" autocomplete="off" autofocus>
                                </div>
                                @error('prenom')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Nom --}}
                            <div class="field">
                                <label>Nom <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-user field-prefix"></i>
                                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                                        placeholder="Ex : ALAMI" required maxlength="100" autocomplete="off">
                                </div>
                                @error('nom')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="fields-grid-2">
                            {{-- Téléphone --}}
                            <div class="field">
                                <label>Téléphone <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-phone field-prefix"></i>
                                    <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}"
                                        placeholder="Ex : 06XXXXXXXX" required maxlength="20" autocomplete="off">
                                </div>
                                @error('telephone')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Statut --}}
                            <div class="field">
                                <label>Statut <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-circle-dot field-prefix"></i>
                                    <select name="statut" id="statut" style="padding-left:36px" required>
                                        <option value="">— Choisir —</option>
                                        <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>
                                            Actif</option>
                                        <option value="inactif" {{ old('statut') == 'inactif' ? 'selected' : '' }}>Inactif
                                        </option>
                                        <option value="suspendu" {{ old('statut') == 'suspendu' ? 'selected' : '' }}>
                                            Suspendu</option>
                                    </select>
                                </div>
                                @error('statut')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CIN --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-id-card"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Carte d'identité nationale (CIN)
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid-2">
                            <div class="field">
                                <label>Numéro CIN <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-id-card field-prefix"></i>
                                    <input type="text" name="cin" id="cin" value="{{ old('cin') }}"
                                        placeholder="Ex : AB123456" required maxlength="20" autocomplete="off">
                                </div>
                                @error('cin')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Date d'expiration CIN <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-calendar-xmark field-prefix"></i>
                                    <input type="date" name="date_exp_cin" value="{{ old('date_exp_cin') }}"
                                        required>
                                </div>
                                @error('date_exp_cin')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="field">
                            <label>Scan / Photo CIN <span
                                    style="color:var(--text-muted);font-weight:400">(optionnel)</span></label>
                            <p class="field-hint">Formats acceptés : PDF, JPG, PNG · Max 5 Mo</p>
                            <div class="file-drop-zone" id="cinDropZone">
                                <input type="file" name="cin_path" id="cin_path" accept=".pdf,.jpg,.jpeg,.png"
                                    onchange="handleFileChange(this,'cinDropZone','cinPreview','cinFileName')">
                                <div class="file-drop-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                <div class="file-drop-label"><strong>Cliquer</strong> ou glisser-déposer un fichier</div>
                            </div>
                            <div class="file-preview" id="cinPreview">
                                <i class="fa-solid fa-file" style="color:var(--color-primary)"></i>
                                <span class="file-preview-name" id="cinFileName"></span>
                                <span class="file-preview-remove"
                                    onclick="clearFile('cin_path','cinDropZone','cinPreview')">
                                    <i class="fa-solid fa-xmark"></i> Retirer
                                </span>
                            </div>
                            @error('cin_path')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Permis --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-car"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Permis de conduire
                        </h2>
                    </div>
                    <div class="section-body">

                        {{-- Date expiration --}}
                        <div class="field" style="max-width:320px">
                            <label>Date d'expiration du permis <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar-xmark field-prefix"></i>
                                <input type="date" name="date_exp_permis" value="{{ old('date_exp_permis') }}"
                                    required>
                            </div>
                            @error('date_exp_permis')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Catégories --}}
                        <div class="field">
                            <label>Catégories de permis</label>
                            <p class="field-hint">Sélectionnez une ou plusieurs catégories</p>
                            <div class="permis-selector" id="permisSelector">
                                @foreach ($categoriesPermis as $cat)
                                    <label class="permis-toggle">
                                        <input type="checkbox" name="categories[]" value="{{ $cat }}"
                                            {{ in_array($cat, old('categories', [])) ? 'checked' : '' }}
                                            onchange="updatePermisPreview()">
                                        <span class="permis-toggle-label">{{ $cat }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="permis-selected-preview" id="permisPreview">
                                <span style="font-size:11px;color:var(--text-muted);font-style:italic">Aucune catégorie
                                    sélectionnée</span>
                            </div>
                            @error('categories')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Rémunération --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-money-bill-wave"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Rémunération
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid-2">
                            <div class="field">
                                <label>Salaire net (MAD) <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-coins field-prefix"></i>
                                    <input type="number" name="salaire_net" id="salaire_net"
                                        value="{{ old('salaire_net') }}" placeholder="0.00" min="0"
                                        step="0.01" required>
                                </div>
                                @error('salaire_net')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Salaire brut (MAD) <span style="color:var(--color-primary)">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-coins field-prefix"></i>
                                    <input type="number" name="salaire_brut" id="salaire_brut"
                                        value="{{ old('salaire_brut') }}" placeholder="0.00" min="0"
                                        step="0.01" required>
                                </div>
                                @error('salaire_brut')
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
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-drv-icon" id="previewAvatar">?</div>
                        <div>
                            <div class="preview-header-label">Aperçu chauffeur</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Code</div>
                            <div class="prev-value"
                                style="font-family:'JetBrains Mono',monospace;font-size:13px;color:var(--color-primary)">
                                {{ $nextCode }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Nom complet</div>
                            <div class="prev-value muted" id="prevNom">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">CIN</div>
                            <div class="prev-value muted" id="prevCin">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Téléphone</div>
                            <div class="prev-value muted" id="prevTel">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatut"><span class="prev-value muted">—</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Catégories permis</div>
                            <div id="prevPermis" style="display:flex;flex-wrap:wrap;gap:4px;margin-top:2px">
                                <span class="prev-value muted" style="font-size:11px">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="chauffeurForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer le chauffeur
                    </button>
                    <a href="{{ route('chauffeurs.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong><i class="fa-solid fa-wand-magic-sparkles" style="color:var(--color-primary)"></i> Code
                        automatique :</strong>
                    Le code chauffeur est généré automatiquement par le système au format
                    <strong>DRV-XXXXX</strong>.<br><br>
                    <strong>Permis :</strong> Plusieurs catégories peuvent être sélectionnées simultanément.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        const statutColors = {
            actif: {
                bg: 'rgba(16,185,129,.1)',
                color: '#059669'
            },
            inactif: {
                bg: 'rgba(107,114,128,.1)',
                color: '#6b7280'
            },
            suspendu: {
                bg: 'rgba(245,158,11,.1)',
                color: '#d97706'
            },
        };

        // ── APERÇU TEMPS RÉEL ──
        function updatePreview() {
            const prenom = document.getElementById('prenom')?.value.trim() ?? '';
            const nom = document.getElementById('nom')?.value.trim() ?? '';
            const cin = document.getElementById('cin')?.value.trim() ?? '';
            const tel = document.getElementById('telephone')?.value.trim() ?? '';
            const statut = document.getElementById('statut')?.value ?? '';

            const fullName = [prenom, nom].filter(Boolean).join(' ') || '—';
            const el = (id) => document.getElementById(id);

            el('prevNom').textContent = fullName;
            el('prevNom').className = 'prev-value' + ((prenom || nom) ? ' primary' : ' muted');
            el('prevCin').textContent = cin || '—';
            el('prevCin').className = 'prev-value' + (cin ? '' : ' muted');
            el('prevTel').textContent = tel || '—';
            el('prevTel').className = 'prev-value' + (tel ? '' : ' muted');

            const initials = (prenom[0] || '') + (nom[0] || '');
            el('previewAvatar').textContent = initials.toUpperCase() || '?';

            const sWrap = el('prevStatut');
            if (statut && statutColors[statut]) {
                const c = statutColors[statut];
                sWrap.innerHTML = `<span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;
                font-weight:700;padding:3px 9px;border-radius:20px;background:${c.bg};color:${c.color}">
                <span style="width:6px;height:6px;border-radius:50%;background:${c.color}"></span>
                ${statut.charAt(0).toUpperCase() + statut.slice(1)}</span>`;
            } else {
                sWrap.innerHTML = '<span class="prev-value muted">—</span>';
            }
        }

        // ── PERMIS PREVIEW ──
        function updatePermisPreview() {
            const checked = [...document.querySelectorAll('#permisSelector input:checked')].map(i => i.value);

            // Preview sous le selector
            const previewEl = document.getElementById('permisPreview');
            previewEl.innerHTML = checked.length ?
                checked.map(c => `<span class="permis-badge-prev">${c}</span>`).join('') :
                '<span style="font-size:11px;color:var(--text-muted);font-style:italic">Aucune catégorie sélectionnée</span>';

            // Preview sidebar
            const sideEl = document.getElementById('prevPermis');
            sideEl.innerHTML = checked.length ?
                checked.map(c =>
                    `<span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;padding:2px 7px;border-radius:5px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.25);color:#2563eb;font-family:'JetBrains Mono',monospace;">${c}</span>`
                    ).join('') :
                '<span class="prev-value muted" style="font-size:11px">—</span>';
        }

        document.addEventListener('DOMContentLoaded', function() {
            ['prenom', 'nom', 'cin', 'telephone', 'statut'].forEach(id => {
                document.getElementById(id)?.addEventListener('input', updatePreview);
                document.getElementById(id)?.addEventListener('change', updatePreview);
            });
            updatePreview();
            updatePermisPreview(); // init depuis old()
        });

        // ── FILE UPLOAD ──
        function handleFileChange(input, zoneId, previewId, nameId) {
            if (input.files.length > 0) {
                document.getElementById(nameId).textContent = input.files[0].name;
                document.getElementById(previewId).classList.add('show');
                document.getElementById(zoneId).style.borderColor = 'var(--color-primary)';
            }
        }

        function clearFile(inputId, zoneId, previewId) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).classList.remove('show');
            document.getElementById(zoneId).style.borderColor = '';
        }
        document.querySelectorAll('.file-drop-zone').forEach(zone => {
            zone.addEventListener('dragover', e => {
                e.preventDefault();
                zone.classList.add('dragover');
            });
            zone.addEventListener('dragleave', () => zone.classList.remove('dragover'));
            zone.addEventListener('drop', e => {
                e.preventDefault();
                zone.classList.remove('dragover');
            });
        });
    </script>
@endpush
