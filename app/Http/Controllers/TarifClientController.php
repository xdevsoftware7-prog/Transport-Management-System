<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TarifClient;
use App\Models\Trajet;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TarifClientController extends Controller
{
    public function index(Request $request)
    {
        $query = TarifClient::with(['client', 'trajet.villeDepart', 'trajet.villeDestination']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('trajet_id')) {
            $query->where('trajet_id', $request->trajet_id);
        }

        if ($request->filled('type_vehicule')) {
            $query->where('type_vehicule', 'like', '%' . $request->type_vehicule . '%');
        }

        if ($request->filled('prix_min')) {
            $query->where('prix_vente', '>=', $request->prix_min);
        }

        if ($request->filled('prix_max')) {
            $query->where('prix_vente', '<=', $request->prix_max);
        }

        $tarifs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total'      => TarifClient::count(),
            'ce_mois'    => TarifClient::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'prix_moyen' => TarifClient::avg('prix_vente') ?? 0,
        ];

        $clients = Client::orderBy('nom')->get();
        $trajets  = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')->get();

        $typesVehicule = TarifClient::select('type_vehicule')
            ->distinct()
            ->orderBy('type_vehicule')
            ->pluck('type_vehicule');

        return view('tarif_clients.index', compact('tarifs', 'stats', 'clients', 'trajets', 'typesVehicule'));
    }

    public function create()
    {
        $clients = Client::where('is_active', true)->orderBy('nom')->get();
        $trajets  = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')->get();
        return view('tarif_clients.create', compact('clients', 'trajets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'trajet_id'     => 'required|exists:trajets,id',
            'type_vehicule' => 'required|string|max:100',
            'tonnage'       => 'required|numeric|min:0',
            'prix_vente'    => 'required|numeric|min:0',
        ]);

        TarifClient::create($validated);
        Alert::success('Tarif client enregistré avec succès.');
        return redirect()->route('tarif_clients.index');
    }

    public function edit(TarifClient $tarifClient)
    {
        $clients = Client::orderBy('nom')->get();
        $trajets  = Trajet::with(['villeDepart', 'villeDestination'])
            ->orderBy('id')->get();
        return view('tarif_clients.edit', compact('tarifClient', 'clients', 'trajets'));
    }

    public function update(Request $request, TarifClient $tarifClient)
    {
        $validated = $request->validate([
            'client_id'     => 'required|exists:clients,id',
            'trajet_id'     => 'required|exists:trajets,id',
            'type_vehicule' => 'required|string|max:100',
            'tonnage'       => 'required|numeric|min:0',
            'prix_vente'    => 'required|numeric|min:0',
        ]);

        $tarifClient->update($validated);
        Alert::success('Tarif client mis à jour avec succès.');
        return redirect()->route('tarif_clients.index');
    }

    public function destroy(TarifClient $tarifClient)
    {
        $tarifClient->delete();
        Alert::success('Tarif client supprimé.');
        return redirect()->route('tarif_clients.index');
    }
}
