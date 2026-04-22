{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UNE SOCIÉTÉ DE LOCATION — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $locationSociete : App\Models\LocationSociete
|
| ROUTE : GET  /location-societes/{locationSociete}/edit → edit
|         PUT  /location-societes/{locationSociete}      → update
--}}

@extends('layouts.app')

@section('title', 'Modifier · ' . $locationSociete->nom_societe)
@section('page-title', 'Modifier la société')
@section('page-subtitle', $locationSociete->nom_societe)

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

        /* EDIT BANNER */
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

        /* LAYOUT */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }

        .form-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* CARDS */
        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .section-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-body);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* FIELDS */
        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .field-full {
            grid-column: 1/-1;
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

        .required-star {
            color: var(--color-primary);
        }

        .field-hint {
            font-size: 11px;
            color: var(--text-muted);
        }

        .field-input-wrap {
            position: relative;
        }

        .field-prefix {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .field-input-wrap input,
        .field-input-wrap select,
        .field-input-wrap textarea {
            width: 100%;
            padding: 10px 12px 10px 34px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field-input-wrap input:focus,
        .field-input-wrap select:focus,
        .field-input-wrap textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field-input-wrap input.is-invalid,
        .field-input-wrap select.is-invalid,
        .field-input-wrap textarea.is-invalid {
            border-color: var(--color-primary);
        }

        .field-input-wrap select {
            cursor: pointer;
        }

        .field-input-wrap textarea {
            resize: vertical;
            min-height: 90px;
        }

        .field-input-wrap.no-icon input,
        .field-input-wrap.no-icon select,
        .field-input-wrap.no-icon textarea {
            padding-left: 12px;
        }

        .field-error {
            font-size: 11px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* STATUT TABS */
        .statut-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .statut-tab {
            flex: 1;
            min-width: 100px;
            padding: 10px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: var(--bg-body);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-secondary);
            transition: all var(--transition);
        }

        .statut-tab.selected-actif {
            border-color: #10b981;
            background: rgba(16, 185, 129, .06);
            color: #059669;
        }

        .statut-tab.selected-en_attente {
            border-color: #f59e0b;
            background: rgba(245, 158, 11, .06);
            color: #d97706;
        }

        .statut-tab.selected-termine {
            border-color: #9ca3af;
            background: rgba(107, 114, 128, .06);
            color: #6b7280;
        }

        .statut-tab-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .dot-actif {
            background: #10b981;
        }

        .dot-en_attente {
            background: #f59e0b;
        }

        .dot-termine {
            background: #9ca3af;
        }

        /* PDF */
        .pdf-current {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(239, 68, 68, .06);
            border: 1px solid rgba(239, 68, 68, .15);
            border-radius: var(--border-radius-sm);
        }

        .pdf-current i {
            color: #ef4444;
            font-size: 18px;
        }

        .pdf-current-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            flex: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pdf-current-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .pdf-btn {
            padding: 5px 10px;
            font-size: 11px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            border-radius: 5px;
            border: 1.5px solid;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all var(--transition);
        }

        .pdf-btn-view {
            color: #ef4444;
            border-color: rgba(239, 68, 68, .3);
            background: transparent;
        }

        .pdf-btn-view:hover {
            background: rgba(239, 68, 68, .08);
        }

        .pdf-btn-replace {
            color: var(--text-muted);
            border-color: var(--border);
            background: transparent;
        }

        .pdf-btn-replace:hover {
            color: var(--color-primary);
            border-color: var(--color-primary);
        }

        .pdf-dropzone {
            border: 2px dashed var(--border);
            border-radius: var(--border-radius-sm);
            padding: 20px;
            text-align: center;
            background: var(--bg-body);
            cursor: pointer;
            transition: border-color var(--transition), background var(--transition);
            position: relative;
        }

        .pdf-dropzone:hover {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .pdf-dropzone input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .pdf-dropzone-icon {
            font-size: 22px;
            color: var(--border);
            margin-bottom: 6px;
        }

        .pdf-dropzone-text {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .pdf-new-selected {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: rgba(239, 68, 68, .06);
            border: 1px solid rgba(239, 68, 68, .15);
            border-radius: var(--border-radius-sm);
            font-size: 12px;
        }

        .pdf-new-selected i {
            color: #ef4444;
            font-size: 18px;
        }

        .pdf-remove {
            cursor: pointer;
            color: var(--text-muted);
            font-size: 12px;
            padding: 4px;
            border-radius: 4px;
            background: transparent;
            border: none;
            transition: color var(--transition);
        }

        .pdf-remove:hover {
            color: #ef4444;
        }

        /* SIDEBAR */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .preview-header {
            padding: 14px 16px;
            background: var(--color-dark);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-societe-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(224, 32, 32, .15);
            border: 1px solid rgba(224, 32, 32, .25);
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
            color: #666;
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
            font-size: 12px;
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-statut-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* ACTIONS */
        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: var(--shadow-sm);
        }

        .btn-submit {
            width: 100%;
            padding: 11px;
            background: var(--color-dark);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background var(--transition), transform var(--transition);
        }

        .btn-submit:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
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
            transition: background var(--transition), border-color var(--transition);
        }

        .btn-delete:hover {
            background: rgba(224, 32, 32, .06);
            border-color: var(--color-primary);
        }

        /* META */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .meta-item {
            padding: 12px 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
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
            gap: 5px;
        }

        .meta-item-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .meta-item-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
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
            padding: 8px 12px;
        }

        .change-indicator.visible {
            display: flex;
        }

        @media (max-width:1024px) {
            .form-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:640px) {
            .fields-grid {
                grid-template-columns: 1fr;
            }

            .statut-tabs {
                flex-direction: column;
            }

            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('location_societes.index') }}">Sociétés de location</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>{{ $locationSociete->nom_societe }}</span>
    </div>

    {{-- EDIT BANNER --}}
    <div class="edit-banner">
        <div class="edit-banner-icon"><i class="fa-solid fa-pen"></i></div>
        <div class="edit-banner-text">
            <strong>{{ $locationSociete->nom_societe }}</strong>
            <span>Modification de la fiche société</span>
        </div>
        <div class="edit-banner-right">
            <span class="edit-banner-id">#{{ $locationSociete->id }}</span>
        </div>
    </div>
    <form method="POST" action="{{ route('location_societes.destroy', $locationSociete) }}" id="deleteForm">
        @csrf @method('DELETE')
    </form>
    <form method="POST" action="{{ route('location_societes.update', $locationSociete) }}" id="societeForm"
        enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        <input type="hidden" id="statutHidden" name="statut" value="{{ old('statut', $locationSociete->statut) }}">

        <div class="form-layout">

            {{-- COLONNE PRINCIPALE --}}
            <div class="form-col">

                {{-- Indicateur changements --}}
                <div class="change-indicator" id="changeIndicator">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Des modifications non enregistrées sont en cours.
                </div>

                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title"><i class="fa-solid fa-building"
                                style="color:var(--color-primary);font-size:13px"></i> Informations générales</h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid">
                            <div class="field field-full">
                                <label>Nom de la société <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-building field-prefix"></i>
                                    <input type="text" name="nom_societe" id="nomSociete"
                                        value="{{ old('nom_societe', $locationSociete->nom_societe) }}" required
                                        maxlength="255" autocomplete="off" autofocus
                                        class="{{ $errors->has('nom_societe') ? 'is-invalid' : '' }}">
                                </div>
                                @error('nom_societe')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                            <div class="field">
                                <label>E-mail</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-envelope field-prefix"></i>
                                    <input type="email" name="email" id="emailInput"
                                        value="{{ old('email', $locationSociete->email) }}"
                                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                                </div>
                                @error('email')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                            <div class="field">
                                <label>Téléphone</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-phone field-prefix"></i>
                                    <input type="text" name="telephone"
                                        value="{{ old('telephone', $locationSociete->telephone) }}"
                                        class="{{ $errors->has('telephone') ? 'is-invalid' : '' }}">
                                </div>
                                @error('telephone')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CONTRAT --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title"><i class="fa-solid fa-file-contract"
                                style="color:var(--color-primary);font-size:13px"></i> Détails du contrat</h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid">
                            <div class="field">
                                <label>Date de début</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-calendar-plus field-prefix"></i>
                                    <input type="date" name="date_debut_contrat" id="dateDebut"
                                        value="{{ old('date_debut_contrat', $locationSociete->date_debut_contrat?->format('Y-m-d')) }}"
                                        class="{{ $errors->has('date_debut_contrat') ? 'is-invalid' : '' }}">
                                </div>
                                @error('date_debut_contrat')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                            <div class="field">
                                <label>Date de fin</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-calendar-xmark field-prefix"></i>
                                    <input type="date" name="date_fin_contrat" id="dateFin"
                                        value="{{ old('date_fin_contrat', $locationSociete->date_fin_contrat?->format('Y-m-d')) }}"
                                        class="{{ $errors->has('date_fin_contrat') ? 'is-invalid' : '' }}">
                                </div>
                                @error('date_fin_contrat')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- PDF actuel / remplacement --}}
                            <div class="field field-full">
                                <label>Contrat PDF</label>
                                @if ($locationSociete->contrat_pdf_path)
                                    <div class="pdf-current" id="pdfCurrent">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        <span
                                            class="pdf-current-name">{{ basename($locationSociete->contrat_pdf_path) }}</span>
                                        <div class="pdf-current-actions">
                                            <a href="{{ Storage::url($locationSociete->contrat_pdf_path) }}"
                                                target="_blank" class="pdf-btn pdf-btn-view"><i
                                                    class="fa-solid fa-eye"></i> Voir</a>
                                            <button type="button" class="pdf-btn pdf-btn-replace"
                                                onclick="showPdfReplace()"><i class="fa-solid fa-arrow-rotate-right"></i>
                                                Remplacer</button>
                                        </div>
                                    </div>
                                    <div id="pdfReplaceArea" style="display:none; margin-top:8px">
                                        <div class="pdf-dropzone">
                                            <input type="file" name="contrat_pdf" id="contratPdf" accept=".pdf"
                                                onchange="handlePdfSelect(this)">
                                            <div id="pdfPlaceholder">
                                                <div class="pdf-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i>
                                                </div>
                                                <div class="pdf-dropzone-text">Choisir le nouveau PDF</div>
                                            </div>
                                        </div>
                                        <div class="pdf-new-selected" id="pdfSelected">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            <span id="pdfName" style="flex:1;font-size:12px;font-weight:600">—</span>
                                            <span id="pdfSize" style="font-size:11px;color:var(--text-muted)">—</span>
                                            <button type="button" class="pdf-remove" onclick="clearPdf()"><i
                                                    class="fa-solid fa-xmark"></i></button>
                                        </div>
                                        <button type="button" onclick="cancelPdfReplace()"
                                            style="margin-top:6px;font-size:11px;color:var(--text-muted);background:none;border:none;cursor:pointer;padding:0"><i
                                                class="fa-solid fa-xmark"></i> Annuler le remplacement</button>
                                    </div>
                                @else
                                    <p class="field-hint">Aucun fichier · Fichier PDF uniquement · 10 Mo max</p>
                                    <div class="pdf-dropzone">
                                        <input type="file" name="contrat_pdf" id="contratPdf" accept=".pdf"
                                            onchange="handlePdfSelect(this)">
                                        <div id="pdfPlaceholder">
                                            <div class="pdf-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i>
                                            </div>
                                            <div class="pdf-dropzone-text">Glisser ou cliquer pour choisir</div>
                                        </div>
                                    </div>
                                    <div class="pdf-new-selected" id="pdfSelected">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        <span id="pdfName" style="flex:1;font-size:12px;font-weight:600">—</span>
                                        <span id="pdfSize" style="font-size:11px;color:var(--text-muted)">—</span>
                                        <button type="button" class="pdf-remove" onclick="clearPdf()"><i
                                                class="fa-solid fa-xmark"></i></button>
                                    </div>
                                @endif
                                @error('contrat_pdf')
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
                        <h2 class="section-title"><i class="fa-solid fa-circle-half-stroke"
                                style="color:var(--color-primary);font-size:13px"></i> Statut du contrat <span
                                class="required-star">*</span></h2>
                    </div>
                    <div class="section-body">
                        @php $curStatut = old('statut', $locationSociete->statut); @endphp
                        <div class="statut-tabs" id="statutTabs">
                            <button type="button"
                                class="statut-tab {{ $curStatut === 'actif' ? 'selected-actif' : '' }}"
                                onclick="selectStatut('actif',this)">
                                <span class="statut-tab-dot dot-actif"></span> Actif
                            </button>
                            <button type="button"
                                class="statut-tab {{ $curStatut === 'en_attente' ? 'selected-en_attente' : '' }}"
                                onclick="selectStatut('en_attente',this)">
                                <span class="statut-tab-dot dot-en_attente"></span> En attente
                            </button>
                            <button type="button"
                                class="statut-tab {{ $curStatut === 'terminé' ? 'selected-termine' : '' }}"
                                onclick="selectStatut('terminé',this)">
                                <span class="statut-tab-dot dot-termine"></span> Terminé
                            </button>
                        </div>
                        @error('statut')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- NOTES --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title"><i class="fa-solid fa-note-sticky"
                                style="color:var(--color-primary);font-size:13px"></i> Notes</h2>
                    </div>
                    <div class="section-body">
                        <div class="field">
                            <div class="field-input-wrap no-icon">
                                <textarea name="notes" maxlength="2000" id="notesInput" class="{{ $errors->has('notes') ? 'is-invalid' : '' }}">{{ old('notes', $locationSociete->notes) }}</textarea>
                            </div>
                            <div style="display:flex;justify-content:space-between">
                                @error('notes')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                                <span style="font-size:11px;color:var(--text-muted);margin-left:auto"
                                    id="notesCounter">{{ strlen(old('notes', $locationSociete->notes ?? '')) }} /
                                    2000</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MÉTADONNÉES --}}
                <div class="meta-grid">
                    <div class="meta-item">
                        <div class="meta-item-label"><i class="fa-solid fa-calendar-plus"></i> Créé le</div>
                        <div class="meta-item-value">{{ $locationSociete->created_at->format('d/m/Y') }}</div>
                        <div class="meta-item-sub">{{ $locationSociete->created_at->format('H:i') }}</div>
                    </div>
                    <div class="meta-item">
                        <div class="meta-item-label"><i class="fa-solid fa-calendar-pen"></i> Modifié le</div>
                        <div class="meta-item-value">{{ $locationSociete->updated_at->format('d/m/Y') }}</div>
                        <div class="meta-item-sub">{{ $locationSociete->updated_at->diffForHumans() }}</div>
                    </div>
                </div>

            </div>{{-- /form-col --}}

            {{-- SIDEBAR --}}
            <div class="sidebar-col">

                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-societe-icon"><i class="fa-solid fa-building"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu société</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Nom</div>
                            <div class="prev-value primary" id="prevNom">{{ $locationSociete->nom_societe }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">E-mail</div>
                            <div class="prev-value {{ $locationSociete->email ? '' : 'muted' }}" id="prevEmail">
                                {{ $locationSociete->email ?? '—' }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Contrat</div>
                            <div class="prev-value" id="prevContrat">
                                @if ($locationSociete->date_debut_contrat && $locationSociete->date_fin_contrat)
                                    {{ $locationSociete->date_debut_contrat->format('d/m/Y') }} →
                                    {{ $locationSociete->date_fin_contrat->format('d/m/Y') }}
                                @elseif ($locationSociete->date_debut_contrat)
                                    Début : {{ $locationSociete->date_debut_contrat->format('d/m/Y') }}
                                @else
                                    <span class="muted">—</span>
                                @endif
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                @php
                                    $cs = match ($locationSociete->statut) {
                                        'actif'
                                            => 'rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)',
                                        'en_attente'
                                            => 'rgba(245,158,11,.08);color:#d97706;border:1px solid rgba(245,158,11,.2)',
                                        default
                                            => 'rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)',
                                    };
                                    $ci = match ($locationSociete->statut) {
                                        'actif' => 'fa-circle-check',
                                        'en_attente' => 'fa-clock',
                                        default => 'fa-circle-xmark',
                                    };
                                @endphp
                                <span class="prev-statut-badge" style="background:{{ $cs }}"><i
                                        class="fa-solid {{ $ci }}" style="font-size:9px"></i>
                                    {{ ucfirst(str_replace('_', ' ', $locationSociete->statut)) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="action-card">
                    <button type="submit" class="btn-submit" form="societeForm"><i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications</button>
                    <a href="{{ route('location_societes.index') }}" class="btn-cancel"><i
                            class="fa-solid fa-xmark"></i> Annuler</a>

                    <button type="button" class="btn-delete" form="deleteForm"
                        onclick="handleDeleteLocationSociete({{ $locationSociete->id }}, '{{ addslashes($locationSociete->nom_societe) }}')"><i
                            class="fa-solid fa-trash"></i> Supprimer la
                        société</button>
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        var _origNom = document.getElementById('nomSociete')?.value || '';
        var _origEmail = document.getElementById('emailInput')?.value || '';
        var _origDebut = document.getElementById('dateDebut')?.value || '';
        var _origFin = document.getElementById('dateFin')?.value || '';
        var _origStatut = document.getElementById('statutHidden')?.value || '';

        document.addEventListener('DOMContentLoaded', function() {
            ['nomSociete', 'emailInput', 'dateDebut', 'dateFin'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', function() {
                        checkChanges();
                        updatePreview();
                    });
                    el.addEventListener('change', function() {
                        checkChanges();
                        updatePreview();
                    });
                }
            });
            document.getElementById('notesInput').addEventListener('input', function() {
                var l = this.value.length;
                document.getElementById('notesCounter').textContent = l + ' / 2000';
                document.getElementById('notesCounter').style.color = l >= 1900 ? 'var(--color-primary)' :
                    l >= 1600 ? '#f59e0b' : '';
                checkChanges();
            });
            window.updatePreview = updatePreview;
        });

        function checkChanges() {
            var changed = document.getElementById('nomSociete').value !== _origNom ||
                document.getElementById('emailInput').value !== _origEmail ||
                document.getElementById('dateDebut').value !== _origDebut ||
                document.getElementById('dateFin').value !== _origFin ||
                document.getElementById('statutHidden').value !== _origStatut;
            document.getElementById('changeIndicator').classList.toggle('visible', changed);
        }

        function updatePreview() {
            var nom = (document.getElementById('nomSociete').value || '').trim();
            var email = (document.getElementById('emailInput').value || '').trim();
            var debut = document.getElementById('dateDebut').value;
            var fin = document.getElementById('dateFin').value;
            var prevNom = document.getElementById('prevNom');
            prevNom.textContent = nom || '—';
            prevNom.className = 'prev-value' + (nom ? ' primary' : ' muted');
            document.getElementById('prevEmail').textContent = email || '—';
            document.getElementById('prevEmail').className = 'prev-value' + (email ? '' : ' muted');
            var c = '—';
            if (debut && fin) c = formatDate(debut) + ' → ' + formatDate(fin);
            else if (debut) c = 'Début : ' + formatDate(debut);
            else if (fin) c = 'Fin : ' + formatDate(fin);
            document.getElementById('prevContrat').textContent = c;
        }

        function formatDate(d) {
            if (!d) return '—';
            var p = d.split('-');
            return p[2] + '/' + p[1] + '/' + p[0];
        }

        function selectStatut(val, el) {
            document.getElementById('statutHidden').value = val;
            document.querySelectorAll('.statut-tab').forEach(function(t) {
                t.className = 'statut-tab';
            });
            var cm = {
                'actif': 'selected-actif',
                'en_attente': 'selected-en_attente',
                'terminé': 'selected-termine'
            };
            el.classList.add(cm[val]);
            var badges = {
                'actif': '<span class="prev-statut-badge" style="background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Actif</span>',
                'en_attente': '<span class="prev-statut-badge" style="background:rgba(245,158,11,.08);color:#d97706;border:1px solid rgba(245,158,11,.2)"><i class="fa-solid fa-clock" style="font-size:9px"></i> En attente</span>',
                'terminé': '<span class="prev-statut-badge" style="background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i class="fa-solid fa-circle-xmark" style="font-size:9px"></i> Terminé</span>'
            };
            document.getElementById('prevStatutWrap').innerHTML = badges[val] || badges['actif'];
            checkChanges();
        }

        function showPdfReplace() {
            document.getElementById('pdfReplaceArea').style.display = '';
        }

        function cancelPdfReplace() {
            document.getElementById('pdfReplaceArea').style.display = 'none';
            clearPdf();
        }

        function handlePdfSelect(input) {
            var f = input.files[0];
            if (!f) return;
            document.getElementById('pdfPlaceholder').style.display = 'none';
            var s = document.getElementById('pdfSelected');
            s.style.display = 'flex';
            document.getElementById('pdfName').textContent = f.name;
            document.getElementById('pdfSize').textContent = (f.size / 1024 / 1024).toFixed(2) + ' Mo';
        }

        function clearPdf() {
            var i = document.getElementById('contratPdf');
            if (i) i.value = '';
            var s = document.getElementById('pdfSelected');
            if (s) s.style.display = 'none';
            var p = document.getElementById('pdfPlaceholder');
            if (p) p.style.display = '';
        }

        function handleDeleteLocationSociete(id, name) {
            Swal.fire({
                title: 'Supprimer le societe ?',
                text: `Êtes-vous sûr de vouloir supprimer "${name}" ? Cette action est irréversible.`,
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
            }).then(result => {
                if (result.isConfirmed) document.getElementById('deleteForm').submit();
            });
        }
    </script>
@endpush
