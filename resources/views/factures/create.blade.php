{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE FACTURE — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $clients : Collection (id, nom, type) — clients actifs
|
| ROUTE : GET /factures/create → FactureController@create
|         POST /factures       → FactureController@store
--}}

@extends('layouts.app')

@section('title', 'Nouvelle facture')
@section('page-title', 'Nouvelle Facture')
@section('page-subtitle', 'Créer une nouvelle facture client')

@section('content')

    <style>
        /* ── BREADCRUMB ── */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 16px
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

        /* ── LAYOUT ── */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
            align-items: start
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 16px
        }

        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 14px;
            position: sticky;
            top: 20px
        }

        /* ── SECTION CARD ── */
        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--shadow-sm)
        }

        .section-header {
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border)
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center
        }

        .section-divider {
            height: 1px;
            background: var(--border);
            margin: 14px 0
        }

        /* ── FIELDS ── */
        .fields-grid {
            display: grid;
            gap: 14px
        }

        .fields-grid-2 {
            grid-template-columns: 1fr 1fr
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 5px
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-muted)
        }

        .field-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: -2px
        }

        .field-input-wrap {
            position: relative
        }

        .field-input-wrap input,
        .field-input-wrap select {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition)
        }

        .field-input-wrap input.with-prefix,
        .field-input-wrap select.with-prefix {
            padding-left: 34px
        }

        .field-prefix {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--text-muted);
            pointer-events: none
        }

        .field-input-wrap input:focus,
        .field-input-wrap select:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08)
        }

        .field-input-wrap input.is-invalid,
        .field-input-wrap select.is-invalid {
            border-color: var(--color-primary)
        }

        .field-error {
            font-size: 11px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px
        }

        /* ── STATUT TABS ── */
        .statut-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .statut-tab {
            padding: 8px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-body);
            color: var(--text-muted);
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .statut-tab:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .statut-tab.selected-regle {
            border-color: #10b981;
            background: rgba(16, 185, 129, .1);
            color: #10b981
        }

        .statut-tab.selected-non-regle {
            border-color: #f59e0b;
            background: rgba(245, 158, 11, .1);
            color: #f59e0b
        }

        .statut-tab.selected-retard {
            border-color: #e02020;
            background: rgba(224, 32, 32, .1);
            color: #e02020
        }

        /* ── PREVIEW CARD ── */
        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px;
            box-shadow: var(--shadow-sm)
        }

        .preview-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border)
        }

        .preview-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--color-primary-dim);
            border: 1px solid rgba(224, 32, 32, .15);
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
            color: var(--text-primary)
        }

        .preview-header-sub {
            font-size: 11px;
            color: var(--text-muted)
        }

        .prev-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid var(--border)
        }

        .prev-row:last-child {
            border-bottom: none;
            padding-bottom: 0
        }

        .prev-label {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0
        }

        .prev-value {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            text-align: right
        }

        .prev-value.muted {
            color: var(--text-muted);
            font-weight: 400
        }

        .prev-value.primary {
            color: var(--color-primary)
        }

        .prev-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block
        }

        .prev-badge.success {
            background: rgba(16, 185, 129, .1);
            color: #10b981
        }

        .prev-badge.warning {
            background: rgba(245, 158, 11, .1);
            color: #f59e0b
        }

        .prev-badge.danger {
            background: rgba(224, 32, 32, .1);
            color: #e02020
        }

        /* ── ACTION CARD ── */
        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
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
            transition: opacity .15s
        }

        .btn-submit:hover {
            opacity: .88
        }

        .btn-cancel {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: var(--text-muted);
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
            transition: border-color .15s, color .15s
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary)
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.6
        }

        .info-box strong {
            color: var(--text-primary)
        }
    </style>

    {{-- ── BREADCRUMB ── --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i></a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('factures.index') }}">Factures</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Nouvelle facture</span>
    </div>

    <form method="POST" action="{{ route('factures.store') }}" id="factureForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- ── Identification ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-file-invoice"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Identification
                        </h2>
                    </div>

                    <div class="fields-grid fields-grid-2">

                        {{-- Numéro facture --}}
                        <div class="field">
                            <label for="num_facture">N° Facture <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Référence unique de la facture</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-hashtag field-prefix"></i>
                                <input type="text" id="num_facture" name="num_facture"
                                    class="with-prefix @error('num_facture') is-invalid @enderror"
                                    value="{{ old('num_facture') }}" placeholder="Ex : FAC-2024-001" required
                                    maxlength="100" autocomplete="off" autofocus>
                            </div>
                            @error('num_facture')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Client --}}
                        <div class="field">
                            <label for="client_id">Client <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Client associé à la facture</p>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-user field-prefix"></i>
                                <select id="client_id" name="client_id"
                                    class="with-prefix @error('client_id') is-invalid @enderror" required>
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

                    </div>
                </div>

                {{-- ── Dates ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-calendar-days"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Dates
                        </h2>
                    </div>

                    <div class="fields-grid fields-grid-2">

                        <div class="field">
                            <label for="date_facture">Date de facture <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar field-prefix"></i>
                                <input type="date" id="date_facture" name="date_facture"
                                    class="with-prefix @error('date_facture') is-invalid @enderror"
                                    value="{{ old('date_facture', date('Y-m-d')) }}" required>
                            </div>
                            @error('date_facture')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="date_echeance">Date d'échéance <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-calendar-xmark field-prefix"></i>
                                <input type="date" id="date_echeance" name="date_echeance"
                                    class="with-prefix @error('date_echeance') is-invalid @enderror"
                                    value="{{ old('date_echeance') }}" required>
                            </div>
                            @error('date_echeance')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ── Montants ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-coins"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Montants
                        </h2>
                    </div>

                    <div class="fields-grid fields-grid-2">

                        <div class="field">
                            <label for="total_ht">Total HT (MAD) <span style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-money-bill field-prefix"></i>
                                <input type="number" id="total_ht" name="total_ht"
                                    class="with-prefix @error('total_ht') is-invalid @enderror"
                                    value="{{ old('total_ht', '0.00') }}" step="0.01" min="0" required>
                            </div>
                            @error('total_ht')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="total_tva">Total TVA (MAD) <span
                                    style="color:var(--color-primary)">*</span></label>
                            <div class="field-input-wrap">
                                <i class="fa-solid fa-percent field-prefix"></i>
                                <input type="number" id="total_tva" name="total_tva"
                                    class="with-prefix @error('total_tva') is-invalid @enderror"
                                    value="{{ old('total_tva', '0.00') }}" step="0.01" min="0" required>
                            </div>
                            @error('total_tva')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ── Statut ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-tag"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Statut <span style="color:var(--color-primary)">*</span>
                        </h2>
                    </div>

                    <input type="hidden" id="statut" name="statut" value="{{ old('statut', 'non_réglée') }}">

                    <div class="statut-tabs" id="statutTabs">
                        <button type="button" class="statut-tab" onclick="selectStatut('non_réglée')">
                            <i class="fa-solid fa-clock"></i> Non réglée
                        </button>
                        <button type="button" class="statut-tab" onclick="selectStatut('réglée')">
                            <i class="fa-solid fa-circle-check"></i> Réglée
                        </button>
                        <button type="button" class="statut-tab" onclick="selectStatut('en_retard')">
                            <i class="fa-solid fa-triangle-exclamation"></i> En retard
                        </button>
                    </div>

                    @error('statut')
                        <span class="field-error" style="margin-top:8px">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </span>
                    @enderror
                </div>

            </div>

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-header">
                        <div class="preview-icon"><i class="fa-solid fa-file-invoice"></i></div>
                        <div>
                            <div class="preview-header-label">Aperçu facture</div>
                            <div class="preview-header-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div>
                        <div class="prev-row">
                            <div class="prev-label">N° Facture</div>
                            <div class="prev-value primary" id="prevNum">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Client</div>
                            <div class="prev-value muted" id="prevClient">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Date facture</div>
                            <div class="prev-value muted" id="prevDateFac">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Échéance</div>
                            <div class="prev-value muted" id="prevDateEch">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Total HT</div>
                            <div class="prev-value" id="prevHt">0,00 MAD</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">TVA</div>
                            <div class="prev-value" id="prevTva">0,00 MAD</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Total TTC</div>
                            <div class="prev-value primary" id="prevTtc">0,00 MAD</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Statut</div>
                            <div id="prevStatutWrap"><span class="prev-value muted">—</span></div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="factureForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer la facture
                    </button>
                    <a href="{{ route('factures.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Unicité :</strong> Le numéro de facture doit être unique.<br>
                    <strong>Échéance :</strong> La date d'échéance doit être égale ou postérieure à la date de facture.
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var numInput = document.getElementById('num_facture');
            var clientSelect = document.getElementById('client_id');
            var dateFacInput = document.getElementById('date_facture');
            var dateEchInput = document.getElementById('date_echeance');
            var htInput = document.getElementById('total_ht');
            var tvaInput = document.getElementById('total_tva');

            // Init statut depuis old()
            var initStatut = document.getElementById('statut').value || 'non_réglée';
            highlightStatut(initStatut);
            renderStatutBadge(initStatut);

            [numInput, clientSelect, dateFacInput, dateEchInput, htInput, tvaInput]
            .forEach(el => el && el.addEventListener('input', updatePreview));
            [numInput, clientSelect, dateFacInput, dateEchInput, htInput, tvaInput]
            .forEach(el => el && el.addEventListener('change', updatePreview));

            updatePreview();

            function updatePreview() {
                // Num
                var num = numInput ? numInput.value.trim() : '';
                document.getElementById('prevNum').textContent = num || '—';
                document.getElementById('prevNum').className = 'prev-value' + (num ? ' primary' : ' muted');

                // Client
                var clientEl = clientSelect ? clientSelect.options[clientSelect.selectedIndex] : null;
                var clientName = clientEl && clientEl.value ? clientEl.text.trim() : '—';
                document.getElementById('prevClient').textContent = clientName;
                document.getElementById('prevClient').className = 'prev-value' + (clientEl && clientEl.value ? '' :
                    ' muted');

                // Dates
                document.getElementById('prevDateFac').textContent = formatDate(dateFacInput ? dateFacInput.value :
                    '');
                document.getElementById('prevDateEch').textContent = formatDate(dateEchInput ? dateEchInput.value :
                    '');

                // Montants
                var ht = parseFloat(htInput ? htInput.value : 0) || 0;
                var tva = parseFloat(tvaInput ? tvaInput.value : 0) || 0;
                var ttc = ht + tva;
                document.getElementById('prevHt').textContent = fmtMontant(ht);
                document.getElementById('prevTva').textContent = fmtMontant(tva);
                document.getElementById('prevTtc').textContent = fmtMontant(ttc);
            }

            function formatDate(val) {
                if (!val) return '—';
                var parts = val.split('-');
                if (parts.length !== 3) return val;
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }

            function fmtMontant(n) {
                return n.toLocaleString('fr-MA', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' MAD';
            }
        });

        function selectStatut(val) {
            document.getElementById('statut').value = val;
            highlightStatut(val);
            renderStatutBadge(val);
        }

        function highlightStatut(val) {
            document.querySelectorAll('.statut-tab').forEach(function(btn) {
                btn.classList.remove('selected-regle', 'selected-non-regle', 'selected-retard');
            });
            var map = {
                'réglée': 'selected-regle',
                'non_réglée': 'selected-non-regle',
                'en_retard': 'selected-retard'
            };
            document.querySelectorAll('.statut-tab').forEach(function(btn) {
                var txt = btn.textContent.trim().toLowerCase();
                if (val === 'réglée' && txt.includes('réglée') && !txt.includes('non')) btn.classList.add(
                    'selected-regle');
                if (val === 'non_réglée' && txt.includes('non')) btn.classList.add('selected-non-regle');
                if (val === 'en_retard' && txt.includes('retard')) btn.classList.add('selected-retard');
            });
        }

        function renderStatutBadge(val) {
            var wrap = document.getElementById('prevStatutWrap');
            if (!wrap) return;
            var labels = {
                'réglée': ['success', 'Réglée'],
                'non_réglée': ['warning', 'Non réglée'],
                'en_retard': ['danger', 'En retard']
            };
            var info = labels[val];
            if (info) {
                wrap.innerHTML = '<span class="prev-badge ' + info[0] + '">' + info[1] + '</span>';
            } else {
                wrap.innerHTML = '<span class="prev-value muted">—</span>';
            }
        }
    </script>
@endpush
