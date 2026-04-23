{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE LIGNE DE COMMANDE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $commandes : Collection (id, code_commande, client_id) with client
|   - $articles  : Collection (id, designation, unite)
|
| ROUTE : GET  /ligne_commandes/create → LigneCommandeController@create
|         POST /ligne_commandes        → LigneCommandeController@store
--}}

@extends('layouts.app')

@section('title', 'Nouvelle ligne de commande')
@section('page-title', 'Nouvelle ligne de commande')
@section('page-subtitle', 'Associer un article à une commande')

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

        @media(max-width:900px) {
            .form-layout {
                grid-template-columns: 1fr;
            }
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 22px 24px;
            box-shadow: var(--shadow-sm);
        }

        .section-header {
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
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
            margin: 0;
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
            z-index: 1;
        }

        .field-input-wrap input,
        .field-input-wrap select,
        .field input[type="text"],
        .field input[type="number"],
        .field select {
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
        .field-input-wrap select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field-error {
            font-size: 11px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .fields-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        /* Sidebar */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px;
            box-shadow: var(--shadow-sm);
        }

        .preview-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }

        .preview-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
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
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
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
            padding: 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: opacity var(--transition);
        }

        .btn-submit:hover {
            opacity: .88;
        }

        .btn-cancel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
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
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .info-box strong {
            color: var(--text-primary);
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
        <a href="{{ route('ligne_commandes.index') }}">Lignes de commande</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle ligne</span>
    </div>

    @if (session('error'))
        <div class="flash flash-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
    @endif

    <form id="ligneForm" method="POST" action="{{ route('ligne_commandes.store') }}">
        @csrf

        <div class="form-layout">

            {{-- ── COLONNE PRINCIPALE ── --}}
            <div class="form-section">

                {{-- Commande + Article --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-file-invoice" style="color:var(--color-primary);font-size:14px"></i>
                            Commande &amp; Article
                        </h2>
                    </div>

                    <div class="fields-row">

                        {{-- Commande --}}
                        <div class="field">
                            <label>Commande <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Sélectionnez la commande parente</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-file-invoice field-prefix"></i>
                                <select name="commande_id" id="commandeSelect" required>
                                    <option value="">— Choisir une commande —</option>
                                    @foreach ($commandes as $cmd)
                                        <option value="{{ $cmd->id }}" data-client="{{ $cmd->client->nom ?? '' }}"
                                            {{ old('commande_id') == $cmd->id ? 'selected' : '' }}>
                                            {{ $cmd->code_commande }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('commande_id')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Article --}}
                        <div class="field">
                            <label>Article <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Sélectionnez l'article à livrer</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-box field-prefix"></i>
                                <select name="article_id" id="articleSelect" required>
                                    <option value="">— Choisir un article —</option>
                                    @foreach ($articles as $art)
                                        <option value="{{ $art->id }}" data-unite="{{ $art->unite }}"
                                            {{ old('article_id') == $art->id ? 'selected' : '' }}>
                                            {{ $art->designation }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('article_id')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Quantité + Poids --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-scale-balanced" style="color:var(--color-primary);font-size:14px"></i>
                            Quantité &amp; Poids
                        </h2>
                    </div>

                    <div class="fields-row">

                        {{-- Quantité --}}
                        <div class="field">
                            <label>Quantité <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Nombre d'unités — entier ≥ 1</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-hashtag field-prefix"></i>
                                <input type="number" name="quantite" id="quantiteInput" value="{{ old('quantite') }}"
                                    placeholder="Ex : 100" min="1" step="1" required>
                            </div>
                            @error('quantite')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Poids --}}
                        <div class="field">
                            <label>Poids (kg) <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Poids total en kilogrammes</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-weight-hanging field-prefix"></i>
                                <input type="number" name="poids_kg" id="poidsInput" value="{{ old('poids_kg') }}"
                                    placeholder="Ex : 250.500" min="0" step="0.001" required>
                            </div>
                            @error('poids_kg')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ── SIDEBAR ── --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-icon"><i class="fa-solid fa-list-check"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu ligne</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Commande</div>
                            <div class="prev-value primary" id="prevCommande">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Client</div>
                            <div class="prev-value muted" id="prevClient">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Article</div>
                            <div class="prev-value" id="prevArticle">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Quantité</div>
                            <div class="prev-value" id="prevQte">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Poids</div>
                            <div class="prev-value" id="prevPoids">—</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="ligneForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer la ligne
                    </button>
                    <a href="{{ route('ligne_commandes.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Note :</strong> La quantité doit être un entier positif.
                    Le poids accepte 3 décimales (ex : 12.500 kg).
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var cmdSel = document.getElementById('commandeSelect');
            var artSel = document.getElementById('articleSelect');
            var qteInput = document.getElementById('quantiteInput');
            var poidsInput = document.getElementById('poidsInput');

            function update() {
                var cmdOpt = cmdSel.options[cmdSel.selectedIndex];
                var artOpt = artSel.options[artSel.selectedIndex];
                var unite = artOpt ? artOpt.dataset.unite : '';
                var client = cmdOpt ? cmdOpt.dataset.client : '';

                document.getElementById('prevCommande').textContent = cmdOpt && cmdOpt.value ? cmdOpt.text : '—';
                document.getElementById('prevClient').textContent = client || '—';
                document.getElementById('prevArticle').textContent = artOpt && artOpt.value ? artOpt.text : '—';
                document.getElementById('prevQte').textContent = qteInput.value ?
                    qteInput.value + (unite ? ' ' + unite.toUpperCase() : '') :
                    '—';
                document.getElementById('prevPoids').textContent = poidsInput.value ?
                    parseFloat(poidsInput.value).toFixed(3) + ' kg' :
                    '—';
            }

            cmdSel.addEventListener('change', update);
            artSel.addEventListener('change', update);
            qteInput.addEventListener('input', update);
            poidsInput.addEventListener('input', update);

            update(); // init avec old()
        });
    </script>
@endpush
