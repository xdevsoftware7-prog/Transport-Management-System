{{--
|--------------------------------------------------------------------------
| PAGE : DÉTAIL UTILISATEUR — OBTRANS TMS
|--------------------------------------------------------------------------
| Vue en lecture seule du profil d'un utilisateur.
|
| VARIABLES ATTENDUES :
|   - $user : App\Models\User  (avec relations : roles, roles.permissions)
|
| ROUTE : GET /users/{user} → UtilisateurController@show
| --}}

@extends('layouts.app')

@section('title', 'Profil — ' . $user->name)
@section('page-title', $user->name)
@section('page-subtitle', 'Profil utilisateur et accès au système')

@section('content')

    <style>
        /* ── BREADCRUMB ── */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .breadcrumb a {
            color: var(--text-muted);
            transition: color var(--transition);
        }

        .breadcrumb a:hover {
            color: var(--color-primary);
        }

        .breadcrumb-sep {
            font-size: 10px;
        }

        /* ══════════════════════════════════════════
           HERO CARD
        ══════════════════════════════════════════ */
        .user-hero {
            background: var(--color-dark);
            border-radius: var(--border-radius);
            padding: 32px 36px;
            display: flex;
            align-items: center;
            gap: 28px;
            position: relative;
            overflow: hidden;
        }

        /* Grille décorative */
        .user-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(224, 32, 32, .05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(224, 32, 32, .05) 1px, transparent 1px);
            background-size: 44px 44px;
            pointer-events: none;
        }

        /* Halo rouge */
        .user-hero::after {
            content: '';
            position: absolute;
            right: -100px;
            top: -100px;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(224, 32, 32, .18) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Avatar large */
        .hero-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(224, 32, 32, .15);
            border: 3px solid rgba(224, 32, 32, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 900;
            color: var(--color-primary);
            font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
            position: relative;
            z-index: 1;
        }

        /* Indicateur en ligne */
        .hero-avatar-status {
            position: absolute;
            bottom: 3px;
            right: 3px;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid var(--color-dark);
        }

        .hero-info {
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .hero-name {
            font-size: 26px;
            font-weight: 900;
            color: #fff;
            letter-spacing: -0.4px;
            margin-bottom: 4px;
        }

        .hero-email {
            font-size: 13px;
            color: #777;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .hero-email i {
            font-size: 11px;
            color: #555;
        }

        .hero-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .hero-role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            background: rgba(224, 32, 32, .12);
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: 5px;
            color: var(--color-primary);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* Stats hero */
        .hero-stats {
            display: flex;
            gap: 28px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
            border-left: 1px solid #222;
            padding-left: 28px;
        }

        .hero-stat {
            text-align: center;
        }

        .hero-stat strong {
            display: block;
            font-size: 26px;
            font-weight: 900;
            font-family: 'JetBrains Mono', monospace;
            color: #fff;
            line-height: 1;
            margin-bottom: 4px;
        }

        .hero-stat span {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Boutons hero */
        .hero-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }

        .btn-edit-hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 20px;
            background: var(--color-primary);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            cursor: pointer;
            transition: opacity var(--transition), transform var(--transition);
        }

        .btn-edit-hero:hover {
            opacity: .88;
            transform: translateY(-1px);
        }

        .btn-back-hero {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 9px 20px;
            background: transparent;
            color: #888;
            border: 1.5px solid #333;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-back-hero:hover {
            border-color: #666;
            color: #ccc;
        }

        /* ══════════════════════════════════════════
           LAYOUT DEUX COLONNES
        ══════════════════════════════════════════ */
        .detail-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }

        .detail-main {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .detail-aside {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* ══════════════════════════════════════════
           SECTION INFOS PERSONNELLES
        ══════════════════════════════════════════ */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .info-cell {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            border-right: 1px solid var(--border);
        }

        .info-cell:nth-child(2n) {
            border-right: none;
        }

        .info-cell:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .info-cell-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-cell-label i {
            font-size: 10px;
        }

        .info-cell-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            word-break: break-all;
        }

        .info-cell-value.muted {
            color: var(--text-muted);
            font-weight: 400;
            font-style: italic;
        }

        .info-cell-value.mono {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        /* ══════════════════════════════════════════
           PERMISSIONS PAR RÔLE
        ══════════════════════════════════════════ */
        .roles-perms-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .role-perm-block {
            border-bottom: 1px solid var(--border);
        }

        .role-perm-block:last-child {
            border-bottom: none;
        }

        .role-perm-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            cursor: pointer;
            background: transparent;
            transition: background var(--transition);
            user-select: none;
        }

        .role-perm-header:hover {
            background: #fafafa;
        }

        .role-perm-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--text-muted);
            flex-shrink: 0;
        }

        .role-perm-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            flex: 1;
        }

        .role-perm-count {
            font-size: 11px;
            color: var(--text-muted);
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2px 9px;
        }

        .role-perm-chevron {
            font-size: 10px;
            color: var(--text-muted);
            transition: transform var(--transition);
            margin-left: 4px;
        }

        .role-perm-header.open .role-perm-chevron {
            transform: rotate(180deg);
        }

        .role-perm-body {
            display: none;
            padding: 0 20px 16px;
            flex-wrap: wrap;
            gap: 6px;
        }

        .role-perm-body.open {
            display: flex;
        }

        .perm-tag-granted {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            font-weight: 600;
            padding: 4px 9px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .perm-tag-granted i {
            font-size: 8px;
        }

        .no-perms {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic;
            padding: 4px 0;
        }

        /* ══════════════════════════════════════════
           TIMELINE ACTIVITÉ (placeholder)
        ══════════════════════════════════════════ */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .timeline-item {
            display: flex;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            position: relative;
        }

        .timeline-item:last-child {
            border-bottom: none;
        }

        .timeline-dot-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;
            flex-shrink: 0;
            padding-top: 2px;
        }

        .timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--border);
            flex-shrink: 0;
        }

        .timeline-dot.red {
            background: var(--color-primary);
        }

        .timeline-dot.green {
            background: #10b981;
        }

        .timeline-dot.blue {
            background: #3b82f6;
        }

        .timeline-dot.gray {
            background: #9ca3af;
        }

        .timeline-line {
            width: 1px;
            flex: 1;
            background: var(--border);
            margin-top: 4px;
        }

        .timeline-item:last-child .timeline-line {
            display: none;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .timeline-sub {
            font-size: 11px;
            color: var(--text-muted);
        }

        .timeline-time {
            font-size: 11px;
            color: var(--text-muted);
            flex-shrink: 0;
            white-space: nowrap;
            padding-top: 2px;
        }

        /* ══════════════════════════════════════════
           ASIDE
        ══════════════════════════════════════════ */
        .aside-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .aside-card-header {
            padding: 12px 18px;
            border-bottom: 1px solid var(--border);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            background: var(--bg-body);
        }

        .aside-card-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .aside-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .aside-row-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .aside-row-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .aside-row-value.primary {
            color: var(--color-primary);
        }

        /* Progress bar aside */
        .aside-progress-bar {
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 4px;
        }

        .aside-progress-fill {
            height: 100%;
            background: var(--color-primary);
            border-radius: 2px;
        }

        /* Avatar dans l'aside */
        .aside-avatar-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 20px 18px;
            border-bottom: 1px solid var(--border);
            background: var(--color-dark);
        }

        .aside-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: rgba(224, 32, 32, .15);
            border: 2px solid rgba(224, 32, 32, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 900;
            color: var(--color-primary);
            font-family: 'JetBrains Mono', monospace;
        }

        .aside-avatar-name {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            text-align: center;
        }

        .aside-avatar-email {
            font-size: 11px;
            color: #666;
            text-align: center;
            word-break: break-all;
        }

        /* Boutons aside */
        .aside-actions {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            border-top: 1px solid var(--border);
        }

        .btn-edit-aside {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px;
            background: var(--color-dark);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: background var(--transition), transform var(--transition);
        }

        .btn-edit-aside:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
        }

        .btn-list-aside {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-list-aside:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* Chip date */
        .date-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: var(--text-muted);
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 3px 8px;
        }

        /* Statut badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 5px;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, .1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, .2);
        }

        .status-badge.pending {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, .2);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Supprimer (danger zone) */
        .danger-zone {
            border: 1px solid rgba(224, 32, 32, .2);
            border-radius: var(--border-radius-sm);
            padding: 14px 16px;
            background: rgba(224, 32, 32, .02);
        }

        .danger-zone-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--color-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .danger-zone-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .btn-danger-full {
            width: 100%;
            padding: 9px;
            background: transparent;
            color: var(--color-primary);
            border: 1.5px solid rgba(224, 32, 32, .3);
            border-radius: var(--border-radius-sm);
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background var(--transition), border-color var(--transition);
        }

        .btn-danger-full:hover {
            background: rgba(224, 32, 32, .06);
            border-color: var(--color-primary);
        }

        #deleteForm {
            display: none;
        }

        /* ── btn-icon warning manquant dans app.css ── */
        .btn-icon--warning:hover {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .detail-layout {
                grid-template-columns: 1fr;
            }

            .detail-aside {
                order: -1;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .detail-aside>* {
                flex: 1;
                min-width: 260px;
            }
        }

        @media (max-width: 720px) {
            .user-hero {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .hero-stats {
                border-left: none;
                border-top: 1px solid #222;
                padding-left: 0;
                padding-top: 16px;
                width: 100%;
                justify-content: space-around;
            }

            .hero-actions {
                flex-direction: row;
                width: 100%;
            }

            .btn-edit-hero,
            .btn-back-hero {
                flex: 1;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .info-cell {
                border-right: none;
            }

            .info-cell:nth-last-child(-n+2) {
                border-bottom: 1px solid var(--border);
            }

            .info-cell:last-child {
                border-bottom: none;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('users.index') }}">users</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>{{ $user->name }}</span>
    </div>

    {{-- ── HERO ── --}}
    @php
        $nameParts = explode(' ', $user->name);
        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
        $rolesCount = $user->roles->count();
        $permissionsCount = $user->roles->flatMap(fn($r) => $r->permissions ?? collect())->unique('id')->count();
        $isVerified = isset($user->email_verified_at) ? (bool) $user->email_verified_at : true;
        $isMe = auth()->id() === $user->id;
    @endphp

    <div class="user-hero">
        <div class="hero-avatar" style="position:relative">
            {{ $initials }}
            <div class="hero-avatar-status"></div>
        </div>

        <div class="hero-info">
            <div class="hero-name">
                {{ $user->name }}
                @if ($isMe)
                    <span style="font-size:12px;font-weight:600;color:#555;margin-left:8px">(Vous)</span>
                @endif
            </div>
            <div class="hero-email">
                <i class="fa-solid fa-envelope"></i>
                {{ $user->email }}
            </div>
            <div class="hero-badges">
                @forelse($user->roles as $role)
                    <span class="hero-role-badge">
                        <i class="fa-solid fa-shield-halved"></i>
                        {{ $role->name }}
                    </span>
                @empty
                    <span style="font-size:12px;color:#555;font-style:italic">Aucun rôle assigné</span>
                @endforelse
            </div>
        </div>

        <div class="hero-stats">
            <div class="hero-stat">
                <strong>{{ $rolesCount }}</strong>
                <span>Rôles</span>
            </div>
            <div class="hero-stat">
                <strong>{{ $permissionsCount }}</strong>
                <span>Permissions</span>
            </div>
            <div class="hero-stat">
                <strong>{{ $user->created_at->diffInDays(now()) }}</strong>
                <span>Jours</span>
            </div>
        </div>

        <div class="hero-actions">
            <a href="{{ route('users.edit', $user) }}" class="btn-edit-hero">
                <i class="fa-solid fa-pen-to-square"></i>
                Modifier
            </a>
            <a href="{{ route('users.index') }}" class="btn-back-hero">
                <i class="fa-solid fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    {{-- ── LAYOUT PRINCIPAL ── --}}
    <div class="detail-layout">

        {{-- ══ COLONNE PRINCIPALE ══ --}}
        <div class="detail-main">

            {{-- Informations personnelles --}}
            <div class="section-card" style="padding:0;overflow:hidden">
                <div class="section-header" style="padding:16px 20px;border-bottom:1px solid var(--border)">
                    <h2 class="section-title">
                        <i class="fa-solid fa-user" style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                        Informations personnelles
                    </h2>
                    <div style="display:flex;gap:6px;flex-wrap:wrap">
                        <span class="date-chip">
                            <i class="fa-solid fa-calendar-plus"></i>
                            Créé {{ $user->created_at->diffForHumans() }}
                        </span>
                        <span class="date-chip">
                            <i class="fa-solid fa-calendar-pen"></i>
                            Modifié {{ $user->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-cell">
                        <div class="info-cell-label">
                            <i class="fa-solid fa-user"></i> Nom complet
                        </div>
                        <div class="info-cell-value">{{ $user->name }}</div>
                    </div>

                    <div class="info-cell">
                        <div class="info-cell-label">
                            <i class="fa-solid fa-envelope"></i> Adresse e-mail
                        </div>
                        <div class="info-cell-value mono">{{ $user->email }}</div>
                    </div>

                    <div class="info-cell">
                        <div class="info-cell-label">
                            <i class="fa-solid fa-circle-check"></i> Statut du compte
                        </div>
                        <div class="info-cell-value">
                            <span class="status-badge {{ $isVerified ? 'active' : 'pending' }}">
                                <span class="status-dot"></span>
                                {{ $isVerified ? 'Actif — email vérifié' : 'En attente de vérification' }}
                            </span>
                        </div>
                    </div>

                    <div class="info-cell">
                        <div class="info-cell-label">
                            <i class="fa-solid fa-id-badge"></i> Identifiant système
                        </div>
                        <div class="info-cell-value mono" style="color:var(--text-muted)">#{{ $user->id }}</div>
                    </div>

                    <div class="info-cell">
                        <div class="info-cell-label">
                            <i class="fa-solid fa-calendar"></i> Date de création
                        </div>
                        <div class="info-cell-value">{{ $user->created_at->format('d/m/Y à H:i') }}</div>
                    </div>

                    <div class="info-cell">
                        <div class="info-cell-label">
                            <i class="fa-solid fa-clock-rotate-left"></i> Dernière modification
                        </div>
                        <div class="info-cell-value">{{ $user->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Rôles et permissions --}}
            <div class="section-card" style="padding:0;overflow:hidden">
                <div class="section-header" style="padding:16px 20px;border-bottom:1px solid var(--border)">
                    <h2 class="section-title">
                        <i class="fa-solid fa-shield-halved"
                            style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                        Rôles et permissions
                    </h2>
                    <span class="tag {{ $rolesCount > 0 ? 'tag-active' : 'tag-expired' }}">
                        {{ $rolesCount }} rôle(s) · {{ $permissionsCount }} permission(s)
                    </span>
                </div>

                @if ($user->roles->count() > 0)
                    <div class="roles-perms-list">
                        @foreach ($user->roles as $role)
                            <div class="role-perm-block">
                                <div class="role-perm-header" data-target="rp-{{ $role->id }}">
                                    <div class="role-perm-icon">
                                        <i class="fa-solid fa-shield-halved" style="color:var(--color-primary)"></i>
                                    </div>
                                    <span class="role-perm-name">{{ $role->name }}</span>
                                    @if ($role->description)
                                        <span style="font-size:12px;color:var(--text-muted);flex:1;padding:0 12px">
                                            {{ $role->description }}
                                        </span>
                                    @endif
                                    <span class="role-perm-count">
                                        {{ ($role->permissions ?? collect())->count() }} permission(s)
                                    </span>
                                    <i class="fa-solid fa-chevron-down role-perm-chevron"></i>
                                </div>
                                <div class="role-perm-body" id="rp-{{ $role->id }}">
                                    @if (($role->permissions ?? collect())->count() > 0)
                                        @foreach ($role->permissions as $perm)
                                            <span class="perm-tag-granted">
                                                <i class="fa-solid fa-check"></i>
                                                {{ str_replace('.', ' › ', $perm->slug ?? $perm->name) }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="no-perms">Aucune permission associée à ce rôle.</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding:36px;text-align:center;color:var(--text-muted)">
                        <i class="fa-solid fa-shield-slash"
                            style="font-size:28px;color:var(--border);display:block;margin-bottom:10px"></i>
                        <p style="font-size:13px">Aucun rôle assigné à cet utilisateur.</p>
                        <a href="{{ route('users.edit', $user) }}"
                            style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;font-size:13px;color:var(--color-primary);font-weight:600">
                            <i class="fa-solid fa-pen"></i> Assigner un rôle
                        </a>
                    </div>
                @endif
            </div>

            {{-- Activité récente (placeholder / à connecter à vos logs) --}}
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fa-solid fa-clock-rotate-left"
                            style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                        Activité récente
                    </h2>
                </div>

                <div class="timeline">
                    {{--
                ─────────────────────────────────────────────────────────
                POUR CONNECTER À VOS LOGS :
                  Remplacer ces items statiques par @foreach ($activities as $activity)
                  et adapter les champs (date, description, type).
                ─────────────────────────────────────────────────────────
                --}}
                    <div class="timeline-item">
                        <div class="timeline-dot-col">
                            <div class="timeline-dot green"></div>
                            <div class="timeline-line"></div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title">Compte créé</div>
                            <div class="timeline-sub">Compte créé par l'administrateur</div>
                        </div>
                        <div class="timeline-time">{{ $user->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if ($user->updated_at->ne($user->created_at))
                        <div class="timeline-item">
                            <div class="timeline-dot-col">
                                <div class="timeline-dot blue"></div>
                                <div class="timeline-line"></div>
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-title">Profil mis à jour</div>
                                <div class="timeline-sub">Informations ou rôles modifiés</div>
                            </div>
                            <div class="timeline-time">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif

                    <div class="timeline-item">
                        <div class="timeline-dot-col">
                            <div class="timeline-dot gray"></div>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-title" style="color:var(--text-muted)">Aucune autre activité enregistrée
                            </div>
                            <div class="timeline-sub">Connectez vos logs pour afficher l'historique complet</div>
                        </div>
                        <div class="timeline-time">—</div>
                    </div>
                </div>
            </div>

        </div>{{-- /detail-main --}}

        {{-- ══ ASIDE ══ --}}
        <div class="detail-aside">

            {{-- Carte profil --}}
            <div class="aside-card">
                <div class="aside-avatar-block">
                    <div class="aside-avatar">{{ $initials }}</div>
                    <div class="aside-avatar-name">{{ $user->name }}</div>
                    <div class="aside-avatar-email">{{ $user->email }}</div>
                </div>
                <div class="aside-card-body">
                    <div class="aside-row">
                        <div class="aside-row-label">Statut</div>
                        <div class="aside-row-value">
                            <span class="status-badge {{ $isVerified ? 'active' : 'pending' }}">
                                <span class="status-dot"></span>
                                {{ $isVerified ? 'Actif' : 'En attente' }}
                            </span>
                        </div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Rôles assignés</div>
                        <div class="aside-row-value primary">{{ $rolesCount }}</div>
                        <div class="aside-progress-bar">
                            <div class="aside-progress-fill" style="width:{{ min(100, $rolesCount * 25) }}%"></div>
                        </div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Permissions totales</div>
                        <div class="aside-row-value">{{ $permissionsCount }}</div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Membre depuis</div>
                        <div class="aside-row-value" style="font-size:12px;font-weight:500">
                            {{ $user->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="aside-row">
                        <div class="aside-row-label">Créé le</div>
                        <div class="aside-row-value" style="font-size:12px;font-weight:500">
                            {{ $user->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                <div class="aside-actions">
                    <a href="{{ route('users.edit', $user) }}" class="btn-edit-aside">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Modifier le profil
                    </a>
                    <a href="{{ route('users.index') }}" class="btn-list-aside">
                        <i class="fa-solid fa-list"></i>
                        Tous les users
                    </a>
                </div>
            </div>

            {{-- Rôles liste compacte --}}
            <div class="aside-card">
                <div class="aside-card-header">Rôles actifs</div>
                <div class="aside-card-body" style="gap:8px">
                    @forelse($user->roles as $role)
                        <a href="{{ route('roles.show', $role) }}"
                            style="display:flex;align-items:center;gap:8px;padding:8px 10px;
                              border:1px solid var(--border);border-radius:var(--border-radius-sm);
                              background:var(--bg-body);transition:border-color var(--transition);text-decoration:none">
                            <span
                                style="width:28px;height:28px;border-radius:6px;
                                     background:var(--color-primary-dim);
                                     display:flex;align-items:center;justify-content:center;
                                     color:var(--color-primary);font-size:12px;flex-shrink:0">
                                <i class="fa-solid fa-shield-halved"></i>
                            </span>
                            <span
                                style="font-size:13px;font-weight:600;color:var(--text-primary)">{{ $role->name }}</span>
                            <i class="fa-solid fa-arrow-right"
                                style="margin-left:auto;font-size:10px;color:var(--text-muted)"></i>
                        </a>
                    @empty
                        <span style="font-size:12px;color:var(--text-muted);font-style:italic">
                            Aucun rôle assigné.
                        </span>
                    @endforelse
                </div>
            </div>

            {{-- Danger zone (uniquement si ce n'est pas l'utilisateur connecté) --}}
            @if (!$isMe)
                <div class="danger-zone">
                    <div class="danger-zone-title">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Zone dangereuse
                    </div>
                    <div class="danger-zone-text">
                        La suppression de ce compte est irréversible.
                        Toutes les données associées seront perdues.
                    </div>
                    <button type="button" class="btn-danger-full" onclick="confirmDelete()">
                        <i class="fa-solid fa-trash"></i>
                        Supprimer ce compte
                    </button>
                </div>
            @endif

        </div>{{-- /detail-aside --}}

    </div>{{-- /detail-layout --}}

    {{-- Formulaire de suppression caché --}}
    @if (!$isMe)
        <form id="deleteForm" method="POST" action="{{ route('users.destroy', $user) }}">
            @csrf
            @method('DELETE')
        </form>
    @endif

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ── ACCORDION RÔLES/PERMISSIONS ────────────────────────
            document.querySelectorAll('.role-perm-header').forEach(function(header) {
                header.addEventListener('click', function() {
                    var targetId = this.dataset.target;
                    var body = document.getElementById(targetId);
                    var isOpen = body.classList.contains('open');

                    // Fermer tous
                    document.querySelectorAll('.role-perm-body').forEach(function(b) {
                        b.classList.remove('open');
                    });
                    document.querySelectorAll('.role-perm-header').forEach(function(h) {
                        h.classList.remove('open');
                    });

                    // Ouvrir celui cliqué
                    if (!isOpen) {
                        body.classList.add('open');
                        this.classList.add('open');
                    }
                });
            });

            // Ouvrir le premier par défaut
            var firstHeader = document.querySelector('.role-perm-header');
            if (firstHeader) firstHeader.click();

        });

        // ── SUPPRESSION ────────────────────────────────────────
        function confirmDelete() {
            var name = '{{ addslashes($user->name) }}';
            if (confirm('Supprimer le compte de ' + name + ' ? Cette action est irréversible.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endpush
