{{--
|--------------------------------------------------------------------------
| VUE PDF : BON DE LIVRAISON
|--------------------------------------------------------------------------
| Chemin  : resources/views/bon_livraisons/pdf.blade.php
| Moteur  : barryvdh/laravel-dompdf
| Variable: $facture  (avec commande.client, commande.lignesCommandes.article, vehicule, chauffeur)
--}}
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bon de livraison {{ $facture->num_facture }}</title>
    <style>
        /* ── RESET & BASE ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            line-height: 1.5;
            background: #ffffff;
        }

        /* ── PAGE ── */
        .page {
            width: 80%;
            padding: 28px 32px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ── HEADER ── */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 24px;
            border-bottom: 3px solid #e02020;
            padding-bottom: 16px;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: right;
        }

        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #e02020;
            letter-spacing: 1px;
        }

        .company-sub {
            font-size: 9px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }

        .doc-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .doc-num {
            font-size: 13px;
            font-weight: 700;
            color: #e02020;
            font-family: 'DejaVu Sans Mono', monospace;
            margin-top: 4px;
        }

        .doc-meta {
            font-size: 9px;
            color: #888;
            margin-top: 3px;
        }

        /* ── STATUT BADGE ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        .badge-brouillon {
            background: #f3f4f6;
            color: #6b7280;
            border: 1px solid #d1d5db;
        }

        .badge-emis {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .badge-livre {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        .badge-partiel {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }

        .badge-annule {
            background: #fef2f2;
            color: #e02020;
            border: 1px solid #fecaca;
        }

        /* ── INFO BLOCKS ── */
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 18px;
            border-spacing: 10px 0;
        }

        .info-block {
            display: table-cell;
            vertical-align: top;
            width: 33.33%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 14px;
        }

        .info-block-title {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin-bottom: 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-block-title i {
            color: #e02020;
            margin-right: 4px;
        }

        .info-line {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .info-key {
            display: table-cell;
            font-size: 9px;
            color: #6b7280;
            width: 45%;
            vertical-align: top;
        }

        .info-val {
            display: table-cell;
            font-size: 10px;
            font-weight: 600;
            color: #1a1a2e;
            vertical-align: top;
        }

        .info-val.mono {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            color: #e02020;
        }

        /* ── TABLE ARTICLES ── */
        .section-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9ca3af;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead tr {
            background: #1a1a2e;
            color: #fff;
        }

        .items-table thead th {
            padding: 8px 12px;
            text-align: left;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table thead th:last-child {
            text-align: right;
        }

        .items-table thead th.center {
            text-align: center;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .items-table tbody td {
            padding: 9px 12px;
            font-size: 10px;
            color: #374151;
            vertical-align: middle;
        }

        .items-table tbody td.center {
            text-align: center;
        }

        .items-table tbody td.right {
            text-align: right;
        }

        .items-table tbody td.ref {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9px;
            color: #6b7280;
        }

        .items-table tbody tr:last-child {
            border-bottom: none;
        }

        /* empty articles */
        .no-items {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-style: italic;
            font-size: 11px;
            background: #fafafa;
            border: 1px dashed #e5e7eb;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        /* ── SIGNATURES ── */
        .signatures {
            display: table;
            width: 100%;
            margin-top: 28px;
        }

        .sig-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 12px;
            vertical-align: top;
        }

        .sig-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .sig-box {
            height: 60px;
            border: 1.5px dashed #d1d5db;
            border-radius: 6px;
            background: #f9fafb;
        }

        .sig-name {
            font-size: 10px;
            font-weight: 600;
            color: #1a1a2e;
            margin-top: 8px;
        }

        .sig-sub {
            font-size: 9px;
            color: #9ca3af;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            font-size: 8px;
            color: #9ca3af;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            font-size: 8px;
            color: #9ca3af;
            text-align: right;
            vertical-align: middle;
        }

        /* ── UTILITY ── */
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
        }

        .red {
            color: #e02020;
        }

        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
        }

        /* ── SPACER for table-cell layout ── */
        .spacer {
            display: table-cell;
            width: 14px;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- ═══════════════════════════════════════════
         EN-TÊTE
    ═══════════════════════════════════════════ --}}
        <div class="header">
            <div class="header-left">
                <div class="company-name">OBTRANS</div>
                <div class="company-sub">Transport &amp; Logistique</div>
            </div>
            <div class="header-right">
                <div class="doc-title">Facture</div>
                <div class="doc-num">{{ $facture->num_facture }}</div>
                <div class="doc-meta">
                    Émis le @php
                        $dateCreation = $facture->created_at ?? ($facture->created_at = now());
                    @endphp
                    {{ $dateCreation->format('d/m/Y à H:i') }}
                </div>
                @php
                    $cssClass = match ($facture->statut) {
                        'réglée' => 'Réglée',
                        'non_réglée' => 'Non Réglé',
                        'en_retard' => 'En Retard',
                        default => 'Non Réglé',
                    };
                @endphp
                <div>
                    <span class="badge {{ $cssClass }}">
                        {{ $facture->statutLabel }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
         BLOCS INFO : Facture · Client
    ═══════════════════════════════════════════ --}}
        <div class="info-row">

            {{-- Commande --}}
            <div class="info-block">
                <div class="info-block-title">Facture</div>

                <div class="info-line">
                    <span class="info-key">N° Facture</span>
                    <span class="info-val mono">{{ $facture->num_facture ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Date Facture</span>
                    <span class="info-val">{{ $facture->date_facture->format('Y-m-d') ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Date Echeance</span>
                    <span class="info-val">{{ $facture->date_echeance->format('Y-m-d') ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Total Hors Taxe</span>
                    <span class="info-val">
                        {{ $facture->total_ht ?? '—' }}
                    </span>
                </div>
                <div class="info-line">
                    <span class="info-key">Total TVA</span>
                    <span class="info-val">{{ $facture->total_tva ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Total TTC</span>
                    <span class="info-val">{{ $facture->total_ttc ?? '—' }}</span>
                </div>
            </div>

            <div class="spacer"></div>

            {{-- Client --}}
            <div class="info-block">
                <div class="info-block-title">Client</div>

                <div class="info-line">
                    <span class="info-key">Nom Client</span>
                    <span class="info-val mono">{{ $facture->client->nom ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Type</span>
                    <span class="info-val">{{ $facture->client->type ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Email</span>
                    <span class="info-val">{{ $facture->client->email ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Telephone</span>
                    <span class="info-val">
                        {{ $facture->client->telephone ?? '—' }}
                    </span>
                </div>
                <div class="info-line">
                    <span class="info-key">Adresse</span>
                    <span class="info-val">{{ $facture->client->adresse ?? '—' }}</span>
                </div>
                <div class="info-line">
                    <span class="info-key">Adresse</span>
                    <span class="info-val">{{ $facture->client->adresse ?? '—' }}</span>
                </div>
            </div>

            <div class="spacer"></div>

        </div>

        <div class="divider"></div>
        <div class="section-title">Signatures &amp; validation</div>

        <div class="signatures">
            <div class="sig-col">
                <div class="sig-label">Responsable expédition</div>
                <div class="sig-box"></div>
                <div class="sig-name">OBTRANS</div>
                <div class="sig-sub">Cachet &amp; signature</div>
            </div>

            <div class="sig-col">
                <div class="sig-label">Client</div>
                <div class="sig-box"></div>
                <div class="sig-name">
                    {{ $facture->commande->destinataire ?? ($facture->commande->client->nom ?? '—') }}</div>
                <div class="sig-sub">Cachet &amp; signature</div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
         PIED DE PAGE
    ═══════════════════════════════════════════ --}}
        <div class="footer">
            <div class="footer-left">
                Document généré le {{ now()->format('d/m/Y à H:i') }} — OBTRANS TMS
            </div>
            <div class="footer-right">
                <span class="mono red">{{ $facture->num_facture }}</span>
                &nbsp;·&nbsp; Page 1/1
            </div>
        </div>

    </div>
</body>

</html>
