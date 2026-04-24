<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Infraction;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class InfractionController extends Controller
{
    public function index(Request $request)
    {
        $query = Infraction::with(['vehicule', 'chauffeur']);

        // Filtrage par recherche (type_infraction ou description)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('type_infraction', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrage par véhicule
        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        // Filtrage par chauffeur
        if ($request->filled('chauffeur_id')) {
            $query->where('chauffeur_id', $request->chauffeur_id);
        }

        // Filtrage par type_infraction
        if ($request->filled('type_infraction')) {
            $query->where('type_infraction', $request->type_infraction);
        }

        // Filtrage par période (date_infraction)
        if ($request->filled('date_from')) {
            $query->whereDate('date_infraction', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_infraction', '<=', $request->date_to);
        }

        // Filtrage par montant min/max
        if ($request->filled('montant_min')) {
            $query->where('montant', '>=', $request->montant_min);
        }
        if ($request->filled('montant_max')) {
            $query->where('montant', '<=', $request->montant_max);
        }

        $query->latest('date_infraction');

        $infractions = $query->paginate(15)->withQueryString();

        // Compteur de filtres actifs
        $activeFilters = collect([
            'search',
            'vehicule_id',
            'chauffeur_id',
            'type_infraction',
            'date_from',
            'date_to',
            'montant_min',
            'montant_max',
        ])->filter(fn($f) => $request->filled($f))->count();

        // Stats
        $stats = [
            'total'      => Infraction::count(),
            'ce_mois'    => Infraction::whereMonth('date_infraction', now()->month)
                ->whereYear('date_infraction', now()->year)
                ->count(),
            'montant_total' => Infraction::sum('montant'),
        ];

        // Listes pour les selects de filtre
        $vehicules  = Vehicule::orderBy('matricule')->get(['id', 'matricule', 'marque']);
        $chauffeurs = Chauffeur::orderBy('nom')->get(['id', 'code_drv', 'nom', 'prenom']);
        $types      = Infraction::select('type_infraction')
            ->distinct()
            ->orderBy('type_infraction')
            ->pluck('type_infraction');

        return view('infractions.index', compact(
            'infractions',
            'stats',
            'activeFilters',
            'vehicules',
            'chauffeurs',
            'types'
        ));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        $vehicules  = Vehicule::orderBy('matricule')->get(['id', 'matricule', 'marque']);
        $chauffeurs = Chauffeur::orderBy('nom')->get(['id', 'code_drv', 'nom', 'prenom']);

        return view('infractions.create', compact('vehicules', 'chauffeurs'));
    }

    /**
     * Enregistrement d'une nouvelle infraction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id'     => 'required|exists:vehicules,id',
            'chauffeur_id'    => 'required|exists:chauffeurs,id',
            'date_infraction' => 'required|date',
            'type_infraction' => 'required|string|max:255',
            'montant'         => 'required|numeric|min:0',
            'description'     => 'nullable|string|max:1000',
        ]);

        Infraction::create($validated);
        Alert::success('Infraction enregistrée avec succès.');
        return redirect()
            ->route('infractions.index');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Infraction $infraction)
    {
        $vehicules  = Vehicule::orderBy('matricule')->get(['id', 'matricule', 'marque']);
        $chauffeurs = Chauffeur::orderBy('nom')->get(['id', 'code_drv', 'nom', 'prenom']);

        return view('infractions.edit', compact('infraction', 'vehicules', 'chauffeurs'));
    }

    /**
     * Mise à jour d'une infraction.
     */
    public function update(Request $request, Infraction $infraction)
    {
        $validated = $request->validate([
            'vehicule_id'     => 'required|exists:vehicules,id',
            'chauffeur_id'    => 'required|exists:chauffeurs,id',
            'date_infraction' => 'required|date',
            'type_infraction' => 'required|string|max:255',
            'montant'         => 'required|numeric|min:0',
            'description'     => 'nullable|string|max:1000',
        ]);

        $infraction->update($validated);
        Alert::success('Infraction mise à jour avec succès.');
        return redirect()
            ->route('infractions.index');
    }

    /**
     * Suppression d'une infraction.
     */
    public function destroy(Infraction $infraction)
    {
        $infraction->delete();
        Alert::success('Infraction supprimée.');
        return redirect()
            ->route('infractions.index');
    }
}
