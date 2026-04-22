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
            <div class="stat-mini"><a href="{{ route('villes.index') }}"></a><span>Villes</span></div>
            <div class="stat-mini"><a href="{{ route('article.index') }}"></a><span>Articles</span></div>
            <div class="stat-mini"><a href="#"></a><span>Chauffeurs</span></div>
            <div class="stat-mini"><a href="#"></a><span>Clients</span></div>
        </div>
    </div>

@endsection
