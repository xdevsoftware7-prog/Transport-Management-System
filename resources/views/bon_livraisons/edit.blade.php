{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UN BON DE LIVRAISON — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $bonLivraison : BonLivraison  (instance à modifier)
|   - $commandes    : Collection (id, code_commande, client_id, destinataire, date_livraison)
|   - $vehicules    : Collection (id, matricule, marque, type_vehicule, statut)
|   - $chauffeurs   : Collection (id, code_drv, nom, prenom, statut)
|
| ROUTE :
|   PUT /bon_livraisons/{bonLivraison}  → update
--}}

@extends('layouts.app')

@section('title', 'Modifier — ' . $bonLivraison->num_bl)
@section('page-title', 'Modifier le bon de livraison')
@section('page-subtitle', $bonLivraison->num_bl)

@section('content')

    <style>
        /* ── BREADCRUMB ── */
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

        /* ── FORM CARD ── */
        .form-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 28px 32px;
            box-shadow: var(--shadow-sm)
        }

        .form-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-muted);
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 7px
        }

        /* ── GRID ── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px
        }

        .form-grid--3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px
        }

        .col-span-2 {
            grid-column: span 2
        }

        /* ── FIELD ── */
        .form-field {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .form-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted)
        }

        .form-label .required {
            color: var(--color-primary);
            margin-left: 2px
        }

        .form-control {
            padding: 10px 13px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
            width: 100%
        }

        .form-control:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .form-control.is-invalid {
            border-color: var(--color-primary) !important;
            background: rgba(224, 32, 32, .03)
        }

        .invalid-feedback {
            font-size: 11px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px
        }

        /* ── SELECT CUSTOM ── */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 34px
        }

        /* ── HINT ── */
        .form-hint {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px
        }

        /* ── PREVIEW CARD ── */
        .preview-block {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 58px
        }

        .preview-block .preview-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: var(--color-primary);
            flex-shrink: 0
        }

        .preview-block .preview-info {
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .preview-block .preview-main {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary)
        }

        .preview-block .preview-sub {
            font-size: 11px;
            color: var(--text-muted)
        }

        .preview-block.empty .preview-main {
            color: var(--text-muted);
            font-style: italic;
            font-weight: 400
        }

        /* ── STATUT BADGES ── */
        .statut-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .3px;
            white-space: nowrap
        }

        .statut-badge.brouillon {
            background: rgba(107, 114, 128, .1);
            color: #6b7280;
            border: 1px solid rgba(107, 114, 128, .25)
        }

        .statut-badge.émis {
            background: rgba(59, 130, 246, .1);
            color: #2563eb;
            border: 1px solid rgba(59, 130, 246, .25)
        }

        .statut-badge.livré {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .25)
        }

        .statut-badge.partiel {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .25)
        }

        .statut-badge.annulé {
            background: rgba(224, 32, 32, .08);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .2)
        }

        /* ── META BAR ── */
        .meta-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            padding: 12px 16px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            margin-bottom: 24px
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted)
        }

        .meta-item strong {
            color: var(--text-primary)
        }

        .meta-sep {
            color: var(--border);
            font-size: 16px;
            line-height: 1
        }

        /* ── ACTIONS ── */
        .form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 24px;
            border-top: 1px solid var(--border);
            margin-top: 8px;
            flex-wrap: wrap;
            gap: 10px
        }

        .form-actions-right {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .btn-cancel {
            padding: 10px 18px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: border-color var(--transition), color var(--transition)
        }

        .btn-cancel:hover {
            border-color: var(--text-muted);
            color: var(--text-primary)
        }

        .btn-submit {
            padding: 10px 22px;
            background: var(--color-dark);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background var(--transition)
        }

        .btn-submit:hover {
            background: var(--color-primary)
        }

        .btn-submit:disabled {
            opacity: .6;
            cursor: not-allowed
        }

        .btn-pdf {
            padding: 10px 16px;
            background: rgba(220, 38, 38, .08);
            color: var(--color-primary);
            border: 1px solid rgba(224, 32, 32, .25);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background var(--transition), border-color var(--transition)
        }

        .btn-pdf:hover {
            background: rgba(224, 32, 32, .15);
            border-color: var(--color-primary)
        }

        /* ── HORS SERVICE WARNING ── */
        .warn-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .3);
            border-radius: 20px;
            margin-left: 6px;
            vertical-align: middle
        }

        @media(max-width:768px) {

            .form-grid,
            .form-grid--3 {
                grid-template-columns: 1fr
            }

            .col-span-2 {
                grid-column: span 1
            }

            .meta-sep {
                display: none
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('bon_livraisons.index') }}">Bons de livraison</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Modifier — {{ $bonLivraison->num_bl }}</span>
    </div>

    {{-- Meta bar --}}
    <div class="meta-bar" style="margin-top:16px">
        <div class="meta-item">
            <i class="fa-solid fa-file-lines" style="font-size:11px"></i>
            <strong>{{ $bonLivraison->num_bl }}</strong>
        </div>
        <span class="meta-sep">·</span>
        <div class="meta-item">
            <span class="statut-badge {{ $bonLivraison->statut }}">
                @switch($bonLivraison->statut)
                    @case('brouillon')
                        <i class="fa-solid fa-pencil" style="font-size:8px"></i>
                    @break

                    @case('émis')
                        <i class="fa-solid fa-paper-plane" style="font-size:8px"></i>
                    @break

                    @case('livré')
                        <i class="fa-solid fa-circle-check" style="font-size:8px"></i>
                    @break

                    @case('partiel')
                        <i class="fa-solid fa-circle-half-stroke" style="font-size:8px"></i>
                    @break

                    @case('annulé')
                        <i class="fa-solid fa-ban" style="font-size:8px"></i>
                    @break
                @endswitch
                {{ \App\Models\BonLivraison::STATUTS[$bonLivraison->statut] ?? $bonLivraison->statut }}
            </span>
        </div>
        <span class="meta-sep">·</span>
        <div class="meta-item">
            <i class="fa-solid fa-clock" style="font-size:10px"></i>
            Créé {{ $bonLivraison->created_at->diffForHumans() }}
        </div>
        @if ($bonLivraison->updated_at->ne($bonLivraison->created_at))
            <span class="meta-sep">·</span>
            <div class="meta-item">
                <i class="fa-solid fa-pen" style="font-size:10px"></i>
                Modifié {{ $bonLivraison->updated_at->diffForHumans() }}
            </div>
        @endif
    </div>

    <div class="form-card">

        <form method="POST" action="{{ route('bon_livraisons.update', $bonLivraison) }}" id="bl-form">
            @csrf
            @method('PUT')

            {{-- ── SECTION 1 : Identification ── --}}
            <div class="form-section-title">
                <i class="fa-solid fa-file-lines"></i>
                Identification
            </div>

            <div class="form-grid" style="margin-bottom:24px">

                {{-- Numéro BL --}}
                <div class="form-field">
                    <label class="form-label">N° BL <span class="required">*</span></label>
                    <input type="text" name="num_bl" id="num_bl"
                        class="form-control @error('num_bl') is-invalid @enderror"
                        value="{{ old('num_bl', $bonLivraison->num_bl) }}" maxlength="50" required autocomplete="off">
                    @error('num_bl')
                        <span class="invalid-feedback"><i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>
                            {{ $message }}</span>
                    @enderror
                </div>

                {{-- Statut --}}
                <div class="form-field">
                    <label class="form-label">Statut <span class="required">*</span></label>
                    <select name="statut" id="statut" class="form-control @error('statut') is-invalid @enderror"
                        required>
                        @foreach (\App\Models\BonLivraison::STATUTS as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('statut', $bonLivraison->statut) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('statut')
                        <span class="invalid-feedback"><i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>
                            {{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- ── SECTION 2 : Commande ── --}}
            <div class="form-section-title">
                <i class="fa-solid fa-file-invoice"></i>
                Commande
            </div>

            <div class="form-grid" style="margin-bottom:24px">

                {{-- Select commande --}}
                <div class="form-field">
                    <label class="form-label">Commande <span class="required">*</span></label>
                    <select name="commande_id" id="commande_id"
                        class="form-control @error('commande_id') is-invalid @enderror" required>
                        <option value="">— Sélectionner une commande —</option>
                        @foreach ($commandes as $cmd)
                            <option value="{{ $cmd->id }}" data-client="{{ $cmd->client->nom ?? '—' }}"
                                data-destinataire="{{ $cmd->destinataire ?? '' }}"
                                data-date="{{ $cmd->date_livraison ? $cmd->date_livraison->format('d/m/Y') : '' }}"
                                {{ old('commande_id', $bonLivraison->commande_id) == $cmd->id ? 'selected' : '' }}>
                                {{ $cmd->code_commande }}
                            </option>
                        @endforeach
                    </select>
                    @error('commande_id')
                        <span class="invalid-feedback"><i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>
                            {{ $message }}</span>
                    @enderror
                </div>

                {{-- Preview commande --}}
                <div class="form-field">
                    <label class="form-label">Aperçu commande</label>
                    <div class="preview-block {{ $bonLivraison->commande_id ? '' : 'empty' }}" id="preview-commande">
                        <div class="preview-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div class="preview-info">
                            @if ($bonLivraison->commande)
                                <div class="preview-main">{{ $bonLivraison->commande->code_commande }}</div>
                                <div class="preview-sub">
                                    Client : {{ $bonLivraison->commande->client->nom ?? '—' }}
                                    {{ $bonLivraison->commande->date_livraison ? ' · Prévue le ' . $bonLivraison->commande->date_livraison->format('d/m/Y') : '' }}
                                </div>
                            @else
                                <div class="preview-main">Aucune commande sélectionnée</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── SECTION 3 : Véhicule & Chauffeur ── --}}
            <div class="form-section-title">
                <i class="fa-solid fa-truck"></i>
                Véhicule & Chauffeur
            </div>

            <div class="form-grid--3" style="margin-bottom:24px">

                {{-- Véhicule --}}
                <div class="form-field">
                    <label class="form-label">Véhicule <span class="required">*</span></label>
                    <select name="vehicule_id" id="vehicule_id"
                        class="form-control @error('vehicule_id') is-invalid @enderror" required>
                        <option value="">— Sélectionner un véhicule —</option>
                        @foreach ($vehicules as $veh)
                            <option value="{{ $veh->id }}" data-marque="{{ $veh->marque }}"
                                data-type="{{ $veh->type_vehicule }}" data-statut="{{ $veh->statut }}"
                                {{ old('vehicule_id', $bonLivraison->vehicule_id) == $veh->id ? 'selected' : '' }}>
                                {{ $veh->matricule }}{{ $veh->statut === 'hors_service' ? ' ⚠ Hors service' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicule_id')
                        <span class="invalid-feedback"><i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>
                            {{ $message }}</span>
                    @enderror
                </div>

                {{-- Preview véhicule --}}
                <div class="form-field">
                    <label class="form-label">Aperçu véhicule</label>
                    <div class="preview-block {{ $bonLivraison->vehicule_id ? '' : 'empty' }}" id="preview-vehicule">
                        <div class="preview-icon"><i class="fa-solid fa-truck"></i></div>
                        <div class="preview-info">
                            @if ($bonLivraison->vehicule)
                                <div class="preview-main">
                                    {{ $bonLivraison->vehicule->matricule }}
                                    @if ($bonLivraison->vehicule->statut === 'hors_service')
                                        <span class="warn-badge"><i class="fa-solid fa-triangle-exclamation"
                                                style="font-size:9px"></i> Hors service</span>
                                    @endif
                                </div>
                                <div class="preview-sub">{{ $bonLivraison->vehicule->marque }} ·
                                    {{ $bonLivraison->vehicule->type_vehicule }}</div>
                            @else
                                <div class="preview-main">Aucun véhicule sélectionné</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Chauffeur --}}
                <div class="form-field">
                    <label class="form-label">Chauffeur <span class="required">*</span></label>
                    <select name="chauffeur_id" id="chauffeur_id"
                        class="form-control @error('chauffeur_id') is-invalid @enderror" required>
                        <option value="">— Sélectionner un chauffeur —</option>
                        @foreach ($chauffeurs as $drv)
                            <option value="{{ $drv->id }}" data-code="{{ $drv->code_drv }}"
                                data-statut="{{ $drv->statut }}"
                                {{ old('chauffeur_id', $bonLivraison->chauffeur_id) == $drv->id ? 'selected' : '' }}>
                                {{ $drv->prenom }} {{ $drv->nom }}
                                {{ $drv->statut !== 'actif' ? ' (' . $drv->statut . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('chauffeur_id')
                        <span class="invalid-feedback"><i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>
                            {{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- ── SECTION 4 : Date de livraison ── --}}
            <div class="form-section-title">
                <i class="fa-solid fa-calendar-check"></i>
                Date de livraison réelle
            </div>

            <div class="form-grid" style="margin-bottom:8px">

                <div class="form-field">
                    <label class="form-label">Date & heure de livraison réelle</label>
                    <input type="datetime-local" name="date_livraison_reelle" id="date_livraison_reelle"
                        class="form-control @error('date_livraison_reelle') is-invalid @enderror"
                        value="{{ old('date_livraison_reelle', $bonLivraison->date_livraison_reelle?->format('Y-m-d\TH:i')) }}">
                    @error('date_livraison_reelle')
                        <span class="invalid-feedback"><i class="fa-solid fa-circle-exclamation" style="font-size:10px"></i>
                            {{ $message }}</span>
                    @enderror
                    <span class="form-hint"><i class="fa-solid fa-circle-info" style="font-size:9px"></i> Laissez vide si
                        la livraison n'a pas encore eu lieu</span>
                </div>

            </div>

            {{-- ── ACTIONS ── --}}
            <div class="form-actions">
                {{-- PDF download (gauche) --}}
                <a href="{{ route('bon_livraisons.pdf', $bonLivraison) }}" class="btn-pdf" target="_blank">
                    <i class="fa-solid fa-file-pdf"></i> Télécharger PDF
                </a>

                <div class="form-actions-right">
                    <a href="{{ route('bon_livraisons.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </a>
                    <button type="submit" class="btn-submit" id="btn-submit">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>

        </form>
    </div>

@endsection

@push('scripts')
    <script>
        (function() {
            /* ── Commande preview ── */
            const selCmd = document.getElementById('commande_id');
            const preCmd = document.getElementById('preview-commande');

            function updateCmd() {
                const opt = selCmd.options[selCmd.selectedIndex];
                if (!opt || !opt.value) {
                    preCmd.classList.add('empty');
                    preCmd.querySelector('.preview-info').innerHTML =
                        '<div class="preview-main">Aucune commande sélectionnée</div>';
                    return;
                }
                preCmd.classList.remove('empty');
                preCmd.querySelector('.preview-info').innerHTML = `
            <div class="preview-main">${opt.text}</div>
            <div class="preview-sub">
                Client : ${opt.dataset.client || '—'}
                ${opt.dataset.date ? ' · Prévue le ' + opt.dataset.date : ''}
                ${opt.dataset.destinataire ? ' · ' + opt.dataset.destinataire : ''}
            </div>`;
            }

            selCmd.addEventListener('change', updateCmd);

            /* ── Véhicule preview ── */
            const selVeh = document.getElementById('vehicule_id');
            const preVeh = document.getElementById('preview-vehicule');

            function updateVeh() {
                const opt = selVeh.options[selVeh.selectedIndex];
                if (!opt || !opt.value) {
                    preVeh.classList.add('empty');
                    preVeh.querySelector('.preview-info').innerHTML =
                        '<div class="preview-main">Aucun véhicule sélectionné</div>';
                    return;
                }
                preVeh.classList.remove('empty');
                const warnBadge = opt.dataset.statut === 'hors_service' ?
                    `<span class="warn-badge"><i class="fa-solid fa-triangle-exclamation" style="font-size:9px"></i> Hors service</span>` :
                    '';
                preVeh.querySelector('.preview-info').innerHTML = `
            <div class="preview-main">${opt.text.split(' ⚠')[0]}${warnBadge}</div>
            <div class="preview-sub">${opt.dataset.marque || ''} · ${opt.dataset.type || ''}</div>`;
            }

            selVeh.addEventListener('change', updateVeh);

            /* ── Submit protection ── */
            document.getElementById('bl-form').addEventListener('submit', function() {
                const btn = document.getElementById('btn-submit');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement…';
            });
        })();
    </script>
@endpush
