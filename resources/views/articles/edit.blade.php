{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UN ARTICLE — OBTRANS TMS
|--------------------------------------------------------------------------
| CHAMPS :
|   - designation  required|string|max:255|unique:articles,designation,{article->id}
|   - unite        required|string|max:50
|
| VARIABLES ATTENDUES :
|   - $article : App\Models\Article
|
| ROUTE : PUT /articles/{article} → ArticleController@update
| --}}

@extends('layouts.app')

@section('title', 'Modifier — ' . $article->designation)
@section('page-title', 'Modifier l\'Article')
@section('page-subtitle', 'Mettre à jour la désignation et l\'unité')

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

        /* Bandeau édition */
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

        .edit-banner-unite {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--color-primary);
            background: rgba(224, 32, 32, .1);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 6px;
            padding: 5px 12px;
            font-weight: 700;
            text-transform: uppercase;
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

        .field-input-wrap input {
            padding-left: 34px;
        }

        .field input[type="text"] {
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

        .field input:focus {
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

        /* Indicateur changements */
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

        /* Tabs unité */
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

        .section-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0 16px;
        }

        /* Comparaison avant/après */
        .compare-box {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
            padding: 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
        }

        .compare-side {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .compare-side-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .compare-side.before .compare-side-label {
            color: var(--text-muted);
        }

        .compare-side.after .compare-side-label {
            color: var(--color-primary);
        }

        .compare-sep {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--text-muted);
            align-self: center;
        }

        .compare-val {
            font-size: 13px;
            font-weight: 600;
        }

        .compare-side.before .compare-val {
            color: var(--text-muted);
        }

        .compare-side.after .compare-val {
            color: var(--color-primary);
        }

        .compare-unite {
            font-size: 10px;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }

        .compare-side.before .compare-unite {
            background: var(--border);
            color: var(--text-muted);
        }

        .compare-side.after .compare-unite {
            background: var(--color-primary-dim);
            color: var(--color-primary);
        }

        /* Métadonnées */
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
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
            letter-spacing: 0.6px;
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

        /* Sidebar */
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

        .prev-value.muted {
            font-weight: 400;
            color: var(--text-muted);
            font-style: italic;
            font-size: 13px;
        }

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

        .divider {
            height: 1px;
            background: var(--border);
            margin: 2px 0;
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

        #deleteForm {
            display: none;
        }

        @media (max-width: 960px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-col {
                order: -1;
            }

            .meta-grid {
                grid-template-columns: 1fr 1fr;
            }

            .compare-box {
                grid-template-columns: 1fr;
            }

            .compare-sep {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('articles.index') }}">Articles</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Modifier · #{{ $article->id }}</span>
    </div>

    {{-- Bandeau édition --}}
    <div class="edit-banner">
        <div class="edit-banner-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="edit-banner-text">
            <strong>{{ Str::limit($article->designation, 60) }}</strong>
            <span>Créé {{ $article->created_at->diffForHumans() }} · Modifié
                {{ $article->updated_at->diffForHumans() }}</span>
        </div>
        <div class="edit-banner-right">
            <span class="edit-banner-unite">{{ strtoupper($article->unite) }}</span>
            <span class="edit-banner-id">#{{ $article->id }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('articles.update', $article) }}" id="articleForm">
        @csrf @method('PUT')

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Désignation --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-box"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Désignation
                        </h2>
                        <div class="change-indicator" id="changeIndicator">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            Modifications non sauvegardées
                        </div>
                    </div>

                    {{-- Comparaison avant / après --}}
                    <div class="compare-box">
                        <div class="compare-side before">
                            <div class="compare-side-label"><i class="fa-solid fa-clock-rotate-left"></i> Actuel</div>
                            <div class="compare-val" id="compareOriginalDesig">{{ $article->designation }}</div>
                            <span class="compare-unite">{{ strtoupper($article->unite) }}</span>
                        </div>
                        <div class="compare-sep"><i class="fa-solid fa-arrow-right"></i></div>
                        <div class="compare-side after">
                            <div class="compare-side-label"><i class="fa-solid fa-pen"></i> Nouveau</div>
                            <div class="compare-val" id="compareNewDesig">{{ old('designation', $article->designation) }}
                            </div>
                            <span class="compare-unite"
                                id="compareNewUnite">{{ strtoupper(old('unite', $article->unite)) }}</span>
                        </div>
                    </div>

                    <div class="field">
                        <label for="designation">
                            Désignation <span style="color:var(--color-primary)">*</span>
                        </label>
                        <p class="field-hint">255 caractères maximum · Doit être unique</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-box field-prefix"></i>
                            <input type="text" id="designation" name="designation"
                                value="{{ old('designation', $article->designation) }}" required maxlength="255"
                                autocomplete="off">
                        </div>
                        <div class="char-counter" id="designationCounter">
                            {{ strlen(old('designation', $article->designation)) }} / 255
                        </div>
                        @error('designation')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Unité --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-ruler"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Unité de mesure <span style="color:var(--color-primary)">*</span>
                        </h2>
                    </div>

                    <p
                        style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:10px">
                        Unités courantes
                    </p>
                    <div class="unite-tabs" id="uniteTabs">
                        @foreach (['KG', 'L', 'T', 'M', 'M²', 'M³', 'UNITE', 'PALETTE', 'CARTON', 'H', 'KM'] as $u)
                            <button type="button"
                                class="unite-tab {{ strtoupper(old('unite', $article->unite)) === $u ? 'selected' : '' }}"
                                onclick="selectUnite('{{ $u }}')">
                                {{ $u }}
                            </button>
                        @endforeach
                    </div>

                    <div class="section-divider"></div>

                    <input type="hidden" id="unite" name="unite" value="{{ old('unite', $article->unite) }}">
                    <div class="unite-custom-wrap">
                        <span class="unite-custom-label">Ou saisir :</span>
                        <input type="text" id="uniteCustom" class="unite-custom-input" placeholder="Ex : ML, PCE, BAG…"
                            maxlength="50" autocomplete="off" value="{{ old('unite', $article->unite) }}">
                    </div>

                    @error('unite')
                        <span class="field-error" style="margin-top:8px">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

                {{-- Métadonnées --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title" style="font-size:13px">
                            <i class="fa-solid fa-circle-info"
                                style="color:var(--color-primary);margin-right:5px;font-size:12px"></i>
                            Informations système
                        </h2>
                    </div>
                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-hashtag"></i> Identifiant</div>
                            <div class="meta-item-value" style="font-family:'JetBrains Mono',monospace">
                                #{{ $article->id }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-plus"></i> Créé le</div>
                            <div class="meta-item-value">{{ $article->created_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $article->created_at->format('H:i') }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-item-label"><i class="fa-solid fa-calendar-pen"></i> Modifié le</div>
                            <div class="meta-item-value">{{ $article->updated_at->format('d/m/Y') }}</div>
                            <div class="meta-item-sub">{{ $article->updated_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-article-icon"><i class="fa-solid fa-box"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Désignation</div>
                            <div class="prev-value primary" id="prevDesignation">{{ $article->designation }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Unité</div>
                            <div id="prevUniteWrap">
                                <span class="prev-unite-badge">
                                    <i class="fa-solid fa-ruler" style="font-size:9px"></i>
                                    {{ strtoupper(old('unite', $article->unite)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="articleForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('articles.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                    <div class="divider"></div>
                    <button type="button" class="btn-delete" onclick="handleDeleteArticle()">
                        <i class="fa-solid fa-trash"></i>
                        Supprimer cet article
                    </button>
                </div>

                <div class="info-box">
                    <strong>Attention :</strong> Si cet article est référencé dans des commandes,
                    sa suppression peut créer des incohérences dans l'historique.
                </div>

            </div>

        </div>
    </form>

    <form id="deleteForm" method="POST" action="{{ route('articles.destroy', $article) }}">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        // Valeurs d'origine pour détection de changements
        var originalDesig = '{{ addslashes($article->designation) }}';
        var originalUnite = '{{ strtoupper(addslashes($article->unite)) }}';

        document.addEventListener('DOMContentLoaded', function() {

            var designInput = document.getElementById('designation');
            var uniteHidden = document.getElementById('unite');
            var uniteCustom = document.getElementById('uniteCustom');
            var counter = document.getElementById('designationCounter');
            var indicator = document.getElementById('changeIndicator');

            // ── COMPTEUR + APERÇU ─────────────────────────────────
            designInput.addEventListener('input', function() {
                var len = this.value.length;
                counter.textContent = len + ' / 255';
                counter.className = 'char-counter';
                if (len >= 255) counter.classList.add('over');
                else if (len >= 204) counter.classList.add('warn');
                updateAll();
            });

            // ── UNITÉ SAISIE LIBRE ────────────────────────────────
            uniteCustom.addEventListener('input', function() {
                var val = this.value.toUpperCase();
                this.value = val;
                uniteHidden.value = val;
                document.querySelectorAll('.unite-tab').forEach(function(t) {
                    t.classList.remove('selected');
                });
                if (val) highlightTab(val);
                updateAll();
            });

            function updateAll() {
                var d = designInput.value.trim();
                var u = uniteHidden.value.trim().toUpperCase();

                // Aperçu sidebar
                document.getElementById('prevDesignation').textContent = d || '—';
                var uniteWrap = document.getElementById('prevUniteWrap');
                uniteWrap.innerHTML = u ?
                    '<span class="prev-unite-badge"><i class="fa-solid fa-ruler" style="font-size:9px"></i>' + u +
                    '</span>' :
                    '<span class="prev-value muted">Non sélectionnée</span>';

                // Comparaison après
                document.getElementById('compareNewDesig').textContent = d || '—';
                document.getElementById('compareNewUnite').textContent = u || '—';

                // Indicateur changements
                var changed = (d !== originalDesig) || (u !== originalUnite);
                indicator.classList.toggle('visible', changed);
            }

            updateAll();
        });

        // ── TABS UNITÉ ────────────────────────────────────────
        function selectUnite(val) {
            document.getElementById('unite').value = val;
            document.getElementById('uniteCustom').value = val;
            highlightTab(val);
            // Déclencher l'update
            document.getElementById('uniteCustom').dispatchEvent(new Event('input'));
        }

        function highlightTab(val) {
            document.querySelectorAll('.unite-tab').forEach(function(t) {
                t.classList.toggle('selected', t.textContent.trim() === val);
            });
        }

        // ── SUPPRESSION ───────────────────────────────────────
        // function confirmDelete() {
        //     var desig = '{{ addslashes(Str::limit($article->designation, 40)) }}';
        //     if (confirm('Supprimer l\'article « ' + desig + ' » ? Cette action est irréversible.')) {
        //         document.getElementById('deleteForm').submit();
        //     }
        // }
        function handleDeleteArticle(id, name) {
            Swal.fire({
                title: 'Supprimer l\'article ?',
                text: `Êtes-vous sûr de vouloir supprimer cet article "${name}" ? Cette action est irréversible.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e02020', // Ton rouge OBTRANS
                cancelButtonColor: '#1a1a1a', // Ton gris foncé/noir
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                background: '#111', // Fond sombre pour matcher ton thème
                color: '#fff', // Texte blanc
                customClass: {
                    popup: 'swal-custom-radius'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // On soumet le formulaire correspondant
                    document.getElementById('deleteForm').submit();
                }
            })
        }
    </script>
@endpush
