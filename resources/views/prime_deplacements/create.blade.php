{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE PRIME DE DÉPLACEMENT — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $trajets       : Collection (statut=actif, avec villeDepart, villeDestination)
|   - $typesVehicule : array (clé => ['label', 'icon'])
|
| ROUTE : POST /prime-deplacements → PrimeDeplacementController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouvelle Prime de Déplacement')
@section('page-title', 'Nouvelle Prime de Déplacement')
@section('page-subtitle', 'Définir un montant de prime pour un trajet et un type de véhicule')

@section('content')

    <style>
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

        /* Layout 2 colonnes */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px
        }

        /* Champs */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: var(--text-primary)
        }

        .field .field-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: -2px
        }

        .field-input-wrap {
            position: relative
        }

        .field-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--text-muted);
            pointer-events: none
        }

        .field-input-wrap input,
        .field-input-wrap select {
            padding-left: 34px
        }

        .field input[type="number"],
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
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition)
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px
        }

        /* Sélecteur de type véhicule en cartes */
        .veh-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 4px
        }

        .veh-card {
            position: relative;
            cursor: pointer
        }

        .veh-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none
        }

        .veh-card-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 14px 8px;
            border: 2px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: var(--bg-body);
            transition: border-color var(--transition), background var(--transition), transform var(--transition);
            text-align: center;
        }

        .veh-card:hover .veh-card-inner {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
            transform: translateY(-1px)
        }

        .veh-card input:checked+.veh-card-inner {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .1)
        }

        .veh-card-icon {
            font-size: 20px;
            color: var(--text-muted);
            transition: color var(--transition)
        }

        .veh-card:hover .veh-card-icon,
        .veh-card input:checked+.veh-card-inner .veh-card-icon {
            color: var(--color-primary)
        }

        .veh-card-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            transition: color var(--transition)
        }

        .veh-card:hover .veh-card-label,
        .veh-card input:checked+.veh-card-inner .veh-card-label {
            color: var(--color-primary)
        }

        .veh-card input:checked+.veh-card-inner::after {
            content: '✓';
            position: absolute;
            top: 6px;
            right: 8px;
            font-size: 10px;
            font-weight: 800;
            color: var(--color-primary)
        }

        .veh-card-inner {
            position: relative
        }

        /* Info trajet sélectionné */
        .trajet-info-box {
            margin-top: 10px;
            padding: 12px 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            display: none
        }

        .trajet-info-box.visible {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center
        }

        .trajet-info-route {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 6px;
            flex: 1;
            min-width: 200px
        }

        .trajet-info-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .tpill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 5px;
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace
        }

        /* Montant */
        .montant-input-wrap {
            position: relative
        }

        .montant-input-wrap input {
            padding-left: 34px;
            padding-right: 60px;
            width: 100%;
            font-size: 18px;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-primary);
            border: 2px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: #fafafa;
            outline: none;
            padding-top: 13px;
            padding-bottom: 13px;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition)
        }

        .montant-input-wrap input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(224, 32, 32, .08)
        }

        .montant-input-wrap .field-prefix {
            font-size: 15px;
            color: var(--color-primary)
        }

        .montant-unit {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            pointer-events: none;
            font-family: 'DM Sans', sans-serif
        }

        /* Sidebar preview */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm)
        }

        .preview-header {
            background: var(--color-dark);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px
        }

        .preview-icon {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: rgba(224, 32, 32, .15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--color-primary);
            flex-shrink: 0
        }

        .preview-header-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff
        }

        .preview-header-sub {
            font-size: 11px;
            color: #555;
            margin-top: 1px
        }

        .preview-body {
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border)
        }

        .prev-row:last-child {
            border-bottom: none;
            padding-bottom: 0
        }

        .prev-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted)
        }

        .prev-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary)
        }

        .prev-value.primary {
            color: var(--color-primary)
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-weight: 400;
            font-style: italic
        }

        .prev-montant {
            font-size: 22px;
            font-weight: 800;
            color: #059669;
            font-family: 'JetBrains Mono', monospace;
            line-height: 1
        }

        .prev-montant-unit {
            font-size: 12px;
            font-weight: 600;
            color: #059669;
            opacity: .7;
            margin-top: 2px
        }

        /* Badge véhicule preview */
        .badge-veh {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px
        }

        .badge-veh.tracteur {
            background: rgba(99, 102, 241, .1);
            border: 1px solid rgba(99, 102, 241, .25);
            color: #4f46e5
        }

        .badge-veh.semi-remorque {
            background: rgba(139, 92, 246, .1);
            border: 1px solid rgba(139, 92, 246, .25);
            color: #7c3aed
        }

        .badge-veh.camion {
            background: rgba(59, 130, 246, .1);
            border: 1px solid rgba(59, 130, 246, .25);
            color: #2563eb
        }

        .badge-veh.fourgon {
            background: rgba(16, 185, 129, .08);
            border: 1px solid rgba(16, 185, 129, .2);
            color: #059669
        }

        .badge-veh.benne {
            background: rgba(245, 158, 11, .1);
            border: 1px solid rgba(245, 158, 11, .25);
            color: #d97706
        }

        .badge-veh.citerne {
            background: rgba(6, 182, 212, .1);
            border: 1px solid rgba(6, 182, 212, .25);
            color: #0891b2
        }

        .badge-veh.frigo {
            background: rgba(147, 197, 253, .2);
            border: 1px solid rgba(96, 165, 250, .3);
            color: #1d4ed8
        }

        .badge-veh.plateau {
            background: rgba(107, 114, 128, .1);
            border: 1px solid rgba(107, 114, 128, .25);
            color: #4b5563
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px
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
            transition: background var(--transition), transform var(--transition)
        }

        .btn-submit:hover {
            background: #c01010;
            transform: translateY(-1px)
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
            transition: border-color var(--transition), color var(--transition)
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 11px;
            color: var(--text-secondary);
            line-height: 1.6
        }

        .info-box strong {
            color: var(--text-primary)
        }

        @media(max-width:900px) {
            .form-layout {
                grid-template-columns: 1fr
            }

            .sidebar-col {
                order: -1
            }
        }

        @media(max-width:600px) {
            .veh-grid {
                grid-template-columns: repeat(2, 1fr)
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('prime_deplacements.index') }}">Primes de Déplacement</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle prime</span>
    </div>

    <form method="POST" action="{{ route('prime_deplacements.store') }}" id="primeForm">
        @csrf

        {{-- Data trajets pour JS --}}
        <script id="trajetsData" type="application/json">
        {!! json_encode($trajets->map(fn($t) => [
            'id'       => $t->id,
            'depart'   => $t->villeDepart?->nom  ?? '?',
            'dest'     => $t->villeDestination?->nom ?? '?',
            'distance' => $t->distance_km,
            'duree'    => $t->duree_minutes,
            'autoroute'=> $t->prix_autoroute,
        ])) !!}
    </script>

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Trajet --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-route"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Trajet
                        </h2>
                    </div>
                    <div class="field">
                        <label for="trajet_id">Trajet <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Uniquement les trajets actifs sont disponibles</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-route field-prefix"></i>
                            <select id="trajet_id" name="trajet_id" required>
                                <option value="">— Sélectionner un trajet —</option>
                                @foreach ($trajets as $trajet)
                                    <option value="{{ $trajet->id }}"
                                        {{ old('trajet_id') == $trajet->id ? 'selected' : '' }}>
                                        {{ $trajet->villeDepart?->nom }} → {{ $trajet->villeDestination?->nom }}
                                        &nbsp;·&nbsp; {{ number_format($trajet->distance_km, 0) }} km
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('trajet_id')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror

                        {{-- Aperçu trajet --}}
                        <div class="trajet-info-box" id="trajetInfoBox">
                            <div class="trajet-info-route">
                                <span id="tpDepart">—</span>
                                <i class="fa-solid fa-arrow-right" style="font-size:11px;color:var(--text-muted)"></i>
                                <span id="tpDest">—</span>
                            </div>
                            <div class="trajet-info-pills">
                                <span class="tpill"><i class="fa-solid fa-road" style="font-size:9px"></i><span
                                        id="tpDist">—</span> km</span>
                                <span class="tpill"><i class="fa-regular fa-clock" style="font-size:9px"></i><span
                                        id="tpDuree">—</span> min</span>
                                <span class="tpill"><i class="fa-solid fa-coins" style="font-size:9px"></i>Péage : <span
                                        id="tpAuto">—</span> MAD</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Type véhicule --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-truck"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Type de véhicule
                        </h2>
                    </div>
                    <p style="font-size:12px;color:var(--text-muted);margin-bottom:12px">
                        Sélectionnez le type de véhicule concerné par cette prime <span
                            style="color:var(--color-primary)">*</span>
                    </p>

                    <div class="veh-grid">
                        @foreach ($typesVehicule as $key => $tv)
                            <label class="veh-card">
                                <input type="radio" name="type_vehicule" value="{{ $key }}"
                                    {{ old('type_vehicule') === $key ? 'checked' : '' }}>
                                <div class="veh-card-inner">
                                    <span class="veh-card-icon"><i class="fa-solid {{ $tv['icon'] }}"></i></span>
                                    <span class="veh-card-label">{{ $tv['label'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('type_vehicule')
                        <span class="field-error" style="margin-top:8px"><i class="fa-solid fa-circle-exclamation"></i>
                            {{ $message }}</span>
                    @enderror
                </div>

                {{-- Montant prime --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-coins"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Montant de la prime
                        </h2>
                    </div>
                    <div class="field">
                        <label for="montant_prime">Montant (MAD) <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Montant brut de la prime allouée au chauffeur pour ce trajet / véhicule</p>
                        <div class="montant-input-wrap">
                            <i class="fa-solid fa-coins field-prefix"></i>
                            <input type="number" id="montant_prime" name="montant_prime"
                                value="{{ old('montant_prime') }}" step="0.01" min="0" placeholder="0.00">
                            <span class="montant-unit">MAD</span>
                        </div>
                        @error('montant_prime')
                            <span class="field-error" style="margin-top:4px"><i class="fa-solid fa-circle-exclamation"></i>
                                {{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-icon"><i class="fa-solid fa-star-half-stroke"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu prime</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Trajet</div>
                            <div class="prev-value" id="prevTrajet"><span class="muted">—</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Type véhicule</div>
                            <div id="prevVehicule"><span class="prev-value muted">—</span></div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Montant prime</div>
                            <div class="prev-montant" id="prevMontant">—</div>
                            <div class="prev-montant-unit" id="prevMontantUnit" style="display:none">MAD</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="primeForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer la prime
                    </button>
                    <a href="{{ route('prime_deplacements.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Note :</strong> La combinaison trajet + type de véhicule définit
                    le montant de prime versé au chauffeur à chaque déplacement.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var trajetsData = JSON.parse(document.getElementById('trajetsData').textContent);
            var trajetSel = document.getElementById('trajet_id');
            var montantInput = document.getElementById('montant_prime');
            var radios = document.querySelectorAll('input[name="type_vehicule"]');

            // Métadonnées véhicule passées côté PHP
            var vehMeta = @json(collect($typesVehicule)->map(fn($v, $k) => array_merge($v, ['key' => $k]))->values());

            // Init si old()
            updateTrajetBox(trajetSel.value);

            trajetSel.addEventListener('change', function() {
                updateTrajetBox(this.value);
                updatePreview();
            });

            radios.forEach(function(r) {
                r.addEventListener('change', updatePreview);
            });

            montantInput.addEventListener('input', updatePreview);

            function updateTrajetBox(val) {
                var t = trajetsData.find(x => x.id == val);
                var box = document.getElementById('trajetInfoBox');
                if (t) {
                    document.getElementById('tpDepart').textContent = t.depart;
                    document.getElementById('tpDest').textContent = t.dest;
                    document.getElementById('tpDist').textContent = parseFloat(t.distance).toFixed(1);
                    document.getElementById('tpDuree').textContent = t.duree;
                    document.getElementById('tpAuto').textContent = parseFloat(t.autoroute).toFixed(2);
                    box.classList.add('visible');
                } else {
                    box.classList.remove('visible');
                }
            }

            function updatePreview() {
                // Trajet
                var tOpt = trajetSel.options[trajetSel.selectedIndex];
                document.getElementById('prevTrajet').innerHTML = (tOpt && tOpt.value) ?
                    '<span class="prev-value primary">' + tOpt.text + '</span>' :
                    '<span class="prev-value muted">—</span>';

                // Véhicule
                var checked = document.querySelector('input[name="type_vehicule"]:checked');
                var vehWrap = document.getElementById('prevVehicule');
                if (checked) {
                    var meta = vehMeta.find(v => v.key === checked.value);
                    var label = meta ? meta.label : checked.value;
                    var key = checked.value;
                    vehWrap.innerHTML = '<span class="badge-veh ' + key + '">' +
                        (meta ? '<i class="fa-solid ' + meta.icon + '" style="font-size:10px"></i>' : '') +
                        label + '</span>';
                } else {
                    vehWrap.innerHTML = '<span class="prev-value muted">—</span>';
                }

                // Montant
                var m = parseFloat(montantInput.value);
                var montantEl = document.getElementById('prevMontant');
                var unitEl = document.getElementById('prevMontantUnit');
                if (!isNaN(m) && m > 0) {
                    montantEl.textContent = m.toFixed(2);
                    unitEl.style.display = 'block';
                } else {
                    montantEl.textContent = '—';
                    unitEl.style.display = 'none';
                }
            }

            updatePreview();
        });
    </script>
@endpush
