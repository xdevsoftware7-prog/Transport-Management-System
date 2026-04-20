@extends('layouts.app')

@section('title', 'Gestion R[oles]')

@section('page-title', 'Gestion Rôles')
@section('page-subtitle', 'Gérer les rôles et les permissions de votre organisation')

@section('content')

    {{-- ─────────────────────────────────────────
         VOTRE CONTENU ICI
    ───────────────────────────────────────── --}}

    <div class="kpi-grid">

        <div class="kpi-card">
            <div class="kpi-label">Total des rôles</div>
            <div class="kpi-value">{{ $stats['total'] }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Role Utilisées</div>
            <div class="kpi-value">{{ $stats['utilises'] }}</div>
        </div>

        <div class="kpi-card">
            <div class="kpi-label">Rôle Libre</div>
            <div class="kpi-value">{{ $stats['libres'] }}</div>
        </div>

    </div>

    {{-- Exemple : Tableau simple --}}
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Liste des rôles</h2>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Utilisateurs Assegner</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td><span
                                class="tag">{{ $role->description ? Str::limit($role->description, 50, '...') : '-' }}</span>
                        </td>
                        <td><span class="tag tag-active">{{ $role->users_count }}</span></td>
                        <td>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn-icon" title="Modifier"><i
                                    class="fa-solid fa-pen"></i></a>
                            <a href="#" class="btn-icon btn-icon--danger" title="Supprimer"><i
                                    class="fa-solid fa-trash"></i></a>
                            <a href="{{ route('roles.show', $role->id) }}" class="btn-icon btn-icon--warning"><i
                                    class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
