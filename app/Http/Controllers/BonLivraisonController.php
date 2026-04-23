<?php

namespace App\Http\Controllers;

use App\Models\BonLivraison;
use App\Models\Chauffeur;
use App\Models\Commande;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use RealRashid\SweetAlert\Facades\Alert;

class BonLivraisonController extends Controller
{
    public function index(Request $request)
    {
        $query = BonLivraison::with([
            'commande.client',
            'vehicule',
            'chauffeur',
        ])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('num_bl', 'like', "%{$s}%")
                    ->orWhereHas('commande', fn($q2) =>
                    $q2->where('code_commande', 'like', "%{$s}%"))
                    ->orWhereHas('vehicule', fn($q2) =>
                    $q2->where('matricule', 'like', "%{$s}%"))
                    ->orWhereHas('chauffeur', fn($q2) =>
                    $q2->where('nom', 'like', "%{$s}%")
                        ->orWhere('prenom', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('commande_id')) {
            $query->where('commande_id', $request->commande_id);
        }

        if ($request->filled('chauffeur_id')) {
            $query->where('chauffeur_id', $request->chauffeur_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_livraison_reelle', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_livraison_reelle', '<=', $request->date_to);
        }

        $bons = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => BonLivraison::count(),
            'ce_mois'    => BonLivraison::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'livres'     => BonLivraison::where('statut', 'livré')->count(),
            'en_cours'   => BonLivraison::whereIn('statut', ['émis'])->count(),
        ];

        $commandes  = Commande::orderBy('code_commande')->get(['id', 'code_commande']);
        $chauffeurs = Chauffeur::orderBy('nom')->get(['id', 'code_drv', 'nom', 'prenom']);

        return view('bon_livraisons.index', compact(
            'bons',
            'stats',
            'commandes',
            'chauffeurs'
        ));
    }

    /* ── CREATE ────────────────────────────────────────────────────── */

    public function create()
    {
        $commandes  = Commande::with('client')->orderBy('code_commande')
            ->get(['id', 'code_commande', 'client_id', 'destinataire', 'date_livraison']);
        $vehicules  = Vehicule::where('statut', '!=', 'hors_service')
            ->orderBy('matricule')->get(['id', 'matricule', 'marque', 'type_vehicule']);
        $chauffeurs = Chauffeur::where('statut', 'actif')
            ->orderBy('nom')->get(['id', 'code_drv', 'nom', 'prenom']);

        // Auto-generate num_bl
        $nextNum = BonLivraison::max('id') + 1;
        $numBl   = 'BL-' . date('Y') . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

        return view('bon_livraisons.create', compact(
            'commandes',
            'vehicules',
            'chauffeurs',
            'numBl'
        ));
    }

    /* ── STORE ─────────────────────────────────────────────────────── */

    public function store(Request $request)
    {

        $validated = $request->validate([
            'num_bl'                 => ['required', 'string', 'max:50', 'unique:bon_livraisons,num_bl'],
            'commande_id'            => ['required', 'exists:commandes,id'],
            'vehicule_id'            => ['required', 'exists:vehicules,id'],
            'chauffeur_id'           => ['required', 'exists:chauffeurs,id'],
            'date_livraison_reelle'  => ['nullable', 'date'],
            'statut'                 => ['required', 'in:' . implode(',', array_keys(BonLivraison::STATUTS))],
        ], [
            'num_bl.required'    => 'Le numéro BL est obligatoire.',
            'num_bl.unique'      => 'Ce numéro BL existe déjà.',
            'commande_id.required' => 'La commande est obligatoire.',
            'commande_id.exists'   => 'La commande sélectionnée est invalide.',
            'vehicule_id.required' => 'Le véhicule est obligatoire.',
            'vehicule_id.exists'   => 'Le véhicule sélectionné est invalide.',
            'chauffeur_id.required' => 'Le chauffeur est obligatoire.',
            'chauffeur_id.exists'   => 'Le chauffeur sélectionné est invalide.',
            'statut.required'       => 'Le statut est obligatoire.',
            'statut.in'             => 'Le statut sélectionné est invalide.',
        ]);

        BonLivraison::create($validated);
        Alert::success('Bon de livraison ' . $validated['num_bl'] . ' créé avec succès.');
        return redirect()
            ->route('bon_livraisons.index');
    }

