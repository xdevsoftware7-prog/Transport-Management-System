{{--
|--------------------------------------------------------------------------
| PAGE : DASHBOARD
|--------------------------------------------------------------------------
| Exemple de page utilisant le layout principal.
| Chaque page doit étendre 'layouts.app' et définir ses sections.
|--}}

@extends('layouts.app')

{{-- Titre dans l'onglet navigateur --}}
@section('title', 'Dashboard')

{{-- Titre affiché dans la topbar --}}
@section('page-title', 'Dashboard TMS')
@section('page-subtitle', 'Transport Management System — Vue générale')

@section('content')

{{-- ══════════════════════════════════════════
     ALERTES SYSTÈME
══════════════════════════════════════════ --}}
<div class="alerts-section">
    <div class="alerts-header">
        <span class="alerts-title">
            <span class="alert-dot"></span>
            Alertes Système
        </span>
        <span class="badge-count">178</span>
        <span class="alerts-counter">4 / 178</span>
    </div>
    <div class="alert-item">
        <span class="tag tag-expired">Expiré</span>
        <span class="alert-text">Assurance — 20702-81 · depuis 108 jours</span>
    </div>
</div>

{{-- ══════════════════════════════════════════
     CARTES KPI PRINCIPALES
══════════════════════════════════════════ --}}
<div class="kpi-grid">

    {{-- KPI : Flotte Véhicules --}}
    <div class="kpi-card">
        <div class="kpi-value">102</div>
        <div class="kpi-label">Flotte Véhicules</div>
        <ul class="kpi-details">
            <li><span class="dot dot-green"></span> Disponibles <strong>79</strong></li>
            <li><span class="dot dot-blue"></span> En location <strong>2</strong></li>
            <li><span class="dot dot-red"></span> En panne <strong>21</strong></li>
        </ul>
        <div class="kpi-progress-bar">
            <div class="kpi-progress-fill" style="width: 15%"></div>
        </div>
        <div class="kpi-progress-label">Disponibilité 15%</div>
    </div>

    {{-- KPI : Chauffeurs --}}
    <div class="kpi-card">
        <div class="kpi-value">106</div>
        <div class="kpi-label">Chauffeurs</div>
        <ul class="kpi-details">
            <li><span class="dot dot-green"></span> Affectés <strong>95</strong></li>
            <li><span class="dot dot-orange"></span> Non affectés <strong>11</strong></li>
            <li><span class="dot dot-red"></span> Absents <strong>0</strong></li>
        </ul>
        <div class="kpi-progress-bar">
            <div class="kpi-progress-fill" style="width: 90%"></div>
        </div>
        <div class="kpi-progress-label">Taux d'affectation 90%</div>
    </div>

    {{-- KPI : Commandes Aujourd'hui --}}
    <div class="kpi-card">
        <div class="kpi-value">0</div>
        <div class="kpi-label">Commandes Aujourd'hui</div>
        <ul class="kpi-details">
            <li><span class="dot dot-orange"></span> En attente <strong>0</strong></li>
            <li><span class="dot dot-blue"></span> Planifiées <strong>0</strong></li>
            <li><span class="dot dot-green"></span> En cours <strong>0</strong></li>
            <li><span class="dot dot-gray"></span> Exécutées <strong>0</strong></li>
        </ul>
        <div class="kpi-progress-bar">
            <div class="kpi-progress-fill" style="width: 0%"></div>
        </div>
        <div class="kpi-progress-label">Taux d'exécution 0%</div>
    </div>

    {{-- KPI : Maintenances du Mois --}}
    <div class="kpi-card">
        <div class="kpi-value">0</div>
        <div class="kpi-label">Maintenances du Mois</div>
        <ul class="kpi-details">
            <li><span class="dot dot-green"></span> Terminées <strong>0</strong></li>
            <li><span class="dot dot-blue"></span> En cours <strong>0</strong></li>
            <li><span class="dot dot-orange"></span> En attente <strong>0</strong></li>
        </ul>
        <div class="kpi-progress-bar">
            <div class="kpi-progress-fill" style="width: 0%"></div>
        </div>
        <div class="kpi-progress-label">Terminées 0%</div>
    </div>

</div>

{{-- ══════════════════════════════════════════
     RÉSUMÉ FINANCIER
══════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-header">
        <h2 class="section-title">Résumé Financier</h2>
        <div class="section-actions">
            <label class="filter-label">Filtrer :</label>
            <select class="filter-select">
                <option>Par jour</option>
                <option>Par semaine</option>
                <option>Par mois</option>
            </select>
            <input type="date" class="filter-date" value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <div class="finance-grid">
        <div class="finance-card">
            <div class="finance-icon"><i class="fa-solid fa-arrow-trend-up"></i></div>
            <div class="finance-label">Revenu Total</div>
            <div class="finance-value">0,00 <span class="currency">MAD</span></div>
            <a href="#" class="finance-link">Voir détails</a>
        </div>
        <div class="finance-card">
            <div class="finance-icon"><i class="fa-solid fa-arrow-trend-down"></i></div>
            <div class="finance-label">Charges Totales</div>
            <div class="finance-value">57 541,35 <span class="currency">MAD</span></div>
            <a href="#" class="finance-link">Voir détails</a>
        </div>
        <div class="finance-card finance-card--danger">
            <div class="finance-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="finance-label">Profit Net</div>
            <div class="finance-value finance-value--danger">-57 541,35 <span class="currency">MAD</span></div>
            <a href="#" class="finance-link">Voir détails</a>
        </div>
        <div class="finance-card">
            <div class="finance-icon"><i class="fa-solid fa-gas-pump"></i></div>
            <div class="finance-label">Gasoil Consommé</div>
            <div class="finance-value">2 631 <span class="currency">MAD</span></div>
            <p class="finance-sub">719 km · 299 L</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     STATISTIQUES DE MAINTENANCE
══════════════════════════════════════════ --}}
<div class="section-card">
    <div class="section-header">
        <h2 class="section-title">Statistiques de Maintenance</h2>
        <a href="#" class="section-link">Voir tout</a>
    </div>

    <div class="maintenance-grid">
        <div class="stat-mini"><strong>0</strong><span>Total maintenances</span></div>
        <div class="stat-mini"><strong>0</strong><span>Terminées</span></div>
        <div class="stat-mini"><strong>0</strong><span>En cours</span></div>
        <div class="stat-mini"><strong>0</strong><span>En attente</span></div>
        <div class="stat-mini"><strong>0</strong><span>Coût total (MAD)</span></div>
        <div class="stat-mini"><strong>0</strong><span>Coût moyen (MAD)</span></div>
        <div class="stat-mini"><strong>0</strong><span>Ce mois</span></div>
        <div class="stat-mini stat-mini--action">
            <a href="#" class="btn btn-dark">
                <i class="fa-solid fa-wrench"></i>
                Accéder aux maintenances
            </a>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     ACTIONS RAPIDES
══════════════════════════════════════════ --}}
<div class="section-card">
    <h2 class="section-title">Actions Rapides</h2>
    <div class="quick-actions">
        <a href="#" class="quick-card">
            <div class="quick-icon"><i class="fa-solid fa-plus-circle"></i></div>
            <div class="quick-title">Nouvelle Commande</div>
            <div class="quick-sub">Créer une nouvelle mission de transport</div>
        </a>
        <a href="#" class="quick-card">
            <div class="quick-icon"><i class="fa-solid fa-truck"></i></div>
            <div class="quick-title">Gérer la Flotte</div>
            <div class="quick-sub">Administration des véhicules et affectations</div>
        </a>
        <a href="#" class="quick-card">
            <div class="quick-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="quick-title">Factures</div>
            <div class="quick-sub">Facturation et suivi des paiements</div>
        </a>
    </div>
</div>

@endsection