{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN CLIENT — OBTRANS TMS
|--------------------------------------------------------------------------
| ROUTE : GET  /clients/create → ClientController@create
|         POST /clients         → ClientController@store
--}}

@extends('layouts.app')

@section('title', 'Nouveau client')
@section('page-title', 'Nouveau Client')
@section('page-subtitle', 'Créer un nouveau client dans le référentiel')

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

        .form-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── SECTION CARD ── */
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

        /* ── GRID FIELDS ── */
        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .fields-grid.cols-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .field-full {
            grid-column: 1 / -1;
        }

        /* ── FIELD ── */
        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .field-hint {
            font-size: 11px;
            color: var(--text-muted);
        }

        .required-star {
            color: var(--color-primary);
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
            min-height: 80px;
        }

        /* No-icon variant */
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

        /* ── TYPE TABS ── */
        .type-tabs {
            display: flex;
            gap: 8px;
        }

        .type-tab {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: var(--bg-body);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-secondary);
            transition: border-color var(--transition), color var(--transition), background var(--transition);
        }

        .type-tab:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .type-tab.selected {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
            color: var(--color-primary);
        }

        .type-tab .tab-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            flex-shrink: 0;
        }

        .type-tab.selected .tab-icon {
            background: rgba(224, 32, 32, .15);
            color: var(--color-primary);
        }

        /* ── TOGGLE ACTIF ── */
        .toggle-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: var(--bg-body);
            cursor: pointer;
            transition: border-color var(--transition);
        }

        .toggle-wrap:hover {
            border-color: var(--color-primary);
        }

        .toggle-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .toggle-sub {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .toggle-switch {
            width: 40px;
            height: 22px;
            border-radius: 20px;
            background: var(--border);
            position: relative;
            transition: background .2s;
            flex-shrink: 0;
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, .2);
        }

        .toggle-switch.on {
            background: #10b981;
        }

        .toggle-switch.on::after {
            transform: translateX(18px);
        }

        /* ── SIDEBAR ── */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Preview */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .preview-header {
            padding: 14px 16px;
            background: var(--bg-body);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-client-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
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

        .prev-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* Action card */
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
            background: var(--color-primary);
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
            transition: background var(--transition);
        }

        .btn-submit:hover {
            background: var(--color-primary-dark, #c01010);
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
            background: rgba(59, 130, 246, .06);
            border: 1px solid rgba(59, 130, 246, .18);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: #3b82f6;
            line-height: 1.5;
        }

        /* Sections entreprise / cachées */
        .section-entreprise {
            transition: opacity .2s;
        }

        @media (max-width: 1024px) {
            .form-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {

            .fields-grid,
            .fields-grid.cols-3 {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('clients.index') }}">Clients</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouveau client</span>
    </div>

    <form method="POST" action="{{ route('clients.store') }}" id="clientForm" novalidate>
        @csrf

        {{-- Champ caché type --}}
        <input type="hidden" id="typeHidden" name="type" value="{{ old('type', 'entreprise') }}">
        <input type="hidden" id="isActiveHidden" name="is_active" value="{{ old('is_active', '1') }}">

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-col">

                {{-- TYPE DE CLIENT --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-tag" style="color:var(--color-primary);font-size:13px"></i>
                            Type de client <span class="required-star">*</span>
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="type-tabs" id="typeTabs">
                            <button type="button"
                                class="type-tab {{ old('type', 'entreprise') === 'entreprise' ? 'selected' : '' }}"
                                onclick="selectType('entreprise')">
                                <span class="tab-icon"><i class="fa-solid fa-building"></i></span>
                                Entreprise
                            </button>
                            <button type="button" class="type-tab {{ old('type') === 'particulier' ? 'selected' : '' }}"
                                onclick="selectType('particulier')">
                                <span class="tab-icon"><i class="fa-solid fa-user"></i></span>
                                Particulier
                            </button>
                        </div>
                        @error('type')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-address-card" style="color:var(--color-primary);font-size:13px"></i>
                            Informations générales
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid">

                            {{-- Nom --}}
                            <div class="field field-full">
                                <label>Nom / Raison sociale <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-user field-prefix"></i>
                                    <input type="text" name="nom" id="nom" value="{{ old('nom') }}"
                                        placeholder="Ex : ACME SARL, Jean Dupont…" required maxlength="255"
                                        autocomplete="off" autofocus class="{{ $errors->has('nom') ? 'is-invalid' : '' }}">
                                </div>
                                @error('nom')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="field">
                                <label>E-mail</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-envelope field-prefix"></i>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        placeholder="contact@exemple.ma"
                                        class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
                                </div>
                                @error('email')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div class="field">
                                <label>Téléphone</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-phone field-prefix"></i>
                                    <input type="text" name="telephone" value="{{ old('telephone') }}"
                                        placeholder="+212 6XX XXX XXX"
                                        class="{{ $errors->has('telephone') ? 'is-invalid' : '' }}">
                                </div>
                                @error('telephone')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Adresse --}}
                            <div class="field field-full">
                                <label>Adresse</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-location-dot field-prefix" style="top:14px;transform:none"></i>
                                    <textarea name="adresse" placeholder="Rue, ville, code postal…"
                                        class="{{ $errors->has('adresse') ? 'is-invalid' : '' }}">{{ old('adresse') }}</textarea>
                                </div>
                                @error('adresse')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- INFORMATIONS FISCALES & LÉGALES (entreprise) --}}
                <div class="section-card section-entreprise" id="sectionFiscal">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-file-invoice" style="color:var(--color-primary);font-size:13px"></i>
                            Informations fiscales &amp; légales
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid cols-3">

                            {{-- ICE --}}
                            <div class="field">
                                <label>ICE</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-hashtag field-prefix"></i>
                                    <input type="text" name="ice" value="{{ old('ice') }}"
                                        placeholder="000000000000000"
                                        class="{{ $errors->has('ice') ? 'is-invalid' : '' }}"
                                        style="font-family:'JetBrains Mono',monospace">
                                </div>
                                @error('ice')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Identifiant fiscal --}}
                            <div class="field">
                                <label>Identifiant fiscal</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-id-card field-prefix"></i>
                                    <input type="text" name="identifiant_fiscal"
                                        value="{{ old('identifiant_fiscal') }}" placeholder="IF…"
                                        class="{{ $errors->has('identifiant_fiscal') ? 'is-invalid' : '' }}">
                                </div>
                                @error('identifiant_fiscal')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Registre de commerce --}}
                            <div class="field">
                                <label>Registre de commerce</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-registered field-prefix"></i>
                                    <input type="text" name="registre_commerce"
                                        value="{{ old('registre_commerce') }}" placeholder="RC…"
                                        class="{{ $errors->has('registre_commerce') ? 'is-invalid' : '' }}">
                                </div>
                                @error('registre_commerce')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Statut juridique --}}
                            <div class="field">
                                <label>Statut juridique</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-scale-balanced field-prefix"></i>
                                    <input type="text" name="statut_juridique" value="{{ old('statut_juridique') }}"
                                        placeholder="SARL, SA, SAS…"
                                        class="{{ $errors->has('statut_juridique') ? 'is-invalid' : '' }}">
                                </div>
                                @error('statut_juridique')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Patente --}}
                            <div class="field">
                                <label>Patente</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-stamp field-prefix"></i>
                                    <input type="text" name="patente" value="{{ old('patente') }}"
                                        placeholder="N° patente…"
                                        class="{{ $errors->has('patente') ? 'is-invalid' : '' }}">
                                </div>
                                @error('patente')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- CNSS --}}
                            <div class="field">
                                <label>N° CNSS</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-shield field-prefix"></i>
                                    <input type="text" name="num_cnss" value="{{ old('num_cnss') }}"
                                        placeholder="N° CNSS…"
                                        class="{{ $errors->has('num_cnss') ? 'is-invalid' : '' }}">
                                </div>
                                @error('num_cnss')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- MODALITÉ DE PAIEMENT --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-credit-card" style="color:var(--color-primary);font-size:13px"></i>
                            Modalité de paiement <span class="required-star">*</span>
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid">
                            <div class="field field-full">
                                <div class="field-input-wrap no-icon">
                                    <select name="modalite_paiement" id="modalitePaiement"
                                        class="{{ $errors->has('modalite_paiement') ? 'is-invalid' : '' }}">
                                        <option value="">-- Choisir une modalité --</option>
                                        <option value="comptant"
                                            {{ old('modalite_paiement') === 'comptant' ? 'selected' : '' }}>Comptant
                                        </option>
                                        <option value="30_jours"
                                            {{ old('modalite_paiement') === '30_jours' ? 'selected' : '' }}>30 jours
                                        </option>
                                        <option value="60_jours"
                                            {{ old('modalite_paiement') === '60_jours' ? 'selected' : '' }}>60 jours
                                        </option>
                                    </select>
                                </div>
                                @error('modalite_paiement')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /form-col --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-client-icon" id="previewIcon">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <div>
                            <div class="preview-header-label">Aperçu client</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Nom</div>
                            <div class="prev-value primary" id="prevNom">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div id="prevTypeWrap">
                                <span class="prev-badge"
                                    style="background:var(--color-primary-dim);color:var(--color-primary);border:1px solid rgba(224,32,32,.2)">
                                    <i class="fa-solid fa-building" style="font-size:9px"></i> Entreprise
                                </span>
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">E-mail</div>
                            <div class="prev-value muted" id="prevEmail">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Paiement</div>
                            <div class="prev-value muted" id="prevPaiement">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                <span class="prev-badge"
                                    style="background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)">
                                    <i class="fa-solid fa-circle" style="font-size:6px"></i> Actif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statut actif --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-toggle-on" style="color:var(--color-primary);font-size:13px"></i>
                            Statut
                        </h2>
                    </div>
                    <div class="section-body" style="padding:14px 16px">
                        <div class="toggle-wrap" id="toggleWrap" onclick="toggleActive()">
                            <div>
                                <div class="toggle-label">Client actif</div>
                                <div class="toggle-sub" id="toggleSub">Le client est actif et utilisable</div>
                            </div>
                            <div class="toggle-switch on" id="toggleSwitch"></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="clientForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer le client
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Astuce :</strong> Les champs marqués <span style="color:var(--color-primary)">*</span> sont
                    obligatoires.
                    L'ICE doit être unique dans le système.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var typeHidden = document.getElementById('typeHidden');
            var activeHidden = document.getElementById('isActiveHidden');
            var isActive = activeHidden.value === '1';

            // Sync preview on load
            updatePreview();

            // Nom
            document.getElementById('nom').addEventListener('input', updatePreview);

            // Email
            var emailInput = document.querySelector('input[name="email"]');
            if (emailInput) emailInput.addEventListener('input', updatePreview);

            // Modalité paiement
            document.getElementById('modalitePaiement').addEventListener('change', updatePreview);

            function updatePreview() {
                var nom = document.getElementById('nom').value.trim();
                var email = emailInput ? emailInput.value.trim() : '';
                var type = typeHidden.value;
                var modalite = document.getElementById('modalitePaiement').value;

                // Nom
                var prevNom = document.getElementById('prevNom');
                prevNom.textContent = nom || '—';
                prevNom.className = 'prev-value' + (nom ? ' primary' : ' muted');

                // Email
                var prevEmail = document.getElementById('prevEmail');
                prevEmail.textContent = email || '—';
                prevEmail.className = 'prev-value' + (email ? '' : ' muted');

                // Paiement
                var prevPaiement = document.getElementById('prevPaiement');
                var paiementLabel = {
                    'comptant': 'Comptant',
                    '30_jours': '30 jours',
                    '60_jours': '60 jours'
                };
                prevPaiement.textContent = paiementLabel[modalite] || '—';
                prevPaiement.className = 'prev-value' + (modalite ? '' : ' muted');
            }

            window.updatePreview = updatePreview;
        });

        // ── TYPE ─────────────────────────────────────
        function selectType(val) {
            document.getElementById('typeHidden').value = val;
            document.querySelectorAll('.type-tab').forEach(function(t) {
                t.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            // Icône preview
            var icon = document.getElementById('previewIcon');
            if (val === 'entreprise') {
                icon.innerHTML = '<i class="fa-solid fa-building"></i>';
                icon.style.background = '';
                icon.style.color = '';
            } else {
                icon.innerHTML = '<i class="fa-solid fa-user"></i>';
                icon.style.background = 'rgba(59,130,246,.08)';
                icon.style.color = '#3b82f6';
            }

            // Badge type preview
            var wrap = document.getElementById('prevTypeWrap');
            if (val === 'entreprise') {
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:var(--color-primary-dim);color:var(--color-primary);border:1px solid rgba(224,32,32,.2)"><i class="fa-solid fa-building" style="font-size:9px"></i> Entreprise</span>';
            } else {
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:rgba(59,130,246,.08);color:#3b82f6;border:1px solid rgba(59,130,246,.2)"><i class="fa-solid fa-user" style="font-size:9px"></i> Particulier</span>';
            }

            // Section fiscal : opacité si particulier
            var sectionFiscal = document.getElementById('sectionFiscal');
            sectionFiscal.style.opacity = val === 'entreprise' ? '1' : '0.45';
        }

        // ── TOGGLE ACTIF ─────────────────────────────
        function toggleActive() {
            var hidden = document.getElementById('isActiveHidden');
            var sw = document.getElementById('toggleSwitch');
            var sub = document.getElementById('toggleSub');
            var wrap = document.getElementById('prevStatutWrap');

            var isOn = hidden.value === '1';
            hidden.value = isOn ? '0' : '1';
            sw.classList.toggle('on', !isOn);
            sub.textContent = !isOn ? 'Le client est actif et utilisable' : 'Le client est inactif';

            if (!isOn) {
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i class="fa-solid fa-circle" style="font-size:6px"></i> Actif</span>';
            } else {
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i class="fa-solid fa-circle" style="font-size:6px"></i> Inactif</span>';
            }
        }
    </script>
@endpush
