<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Trajet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class CommandeController extends Controller
{
    public function index(Request $request)
    {
        // ── Stats globales (indépendantes des filtres) ──────────────────────
        $stats = [
            'total'    => Commande::count(),
            'en_cours' => Commande::where('statut', 'en_cours')->count(),
            'livrees'  => Commande::where('statut', 'livree')->count(),
            'annulees' => Commande::where('statut', 'annulee')->count(),
        ];

        // ── Construction de la query avec filtres ───────────────────────────
        $query = Commande::with(['client', 'trajet.villeDepart', 'trajet.villeDestination'])
            ->latest();

        // Recherche texte (code_commande ou destinataire)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code_commande', 'like', "%{$search}%")
                    ->orWhere('destinataire', 'like', "%{$search}%");
            });
        }

        // Client
        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        // Trajet
        if ($trajetId = $request->input('trajet_id')) {
            $query->where('trajet_id', $trajetId);
        }

        // Type
        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        // Statut
        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        // Plage de dates livraison
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date_livraison', '>=', $dateFrom);
        }

        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date_livraison', '<=', $dateTo);
        }

        // ── Pagination (conserve les query params) ──────────────────────────
        $commandes = $query->paginate(20)->withQueryString();

        // ── Comptage des filtres actifs (pour le badge) ─────────────────────
        $activeFilters = collect([
            'search',
            'client_id',
            'trajet_id',
            'type',
            'statut',
            'date_from',
            'date_to',
        ])->filter(fn($key) => filled($request->input($key)))->count();

        // ── Données pour les selects des filtres ────────────────────────────
        $clients = Client::orderBy('nom')->get(['id', 'nom', 'type']);
        $trajets = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')
            ->get();

        return view('commandes.index', compact(
            'commandes',
            'stats',
            'clients',
            'trajets',
            'activeFilters'
        ));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'type']);

        $trajets = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')
            ->get();

        return view('commandes.create', compact('clients', 'trajets'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        Commande::create($validated);
        Alert::success("Commande « {$validated['code_commande']} » créée avec succès.");
        return redirect()
            ->route('commandes.index');
    }


    public function show(Commande $commande)
    {
        $commande->load(['client', 'trajet.villeDepart', 'trajet.villeDestination']);

        return view('commandes.show', compact('commande'));
    }


    public function edit(Commande $commande)
    {
        $commande->load(['client', 'trajet.villeDepart', 'trajet.villeDestination']);

        $clients = Client::where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom', 'type']);

        $trajets = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')
            ->get();

        return view('commandes.edit', compact('commande', 'clients', 'trajets'));
    }


    public function update(Request $request, Commande $commande)
    {
        $validated = $request->validate($this->rules($commande->id));

        $commande->update($validated);
        Alert::success("Commande « {$commande->code_commande} » mise à jour avec succès.");
        return redirect()
            ->route('commandes.index');
    }


    public function destroy(Commande $commande)
    {
        $code = $commande->code_commande;

        $commande->delete();
        Alert::success("Commande « {$code} » supprimée avec succès.");
        return redirect()
            ->route('commandes.index');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'code_commande'  => [
                'required',
                'string',
                'max:100',
                Rule::unique('commandes', 'code_commande')->ignore($ignoreId),
            ],
            'client_id'      => ['required', 'integer', 'exists:clients,id'],
            'trajet_id'      => ['required', 'integer', 'exists:trajets,id'],
            'date_livraison' => ['nullable', 'date', 'after_or_equal:today'],
            'type'           => ['required', Rule::in(['simple', 'groupé', 'composé'])],
            'statut'         => ['required', Rule::in(['en_attente', 'en_cours', 'livree', 'annulee'])],
            'destinataire'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
