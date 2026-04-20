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
                            <a href="#" class="btn-icon btn-icon--danger" title="Supprimer"
                                onclick="handleDeleteRole({{ $role->id }}, '{{ $role->name }}')"><i
                                    class="fa-solid fa-trash"></i></a>
                            <form id="delete-form-{{ $role->id }}" action="{{ route('roles.destroy', $role) }}"
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <a href="{{ route('roles.show', $role->id) }}" class="btn-icon btn-icon--warning"><i
                                    class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- ─────────────────────────────────────────
             PAGINATION
        ───────────────────────────────────────── --}}
        @if ($roles->hasPages())
            <div class="pagination-container">
                <div class="pagination-info">
                    Affichage de <strong>{{ $roles->firstItem() }}</strong> à
                    <strong>{{ $roles->lastItem() }}</strong> sur
                    <strong>{{ $roles->total() }}</strong> rôles
                </div>
                <div class="pagination-links">
                    {{-- Previous Page Link --}}
                    @if ($roles->onFirstPage())
                        <span class="pagination-link disabled" aria-disabled="true">
                            <i class="fa-solid fa-chevron-left"></i> Précédent
                        </span>
                    @else
                        <a href="{{ $roles->previousPageUrl() }}" class="pagination-link">
                            <i class="fa-solid fa-chevron-left"></i> Précédent
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    <div class="pagination-numbers">
                        @foreach ($roles->getUrlRange(1, $roles->lastPage()) as $page => $url)
                            @if ($page == $roles->currentPage())
                                <span class="pagination-number active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pagination-number">{{ $page }}</a>
                            @endif
                        @endforeach
                    </div>

                    {{-- Next Page Link --}}
                    @if ($roles->hasMorePages())
                        <a href="{{ $roles->nextPageUrl() }}" class="pagination-link">
                            Suivant <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <span class="pagination-link disabled" aria-disabled="true">
                            Suivant <i class="fa-solid fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection
@push('scripts')
    <script>
        function handleDeleteRole(id, name) {
            Swal.fire({
                title: 'Supprimer le rôle ?',
                text: `Êtes-vous sûr de vouloir supprimer le rôle "${name}" ? Cette action est irréversible.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e02020', // Ton rouge OBTRANS
                cancelButtonColor: '#1a1a1a', // Ton gris foncé/noir
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                background: '#111', // Fond sombre pour matcher ton thème
                color: '#fff', // Texte blanc
                customClass: {
                    popup: 'swal-custom-radius'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // On soumet le formulaire correspondant
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endpush
