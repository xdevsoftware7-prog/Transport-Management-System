{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN TARIF CLIENT — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $clients  : Collection (is_active=true)
|   - $trajets  : Collection (statut=actif, avec villeDepart, villeDestination)
|
| ROUTE : POST /tarif-clients → TarifClientController@store
| --}}

@extends('layouts.app')

@section('title', 'Nouveau Tarif Client')
@section('page-title', 'Nouveau Tarif Client')
@section('page-subtitle', 'Définir un prix de vente pour un client et un trajet')

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

        /* Layout */
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

        .field input[type="text"],
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

        .field-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        /* Suggestions type véhicule */
        .veh-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 10px
        }

        .veh-tab {
            padding: 6px 12px;
            border: 1.5px solid var(--border);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-secondary);
            background: var(--bg-body);
            cursor: pointer;
            transition: border-color var(--transition), color var(--transition), background var(--transition)
        }

        .veh-tab:hover,
        .veh-tab.selected {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: var(--color-primary-dim)
        }

        .section-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0 16px
        }

        /* Trajet card sélectionné */
        .trajet-preview-box {
            margin-top: 8px;
            padding: 12px 14px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            display: none
        }

        .trajet-preview-box.visible {
            display: block
        }

        .trajet-preview-route {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px
        }

        .trajet-preview-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap
        }

        .trajet-meta-pill {
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

        /* Sidebar */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px
        }

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

        .preview-tarif-icon {
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

        .prev-prix {
            font-size: 18px;
            font-weight: 800;
            color: #059669;
            font-family: 'JetBrains Mono', monospace
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
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('tarif_clients.index') }}">Tarifs Clients</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouveau tarif</span>
    </div>

    <form method="POST" action="{{ route('tarif_clients.store') }}" id="tarifForm">
        @csrf

        {{-- Data trajets pour JS --}}
        <script id="trajetsData" type="application/json">
        {!! json_encode($trajets->map(fn($t) => [
            'id'       => $t->id,
            'depart'   => $t->villeDepart?->nom ?? '?',
            'dest'     => $t->villeDestination?->nom ?? '?',
            'distance' => $t->distance_km,
            'duree'    => $t->duree_minutes,
            'autoroute'=> $t->prix_autoroute,
        ])) !!}
    </script>

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Client --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-building"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Client
                        </h2>
                    </div>
                    <div class="field">
                        <label for="client_id">Client <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Sélectionnez le client concerné par ce tarif</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-building field-prefix"></i>
                            <select id="client_id" name="client_id" required>
                                <option value="">— Sélectionner un client —</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" data-type="{{ $client->type }}"
                                        {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }}
                                        @if ($client->type === 'entreprise')
                                            (Ent.)
                                        @else
                                            (Part.)
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('client_id')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

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
                        <p class="field-hint">Trajet actif uniquement</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-route field-prefix"></i>
                            <select id="trajet_id" name="trajet_id" required>
                                <option value="">— Sélectionner un trajet —</option>
                                @foreach ($trajets as $trajet)
                                    <option value="{{ $trajet->id }}"
                                        {{ old('trajet_id') == $trajet->id ? 'selected' : '' }}>
                                        {{ $trajet->villeDepart?->nom }} → {{ $trajet->villeDestination?->nom }}
                                        ({{ number_format($trajet->distance_km, 0) }} km)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('trajet_id')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror

                        {{-- Aperçu trajet sélectionné --}}
                        <div class="trajet-preview-box" id="trajetPreviewBox">
                            <div class="trajet-preview-route">
                                <span id="tpDepart">—</span>
                                <i class="fa-solid fa-arrow-right" style="font-size:11px;color:var(--text-muted)"></i>
                                <span id="tpDest">—</span>
                            </div>
                            <div class="trajet-preview-meta">
                                <span class="trajet-meta-pill"><i class="fa-solid fa-road" style="font-size:9px"></i><span
                                        id="tpDist">—</span> km</span>
                                <span class="trajet-meta-pill"><i class="fa-regular fa-clock"
                                        style="font-size:9px"></i><span id="tpDuree">—</span> min</span>
                                <span class="trajet-meta-pill"><i class="fa-solid fa-coins" style="font-size:9px"></i>Auto :
                                    <span id="tpAuto">—</span> MAD</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Véhicule & Tonnage --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-truck"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Véhicule & Tonnage
                        </h2>
                    </div>

                    {{-- Suggestions type véhicule --}}
                    <p
                        style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text-muted);margin-bottom:8px">
                        Types courants</p>
                    <div class="veh-tabs" id="vehTabs">
                        @foreach (['Camion 3.5T', 'Camion 6T', 'Camion 10T', 'Camion 20T', 'Semi-remorque', 'Plateau', 'Frigorifique', 'Citerne'] as $vt)
                            <button type="button" class="veh-tab"
                                onclick="selectVehicule('{{ $vt }}')">{{ $vt }}</button>
                        @endforeach
                    </div>
                    <div class="section-divider"></div>

                    <input type="hidden" id="type_vehicule_hidden" name="type_vehicule"
                        value="{{ old('type_vehicule') }}">

                    <div class="field-row-2" style="margin-bottom:0">
                        <div class="field">
                            <label>Ou saisir un type</label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-truck field-prefix"></i>
                                <input type="text" id="type_vehicule_custom" placeholder="Ex : Benne, Grue…"
                                    maxlength="100" autocomplete="off" value="{{ old('type_vehicule') }}"
                                    style="padding-left:34px">
                            </div>
                            @error('type_vehicule')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label for="tonnage">Tonnage (T) <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-weight-hanging field-prefix"></i>
                                <input type="number" id="tonnage" name="tonnage"
                                    value="{{ old('tonnage', '0.00') }}" step="0.01" min="0"
                                    placeholder="0.00" style="padding-left:34px">
                            </div>
                            @error('tonnage')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Prix de vente --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-coins"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Prix de vente
                        </h2>
                    </div>
                    <div class="field">
                        <label for="prix_vente">Prix de vente (MAD) <span
                                style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Tarif hors taxes appliqué au client pour ce trajet</p>
                        <div class="field-input-wrap">
                            <i class="fa-solid fa-coins field-prefix"></i>
                            <input type="number" id="prix_vente" name="prix_vente" value="{{ old('prix_vente') }}"
                                step="0.01" min="0" placeholder="0.00" style="padding-left:34px">
                        </div>
                        @error('prix_vente')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
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
                        <div class="preview-tarif-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu tarif</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-body">
                        <div class="prev-row">
                            <div class="prev-label">Client</div>
                            <div class="prev-value primary" id="prevClient">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Trajet</div>
                            <div class="prev-value" id="prevTrajet">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Véhicule</div>
                            <div class="prev-value" id="prevVehicule">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Tonnage</div>
                            <div class="prev-value" id="prevTonnage">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Prix vente</div>
                            <div class="prev-prix" id="prevPrix">— MAD</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="tarifForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer le tarif
                    </button>
                    <a href="{{ route('tarif_clients.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Règle :</strong> La combinaison client + trajet + type véhicule doit être cohérente.
                    Le prix de vente est exprimé en MAD HT.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var trajetsData = JSON.parse(document.getElementById('trajetsData').textContent);
            var clientSel = document.getElementById('client_id');
            var trajetSel = document.getElementById('trajet_id');
            var vehHidden = document.getElementById('type_vehicule_hidden');
            var vehCustom = document.getElementById('type_vehicule_custom');
            var tonnageInput = document.getElementById('tonnage');
            var prixInput = document.getElementById('prix_vente');

            // Init depuis old()
            if (vehHidden.value) highlightVeh(vehHidden.value);

            // ── Saisie libre véhicule
            vehCustom.addEventListener('input', function() {
                vehHidden.value = this.value;
                document.querySelectorAll('.veh-tab').forEach(t => t.classList.toggle('selected', t
                    .textContent.trim() === this.value));
                updatePreview();
            });

            // ── Trajet → infos
            trajetSel.addEventListener('change', function() {
                var t = trajetsData.find(x => x.id == this.value);
                var box = document.getElementById('trajetPreviewBox');
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
                updatePreview();
            });

            [clientSel, tonnageInput, prixInput].forEach(el => {
                el.addEventListener('change', updatePreview);
                el.addEventListener('input', updatePreview);
            });

            function updatePreview() {
                var cOpt = clientSel.options[clientSel.selectedIndex];
                document.getElementById('prevClient').textContent = (cOpt && cOpt.value) ? cOpt.text : '—';

                var tOpt = trajetSel.options[trajetSel.selectedIndex];
                document.getElementById('prevTrajet').textContent = (tOpt && tOpt.value) ? tOpt.text : '—';

                var veh = vehHidden.value.trim();
                document.getElementById('prevVehicule').textContent = veh || '—';
                document.getElementById('prevVehicule').className = 'prev-value' + (veh ? ' primary' : ' muted');

                var ton = parseFloat(tonnageInput.value);
                document.getElementById('prevTonnage').textContent = (!isNaN(ton) && ton > 0) ? ton.toFixed(2) +
                    ' T' : '—';

                var prix = parseFloat(prixInput.value);
                document.getElementById('prevPrix').textContent = (!isNaN(prix) && prix > 0) ? prix.toFixed(2) +
                    ' MAD' : '— MAD';
            }

            updatePreview();
        });

        function selectVehicule(val) {
            document.getElementById('type_vehicule_hidden').value = val;
            document.getElementById('type_vehicule_custom').value = val;
            highlightVeh(val);
            // Déclencher l'aperçu
            document.getElementById('type_vehicule_custom').dispatchEvent(new Event('input'));
        }

        function highlightVeh(val) {
            document.querySelectorAll('.veh-tab').forEach(t => {
                t.classList.toggle('selected', t.textContent.trim() === val);
            });
        }
    </script>
@endpush
