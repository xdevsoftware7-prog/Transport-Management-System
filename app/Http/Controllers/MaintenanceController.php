<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Maintenance::with(['vehicule.chauffeur']);

        // Filtre : recherche textuelle (type_intervention)
        if ($search = $request->input('search')) {
            $query->where('type_intervention', 'like', "%{$search}%");
        }

        // Filtre : véhicule
        if ($vehiculeId = $request->input('vehicule_id')) {
            $query->where('vehicule_id', $vehiculeId);
        }

        // Filtre : statut
        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        // Filtre : plage de dates (date_debut)
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('date_debut', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('date_debut', '<=', $dateTo);
        }

        $maintenances = $query->latest()->paginate(15)->withQueryString();

        // KPI
        $stats = [
            'total'       => Maintenance::count(),
            'en_attente'  => Maintenance::where('statut', 'en_attente')->count(),
            'en_cours'    => Maintenance::where('statut', 'en_cours')->count(),
            'terminees'   => Maintenance::where('statut', 'terminée')->count(),
        ];

        // Liste véhicules pour le filtre
        $vehicules = Vehicule::orderBy('matricule')->get(['id', 'matricule', 'marque']);

        $activeFilters = collect([
            'search'      => $request->search,
            'vehicule_id' => $request->vehicule_id,
            'statut'      => $request->statut,
            'date_from'   => $request->date_from,
            'date_to'     => $request->date_to,
        ])->filter()->count();

        return view('maintenances.index', compact(
            'maintenances',
            'stats',
            'vehicules',
            'activeFilters'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $vehicules = Vehicule::orderBy('matricule')->get(['id', 'matricule', 'marque', 'type_vehicule']);
        return view('maintenances.create', compact('vehicules'));
    }

    // ──────────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicule_id'        => 'required|exists:vehicules,id',
            'type_intervention'  => 'required|string|max:255',
            'cout_total'         => 'nullable|numeric|min:0',
            'statut'             => 'required|in:en_attente,en_cours,terminée',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after_or_equal:date_debut',
        ], [
            'vehicule_id.required'       => 'Veuillez sélectionner un véhicule.',
            'vehicule_id.exists'         => 'Le véhicule sélectionné est invalide.',
            'type_intervention.required' => 'Le type d\'intervention est obligatoire.',
            'type_intervention.max'      => 'Le type d\'intervention ne peut pas dépasser 255 caractères.',
            'cout_total.numeric'         => 'Le coût total doit être un nombre.',
            'cout_total.min'             => 'Le coût total ne peut pas être négatif.',
            'statut.required'            => 'Le statut est obligatoire.',
            'statut.in'                  => 'Le statut sélectionné est invalide.',
            'date_debut.required'        => 'La date de début est obligatoire.',
            'date_debut.date'            => 'La date de début est invalide.',
            'date_fin.date'              => 'La date de fin est invalide.',
            'date_fin.after_or_equal'    => 'La date de fin doit être postérieure ou égale à la date de début.',
        ]);

        Maintenance::create($validated);
        Alert::success('Maintenance créée avec succès.');
        return redirect()->route('maintenances.index');
    }

    // ──────────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────────
    public function edit(Maintenance $maintenance)
    {
        $vehicules = Vehicule::orderBy('matricule')->get(['id', 'matricule', 'marque', 'type_vehicule']);
        $maintenance->load('vehicule.chauffeur');
        return view('maintenances.edit', compact('maintenance', 'vehicules'));
    }

    // ──────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate([
            'vehicule_id'        => 'required|exists:vehicules,id',
            'type_intervention'  => 'required|string|max:255',
            'cout_total'         => 'nullable|numeric|min:0',
            'statut'             => 'required|in:en_attente,en_cours,terminée',
            'date_debut'         => 'required|date',
            'date_fin'           => 'nullable|date|after_or_equal:date_debut',
        ], [
            'vehicule_id.required'       => 'Veuillez sélectionner un véhicule.',
            'vehicule_id.exists'         => 'Le véhicule sélectionné est invalide.',
            'type_intervention.required' => 'Le type d\'intervention est obligatoire.',
            'type_intervention.max'      => 'Le type d\'intervention ne peut pas dépasser 255 caractères.',
            'cout_total.numeric'         => 'Le coût total doit être un nombre.',
            'cout_total.min'             => 'Le coût total ne peut pas être négatif.',
            'statut.required'            => 'Le statut est obligatoire.',
            'statut.in'                  => 'Le statut sélectionné est invalide.',
            'date_debut.required'        => 'La date de début est obligatoire.',
            'date_debut.date'            => 'La date de début est invalide.',
            'date_fin.date'              => 'La date de fin est invalide.',
            'date_fin.after_or_equal'    => 'La date de fin doit être postérieure ou égale à la date de début.',
        ]);

        $maintenance->update($validated);
        Alert::success('Maintenance mise à jour avec succès.');
        return redirect()->route('maintenances.index');
    }

    // ──────────────────────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        Alert::success('Maintenance supprimée avec succès.');
        return redirect()->route('maintenances.index');
    }
}
