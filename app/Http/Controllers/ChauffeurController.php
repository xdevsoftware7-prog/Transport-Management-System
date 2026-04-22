<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\ChauffeurPermis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class ChauffeurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    const CATEGORIES_PERMIS = ['A', 'A1', 'B', 'C', 'C1', 'D', 'D1', 'E(B)', 'E(C)', 'E(D)'];

    // ──────────────────────────────────────────────────────────────
    //  Génération automatique du code chauffeur  →  DRV-XXXXX
    // ──────────────────────────────────────────────────────────────
    private function generateCodeDrv(): string
    {
        $last = Chauffeur::where('code_drv', 'like', 'DRV-%')
            ->orderByRaw("CAST(SUBSTRING(code_drv, 5) AS UNSIGNED) DESC")
            ->value('code_drv');

        $next = $last ? ((int) substr($last, 4)) + 1 : 1;

        return 'DRV-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    // ──────────────────────────────────────────────────────────────
    //  INDEX — liste + filtres + pagination
    // ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Chauffeur::with('permis');

        // Recherche globale
        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom',       'like', "%{$search}%")
                    ->orWhere('prenom',    'like', "%{$search}%")
                    ->orWhere('code_drv',  'like', "%{$search}%")
                    ->orWhere('cin',       'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Filtre statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre catégorie permis
        if ($request->filled('categorie')) {
            $query->whereHas('permis', fn($q) => $q->where('categorie', $request->categorie));
        }

        // Filtre période de création
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $chauffeurs = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'   => Chauffeur::count(),
            'actifs'  => Chauffeur::where('statut', 'actif')->count(),
            'ce_mois' => Chauffeur::whereMonth('created_at', now()->month)
                ->whereYear('created_at',  now()->year)
                ->count(),
        ];

        $categoriesPermis = self::CATEGORIES_PERMIS;

        return view('chauffeurs.index', compact('chauffeurs', 'stats', 'categoriesPermis'));
    }

    // ──────────────────────────────────────────────────────────────
    //  CREATE
    // ──────────────────────────────────────────────────────────────
    public function create()
    {
        $nextCode         = $this->generateCodeDrv();
        $categoriesPermis = self::CATEGORIES_PERMIS;

        return view('chauffeurs.create', compact('nextCode', 'categoriesPermis'));
    }

    // ──────────────────────────────────────────────────────────────
    //  STORE
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'telephone'       => 'required|string|max:20',
            'cin'             => 'required|string|max:20|unique:chauffeurs,cin',
            'date_exp_cin'    => 'required|date',
            'cin_path'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'date_exp_permis' => 'required|date',
            'salaire_net'     => 'required|numeric|min:0',
            'salaire_brut'    => 'required|numeric|min:0',
            'statut'          => 'required|in:actif,inactif,suspendu',
            'categories'      => 'nullable|array',
            'categories.*'    => 'in:' . implode(',', self::CATEGORIES_PERMIS),
        ]);

        DB::transaction(function () use ($request, $validated) {
            // Code généré côté serveur (non saisi par l'utilisateur)
            $validated['code_drv'] = $this->generateCodeDrv();

            if ($request->hasFile('cin_path')) {
                $validated['cin_path'] = $request->file('cin_path')
                    ->store('chauffeurs/cin', 'public');
            }

            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            $chauffeur = Chauffeur::create($validated);

            foreach ($categories as $cat) {
                ChauffeurPermis::create([
                    'chauffeur_id' => $chauffeur->id,
                    'categorie'    => $cat,
                ]);
            }
        });
        Alert::success('Chauffeur créé avec succès.');
        return redirect()->route('chauffeurs.index');
    }

    // ──────────────────────────────────────────────────────────────
    //  SHOW  → redirige vers edit
    // ──────────────────────────────────────────────────────────────
    public function show(Chauffeur $chauffeur)
    {
        return redirect()->route('chauffeurs.edit', $chauffeur);
    }

    // ──────────────────────────────────────────────────────────────
    //  EDIT
    // ──────────────────────────────────────────────────────────────
    public function edit(Chauffeur $chauffeur)
    {
        $chauffeur->load('permis');
        $categoriesPermis   = self::CATEGORIES_PERMIS;
        $selectedCategories = $chauffeur->permis->pluck('categorie')->toArray();

        return view('chauffeurs.edit', compact('chauffeur', 'categoriesPermis', 'selectedCategories'));
    }

    // ──────────────────────────────────────────────────────────────
    //  UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Chauffeur $chauffeur)
    {
        $validated = $request->validate([
            'nom'             => 'required|string|max:100',
            'prenom'          => 'required|string|max:100',
            'telephone'       => 'required|string|max:20',
            'cin'             => 'required|string|max:20|unique:chauffeurs,cin,' . $chauffeur->id,
            'date_exp_cin'    => 'required|date',
            'cin_path'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'date_exp_permis' => 'required|date',
            'salaire_net'     => 'required|numeric|min:0',
            'salaire_brut'    => 'required|numeric|min:0',
            'statut'          => 'required|in:actif,inactif,suspendu',
            'categories'      => 'nullable|array',
            'categories.*'    => 'in:' . implode(',', self::CATEGORIES_PERMIS),
        ]);

        DB::transaction(function () use ($request, $validated, $chauffeur) {
            if ($request->hasFile('cin_path')) {
                if ($chauffeur->cin_path) {
                    Storage::disk('public')->delete($chauffeur->cin_path);
                }
                $validated['cin_path'] = $request->file('cin_path')
                    ->store('chauffeurs/cin', 'public');
            } else {
                unset($validated['cin_path']);
            }

            $categories = $validated['categories'] ?? [];
            unset($validated['categories']);

            $chauffeur->update($validated);

            // Sync permis : delete + recreate
            $chauffeur->permis()->delete();
            foreach ($categories as $cat) {
                ChauffeurPermis::create([
                    'chauffeur_id' => $chauffeur->id,
                    'categorie'    => $cat,
                ]);
            }
        });
        Alert::success('Chauffeur mis à jour avec succès.');
        return redirect()->route('chauffeurs.index');
    }

    // ──────────────────────────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────────────────────────
    public function destroy(Chauffeur $chauffeur)
    {
        if ($chauffeur->cin_path) {
            Storage::disk('public')->delete($chauffeur->cin_path);
        }

        $chauffeur->delete(); // cascade supprime les permis
        Alert::success('Chauffeur supprimé avec succès.');
        return redirect()->route('chauffeurs.index');
    }
}
