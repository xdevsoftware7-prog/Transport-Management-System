@extends('layouts.app')

@section('title', 'Modifier — ' . $user->name)
@section('page-title', 'Modifier l\'users')
@section('page-subtitle', 'Mettre à jour les informations et les accès')

@section('content')

    <style>
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

        /* Bandeau édition */
        .edit-banner {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--color-dark);
            border-radius: var(--border-radius);
            padding: 14px 20px;
            margin-bottom: 4px;
        }

        .edit-banner-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(224, 32, 32, .15);
            border: 2px solid rgba(224, 32, 32, .25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 800;
            color: var(--color-primary);
            font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
        }

        .edit-banner-text strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .edit-banner-text span {
            font-size: 12px;
            color: #666;
        }

        .edit-banner-meta {
            margin-left: auto;
            text-align: right;
        }

        .edit-banner-meta .meta-label {
            font-size: 10px;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .edit-banner-meta .meta-value {
            font-size: 13px;
            color: #aaa;
            font-weight: 600;
        }

        /* Layout */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 20px;
            align-items: start;
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Champs */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-primary);
        }

        .field .field-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: -2px;
        }

        .field input[type="text"],
        .field input[type="email"],
        .field input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: #fafafa;
            outline: none;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field input:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field input::placeholder {
            color: var(--text-muted);
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Indicateur changements */
        .change-indicator {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #d97706;
            background: rgba(245, 158, 11, .08);
            border: 1px solid rgba(245, 158, 11, .2);
            border-radius: var(--border-radius-sm);
            padding: 8px 12px;
        }

        .change-indicator.visible {
            display: flex;
        }

        /* Mot de passe */
        .pw-wrap {
            position: relative;
        }

        .pw-wrap input {
            padding-right: 42px;
        }

        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 13px;
            padding: 2px;
            transition: color var(--transition);
        }

        .pw-toggle:hover {
            color: var(--color-primary);
        }

        /* Box "changer mot de passe" */
        .pw-optional-box {
            border: 1.5px dashed var(--border);
            border-radius: var(--border-radius-sm);
            padding: 14px 16px;
            background: var(--bg-body);
        }

        .pw-optional-toggle {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            transition: color var(--transition);
        }

        .pw-optional-toggle:hover {
            color: var(--color-primary);
        }

        .pw-optional-toggle input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--color-primary);
            cursor: pointer;
        }

        .pw-fields {
            display: none;
            margin-top: 16px;
        }

        .pw-fields.visible {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Force mot de passe */
        .pw-strength {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .pw-strength-bar {
            display: flex;
            gap: 3px;
            height: 3px;
        }

        .pw-bar-seg {
            flex: 1;
            border-radius: 2px;
            background: var(--border);
            transition: background .3s;
        }

        .pw-bar-seg.filled-weak {
            background: var(--color-primary);
        }

        .pw-bar-seg.filled-medium {
            background: #f59e0b;
        }

        .pw-bar-seg.filled-strong {
            background: #10b981;
        }

        .pw-strength-label {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Correspondance */
        .pw-match-msg {
            font-size: 11px;
            display: none;
        }

        /* Rôles */
        .roles-required-note {
            font-size: 11px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .roles-select-list {
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            overflow: hidden;
            background: #fafafa;
        }

        .role-select-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background var(--transition);
        }

        .role-select-item:last-child {
            border-bottom: none;
        }

        .role-select-item:hover {
            background: #fff;
        }

        .role-select-item input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--color-primary);
            cursor: pointer;
            flex-shrink: 0;
        }

        .role-select-label {
            flex: 1;
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .role-select-sub {
            font-size: 11px;
            color: var(--text-muted);
        }

        .roles-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
        }

        /* Sidebar */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .preview-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .preview-card-header {
            background: var(--color-dark);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-avatar-lg {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(224, 32, 32, .2);
            border: 2px solid rgba(224, 32, 32, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            color: var(--color-primary);
            font-family: 'JetBrains Mono', monospace;
            flex-shrink: 0;
        }

        .preview-card-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .preview-card-sub {
            font-size: 11px;
            color: #666;
            margin-top: 1px;
        }

        .preview-card-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .prev-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .prev-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .prev-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-roles-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            min-height: 22px;
        }

        .prev-role-tag {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .action-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 18px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            box-shadow: var(--shadow-sm);
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--color-dark);
            color: #fff;
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 14px;
            font-weight: 700;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background var(--transition), transform var(--transition);
        }

        .btn-submit:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
        }

        .btn-cancel {
            width: 100%;
            padding: 11px;
            background: transparent;
            color: var(--text-secondary);
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: border-color var(--transition), color var(--transition);
        }

        .btn-cancel:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .btn-delete {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: var(--color-primary);
            border: 1.5px solid rgba(224, 32, 32, .25);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background var(--transition), border-color var(--transition);
        }

        .btn-delete:hover {
            background: rgba(224, 32, 32, .06);
            border-color: var(--color-primary);
        }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 2px 0;
        }

        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .info-box strong {
            color: var(--text-primary);
        }

        /* Stat users */
        .user-stat-row {
            display: flex;
            gap: 12px;
        }

        .user-stat {
            flex: 1;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: var(--border-radius-sm);
            padding: 10px 12px;
            text-align: center;
        }

        .user-stat strong {
            display: block;
            font-size: 18px;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
        }

        .user-stat span {
            font-size: 10px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        #deleteForm {
            display: none;
        }

        @media (max-width: 960px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-col {
                order: -1;
            }

            .field-row {
                grid-template-columns: 1fr;
            }

            .pw-fields.visible {
                grid-template-columns: 1fr;
            }
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('users.index') }}">users</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Modifier · {{ $user->name }}</span>
    </div>

    {{-- Bandeau édition --}}
    @php
        $initials = strtoupper(
            substr($user->name, 0, 1) .
                (strpos($user->name, ' ') !== false ? substr($user->name, strpos($user->name, ' ') + 1, 1) : ''),
        );
        $userRoleIds = $userRoleIds ?? $user->roles->pluck('id')->toArray();
    @endphp
    <div class="edit-banner">
        <div class="edit-banner-avatar">{{ $initials }}</div>
        <div class="edit-banner-text">
            <strong>Mode édition — {{ $user->name }}</strong>
            <span>Compte créé le {{ $user->created_at->format('d/m/Y') }} · {{ $user->roles->count() }} rôle(s)
                assigné(s)</span>
        </div>
        <div class="edit-banner-meta">
            <div class="meta-label">Membre depuis</div>
            <div class="meta-value">{{ $user->created_at->diffForHumans() }}</div>
        </div>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}" id="userForm">
        @csrf
        @method('PUT')

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- ── Informations personnelles ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-user"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Informations personnelles
                        </h2>
                        <div class="change-indicator" id="changeIndicator">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            Modifications non sauvegardées
                        </div>
                    </div>

                    {{-- Nom --}}
                    <div class="field" style="margin-bottom:16px">
                        <label for="name">Nom complet <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Prénom et nom de l'users</p>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            maxlength="255" autocomplete="name">
                        @error('name')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="field">
                        <label for="email">Adresse e-mail <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Doit être unique dans le système</p>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            required maxlength="255" autocomplete="email">
                        @error('email')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ── Mot de passe (optionnel) ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-lock"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Mot de passe
                        </h2>
                    </div>

                    <div class="pw-optional-box">
                        <label class="pw-optional-toggle">
                            <input type="checkbox" id="changePwToggle">
                            <i class="fa-solid fa-rotate"></i>
                            Changer le mot de passe
                        </label>

                        <div class="pw-fields" id="pwFields">
                            {{-- Nouveau mot de passe --}}
                            <div class="field">
                                <label for="password">Nouveau mot de passe</label>
                                <div class="pw-wrap">
                                    <input type="password" id="password" name="password" placeholder="••••••••"
                                        autocomplete="new-password">
                                    <button type="button" class="pw-toggle" onclick="togglePw('password', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <div class="pw-strength" id="pwStrength" style="display:none">
                                    <div class="pw-strength-bar">
                                        <div class="pw-bar-seg" id="seg1"></div>
                                        <div class="pw-bar-seg" id="seg2"></div>
                                        <div class="pw-bar-seg" id="seg3"></div>
                                        <div class="pw-bar-seg" id="seg4"></div>
                                    </div>
                                    <span class="pw-strength-label" id="pwStrengthLabel"></span>
                                </div>
                                @error('password')
                                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Confirmation --}}
                            <div class="field">
                                <label for="password_confirmation">Confirmer</label>
                                <div class="pw-wrap">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        placeholder="••••••••" autocomplete="new-password">
                                    <button type="button" class="pw-toggle"
                                        onclick="togglePw('password_confirmation', this)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                                <span class="pw-match-msg" id="pwMatch"></span>
                            </div>
                        </div>
                    </div>

                    @if (old('password'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.getElementById('changePwToggle').checked = true;
                                document.getElementById('pwFields').classList.add('visible');
                            });
                        </script>
                    @endif
                </div>

                {{-- ── Rôles ── --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-shield-halved"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Rôles <span style="color:var(--color-primary)">*</span>
                        </h2>
                    </div>

                    <p class="roles-required-note">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Au moins un rôle est obligatoire.
                    </p>

                    @if (isset($roles) && $roles->count() > 0)
                        <div class="roles-select-list">
                            @foreach ($roles as $role)
                                <label class="role-select-item">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', $userRoleIds)) ? 'checked' : '' }}
                                        class="role-checkbox">
                                    <div style="flex:1">
                                        <div class="role-select-label">{{ $role->name }}</div>
                                        @if ($role->description)
                                            <div class="role-select-sub">{{ $role->description }}</div>
                                        @endif
                                    </div>
                                    <span style="font-size:11px;color:var(--text-muted)">
                                        {{ $role->permissions_count ?? ($role->permissions->count() ?? 0) }} permission(s)
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('roles')
                        <div class="roles-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-card-header">
                        <div class="preview-avatar-lg" id="prevAvatar">{{ $initials }}</div>
                        <div>
                            <div class="preview-card-label" id="prevName">{{ $user->name }}</div>
                            <div class="preview-card-sub" id="prevEmail">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="preview-card-body">
                        <div class="user-stat-row">
                            <div class="user-stat">
                                <strong>{{ $user->roles->count() }}</strong>
                                <span>Rôles</span>
                            </div>
                            <div class="user-stat">
                                <strong>{{ $user->created_at->diffInDays(now()) }}</strong>
                                <span>Jours</span>
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Nom</div>
                            <div class="prev-value primary" id="prevNameBody">{{ $user->name }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Email</div>
                            <div class="prev-value" id="prevEmailBody" style="font-size:12px;word-break:break-all">
                                {{ $user->email }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Rôles assignés</div>
                            <div class="prev-roles-list" id="prevRoles">
                                {{-- Pré-rempli via JS au chargement --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="userForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('users.show', $user) }}" class="btn-cancel">
                        <i class="fa-solid fa-eye"></i>
                        Voir le profil
                    </a>
                    <a href="{{ route('users.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                    @if (auth()->id() !== $user->id)
                        <div class="divider"></div>
                        <button type="button" class="btn-delete" onclick="confirmDelete()">
                            <i class="fa-solid fa-trash"></i>
                            Supprimer ce compte
                        </button>
                    @endif
                </div>

                <div class="info-box">
                    <strong>Mot de passe :</strong> Si vous ne souhaitez pas le modifier, laissez le champ vide.
                    Le mot de passe actuel sera conservé.
                </div>

            </div>

        </div>
    </form>

    <form id="deleteForm" method="POST" action="{{ route('users.destroy', $user) }}">
        @csrf @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var nameInput = document.getElementById('name');
            var emailInput = document.getElementById('email');
            var pwInput = document.getElementById('password');
            var pwConfirm = document.getElementById('password_confirmation');
            var pwToggle = document.getElementById('changePwToggle');
            var pwFields = document.getElementById('pwFields');

            // ── TOGGLE SECTION MOT DE PASSE ───────────────────────
            pwToggle.addEventListener('change', function() {
                pwFields.classList.toggle('visible', this.checked);
                if (!this.checked) {
                    pwInput.value = '';
                    pwConfirm.value = '';
                    document.getElementById('pwStrength').style.display = 'none';
                    document.getElementById('pwMatch').style.display = 'none';
                }
            });

            // ── APERÇU TEMPS RÉEL ─────────────────────────────────
            function updatePreview() {
                var name = nameInput.value.trim();
                var email = emailInput.value.trim();

                var initials = '?';
                if (name) {
                    var parts = name.split(' ').filter(Boolean);
                    initials = parts.length >= 2 ?
                        (parts[0][0] + parts[1][0]).toUpperCase() :
                        name.substring(0, 2).toUpperCase();
                }

                document.getElementById('prevAvatar').textContent = initials;
                document.getElementById('prevName').textContent = name || '—';
                document.getElementById('prevEmail').textContent = email || '—';
                document.getElementById('prevNameBody').textContent = name || '—';
                document.getElementById('prevEmailBody').textContent = email || '—';

                // Rôles
                var checked = document.querySelectorAll('.role-checkbox:checked');
                var prevRoles = document.getElementById('prevRoles');
                prevRoles.innerHTML = '';
                if (checked.length === 0) {
                    prevRoles.innerHTML =
                        '<span style="font-size:12px;color:var(--text-muted);font-style:italic">Aucun sélectionné</span>';
                } else {
                    checked.forEach(function(cb) {
                        var label = cb.closest('.role-select-item').querySelector('.role-select-label')
                            .textContent.trim();
                        var tag = document.createElement('span');
                        tag.className = 'prev-role-tag';
                        tag.textContent = label;
                        prevRoles.appendChild(tag);
                    });
                }
            }

            nameInput.addEventListener('input', function() {
                updatePreview();
                markChanged();
            });
            emailInput.addEventListener('input', function() {
                updatePreview();
                markChanged();
            });
            document.querySelectorAll('.role-checkbox').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    updatePreview();
                    markChanged();
                });
            });

            // ── INDICATEUR CHANGEMENTS ────────────────────────────
            function markChanged() {
                document.getElementById('changeIndicator').classList.add('visible');
            }

            // ── FORCE MOT DE PASSE ────────────────────────────────
            if (pwInput) {
                pwInput.addEventListener('input', function() {
                    var val = this.value;
                    var score = [
                        val.length >= 8,
                        /[A-Z]/.test(val),
                        /[0-9]/.test(val),
                        /[^A-Za-z0-9]/.test(val),
                    ].filter(Boolean).length;

                    var strengthEl = document.getElementById('pwStrength');
                    var label = document.getElementById('pwStrengthLabel');
                    var cls = score <= 1 ? 'filled-weak' : score <= 2 ? 'filled-medium' : 'filled-strong';
                    var txt = score <= 1 ? 'Faible' : score <= 2 ? 'Moyen' : score === 3 ? 'Bon' : 'Fort';

                    strengthEl.style.display = val.length > 0 ? 'flex' : 'none';
                    label.textContent = txt;
                    label.style.color = score <= 1 ? 'var(--color-primary)' : score <= 2 ? '#f59e0b' :
                        '#10b981';

                    ['seg1', 'seg2', 'seg3', 'seg4'].forEach(function(id, i) {
                        var s = document.getElementById(id);
                        s.className = 'pw-bar-seg';
                        if (i < score) s.classList.add(cls);
                    });
                    checkMatch();
                    markChanged();
                });
            }

            // ── CORRESPONDANCE ────────────────────────────────────
            function checkMatch() {
                var matchEl = document.getElementById('pwMatch');
                if (!pwInput || !pwConfirm || !pwConfirm.value) {
                    matchEl.style.display = 'none';
                    return;
                }
                matchEl.style.display = 'inline';
                if (pwInput.value === pwConfirm.value) {
                    matchEl.textContent = '✓ Les mots de passe correspondent';
                    matchEl.style.color = '#10b981';
                } else {
                    matchEl.textContent = '✗ Les mots de passe ne correspondent pas';
                    matchEl.style.color = 'var(--color-primary)';
                }
            }

            if (pwConfirm) pwConfirm.addEventListener('input', function() {
                checkMatch();
                markChanged();
            });

            // ── INITIALISATION ────────────────────────────────────
            updatePreview();
        });

        // Toggle visibilité mot de passe
        function togglePw(fieldId, btn) {
            var input = document.getElementById(fieldId);
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Suppression avec confirmation
        function confirmDelete() {
            if (confirm('Supprimer ce compte users ? Cette action est irréversible.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endpush
