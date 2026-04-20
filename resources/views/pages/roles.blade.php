@extends('layouts.app')

@section('title', 'Gestion R[oles]')

@section('page-title', 'Gestion Rôles')
@section('page-subtitle', 'Gérer les rôles et les permissions de votre organisation')

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