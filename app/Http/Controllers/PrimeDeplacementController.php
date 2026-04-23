<?php

namespace App\Http\Controllers;

use App\Models\PrimeDeplacement;
use App\Models\Trajet;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PrimeDeplacementController extends Controller
{
    public const TYPES_VEHICULE = [
        'tracteur'       => ['label' => 'Tracteur',       'icon' => 'fa-truck-moving'],
        'semi-remorque'  => ['label' => 'Semi-remorque',  'icon' => 'fa-truck-moving'],
        'camion'         => ['label' => 'Camion',         'icon' => 'fa-truck'],
        'fourgon'        => ['label' => 'Fourgon',        'icon' => 'fa-van-shuttle'],
        'benne'          => ['label' => 'Benne',          'icon' => 'fa-truck-pickup'],
        'citerne'        => ['label' => 'Citerne',        'icon' => 'fa-flask'],
        'frigo'          => ['label' => 'Frigo',          'icon' => 'fa-snowflake'],
        'plateau'        => ['label' => 'Plateau',        'icon' => 'fa-border-all'],
    ];

    public function index(Request $request)
    {
        $query = PrimeDeplacement::with(['trajet.villeDepart', 'trajet.villeDestination']);

        if ($request->filled('trajet_id')) {
            $query->where('trajet_id', $request->trajet_id);
        }

        if ($request->filled('type_vehicule')) {
            $query->where('type_vehicule', $request->type_vehicule);
        }

        if ($request->filled('montant_min')) {
            $query->where('montant_prime', '>=', $request->montant_min);
        }

        if ($request->filled('montant_max')) {
            $query->where('montant_prime', '<=', $request->montant_max);
        }

        $primes = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'total'          => PrimeDeplacement::count(),
            'ce_mois'        => PrimeDeplacement::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'montant_moyen'  => PrimeDeplacement::avg('montant_prime') ?? 0,
        ];

        $trajets = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')->get();

        return view('prime_deplacements.index', compact('primes', 'stats', 'trajets') + [
            'typesVehicule' => self::TYPES_VEHICULE,
        ]);
    }

    public function create()
    {
        $trajets = Trajet::with(['villeDepart', 'villeDestination'])
            ->where('statut', 'actif')
            ->orderBy('id')->get();

        return view('prime_deplacements.create', compact('trajets') + [
            'typesVehicule' => self::TYPES_VEHICULE,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trajet_id'     => 'required|exists:trajets,id',
            'type_vehicule' => ['required', 'in:' . implode(',', array_keys(self::TYPES_VEHICULE))],
            'montant_prime' => 'required|numeric|min:0',
        ]);

        PrimeDeplacement::create($validated);
        Alert::success('Prime de déplacement enregistrée avec succès.');
        return redirect()->route('prime_deplacements.index');
    }

    public function edit(PrimeDeplacement $primeDeplacement)
    {
        $trajets = Trajet::with(['villeDepart', 'villeDestination'])
            ->orderBy('id')->get();

        return view('prime_deplacements.edit', compact('primeDeplacement', 'trajets') + [
            'typesVehicule' => self::TYPES_VEHICULE,
        ]);
    }

    public function update(Request $request, PrimeDeplacement $primeDeplacement)
    {
        $validated = $request->validate([
            'trajet_id'     => 'required|exists:trajets,id',
            'type_vehicule' => ['required', 'in:' . implode(',', array_keys(self::TYPES_VEHICULE))],
            'montant_prime' => 'required|numeric|min:0',
        ]);

        $primeDeplacement->update($validated);
        Alert::success('Prime de déplacement mise à jour avec succès.');
        return redirect()->route('prime_deplacements.index');
    }

    public function destroy(PrimeDeplacement $primeDeplacement)
    {
        $primeDeplacement->delete();
        Alert::success('Prime de déplacement supprimée.');
        return redirect()->route('prime_deplacements.index');
    }
}
