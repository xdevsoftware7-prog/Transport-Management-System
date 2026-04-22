<?php

namespace App\Http\Controllers;

use App\Http\Requests\SemiRemorqueRequest;
use App\Models\SemiRemorque;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class SemiRemorqueController extends Controller
{
    public function index(Request $request)
    {
        $query = SemiRemorque::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('matricule', 'like', "%{$s}%")
                    ->orWhere('marque', 'like', "%{$s}%")
                    ->orWhere('vin', 'like', "%{$s}%")
                    ->orWhere('type_remorque', 'like', "%{$s}%");
            });
        }

        if ($request->filled('marque')) {
            $query->where('marque', $request->marque);
        }

        if ($request->filled('type_remorque')) {
            $query->where('type_remorque', $request->type_remorque);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $remorques = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total'    => SemiRemorque::count(),
            'actives'  => SemiRemorque::where('is_active', true)->count(),
            'inactives' => SemiRemorque::where('is_active', false)->count(),
            'ce_mois'  => SemiRemorque::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
        ];

        $marques       = SemiRemorque::distinct()->orderBy('marque')->pluck('marque')->filter();
        $typesRemorque = SemiRemorque::distinct()->orderBy('type_remorque')->pluck('type_remorque')->filter();

        return view('semi_remorques.index', compact('remorques', 'stats', 'marques', 'typesRemorque'));
    }

    public function create()
    {
        return view('semi_remorques.create');
    }

    public function store(SemiRemorqueRequest $request)
    {
        SemiRemorque::create($request->validated());
        Alert::success('Semi-remorque créée avec succès.');
        return redirect()->route('semi_remorques.index');
    }

    public function edit(SemiRemorque $semiRemorque)
    {
        return view('semi_remorques.edit', compact('semiRemorque'));
    }

    public function update(Request $request, SemiRemorque $semiRemorque)
    {
        $validated = $request->validate([
            'matricule' => [
                'required',
                'string',
                'max:50',
                Rule::unique('semi_remorques', 'matricule')->ignore($semiRemorque->id)
            ],
            'marque' => ['required', 'string', 'max:100'],
            'type_remorque' => ['required', 'string', 'max:100'],
            'ptac' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'vin' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('semi_remorques', 'vin')->ignore($semiRemorque->id)
            ],
            'is_active' => ['boolean'],
        ]);

        // Mise à jour avec les données validées
        $semiRemorque->update($validated);

        Alert::success('Semi-remorque mise à jour avec succès.');
        return redirect()->route('semi_remorques.index');
    }

    public function destroy(SemiRemorque $semiRemorque)
    {
        $semiRemorque->delete();
        Alert::success('Semi-remorque supprimée avec succès.');
        return redirect()->route('semi_remorques.index');
    }
}
