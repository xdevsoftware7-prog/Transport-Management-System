@extends('layouts.app')

{{-- Titre dans l'onglet navigateur --}}
@section('title', 'Dashboard')

{{-- Titre affiché dans la topbar --}}
@section('page-title', 'Dashboard TMS')
@section('page-subtitle', 'Transport Management System — Vue générale')

@section('content')


    {{-- ══════════════════════════════════════════
     Autres lines
══════════════════════════════════════════ --}}
    <div class="section-card">
        <div class="section-header">
            <h2 class="section-title">Liens vers des modules independants</h2>
        </div>

        <div class="maintenance-grid">
            <div class="stat-mini"><a href="{{ route('villes.index') }}"><span>Villes</span></a></div>
            <div class="stat-mini"><a href="{{ route('articles.index') }}"><span>Articles</span></a></div>
            <div class="stat-mini"><a href="{{ route('location_societes.index') }}"><span>Location_Societes</span></a></div>
            <div class="stat-mini"><a href="{{ route('semi_remorques.index') }}"><span>Semi_Remorque</span></a></div>
        </div>

        <div class="maintenance-grid mt-3">
            <div class="stat-mini"><a href="{{ route('trajets.index') }}"><span>Trajets</span></a></div>
            <div class="stat-mini"><a href="{{ route('absences.index') }}"><span>Absences</span></a></div>
            <div class="stat-mini"><a href="{{ route('tarif_clients.index') }}"><span>Tarif Client</span></a></div>
            <div class="stat-mini"><a href="{{ route('ligne_commandes.index') }}"><span>ligne_commandes</span></a></div>
        </div>
        <div class="maintenance-grid mt-3">
            <div class="stat-mini"><a href="{{ route('infractions.index') }}"><span>Infractions</span></a></div>
            <div class="stat-mini"><a href="{{ route('absences.index') }}"><span>Accidents</span></a></div>
            <div class="stat-mini"><a href="{{ route('tarif_clients.index') }}"><span>Docuemnets Vehicules</span></a></div>
            <div class="stat-mini"><a href="{{ route('maintenances.index') }}"><span>Maintenances</span></a></div>
        </div>
    </div>

@endsection
