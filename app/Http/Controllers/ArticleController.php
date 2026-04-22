<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // Filtre désignation
        if ($request->filled('search')) {
            $query->where('designation', 'LIKE', '%' . $request->search . '%');
        }

        // Filtre unité (exact, insensible à la casse)
        if ($request->filled('unite')) {
            $query->whereRaw('LOWER(unite) = ?', [strtolower($request->unite)]);
        }

        // Filtre date de création
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $articles = $query
            ->orderBy('designation')
            ->paginate(2)
            ->withQueryString(); // ← conserve tous les ?params dans les liens de pagination

        // Unités distinctes pour le select du filtre
        $unites = Article::select('unite')
            ->distinct()
            ->orderBy('unite')
            ->pluck('unite');

        $stats = [
            'total'    => Article::count(),
            'unites'   => Article::select('unite')->distinct()->count(),
            'ce_mois'  => Article::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return view('articles.index', compact('articles', 'unites', 'stats'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Enregistrement d'un nouvel article.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'designation' => ['required', 'string', 'max:255', 'unique:articles,designation'],
            'unite'       => ['required', 'string', 'max:50'],
        ], [
            'designation.required' => 'La désignation est obligatoire.',
            'designation.unique'   => 'Cet article existe déjà dans le référentiel.',
            'designation.max'      => 'La désignation ne peut pas dépasser 255 caractères.',
            'unite.required'       => 'L\'unité de mesure est obligatoire.',
        ]);

        // Normaliser l'unité en majuscules
        $data['unite'] = strtoupper($data['unite']);

        Article::create($data);
        Alert::success('L\'article « ' . $data['designation'] . ' » a été créé avec succès.');
        return redirect()
            ->route('articles.index');
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Mise à jour d'un article existant.
     */
    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'designation' => ['required', 'string', 'max:255', 'unique:articles,designation,' . $article->id],
            'unite'       => ['required', 'string', 'max:50'],
        ], [
            'designation.required' => 'La désignation est obligatoire.',
            'designation.unique'   => 'Cet article existe déjà dans le référentiel.',
            'designation.max'      => 'La désignation ne peut pas dépasser 255 caractères.',
            'unite.required'       => 'L\'unité de mesure est obligatoire.',
        ]);

        $data['unite'] = strtoupper($data['unite']);

        $article->update($data);
        Alert::success('L\'article « ' . $article->designation . ' » a été mis à jour.');
        return redirect()
            ->route('articles.index');
    }

    /**
     * Suppression d'un article.
     */
    public function destroy(Article $article)
    {
        $designation = $article->designation;
        $article->delete();
        Alert::success('L\'article « ' . $designation . ' » a été supprimé.');
        return redirect()
            ->route('articles.index');
    }
}
