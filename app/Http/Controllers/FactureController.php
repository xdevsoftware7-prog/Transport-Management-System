<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $query = Facture::with('client');

        // ── Recherche num_facture ──
        if ($search = $request->input('search')) {
            $query->where('num_facture', 'like', '%' . $search . '%');
        }

        // ── Filtre client ──
        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        // ── Filtre statut ──
        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        // ── Filtre date_facture (from / to) ──
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date_facture', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date_facture', '<=', $dateTo);
        }

        $factures = $query->orderByDesc('date_facture')->paginate(15)->withQueryString();

        // ── Stats ──
        $stats = [
            'total'       => Facture::count(),
            'reglees'     => Facture::where('statut', 'réglée')->count(),
            'non_reglees' => Facture::where('statut', 'non_réglée')->count(),
            'en_retard'   => Facture::where('statut', 'en_retard')->count(),
        ];

        $clients       = Client::orderBy('nom')->get(['id', 'nom']);
        $activeFilters = collect(['search', 'client_id', 'statut', 'date_from', 'date_to'])
            ->filter(fn($k) => $request->filled($k))
            ->count();

        return view('factures.index', compact('factures', 'stats', 'clients', 'activeFilters'));
    }

    // ──────────────────────────────────────────────
    //  CREATE
    // ──────────────────────────────────────────────
    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('nom')->get(['id', 'nom', 'type']);
        return view('factures.create', compact('clients'));
    }

    // ──────────────────────────────────────────────
    //  STORE
    // ──────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_facture'   => 'required|string|max:100|unique:factures,num_facture',
            'client_id'     => 'required|exists:clients,id',
            'date_facture'  => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'total_ht'      => 'required|numeric|min:0',
            'total_tva'     => 'required|numeric|min:0',
            'statut'        => ['required', Rule::in(['réglée', 'non_réglée', 'en_retard'])],
        ], [
            'num_facture.required'        => 'Le numéro de facture est obligatoire.',
            'num_facture.unique'          => 'Ce numéro de facture existe déjà.',
            'client_id.required'          => 'Veuillez sélectionner un client.',
            'client_id.exists'            => 'Le client sélectionné est invalide.',
            'date_facture.required'       => 'La date de facture est obligatoire.',
            'date_echeance.required'      => "La date d'échéance est obligatoire.",
            'date_echeance.after_or_equal' => "La date d'échéance doit être ≥ à la date de facture.",
            'total_ht.required'           => 'Le montant HT est obligatoire.',
            'total_ht.numeric'            => 'Le montant HT doit être un nombre.',
            'total_tva.required'          => 'Le montant TVA est obligatoire.',
            'total_tva.numeric'           => 'Le montant TVA doit être un nombre.',
            'statut.required'             => 'Le statut est obligatoire.',
            'statut.in'                   => 'Le statut sélectionné est invalide.',
        ]);

        Facture::create($validated);
        Alert::success('Facture « ' . $validated['num_facture'] . ' » créée avec succès.');
        return redirect()->route('factures.index');
    }

    // ──────────────────────────────────────────────
    //  EDIT
    // ──────────────────────────────────────────────
    public function edit(Facture $facture)
    {
        $clients = Client::where('is_active', true)->orderBy('nom')->get(['id', 'nom', 'type']);
        return view('factures.edit', compact('facture', 'clients'));
    }

    // ──────────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────────
    public function update(Request $request, Facture $facture)
    {
        $validated = $request->validate([
            'num_facture'   => ['required', 'string', 'max:100', Rule::unique('factures', 'num_facture')->ignore($facture->id)],
            'client_id'     => 'required|exists:clients,id',
            'date_facture'  => 'required|date',
            'date_echeance' => 'required|date|after_or_equal:date_facture',
            'total_ht'      => 'required|numeric|min:0',
            'total_tva'     => 'required|numeric|min:0',
            'statut'        => ['required', Rule::in(['réglée', 'non_réglée', 'en_retard'])],
        ], [
            'num_facture.required'        => 'Le numéro de facture est obligatoire.',
            'num_facture.unique'          => 'Ce numéro de facture existe déjà.',
            'client_id.required'          => 'Veuillez sélectionner un client.',
            'date_echeance.after_or_equal' => "La date d'échéance doit être ≥ à la date de facture.",
            'total_ht.numeric'            => 'Le montant HT doit être un nombre.',
            'total_tva.numeric'           => 'Le montant TVA doit être un nombre.',
            'statut.in'                   => 'Le statut sélectionné est invalide.',
        ]);

        $facture->update($validated);
        Alert::success('Facture « ' . $facture->num_facture . ' » mise à jour.');
        return redirect()->route('factures.index');
    }

    public function downloadPdf(Facture $facture)
    {
        // 1. Chargement des relations
        $facture->load([
            'client'
        ]);

        if (!$facture->client) {
            return back()->with('error', "Impossible de générer le PDF : Aucune commande associée.");
        }


        // 3. Génération du PDF
        $pdf = Pdf::loadView('factures.pdf', compact('facture'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Utile si tu ajoutes un logo plus tard
                'defaultFont' => 'DejaVu Sans'
            ]);

        // 4. Nettoyage du nom de fichier avec un fallback
        $numFAC = $facture->num_facture ?? 'SANS_NUMERO_' . $facture->id;
        $filename = 'FAC_' . str_replace(['/', '\\', ' '], '_', $numFAC) . '.pdf';

        return $pdf->download($filename);
    }

    public function destroy(Facture $facture)
    {
        $num = $facture->num_facture;
        $facture->delete();
        Alert::success('Facture « ' . $num . ' » supprimée.');
        return redirect()->route('factures.index');
    }
}
