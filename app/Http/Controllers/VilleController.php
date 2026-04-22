<?php

namespace App\Http\Controllers;

use App\Models\Ville;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class VilleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $villes = Ville::query()
            ->when($search, fn($q) => $q->where('nom', 'LIKE', '%' . $search . '%'))
            ->orderBy('nom')
            ->paginate(10)                    // ← changer 15 pour ajuster le nombre par page
            ->withQueryString();              // ← conserve ?search= dans les liens de pagination

        $stats = [
            'total'    => Ville::count(),
            'ce_mois'  => Ville::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('villes.index', compact('villes', 'stats'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('villes.create');
    }

    /**
     * Enregistrement d'une nouvelle ville.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:100', 'unique:villes,nom'],
        ], [
            'nom.required' => 'Le nom de la ville est obligatoire.',
            'nom.unique'   => 'Cette ville existe déjà dans le référentiel.',
            'nom.max'      => 'Le nom ne peut pas dépasser 100 caractères.',
        ]);

        Ville::create(['nom' => $request->nom]);
        Alert::success('La ville « ' . $request->nom . ' » a été ajoutée avec succès.');
        return redirect()->route('villes.index');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Ville $ville)
    {
        return view('villes.edit', compact('ville'));
    }

    /**
     * Mise à jour d'une ville existante.
     */
    public function update(Request $request, Ville $ville)
    {
        $request->validate([
            // Exclure la ville courante de la vérification d'unicité
            'nom' => ['required', 'string', 'max:100', 'unique:villes,nom,' . $ville->id],
        ], [
            'nom.required' => 'Le nom de la ville est obligatoire.',
            'nom.unique'   => 'Cette ville existe déjà dans le référentiel.',
            'nom.max'      => 'Le nom ne peut pas dépasser 100 caractères.',
        ]);

        $old = $ville->nom;
        $ville->update(['nom' => $request->nom]);
        Alert::success('La ville « ' . $old . ' » a été renommée en « ' . $request->nom . ' ».');
        return redirect()->route('villes.index');
    }

    /**
     * Suppression d'une ville.
     */
    public function destroy(Ville $ville)
    {
        $nom = $ville->nom;
        $ville->delete();
        Alert::success('La ville « ' . $nom . ' » a été supprimée.');
        return redirect()->route('villes.index');
    }
}
