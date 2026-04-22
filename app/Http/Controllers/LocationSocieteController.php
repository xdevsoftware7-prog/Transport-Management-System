<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationSocieteRequest;
use App\Models\LocationSociete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class LocationSocieteController extends Controller
{
    public function index(Request $request)
    {
        $query = LocationSociete::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom_societe', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('telephone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_debut_contrat', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_fin_contrat', '<=', $request->date_to);
        }

        $societes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'      => LocationSociete::count(),
            'actifs'     => LocationSociete::where('statut', 'actif')->count(),
            'en_attente' => LocationSociete::where('statut', 'en_attente')->count(),
            'termines'   => LocationSociete::where('statut', 'terminé')->count(),
        ];

        return view('location_societes.index', compact('societes', 'stats'));
    }

    public function create()
    {
        return view('location_societes.create');
    }

    public function store(LocationSocieteRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('contrat_pdf')) {
            $data['contrat_pdf_path'] = $request->file('contrat_pdf')->store('contrats', 'public');
        }

        LocationSociete::create($data);
        Alert::success('Société de location créée avec succès.');
        return redirect()->route('location_societes.index');
    }

    public function edit(LocationSociete $locationSociete)
    {
        return view('location_societes.edit', compact('locationSociete'));
    }

    public function update(LocationSocieteRequest $request, LocationSociete $locationSociete)
    {
        $data = $request->validated();

        if ($request->hasFile('contrat_pdf')) {
            // Supprimer l'ancien fichier
            if ($locationSociete->contrat_pdf_path) {
                Storage::disk('public')->delete($locationSociete->contrat_pdf_path);
            }
            $data['contrat_pdf_path'] = $request->file('contrat_pdf')->store('contrats', 'public');
        }

        $locationSociete->update($data);
        Alert::success('Société de location mise à jour avec succès.');
        return redirect()->route('location_societes.index');
    }

    public function destroy(LocationSociete $locationSociete)
    {
        if ($locationSociete->contrat_pdf_path) {
            Storage::disk('public')->delete($locationSociete->contrat_pdf_path);
        }

        $locationSociete->delete();
        Alert::success('Société supprimée avec succès.');
        return redirect()->route('location_societes.index');
    }
}