    /* ── EDIT ──────────────────────────────────────────────────────── */

    public function edit(BonLivraison $bonLivraison)
    {
        $commandes  = Commande::with('client')->orderBy('code_commande')
            ->get(['id', 'code_commande', 'client_id', 'destinataire', 'date_livraison']);
        $vehicules  = Vehicule::orderBy('matricule')
            ->get(['id', 'matricule', 'marque', 'type_vehicule', 'statut']);
        $chauffeurs = Chauffeur::orderBy('nom')
            ->get(['id', 'code_drv', 'nom', 'prenom', 'statut']);

        return view('bon_livraisons.edit', compact(
            'bonLivraison',
            'commandes',
            'vehicules',
            'chauffeurs'
        ));
    }

    /* ── UPDATE ────────────────────────────────────────────────────── */

    public function update(Request $request, BonLivraison $bonLivraison)
    {
        $validated = $request->validate([
            'num_bl'                => [
                'required',
                'string',
                'max:50',
                'unique:bon_livraisons,num_bl,' . $bonLivraison->id
            ],
            'commande_id'           => ['required', 'exists:commandes,id'],
            'vehicule_id'           => ['required', 'exists:vehicules,id'],
            'chauffeur_id'          => ['required', 'exists:chauffeurs,id'],
            'date_livraison_reelle' => ['nullable', 'date'],
            'statut'                => ['required', 'in:' . implode(',', array_keys(BonLivraison::STATUTS))],
        ], [
            'num_bl.required'    => 'Le numéro BL est obligatoire.',
            'num_bl.unique'      => 'Ce numéro BL existe déjà.',
            'commande_id.required' => 'La commande est obligatoire.',
            'vehicule_id.required' => 'Le véhicule est obligatoire.',
            'chauffeur_id.required' => 'Le chauffeur est obligatoire.',
            'statut.required'       => 'Le statut est obligatoire.',
            'statut.in'             => 'Le statut sélectionné est invalide.',
        ]);

        $bonLivraison->update($validated);
        Alert::success('Bon de livraison ' . $bonLivraison->num_bl . ' mis à jour.');
        return redirect()
            ->route('bon_livraisons.index');
    }

    /* ── DESTROY ───────────────────────────────────────────────────── */

    public function destroy(BonLivraison $bonLivraison)
    {
        $num = $bonLivraison->num_bl;
        $bonLivraison->delete();
        Alert::success("Bon de livraison {$num} supprimé.");
        return redirect()
            ->route('bon_livraisons.index');
    }

    /* ── DOWNLOAD PDF ──────────────────────────────────────────────── */

    public function downloadPdf(BonLivraison $bonLivraison)
    {

        // 1. Chargement des relations
        $bonLivraison->load([
            'commande',
            'commande.client',
            'commande.lignesCommandes.article',
            'vehicule',
            'chauffeur',
        ]);
        // $bonLivraison->withRelationshipAutoloading();
        // dd($bonLivraison);
        // 2. Vérification de sécurité (Optionnel mais recommandé)
        // Si la commande est manquante, on évite le crash du PDF
        // dd($bonLivraison->commande);
        if (!$bonLivraison->commande) {
            return back()->with('error', "Impossible de générer le PDF : Aucune commande associée.");
        }


        // 3. Génération du PDF
        $pdf = Pdf::loadView('bon_livraisons.pdf', compact('bonLivraison'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Utile si tu ajoutes un logo plus tard
                'defaultFont' => 'DejaVu Sans'
            ]);

        // 4. Nettoyage du nom de fichier avec un fallback
        $numBl = $bonLivraison->num_bl ?? 'SANS_NUMERO_' . $bonLivraison->id;
        $filename = 'BL_' . str_replace(['/', '\\', ' '], '_', $numBl) . '.pdf';

        return $pdf->download($filename);
    }
}
