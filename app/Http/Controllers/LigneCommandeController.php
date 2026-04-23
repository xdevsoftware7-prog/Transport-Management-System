<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Commande;
use App\Models\LigneCommande;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class LigneCommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = LigneCommande::with(['commande.client', 'article'])
            ->orderByDesc('created_at');

        /* Filtres */
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas(
                'article',
                fn($q) =>
                $q->where('designation', 'like', "%{$search}%")
            )->orWhereHas(
                'commande',
                fn($q) =>
                $q->where('code_commande', 'like', "%{$search}%")
            );
        }

        if ($request->filled('commande_id')) {
            $query->where('commande_id', $request->commande_id);
        }

        if ($request->filled('article_id')) {
            $query->where('article_id', $request->article_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $lignes = $query->paginate(15)->withQueryString();

        /* Stats */
        $stats = [
            'total'         => LigneCommande::count(),
            'ce_mois'       => LigneCommande::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'poids_total'   => LigneCommande::sum('poids_kg'),
            'qte_totale'    => LigneCommande::sum('quantite'),
        ];

        /* Listes pour filtres */
        $commandes = Commande::orderBy('code_commande')->get(['id', 'code_commande']);
        $articles  = Article::orderBy('designation')->get(['id', 'designation', 'unite']);

        return view('ligne_commandes.index', compact(
            'lignes',
            'stats',
            'commandes',
            'articles'
        ));
    }


    public function create()
    {
        $commandes = Commande::with('client')
            ->orderBy('code_commande')
            ->get(['id', 'code_commande', 'client_id']);

        $articles = Article::orderBy('designation')
            ->get(['id', 'designation', 'unite']);

        return view('ligne_commandes.create', compact('commandes', 'articles'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'commande_id' => ['required', 'exists:commandes,id'],
            'article_id'  => ['required', 'exists:articles,id'],
            'quantite'    => ['required', 'integer', 'min:1'],
            'poids_kg'    => ['required', 'numeric', 'min:0'],
        ], [
            'commande_id.required' => 'La commande est obligatoire.',
            'commande_id.exists'   => 'La commande sélectionnée est invalide.',
            'article_id.required'  => "L'article est obligatoire.",
            'article_id.exists'    => "L'article sélectionné est invalide.",
            'quantite.required'    => 'La quantité est obligatoire.',
            'quantite.integer'     => 'La quantité doit être un entier.',
            'quantite.min'         => 'La quantité doit être au moins 1.',
            'poids_kg.required'    => 'Le poids est obligatoire.',
            'poids_kg.numeric'     => 'Le poids doit être numérique.',
            'poids_kg.min'         => 'Le poids ne peut pas être négatif.',
        ]);

        LigneCommande::create($validated);
        Alert::success('Ligne de commande ajoutée avec succès.');
        return redirect()
            ->route('ligne_commandes.index');
    }


    public function edit(LigneCommande $ligneCommande)
    {
        $commandes = Commande::with('client')
            ->orderBy('code_commande')
            ->get(['id', 'code_commande', 'client_id']);

        $articles = Article::orderBy('designation')
            ->get(['id', 'designation', 'unite']);

        return view('ligne_commandes.edit', compact('ligneCommande', 'commandes', 'articles'));
    }


    public function update(Request $request, LigneCommande $ligneCommande)
    {
        $validated = $request->validate([
            'commande_id' => ['required', 'exists:commandes,id'],
            'article_id'  => ['required', 'exists:articles,id'],
            'quantite'    => ['required', 'integer', 'min:1'],
            'poids_kg'    => ['required', 'numeric', 'min:0'],
        ], [
            'commande_id.required' => 'La commande est obligatoire.',
            'commande_id.exists'   => 'La commande sélectionnée est invalide.',
            'article_id.required'  => "L'article est obligatoire.",
            'article_id.exists'    => "L'article sélectionné est invalide.",
            'quantite.required'    => 'La quantité est obligatoire.',
            'quantite.integer'     => 'La quantité doit être un entier.',
            'quantite.min'         => 'La quantité doit être au moins 1.',
            'poids_kg.required'    => 'Le poids est obligatoire.',
            'poids_kg.numeric'     => 'Le poids doit être numérique.',
            'poids_kg.min'         => 'Le poids ne peut pas être négatif.',
        ]);

        $ligneCommande->update($validated);
        Alert::success('Ligne de commande modifiée avec succès.');
        return redirect()
            ->route('ligne_commandes.index');
    }


    public function destroy(LigneCommande $ligneCommande)
    {
        $ligneCommande->delete();
        Alert::success('Ligne de commande supprimée.');
        return redirect()
            ->route('ligne_commandes.index');
    }
}
