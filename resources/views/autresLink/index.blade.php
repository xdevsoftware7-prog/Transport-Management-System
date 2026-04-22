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
            <div class="stat-mini"><a href="{{ route('clients.index') }}"><span>Clients</span></a></div>
        </div>
    </div>

@endsection
