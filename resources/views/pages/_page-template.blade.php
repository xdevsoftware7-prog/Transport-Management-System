{{--
|--------------------------------------------------------------------------
| PAGE VIERGE — MODÈLE À COPIER
|--------------------------------------------------------------------------
|
| INSTRUCTIONS POUR LE DÉVELOPPEUR :
|   1. Copier ce fichier dans resources/views/pages/
|   2. Renommer le fichier (ex: vehicules.blade.php)
|   3. Mettre à jour les sections @section ci-dessous
|   4. Ajouter votre contenu dans @section('content')
|
|--}}

@extends('layouts.app')

@section('title', 'Ma Nouvelle Page')

@section('page-title', 'Titre de la Page')
@section('page-subtitle', 'Description courte de cette section')

@section('content')

    {{-- ─────────────────────────────────────────
         VOTRE CONTENU ICI
    ───────────────────────────────────────── --}}

    <div class="section-card">
        <h2 class="section-title">Exemple de section</h2>
        <p>Remplacez ce contenu par votre vrai contenu.</p>
    </div>

    {{-- Exemple : Tableau simple --}}
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Liste des éléments</h2>
            <a href="#" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Exemple</td>
                    <td><span class="tag tag-active">Actif</span></td>
                    <td>
                        <a href="#" class="btn-icon" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                        <a href="#" class="btn-icon btn-icon--danger" title="Supprimer"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection