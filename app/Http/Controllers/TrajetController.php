<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrajetRequest;
use App\Models\Trajet;
use App\Models\Ville;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TrajetController extends Controller
{
    public function index(Request $request)
    {
        $query = Trajet::with(['villeDepart', 'villeDestination']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('villeDepart', fn($q) => $q->where('nom', 'like', "%{$s}%"))
                ->orWhereHas('villeDestination', fn($q) => $q->where('nom', 'like', "%{$s}%"))
                ->orWhere('adresse_depart', 'like', "%{$s}%")
                ->orWhere('adresse_destination', 'like', "%{$s}%");
        }

        if ($request->filled('ville_depart_id')) {
            $query->where('ville_depart_id', $request->ville_depart_id);
        }

        if ($request->filled('ville_destination_id')) {
            $query->where('ville_destination_id', $request->ville_destination_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('distance_min')) {
            $query->where('distance_km', '>=', $request->distance_min);
        }

        if ($request->filled('distance_max')) {
            $query->where('distance_km', '<=', $request->distance_max);
        }

        $trajets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'    => Trajet::count(),
            'actifs'   => Trajet::where('statut', 'actif')->count(),
            'inactifs' => Trajet::where('statut', 'inactif')->count(),
            'dist_moy' => round(Trajet::avg('distance_km') ?? 0),
        ];

        $villes = Ville::orderBy('nom')->get();

        return view('trajets.index', compact('trajets', 'stats', 'villes'));
    }

    public function create()
    {
        $villes = Ville::orderBy('nom')->get();
        return view('trajets.create', compact('villes'));
    }

    public function store(TrajetRequest $request)
    {
        Trajet::create($request->validated());
        Alert::success('Trajet créé avec succès.');
        return redirect()->route('trajets.index');
    }

    public function edit(Trajet $trajet)
    {
        $villes = Ville::orderBy('nom')->get();
        $trajet->load(['villeDepart', 'villeDestination']);
        return view('trajets.edit', compact('trajet', 'villes'));
    }

    public function update(TrajetRequest $request, Trajet $trajet)
    {
        $trajet->update($request->validated());
        $villeDepart = $trajet->villeDepart();
        $villeDestination = $trajet->villeDestination();
        Alert::success('Trajet mis à jour avec succès.');
        return redirect()->route('trajets.index');
    }

    public function destroy(Trajet $trajet)
    {
        $trajet->delete();
        Alert::success('Trajet supprimé avec succès.');
        return redirect()->route('trajets.index');
    }
}
