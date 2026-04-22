{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE VILLE — OBTRANS TMS
|--------------------------------------------------------------------------
| CHAMPS :
|   - nom  required|string|max:100|unique:villes
|
| ROUTE : POST /villes → VilleController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouvelle Ville')
@section('page-title', 'Nouvelle Ville')
@section('page-subtitle', 'Ajouter une ville au référentiel géographique')

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

        /* Layout centré pour un formulaire simple */
        .form-centered {
            max-width: 640px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
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

        .field-input-wrap .field-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .field-input-wrap input {
            padding-left: 36px;
        }

        .field input[type="text"] {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 15px;
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

        /* Compteur de caractères */
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

        /* Aperçu */
        .preview-inline {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            min-height: 54px;
            transition: border-color var(--transition);
        }

        .preview-inline.has-value {
            border-color: rgba(224, 32, 32, .2);
        }

        .preview-ville-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--color-primary);
            flex-shrink: 0;
        }

        .preview-ville-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--color-primary);
        }

        .preview-ville-placeholder {
            font-size: 13px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Actions */
        .form-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            padding-top: 4px;
        }

        .btn-submit {
            padding: 13px 32px;
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
            gap: 8px;
            transition: background var(--transition), transform var(--transition);
        }

        .btn-submit:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
        }

        .btn-cancel {
            padding: 12px 20px;
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
            gap: 7px;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* Suggestions (villes courantes au Maroc) */
        .suggestions-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .suggestions-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .suggestion-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            background: var(--bg-body);
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: border-color var(--transition), color var(--transition), background var(--transition);
        }

        .suggestion-chip:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim);
        }

        .suggestion-chip i {
            font-size: 10px;
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
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('villes.index') }}">Villes</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle ville</span>
    </div>

    <div class="form-centered">

        <form method="POST" action="{{ route('villes.store') }}" id="villeForm">
            @csrf

            {{-- ── Carte principale ── --}}
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fa-solid fa-location-dot"
                            style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                        Informations de la ville
                    </h2>
                </div>

                {{-- Aperçu en temps réel --}}
                <div class="preview-inline" id="previewBox" style="margin-bottom:20px">
                    <div class="preview-ville-icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <span class="preview-ville-placeholder" id="previewPlaceholder">
                        Le nom de la ville apparaîtra ici…
                    </span>
                    <span class="preview-ville-name" id="previewName" style="display:none"></span>
                </div>

                {{-- Champ nom --}}
                <div class="field">
                    <label for="nom">
                        Nom de la ville <span style="color:var(--color-primary)">*</span>
                    </label>
                    <p class="field-hint">Entrez le nom officiel de la ville · 100 caractères maximum</p>
                    <div class="field-input-wrap">
                        <i class="fa-solid fa-location-dot field-prefix"></i>
                        <input type="text" id="nom" name="nom" value="{{ old('nom') }}"
                            placeholder="Ex : Tanger, Casablanca, Rabat…" required maxlength="100" autocomplete="off"
                            autofocus>
                    </div>
                    <div class="char-counter" id="charCounter">0 / 100</div>
                    @error('nom')
                        <span class="field-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>
            </div>

            {{-- ── Suggestions rapides ── --}}
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title" style="font-size:13px">
                        <i class="fa-solid fa-bolt" style="color:var(--color-primary);margin-right:5px;font-size:12px"></i>
                        Suggestions rapides
                    </h2>
                </div>
                <div class="suggestions-label">Villes fréquentes du Maroc</div>
                <div class="suggestions-list">
                    @foreach (['Tanger', 'Casablanca', 'Rabat', 'Fès', 'Meknès', 'Marrakech', 'Agadir', 'Oujda', 'Kenitra', 'Tétouan', 'Laâyoune', 'Dakhla', 'Nador', 'Beni Mellal', 'El Jadida'] as $suggestion)
                        <button type="button" class="suggestion-chip" onclick="setSuggestion('{{ $suggestion }}')">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- ── Actions ── --}}
            <div class="form-actions">
                <button type="submit" class="btn-submit" form="villeForm">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Enregistrer la ville
                </button>
                <a href="{{ route('villes.index') }}" class="btn-cancel">
                    <i class="fa-solid fa-xmark"></i>
                    Annuler
                </a>
            </div>

            <div class="info-box">
                <strong>Unicité :</strong> Le nom de la ville doit être unique dans le système.
                Les doublons seront refusés lors de l'enregistrement.
            </div>

        </form>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var input = document.getElementById('nom');
            var counter = document.getElementById('charCounter');
            var previewBox = document.getElementById('previewBox');
            var prevPlaceholder = document.getElementById('previewPlaceholder');
            var prevName = document.getElementById('previewName');
            var maxLen = 100;

            // ── APERÇU + COMPTEUR ─────────────────────────────────
            function update() {
                var val = input.value.trim();
                var len = input.value.length;

                // Compteur
                counter.textContent = len + ' / ' + maxLen;
                counter.className = 'char-counter';
                if (len >= maxLen) counter.classList.add('over');
                else if (len >= maxLen * 0.8) counter.classList.add('warn');

                // Aperçu
                if (val) {
                    prevPlaceholder.style.display = 'none';
                    prevName.style.display = 'inline';
                    prevName.textContent = val;
                    previewBox.classList.add('has-value');
                } else {
                    prevPlaceholder.style.display = 'inline';
                    prevName.style.display = 'none';
                    previewBox.classList.remove('has-value');
                }
            }

            input.addEventListener('input', update);

            // Init (si old())
            if (input.value) update();
        });

        // ── SUGGESTION RAPIDE ─────────────────────────────────
        function setSuggestion(name) {
            var input = document.getElementById('nom');
            input.value = name;
            input.dispatchEvent(new Event('input'));
            input.focus();
        }
    </script>
@endpush
