<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Chauffeur;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AbsenceController extends Controller
{
    public function index(Request $request)
    {
        $query = Absence::with('chauffeur');

        // Filtre chauffeur
        if ($request->filled('chauffeur_id')) {
            $query->where('chauffeur_id', $request->chauffeur_id);
        }

        // Filtre motif (recherche libre)
        if ($request->filled('search')) {
            $query->where('motif', 'like', '%' . $request->search . '%');
        }

        // Filtre période
        if ($request->filled('date_from')) {
            $query->whereDate('date_absence', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_absence', '<=', $request->date_to);
        }

        $absences = $query->orderByDesc('date_absence')->paginate(15)->withQueryString();

        $stats = [
            'total'      => Absence::count(),
            'ce_mois'    => Absence::whereMonth('date_absence', now()->month)
                ->whereYear('date_absence', now()->year)->count(),
            'heures_sup' => Absence::sum('heures_sup'),
        ];

        $chauffeurs = Chauffeur::orderBy('nom')->get();

        return view('absences.index', compact('absences', 'stats', 'chauffeurs'));
    }

    public function create()
    {
        $chauffeurs = Chauffeur::orderBy('nom')->get();
        return view('absences.create', compact('chauffeurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'chauffeur_id'  => 'required|exists:chauffeurs,id',
            'date_absence'  => 'required|date',
            'heure_entree'  => 'required|date_format:H:i',
            'heure_sortie'  => 'required|date_format:H:i|after:heure_entree',
            'heures_sup'    => 'nullable|numeric|min:0|max:99.99',
            'motif'         => 'nullable|string|max:255',
        ]);

        Absence::create($validated);
        Alert::success('Absence enregistrée avec succès.');
        return redirect()->route('absences.index');
    }

    public function edit(Absence $absence)
    {
        $chauffeurs = Chauffeur::orderBy('nom')->get();
        return view('absences.edit', compact('absence', 'chauffeurs'));
    }

    public function update(Request $request, Absence $absence)
    {
        $validated = $request->validate([
            'chauffeur_id'  => 'required|exists:chauffeurs,id',
            'date_absence'  => 'required|date',
            'heure_entree'  => 'required|date_format:H:i',
            'heure_sortie'  => 'required|date_format:H:i|after:heure_entree',
            'heures_sup'    => 'nullable|numeric|min:0|max:99.99',
            'motif'         => 'nullable|string|max:255',
        ]);

        $absence->update($validated);
        Alert::success('Absence mise à jour avec succès.');
        return redirect()->route('absences.index');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        Alert::success('Absence supprimée.');
        return redirect()->route('absences.index');
    }
}
