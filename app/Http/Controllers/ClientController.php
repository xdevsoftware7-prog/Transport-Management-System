<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('telephone', 'like', "%{$s}%")
                    ->orWhere('ice', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut_juridique')) {
            $query->where('statut_juridique', $request->statut_juridique);
        }

        if ($request->filled('modalite_paiement')) {
            $query->where('modalite_paiement', $request->modalite_paiement);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $clients = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'      => Client::count(),
            'actifs'     => Client::where('is_active', true)->count(),
            'entreprises' => Client::where('type', 'entreprise')->count(),
            'ce_mois'    => Client::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $statutsJuridiques = Client::distinct()->pluck('statut_juridique')->filter()->sort()->values();

        return view('clients.index', compact('clients', 'stats', 'statutsJuridiques'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(ClientRequest $request)
    {
        Client::create($request->validated());
        Alert::success('Client créé avec succès.');
        return redirect()->route('clients.index');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(ClientRequest $request, Client $client)
    {
        $client->update($request->validated());
        Alert::success('Client mis à jour avec succès.');
        return redirect()->route('clients.index');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        Alert::success('Client supprimé avec succès.');
        return redirect()->route('clients.index');
    }
}
