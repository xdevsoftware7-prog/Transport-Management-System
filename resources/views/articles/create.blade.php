{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN ARTICLE — OBTRANS TMS
|--------------------------------------------------------------------------
| CHAMPS :
|   - designation  required|string|max:255|unique:articles
|   - unite        required|string|max:50
|
| ROUTE : POST /articles → ArticleController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouvel Article')
@section('page-title', 'Nouvel Article')
@section('page-subtitle', 'Ajouter un article au référentiel')

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

        /* ── LAYOUT ── */
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

        /* ── CHAMPS ── */
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
        .field select {
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

        .field select {
            cursor: pointer;
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field input::placeholder {
            color: var(--text-muted);
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Compteur */
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

        /* Entrée unité custom */
        .unite-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px;
        }

        .unite-tab {
            padding: 6px 12px;
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            color: var(--text-secondary);
            background: var(--bg-body);
            cursor: pointer;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
        }

        .unite-tab:hover,
        .unite-tab.selected {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .unite-custom-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .unite-custom-label {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .unite-custom-input {
            flex: 1;
            padding: 9px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'JetBrains Mono', monospace;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            text-transform: uppercase;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .unite-custom-input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        /* ── APERÇU SIDEBAR ── */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .preview-header {
            background: var(--color-dark);
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .preview-article-icon {
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
            flex-shrink: 0;
        }

        .preview-header-text {}

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
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .prev-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .prev-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-value.mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .prev-value.muted {
            font-weight: 400;
            color: var(--text-muted);
            font-style: italic;
            font-size: 13px;
        }

        /* Badge unité dans l'aperçu */
        .prev-unite-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 5px;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--shadow-sm);
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
            transition: background var(--transition), transform var(--transition);
        }

        .btn-submit:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
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
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .info-box strong {
            color: var(--text-primary);
        }

        .section-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0 16px;
        }

        @media (max-width: 960px) {
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
        <a href="{{ route('articles.index') }}">Articles</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvel article</span>
    </div>

    <form method="POST" action="{{ route('articles.store') }}" id="articleForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Désignation --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-box"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Désignation de l'article
                        </h2>
                    </div>

                    <div class="field">
                        <label for="designation">
                            Désignation <span style="color:var(--color-primary)">*</span>
                        </label>
                        <p class="field-hint">Nom complet et descriptif de l'article · 255 caractères max</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-box field-prefix"></i>
                            <input type="text" id="designation" name="designation" value="{{ old('designation') }}"
                                placeholder="Ex : Carburant Gasoil 50 ppm, Pneu 295/80 R22.5…" required maxlength="255"
                                autocomplete="off" autofocus>
                        </div>
                        <div class="char-counter" id="designationCounter">0 / 255</div>
                        @error('designation')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Unité de mesure --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-ruler"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Unité de mesure <span style="color:var(--color-primary)">*</span>
                        </h2>
                    </div>

                    {{-- Sélection rapide --}}
                    <p
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:10px">
                        Unités courantes
                    </p>
                    <div class="unite-tabs" id="uniteTabs">
                        @foreach (['KG', 'L', 'T', 'M', 'M²', 'M³', 'UNITE', 'PALETTE', 'CARTON', 'H', 'KM'] as $u)
                            <button type="button" class="unite-tab" onclick="selectUnite('{{ $u }}')">
                                {{ $u }}
                            </button>
                        @endforeach
                    </div>

                    <div class="section-divider"></div>

                    {{-- Champ caché alimenté par les tabs OU la saisie libre --}}
                    <input type="hidden" id="unite" name="unite" value="{{ old('unite') }}">

                    {{-- Saisie libre --}}
                    <div class="unite-custom-wrap">
                        <span class="unite-custom-label">Ou saisir :</span>
                        <input type="text" id="uniteCustom" class="unite-custom-input" placeholder="Ex : ML, PCE, BAG…"
                            maxlength="50" autocomplete="off" value="{{ old('unite') }}">
                    </div>

                    @error('unite')
                        <span class="field-error" style="margin-top:8px">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-article-icon">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <div class="preview-header-text">
                            <div class="preview-header-label">Aperçu article</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Désignation</div>
                            <div class="prev-value primary" id="prevDesignation">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Unité</div>
                            <div id="prevUniteWrap">
                                <span class="prev-value muted">Non sélectionnée</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="articleForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer l'article
                    </button>
                    <a href="{{ route('articles.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Unicité :</strong> La désignation doit être unique.
                    L'unité peut être choisie parmi les suggestions ou saisie librement en majuscules.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var designInput = document.getElementById('designation');
            var uniteHidden = document.getElementById('unite');
            var uniteCustom = document.getElementById('uniteCustom');
            var counter = document.getElementById('designationCounter');

            // Init depuis old() si présent
            if (uniteHidden.value) {
                highlightTab(uniteHidden.value);
            }

            // ── COMPTEUR DÉSIGNATION ─────────────────────────────
            designInput.addEventListener('input', function() {
                var len = this.value.length;
                counter.textContent = len + ' / 255';
                counter.className = 'char-counter';
                if (len >= 255) counter.classList.add('over');
                else if (len >= 204) counter.classList.add('warn');
                updatePreview();
            });

            // ── SAISIE LIBRE UNITÉ ────────────────────────────────
            uniteCustom.addEventListener('input', function() {
                var val = this.value.toUpperCase();
                this.value = val;
                uniteHidden.value = val;
                // Désélectionner les tabs
                document.querySelectorAll('.unite-tab').forEach(function(t) {
                    t.classList.remove('selected');
                });
                if (val) highlightTab(val);
                updatePreview();
            });

            // ── APERÇU ────────────────────────────────────────────
            function updatePreview() {
                var d = designInput.value.trim();
                var u = uniteHidden.value.trim();

                document.getElementById('prevDesignation').textContent = d || '—';
                document.getElementById('prevDesignation').className =
                    'prev-value' + (d ? ' primary' : '');

                var uniteWrap = document.getElementById('prevUniteWrap');
                if (u) {
                    uniteWrap.innerHTML =
                        '<span class="prev-unite-badge"><i class="fa-solid fa-ruler" style="font-size:9px"></i>' +
                        u + '</span>';
                } else {
                    uniteWrap.innerHTML = '<span class="prev-value muted">Non sélectionnée</span>';
                }
            }
        });

        // ── TAB SÉLECTION ─────────────────────────────────────
        function selectUnite(val) {
            document.getElementById('unite').value = val;
            document.getElementById('uniteCustom').value = val;
            highlightTab(val);
            updatePreviewGlobal();
        }

        function highlightTab(val) {
            document.querySelectorAll('.unite-tab').forEach(function(t) {
                t.classList.toggle('selected', t.textContent.trim() === val);
            });
        }

        function updatePreviewGlobal() {
            var u = document.getElementById('unite').value.trim();
            var d = document.getElementById('designation').value.trim();
            document.getElementById('prevDesignation').textContent = d || '—';
            document.getElementById('prevDesignation').className =
                'prev-value' + (d ? ' primary' : '');
            var uniteWrap = document.getElementById('prevUniteWrap');
            if (u) {
                uniteWrap.innerHTML =
                    '<span class="prev-unite-badge"><i class="fa-solid fa-ruler" style="font-size:9px"></i>' + u +
                    '</span>';
            } else {
                uniteWrap.innerHTML = '<span class="prev-value muted">Non sélectionnée</span>';
            }
        }
    </script>
@endpush
