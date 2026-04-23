{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE COMMANDE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $clients  : Collection (select)
|   - $trajets  : Collection avec relations villeDepart / villeDestination (select + infos dynamiques)
|
| ROUTE : GET  /commandes/create → CommandeController@create
|         POST /commandes        → CommandeController@store
--}}

@extends('layouts.app')

@section('title', 'Nouvelle commande')
@section('page-title', 'Nouvelle commande')
@section('page-subtitle', 'Créer une nouvelle commande de transport')

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

        /* ── LAYOUT FORM ── */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start;
        }

        .form-main {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* ── SECTION CARD ── */
        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 20px 22px;
            box-shadow: var(--shadow-sm);
        }

        .section-header {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
        }

        /* ── FIELDS ── */
        .fields-grid {
            display: grid;
            gap: 16px;
        }

        .fields-grid--2 {
            grid-template-columns: 1fr 1fr;
        }

        .fields-grid--3 {
            grid-template-columns: 1fr 1fr 1fr;
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
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .field-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin: 0;
        }

        .field-input-wrap {
            position: relative;
        }

        .field-input-wrap input,
        .field-input-wrap select,
        .field-input-wrap textarea,
        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field-input-wrap input.with-prefix {
            padding-left: 36px;
        }

        .field-input-wrap select.with-prefix {
            padding-left: 36px;
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

        .field-input-wrap input:focus,
        .field-input-wrap select:focus,
        .field-input-wrap textarea:focus,
        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field-input-wrap input.is-invalid,
        .field-input-wrap select.is-invalid,
        .form-input.is-invalid {
            border-color: var(--color-primary);
        }

        .field-error {
            font-size: 11px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── TRAJET INFO BOX ── */
        .trajet-info {
            display: none;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            margin-top: 10px;
            gap: 16px;
        }

        .trajet-info.visible {
            display: flex;
            flex-wrap: wrap;
        }

        .trajet-info-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .trajet-info-item strong {
            color: var(--text-primary);
            font-weight: 700;
        }

        .trajet-info-item i {
            color: var(--color-primary);
            font-size: 11px;
        }

        /* ── SIDEBAR ── */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        /* Aperçu */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .preview-header {
            background: var(--color-dark);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-cmd-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #fff;
        }

        .preview-header-text {
            flex: 1;
        }

        .preview-header-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .preview-header-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, .45);
            margin-top: 1px;
        }

        .preview-body {
            padding: 14px 16px;
        }

        .prev-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
            gap: 8px;
        }

        .prev-row:last-child {
            border-bottom: none;
        }

        .prev-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            flex-shrink: 0;
        }

        .prev-value {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-weight: 400;
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        /* ── ACTION CARD ── */
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
            gap: 6px;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* ── INFO BOX ── */
        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 10px 14px;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        /* ── STATUT PILLS ── */
        .statut-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .statut-pill {
            padding: 7px 14px;
            border-radius: var(--border-radius-sm);
            border: 1.5px solid var(--border);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-secondary);
            transition: all var(--transition);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .statut-pill:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .statut-pill.selected--en_attente {
            background: rgba(99, 102, 241, .12);
            border-color: #6366f1;
            color: #6366f1;
        }

        .statut-pill.selected--en_cours {
            background: rgba(245, 158, 11, .12);
            border-color: #d97706;
            color: #d97706;
        }

        .statut-pill.selected--livree {
            background: rgba(16, 185, 129, .12);
            border-color: #059669;
            color: #059669;
        }

        .statut-pill.selected--annulee {
            background: rgba(224, 32, 32, .10);
            border-color: #e02020;
            color: #e02020;
        }
    </style>

    {{-- BREADCRUMB --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('commandes.index') }}">Commandes</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle commande</span>
    </div>

    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:16px">
            <i class="fa-solid fa-circle-exclamation"></i>
            Veuillez corriger les erreurs ci-dessous avant de continuer.
        </div>
    @endif

    <form id="commandeForm" method="POST" action="{{ route('commandes.store') }}">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-main">

                {{-- ── IDENTIFICATION ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-file-invoice"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Identification de la commande
                        </h2>
                    </div>

                    <div class="fields-grid fields-grid--2">

                        {{-- Code commande --}}
                        <div class="field">
                            <label for="code_commande">
                                Code commande <span style="color:var(--color-primary)">*</span>
                            </label>
                            <p class="field-hint">Identifiant unique · généré automatiquement ou saisi manuellement</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-hashtag field-prefix"></i>
                                <input type="text" id="code_commande" name="code_commande"
                                    class="with-prefix {{ $errors->has('code_commande') ? 'is-invalid' : '' }}"
                                    value="{{ old('code_commande', 'CMD-' . date('Ymd') . '-') }}"
                                    placeholder="Ex : CMD-20250423-001" required maxlength="100" autocomplete="off"
                                    autofocus>
                            </div>
                            @error('code_commande')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Date livraison --}}
                        <div class="field">
                            <label for="date_livraison">Date de livraison souhaitée</label>
                            <p class="field-hint">Date prévue de remise au destinataire</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar-day field-prefix"></i>
                                <input type="date" id="date_livraison" name="date_livraison"
                                    class="with-prefix {{ $errors->has('date_livraison') ? 'is-invalid' : '' }}"
                                    value="{{ old('date_livraison') }}">
                            </div>
                            @error('date_livraison')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ── CLIENT & TRAJET ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-route"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Client & Trajet
                        </h2>
                    </div>

                    <div class="fields-grid fields-grid--2">

                        {{-- Client --}}
                        <div class="field">
                            <label for="client_id">
                                Client <span style="color:var(--color-primary)">*</span>
                            </label>
                            <p class="field-hint">Donneur d'ordre de la commande</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-user field-prefix"></i>
                                <select id="client_id" name="client_id"
                                    class="with-prefix {{ $errors->has('client_id') ? 'is-invalid' : '' }}" required>
                                    <option value="">— Sélectionner un client —</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" data-type="{{ $client->type }}"
                                            {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom }}
                                            ({{ $client->type === 'entreprise' ? 'Entreprise' : 'Particulier' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('client_id')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Trajet --}}
                        <div class="field">
                            <label for="trajet_id">
                                Trajet <span style="color:var(--color-primary)">*</span>
                            </label>
                            <p class="field-hint">Itinéraire de départ → destination</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-map-location-dot field-prefix"></i>
                                <select id="trajet_id" name="trajet_id"
                                    class="with-prefix {{ $errors->has('trajet_id') ? 'is-invalid' : '' }}" required>
                                    <option value="">— Sélectionner un trajet —</option>
                                    @foreach ($trajets as $trajet)
                                        <option value="{{ $trajet->id }}"
                                            data-depart="{{ $trajet->villeDepart->nom ?? '?' }}"
                                            data-destination="{{ $trajet->villeDestination->nom ?? '?' }}"
                                            data-distance="{{ $trajet->distance_km }}"
                                            data-duree="{{ $trajet->duree_minutes }}"
                                            data-prix="{{ $trajet->prix_autoroute }}"
                                            {{ old('trajet_id') == $trajet->id ? 'selected' : '' }}>
                                            {{ $trajet->villeDepart->nom ?? '?' }} →
                                            {{ $trajet->villeDestination->nom ?? '?' }}
                                            ({{ number_format($trajet->distance_km, 0, ',', ' ') }} km)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('trajet_id')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- Infos trajet dynamiques --}}
                    <div class="trajet-info" id="trajetInfo">
                        <div class="trajet-info-item">
                            <i class="fa-solid fa-road"></i>
                            Distance : <strong id="infoDistance">—</strong>
                        </div>
                        <div class="trajet-info-item">
                            <i class="fa-solid fa-clock"></i>
                            Durée estimée : <strong id="infoDuree">—</strong>
                        </div>
                        <div class="trajet-info-item">
                            <i class="fa-solid fa-coins"></i>
                            Prix autoroute : <strong id="infoPrix">—</strong>
                        </div>
                    </div>

                </div>

                {{-- ── TYPE & STATUT ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-tag"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Type & Statut
                        </h2>
                    </div>

                    <div class="fields-grid fields-grid--2">

                        {{-- Type --}}
                        <div class="field">
                            <label for="type">
                                Type de commande <span style="color:var(--color-primary)">*</span>
                            </label>
                            <p class="field-hint">Nature du flux de transport</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-boxes-stacked field-prefix"></i>
                                <select id="type" name="type"
                                    class="with-prefix {{ $errors->has('type') ? 'is-invalid' : '' }}" required>
                                    <option value="">— Sélectionner —</option>
                                    <option value="simple" {{ old('type') === 'simple' ? 'selected' : '' }}>
                                        Simple
                                    </option>
                                    <option value="groupé" {{ old('type') === 'groupé' ? 'selected' : '' }}>
                                        Groupé
                                    </option>
                                    <option value="composé" {{ old('type') === 'composé' ? 'selected' : '' }}>
                                        Composé
                                    </option>
                                </select>
                            </div>
                            @error('type')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Statut --}}
                        <div class="field">
                            <label>Statut initial <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">État de la commande à la création</p>
                            <input type="hidden" id="statut" name="statut"
                                value="{{ old('statut', 'en_attente') }}" required>
                            <div class="statut-pills" id="statutPills">
                                @foreach ([
            'en_attente' => ['icon' => 'fa-clock', 'label' => 'En attente'],
            'en_cours' => ['icon' => 'fa-truck-moving', 'label' => 'En cours'],
            'livree' => ['icon' => 'fa-circle-check', 'label' => 'Livrée'],
            'annulee' => ['icon' => 'fa-ban', 'label' => 'Annulée'],
        ] as $val => $opt)
                                    <button type="button"
                                        class="statut-pill {{ old('statut', 'en_attente') === $val ? 'selected--' . $val : '' }}"
                                        data-val="{{ $val }}" onclick="selectStatut('{{ $val }}')">
                                        <i class="fa-solid {{ $opt['icon'] }}" style="font-size:11px"></i>
                                        {{ $opt['label'] }}
                                    </button>
                                @endforeach
                            </div>
                            @error('statut')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ── DESTINATAIRE ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-person-circle-check"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Destinataire
                        </h2>
                    </div>

                    <div class="field">
                        <label for="destinataire">Nom du destinataire</label>
                        <p class="field-hint">Personne ou société qui réceptionne la marchandise</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-user-check field-prefix"></i>
                            <input type="text" id="destinataire" name="destinataire"
                                class="with-prefix {{ $errors->has('destinataire') ? 'is-invalid' : '' }}"
                                value="{{ old('destinataire') }}" placeholder="Ex : Société ABC SARL, M. Dupont…"
                                maxlength="255" autocomplete="off">
                        </div>
                        @error('destinataire')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>

                </div>

            </div>{{-- /form-main --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu commande --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-cmd-icon">
                            <i class="fa-solid fa-file-invoice"></i>
                        </div>
                        <div class="preview-header-text">
                            <div class="preview-header-label">Aperçu commande</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Code</div>
                            <div class="prev-value primary" id="prevCode">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Client</div>
                            <div class="prev-value" id="prevClient"><span class="muted">Non sélectionné</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Trajet</div>
                            <div class="prev-value" id="prevTrajet"><span class="muted">Non sélectionné</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Livraison</div>
                            <div class="prev-value" id="prevLivraison"><span class="muted">—</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type</div>
                            <div class="prev-value" id="prevType"><span class="muted">—</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap">
                                <span class="prev-badge"
                                    style="background:rgba(99,102,241,.12);color:#6366f1;border:1px solid rgba(99,102,241,.25)">
                                    En attente
                                </span>
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Destinataire</div>
                            <div class="prev-value" id="prevDest"><span class="muted">—</span></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="commandeForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer la commande
                    </button>
                    <a href="{{ route('commandes.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Bon à savoir :</strong> Le code commande doit être unique.
                    Le trajet sélectionné détermine les informations de distance et de tarification autoroute.
                </div>

            </div>{{-- /sidebar --}}

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var codeInput = document.getElementById('code_commande');
            var clientSelect = document.getElementById('client_id');
            var trajetSelect = document.getElementById('trajet_id');
            var dateInput = document.getElementById('date_livraison');
            var typeSelect = document.getElementById('type');
            var destinInput = document.getElementById('destinataire');

            // ── Init depuis old() ──────────────────────────────────────
            updateTrajetInfo();
            updatePreview();

            // ── Écouteurs ──────────────────────────────────────────────
            codeInput.addEventListener('input', updatePreview);
            clientSelect.addEventListener('change', updatePreview);
            trajetSelect.addEventListener('change', function() {
                updateTrajetInfo();
                updatePreview();
            });
            dateInput.addEventListener('change', updatePreview);
            typeSelect.addEventListener('change', updatePreview);
            destinInput.addEventListener('input', updatePreview);

            // ── Trajet info dynamique ──────────────────────────────────
            function updateTrajetInfo() {
                var opt = trajetSelect.options[trajetSelect.selectedIndex];
                var box = document.getElementById('trajetInfo');

                if (!opt || !opt.value) {
                    box.classList.remove('visible');
                    return;
                }

                var dist = parseFloat(opt.dataset.distance || 0);
                var duree = parseInt(opt.dataset.duree || 0);
                var prix = parseFloat(opt.dataset.prix || 0);

                document.getElementById('infoDistance').textContent =
                    dist ? dist.toLocaleString('fr-MA') + ' km' : '—';
                document.getElementById('infoDuree').textContent =
                    duree ? Math.floor(duree / 60) + 'h ' + (duree % 60) + 'min' : '—';
                document.getElementById('infoPrix').textContent =
                    prix ? prix.toLocaleString('fr-MA', {
                        minimumFractionDigits: 2
                    }) + ' MAD' : '—';

                box.classList.add('visible');
            }

            // ── Aperçu temps réel ──────────────────────────────────────
            function updatePreview() {
                // Code
                var code = codeInput.value.trim();
                document.getElementById('prevCode').textContent = code || '—';
                document.getElementById('prevCode').className = 'prev-value' + (code ? ' primary' : '');

                // Client
                var cOpt = clientSelect.options[clientSelect.selectedIndex];
                document.getElementById('prevClient').innerHTML =
                    (cOpt && cOpt.value) ?
                    '<span class="prev-value">' + cOpt.text.split('(')[0].trim() + '</span>' :
                    '<span class="prev-value muted">Non sélectionné</span>';

                // Trajet
                var tOpt = trajetSelect.options[trajetSelect.selectedIndex];
                document.getElementById('prevTrajet').innerHTML =
                    (tOpt && tOpt.value) ?
                    '<span class="prev-value">' + (tOpt.dataset.depart || '') + ' → ' + (tOpt.dataset.destination ||
                        '') + '</span>' :
                    '<span class="prev-value muted">Non sélectionné</span>';

                // Date
                var d = dateInput.value;
                document.getElementById('prevLivraison').innerHTML = d ?
                    '<span class="prev-value">' + formatDate(d) + '</span>' :
                    '<span class="prev-value muted">—</span>';

                // Type
                var t = typeSelect.value;
                document.getElementById('prevType').innerHTML = t ?
                    '<span class="prev-value">' + t.charAt(0).toUpperCase() + t.slice(1) + '</span>' :
                    '<span class="prev-value muted">—</span>';

                // Destinataire
                var dest = destinInput.value.trim();
                document.getElementById('prevDest').innerHTML = dest ?
                    '<span class="prev-value">' + dest + '</span>' :
                    '<span class="prev-value muted">—</span>';
            }

            function formatDate(str) {
                var parts = str.split('-');
                if (parts.length === 3) return parts[2] + '/' + parts[1] + '/' + parts[0];
                return str;
            }
        });

        // ── Sélection statut ───────────────────────────────────────────
        var statutColors = {
            en_attente: {
                bg: 'rgba(99,102,241,.12)',
                color: '#6366f1',
                border: 'rgba(99,102,241,.25)',
                label: 'En attente'
            },
            en_cours: {
                bg: 'rgba(245,158,11,.12)',
                color: '#d97706',
                border: 'rgba(245,158,11,.25)',
                label: 'En cours'
            },
            livree: {
                bg: 'rgba(16,185,129,.12)',
                color: '#059669',
                border: 'rgba(16,185,129,.25)',
                label: 'Livrée'
            },
            annulee: {
                bg: 'rgba(224,32,32,.10)',
                color: '#e02020',
                border: 'rgba(224,32,32,.2)',
                label: 'Annulée'
            },
        };

        function selectStatut(val) {
            document.getElementById('statut').value = val;

            // Mise à jour pills
            document.querySelectorAll('.statut-pill').forEach(function(pill) {
                pill.className = 'statut-pill';
                if (pill.dataset.val === val) {
                    pill.classList.add('selected--' + val);
                }
            });

            // Mise à jour aperçu
            var cfg = statutColors[val] || {};
            var wrap = document.getElementById('prevStatutWrap');
            wrap.innerHTML = '<span class="prev-badge" style="background:' + cfg.bg + ';color:' + cfg.color +
                ';border:1px solid ' + cfg.border + '">' + (cfg.label || val) + '</span>';
        }
    </script>
@endpush
