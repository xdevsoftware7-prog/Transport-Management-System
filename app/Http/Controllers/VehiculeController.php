<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class VehiculeController extends Controller
{
    const TYPES_VEHICULE = [
        'tracteur',
        'semi-remorque',
        'camion',
        'fourgon',
        'benne',
        'citerne',
        'frigo',
        'plateau',
        'autre',
    ];

    const STATUTS = ['actif', 'en_maintenance', 'hors_service'];

    public function index(Request $request)
    {
        $query = Vehicule::with('chauffeur');

        // ── Filtres ──────────────────────────────────────────────────────────
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('matricule',   'like', "%{$search}%")
                    ->orWhere('marque',    'like', "%{$search}%")
                    ->orWhere('num_chassis', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type_vehicule', 'like', "%{$type}%");
        }

        if ($acquisition = $request->input('acquisition')) {
            $query->where('acquisition', $acquisition);
        }

        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }

        if ($chauffeurId = $request->input('chauffeur_id')) {
            $query->where('chauffeur_id', $chauffeurId);
        }

        // ── Pagination ───────────────────────────────────────────────────────
        $vehicules = $query->latest()->paginate(15)->withQueryString();

        // ── Stats KPI ────────────────────────────────────────────────────────
        $stats = [
            'total'     => Vehicule::count(),
            'actifs'    => Vehicule::where('statut', 'actif')->count(),
            'achats'    => Vehicule::where('acquisition', 'achat')->count(),
            'locations' => Vehicule::where('acquisition', 'location')->count(),
        ];

        // ── Données filtres ──────────────────────────────────────────────────
        $chauffeurs = Chauffeur::where('statut', 'actif')
            ->orderBy('nom')
            ->get();

        return view('vehicules.index', compact('vehicules', 'stats', 'chauffeurs'));
    }

    /**
     * GET /vehicules/create
     */
    public function create()
    {
        $chauffeurs = Chauffeur::where('statut', 'actif')->orderBy('nom')->get();

        return view('vehicules.create', compact('chauffeurs'));
    }

    /**
     * POST /vehicules
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'matricule'        => ['required', 'string', 'max:30', 'unique:vehicules,matricule'],
            'marque'           => ['required', 'string', 'max:100'],
            'type_vehicule'    => ['nullable', 'string', 'max:100'],
            'acquisition'      => ['required', Rule::in(['achat', 'location'])],
            'date_circulation' => ['nullable', 'date'],
            'poids_a_vide'     => ['nullable', 'numeric', 'min:0'],
            'ptac'             => ['nullable', 'numeric', 'min:0'],
            'num_chassis'      => ['nullable', 'string', 'max:17'],
            'km_initial'       => ['nullable', 'integer', 'min:0'],
            'statut'           => ['required', Rule::in(['actif', 'inactif', 'maintenance'])],
            'chauffeur_id'     => ['nullable', 'exists:chauffeurs,id'],
        ]);

        Vehicule::create($data);
        Alert::success("Véhicule « {$data['matricule']} » enregistré avec succès.");
        return redirect()
            ->route('vehicules.index');
    }

    /**
     * GET /vehicules/{vehicule}/edit
     */
    public function edit(Vehicule $vehicule)
    {
        $chauffeurs = Chauffeur::where('statut', 'actif')->orderBy('nom')->get();

        return view('vehicules.edit', compact('vehicule', 'chauffeurs'));
    }

    /**
     * PUT /vehicules/{vehicule}
     */
    public function update(Request $request, Vehicule $vehicule)
    {
        $data = $request->validate([
            'matricule'        => ['required', 'string', 'max:30', Rule::unique('vehicules', 'matricule')->ignore($vehicule->id)],
            'marque'           => ['required', 'string', 'max:100'],
            'type_vehicule'    => ['nullable', 'string', 'max:100'],
            'acquisition'      => ['required', Rule::in(['achat', 'location'])],
            'date_circulation' => ['nullable', 'date'],
            'poids_a_vide'     => ['nullable', 'numeric', 'min:0'],
            'ptac'             => ['nullable', 'numeric', 'min:0'],
            'num_chassis'      => ['nullable', 'string', 'max:17'],
            'km_initial'       => ['nullable', 'integer', 'min:0'],
            'statut'           => ['required', Rule::in(['actif', 'inactif', 'maintenance'])],
            'chauffeur_id'     => ['nullable', 'exists:chauffeurs,id'],
        ]);

        $vehicule->update($data);
        Alert::success("Véhicule « {$vehicule->matricule} » mis à jour avec succès.");
        return redirect()
            ->route('vehicules.index');
    }

    /**
     * DELETE /vehicules/{vehicule}
     */
    public function destroy(Vehicule $vehicule)
    {
        $matricule = $vehicule->matricule;
        $vehicule->delete();
        Alert::success("Véhicule « {$matricule} » supprimé.");
        return redirect()
            ->route('vehicules.index');
    }
}
