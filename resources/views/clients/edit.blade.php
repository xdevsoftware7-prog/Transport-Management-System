{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UN CLIENT — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $client : App\Models\Client
|
| ROUTE : GET   /clients/{client}/edit → ClientController@edit
|         PUT   /clients/{client}      → ClientController@update
--}}

@extends('layouts.app')

@section('title', 'Modifier · ' . $client->nom)
@section('page-title', 'Modifier le client')
@section('page-subtitle', $client->nom)

@section('content')

    {{-- Réutilise le même CSS que create --}}
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
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }

        .form-col {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

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

        .fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .fields-grid.cols-3 {
            grid-template-columns: 1fr 1fr 1fr;
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

        /* Badge "modifié" dans le header */
        .edit-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            background: rgba(245, 158, 11, .08);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .2);
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

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

        .btn-danger {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: #ef4444;
            border: 1.5px solid rgba(239, 68, 68, .3);
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
            transition: background var(--transition), border-color var(--transition);
        }

        .btn-danger:hover {
            background: rgba(239, 68, 68, .06);
            border-color: #ef4444;
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

        .meta-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.8;
        }

        .section-entreprise {
            transition: opacity .2s;
        }

        @media (max-width:1024px) {
            .form-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width:640px) {

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
        <span>{{ $client->nom }}</span>
    </div>

    {{-- Badge edit --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:2px">
        <span class="edit-badge"><i class="fa-solid fa-pen" style="font-size:9px"></i> Modification</span>
        <span style="font-size:12px;color:var(--text-muted)">ID #{{ $client->id }}</span>
    </div>

    <form method="POST" action="{{ route('clients.destroy', $client) }}" id="deleteForm">
        @csrf @method('DELETE')
    </form>

    <form method="POST" action="{{ route('clients.update', $client) }}" id="clientForm" novalidate>
        @csrf
        @method('PUT')

        <input type="hidden" id="typeHidden" name="type" value="{{ old('type', $client->type) }}">
        <input type="hidden" id="isActiveHidden" name="is_active"
            value="{{ old('is_active', $client->is_active ? '1' : '0') }}">

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-col">

                {{-- TYPE --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-tag" style="color:var(--color-primary);font-size:13px"></i>
                            Type de client <span class="required-star">*</span>
                        </h2>
                    </div>
                    <div class="section-body">
                        @php $currentType = old('type', $client->type); @endphp
                        <div class="type-tabs" id="typeTabs">
                            <button type="button" class="type-tab {{ $currentType === 'entreprise' ? 'selected' : '' }}"
                                onclick="selectType('entreprise')">
                                <span class="tab-icon"><i class="fa-solid fa-building"></i></span>
                                Entreprise
                            </button>
                            <button type="button" class="type-tab {{ $currentType === 'particulier' ? 'selected' : '' }}"
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

                            <div class="field field-full">
                                <label>Nom / Raison sociale <span class="required-star">*</span></label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-user field-prefix"></i>
                                    <input type="text" name="nom" id="nom"
                                        value="{{ old('nom', $client->nom) }}" required maxlength="255" autocomplete="off"
                                        autofocus class="{{ $errors->has('nom') ? 'is-invalid' : '' }}">
                                </div>
                                @error('nom')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>E-mail</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-envelope field-prefix"></i>
                                    <input type="email" name="email" value="{{ old('email', $client->email) }}"
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
                                        value="{{ old('telephone', $client->telephone) }}"
                                        class="{{ $errors->has('telephone') ? 'is-invalid' : '' }}">
                                </div>
                                @error('telephone')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field field-full">
                                <label>Adresse</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-location-dot field-prefix" style="top:14px;transform:none"></i>
                                    <textarea name="adresse" class="{{ $errors->has('adresse') ? 'is-invalid' : '' }}">{{ old('adresse', $client->adresse) }}</textarea>
                                </div>
                                @error('adresse')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- INFORMATIONS FISCALES & LÉGALES --}}
                <div class="section-card section-entreprise" id="sectionFiscal"
                    style="opacity:{{ old('type', $client->type) === 'particulier' ? '0.45' : '1' }}">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-file-invoice" style="color:var(--color-primary);font-size:13px"></i>
                            Informations fiscales &amp; légales
                        </h2>
                    </div>
                    <div class="section-body">
                        <div class="fields-grid cols-3">

                            <div class="field">
                                <label>ICE</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-hashtag field-prefix"></i>
                                    <input type="text" name="ice" value="{{ old('ice', $client->ice) }}"
                                        class="{{ $errors->has('ice') ? 'is-invalid' : '' }}"
                                        style="font-family:'JetBrains Mono',monospace">
                                </div>
                                @error('ice')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Identifiant fiscal</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-id-card field-prefix"></i>
                                    <input type="text" name="identifiant_fiscal"
                                        value="{{ old('identifiant_fiscal', $client->identifiant_fiscal) }}"
                                        class="{{ $errors->has('identifiant_fiscal') ? 'is-invalid' : '' }}">
                                </div>
                                @error('identifiant_fiscal')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Registre de commerce</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-registered field-prefix"></i>
                                    <input type="text" name="registre_commerce"
                                        value="{{ old('registre_commerce', $client->registre_commerce) }}"
                                        class="{{ $errors->has('registre_commerce') ? 'is-invalid' : '' }}">
                                </div>
                                @error('registre_commerce')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Statut juridique</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-scale-balanced field-prefix"></i>
                                    <input type="text" name="statut_juridique"
                                        value="{{ old('statut_juridique', $client->statut_juridique) }}"
                                        class="{{ $errors->has('statut_juridique') ? 'is-invalid' : '' }}">
                                </div>
                                @error('statut_juridique')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>Patente</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-stamp field-prefix"></i>
                                    <input type="text" name="patente" value="{{ old('patente', $client->patente) }}"
                                        class="{{ $errors->has('patente') ? 'is-invalid' : '' }}">
                                </div>
                                @error('patente')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            <div class="field">
                                <label>N° CNSS</label>
                                <div class="field-input-wrap">
                                    <i class="fa-solid fa-shield field-prefix"></i>
                                    <input type="text" name="num_cnss"
                                        value="{{ old('num_cnss', $client->num_cnss) }}"
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
                        <div class="field">
                            <div class="field-input-wrap no-icon">
                                <select name="modalite_paiement" id="modalitePaiement"
                                    class="{{ $errors->has('modalite_paiement') ? 'is-invalid' : '' }}">
                                    <option value="">-- Choisir --</option>
                                    <option value="comptant"
                                        {{ old('modalite_paiement', $client->modalite_paiement) === 'comptant' ? 'selected' : '' }}>
                                        Comptant</option>
                                    <option value="30_jours"
                                        {{ old('modalite_paiement', $client->modalite_paiement) === '30_jours' ? 'selected' : '' }}>
                                        30 jours</option>
                                    <option value="60_jours"
                                        {{ old('modalite_paiement', $client->modalite_paiement) === '60_jours' ? 'selected' : '' }}>
                                        60 jours</option>
                                </select>
                            </div>
                            @error('modalite_paiement')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>{{-- /form-col --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-client-icon" id="previewIcon"
                            style="{{ $client->type === 'particulier' ? 'background:rgba(59,130,246,.08);color:#3b82f6' : '' }}">
                            <i class="fa-solid {{ $client->type === 'entreprise' ? 'fa-building' : 'fa-user' }}"></i>
                        </div>
                        <div>
                            <div class="preview-header-label">Aperçu client</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Nom</div>
                            <div class="prev-value primary" id="prevNom">{{ $client->nom }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div id="prevTypeWrap">
                                @if ($client->type === 'entreprise')
                                    <span class="prev-badge"
                                        style="background:var(--color-primary-dim);color:var(--color-primary);border:1px solid rgba(224,32,32,.2)">
                                        <i class="fa-solid fa-building" style="font-size:9px"></i> Entreprise
                                    </span>
                                @else
                                    <span class="prev-badge"
                                        style="background:rgba(59,130,246,.08);color:#3b82f6;border:1px solid rgba(59,130,246,.2)">
                                        <i class="fa-solid fa-user" style="font-size:9px"></i> Particulier
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">E-mail</div>
                            <div class="prev-value {{ $client->email ? '' : 'muted' }}" id="prevEmail">
                                {{ $client->email ?? '—' }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Paiement</div>
                            <div class="prev-value" id="prevPaiement">
                                {{ match ($client->modalite_paiement) {'comptant' => 'Comptant','30_jours' => '30 jours','60_jours' => '60 jours',default => $client->modalite_paiement} }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                @if ($client->is_active)
                                    <span class="prev-badge"
                                        style="background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)">
                                        <i class="fa-solid fa-circle" style="font-size:6px"></i> Actif
                                    </span>
                                @else
                                    <span class="prev-badge"
                                        style="background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)">
                                        <i class="fa-solid fa-circle" style="font-size:6px"></i> Inactif
                                    </span>
                                @endif
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
                        @php $isActiveVal = old('is_active', $client->is_active ? '1' : '0'); @endphp
                        <div class="toggle-wrap" id="toggleWrap" onclick="toggleActive()">
                            <div>
                                <div class="toggle-label">Client actif</div>
                                <div class="toggle-sub" id="toggleSub">
                                    {{ $isActiveVal === '1' ? 'Le client est actif et utilisable' : 'Le client est inactif' }}
                                </div>
                            </div>
                            <div class="toggle-switch {{ $isActiveVal === '1' ? 'on' : '' }}" id="toggleSwitch"></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="clientForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                    <button type="button" class="btn-danger"
                        onclick="handleDeleteClient({{ $client->id }}, '{{ addslashes($client->prenom . ' ' . $client->nom) }}')">
                        <i class="fa-solid fa-trash"></i>
                        Supprimer ce client
                    </button>
                </div>

                {{-- Meta --}}
                <div class="meta-box">
                    <strong>Créé le :</strong> {{ $client->created_at->format('d/m/Y à H:i') }}<br>
                    <strong>Modifié le :</strong> {{ $client->updated_at->format('d/m/Y à H:i') }}
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var emailInput = document.querySelector('input[name="email"]');
            if (emailInput) emailInput.addEventListener('input', updatePreview);
            document.getElementById('nom').addEventListener('input', updatePreview);
            document.getElementById('modalitePaiement').addEventListener('change', updatePreview);
            updatePreview();
            window.updatePreview = updatePreview;
        });

        function updatePreview() {
            var nom = document.getElementById('nom').value.trim();
            var email = (document.querySelector('input[name="email"]') || {}).value || '';
            var modalite = document.getElementById('modalitePaiement').value;

            var prevNom = document.getElementById('prevNom');
            prevNom.textContent = nom || '—';
            prevNom.className = 'prev-value' + (nom ? ' primary' : ' muted');

            var prevEmail = document.getElementById('prevEmail');
            prevEmail.textContent = email.trim() || '—';

            var labels = {
                'comptant': 'Comptant',
                '30_jours': '30 jours',
                '60_jours': '60 jours'
            };
            document.getElementById('prevPaiement').textContent = labels[modalite] || '—';
        }

        function selectType(val) {
            document.getElementById('typeHidden').value = val;
            document.querySelectorAll('.type-tab').forEach(function(t) {
                t.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');

            var icon = document.getElementById('previewIcon');
            var wrap = document.getElementById('prevTypeWrap');
            if (val === 'entreprise') {
                icon.innerHTML = '<i class="fa-solid fa-building"></i>';
                icon.style.background = '';
                icon.style.color = '';
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:var(--color-primary-dim);color:var(--color-primary);border:1px solid rgba(224,32,32,.2)"><i class="fa-solid fa-building" style="font-size:9px"></i> Entreprise</span>';
                document.getElementById('sectionFiscal').style.opacity = '1';
            } else {
                icon.innerHTML = '<i class="fa-solid fa-user"></i>';
                icon.style.background = 'rgba(59,130,246,.08)';
                icon.style.color = '#3b82f6';
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:rgba(59,130,246,.08);color:#3b82f6;border:1px solid rgba(59,130,246,.2)"><i class="fa-solid fa-user" style="font-size:9px"></i> Particulier</span>';
                document.getElementById('sectionFiscal').style.opacity = '0.45';
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
            sub.textContent = !isOn ? 'Le client est actif et utilisable' : 'Le client est inactif';

            if (!isOn) {
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:rgba(16,185,129,.08);color:#059669;border:1px solid rgba(16,185,129,.2)"><i class="fa-solid fa-circle" style="font-size:6px"></i> Actif</span>';
            } else {
                wrap.innerHTML =
                    '<span class="prev-badge" style="background:rgba(107,114,128,.08);color:#6b7280;border:1px solid rgba(107,114,128,.2)"><i class="fa-solid fa-circle" style="font-size:6px"></i> Inactif</span>';
            }
        }

        function handleDeleteClient(id, name) {
            Swal.fire({
                title: 'Supprimer le client ?',
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
