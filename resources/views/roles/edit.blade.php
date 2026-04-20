{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN RÔLE — OBTRANS TMS
|--------------------------------------------------------------------------
| Formulaire d'ajout d'un rôle avec permissions groupées.
|
| POUR LE DÉVELOPPEUR :
|   - Action du form : route('roles.store')
|   - Controller attendu : RoleController@store
|   - Champs : name, description, permissions[]
| --}}
@php
    // Configuration pour mapper les icônes et noms propres (Optionnel mais recommandé)
    $config = [
        'villes' => ['icon' => 'fa-city', 'label' => 'Villes'],
        'vehicules' => ['icon' => 'fa-truck', 'label' => 'Véhicules'],
        'chauffeurs' => ['icon' => 'fa-user-tie', 'label' => 'Chauffeurs'],
        'commandes' => ['icon' => 'fa-file-invoice', 'label' => 'Commandes'],
        'roles' => ['icon' => 'fa-shield-halved', 'label' => 'Rôles'],
    ];

    $actions = [
        'index' => ['label' => 'Voir la liste', 'desc' => 'Lecture seule'],
        'show' => ['label' => 'Voir détail', 'desc' => 'Lecture seule'],
        'create' => ['label' => 'Ajouter', 'desc' => 'Création'],
        'edit' => ['label' => 'Modifier', 'desc' => 'Édition'],
        'delete' => ['label' => 'Supprimer', 'desc' => 'Suppression définitive'],
    ];
@endphp
@extends('layouts.app')

@section('title', 'Edit Rôle')
@section('page-title', 'Edit Rôle')
@section('page-subtitle', 'Définir les permissions associées à ce rôle')

@section('content')

    {{-- Styles spécifiques à ce formulaire --}}
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

        /* ── LAYOUT FORM : deux colonnes ── */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 20px;
            align-items: start;
        }

        /* ── FIELDSET (groupe de champs) ── */
        .form-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── CHAMP GÉNÉRIQUE ── */
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

        /* Inputs & textarea */
        .field input[type="text"],
        .field textarea {
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
            resize: vertical;
        }

        .field input[type="text"]:focus,
        .field textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted);
        }

        /* Erreur de validation */
        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── PERMISSIONS ── */
        .permissions-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .permissions-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .permissions-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .permissions-count {
            font-size: 11px;
            color: var(--text-muted);
            font-variant-numeric: tabular-nums;
        }

        /* Groupe de permissions (ex: Véhicules, Chauffeurs…) */
        .perm-group {
            border-bottom: 1px solid var(--border);
        }

        .perm-group:last-child {
            border-bottom: none;
        }

        .perm-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 13px 20px;
            cursor: pointer;
            background: transparent;
            transition: background var(--transition);
            user-select: none;
        }

        .perm-group-header:hover {
            background: #f9f9f9;
        }

        .perm-group-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .perm-group-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: var(--bg-body);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .perm-group-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .perm-group-badge {
            font-size: 10px;
            color: var(--text-muted);
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1px 7px;
        }

        /* Toggle "tout cocher" le groupe */
        .perm-group-toggle {
            position: relative;
            width: 36px;
            height: 20px;
            flex-shrink: 0;
        }

        .perm-group-toggle input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-track {
            position: absolute;
            inset: 0;
            background: var(--border);
            border-radius: 20px;
            cursor: pointer;
            transition: background var(--transition);
        }

        .toggle-thumb {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
            transition: transform var(--transition);
            pointer-events: none;
        }

        .perm-group-toggle input:checked~.toggle-track {
            background: var(--color-primary);
        }

        .perm-group-toggle input:checked~.toggle-track .toggle-thumb {
            transform: translateX(16px);
        }

        /* Liste des permissions individuelles */
        .perm-list {
            display: none;
            padding: 0 20px 14px;
            flex-direction: column;
            gap: 2px;
        }

        .perm-list.open {
            display: flex;
        }

        .perm-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--border-radius-sm);
            transition: background var(--transition);
            cursor: pointer;
        }

        .perm-item:hover {
            background: var(--bg-body);
        }

        .perm-item input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--color-primary);
            cursor: pointer;
            flex-shrink: 0;
        }

        .perm-item-label {
            flex: 1;
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
        }

        .perm-item-desc {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* ── SIDEBAR DROITE : résumé + actions ── */
        .sidebar-col {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Résumé du rôle */
        .role-preview {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .role-preview-header {
            background: var(--color-dark);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .role-preview-icon {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, .1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
        }

        .role-preview-label {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .role-preview-sub {
            font-size: 11px;
            color: #666;
            margin-top: 1px;
        }

        .role-preview-body {
            padding: 16px 18px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .preview-row {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .preview-row-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .preview-row-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        #previewName {
            color: var(--color-primary);
        }

        .preview-perms-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            min-height: 24px;
        }

        .preview-perm-tag {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 8px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .preview-empty {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic;
        }

        /* Compteur de permissions sélectionnées */
        .perm-counter {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 18px;
            border-top: 1px solid var(--border);
            background: var(--bg-body);
        }

        .perm-counter-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        .perm-counter-value {
            font-size: 18px;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-primary);
        }

        /* Boutons d'action */
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

        .btn-submit-role {
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

        .btn-submit-role:hover {
            background: var(--color-primary);
            transform: translateY(-1px);
        }

        .btn-cancel-role {
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

        .btn-cancel-role:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        /* Info box */
        .info-box {
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-left: 3px solid var(--color-primary);
            border-radius: var(--border-radius-sm);
            padding: 12px 14px;
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .info-box strong {
            color: var(--text-primary);
        }

        /* Chevron toggle */
        .perm-chevron {
            font-size: 10px;
            color: var(--text-muted);
            transition: transform var(--transition);
            margin-left: 6px;
        }

        .perm-group-header.open .perm-chevron {
            transform: rotate(180deg);
        }

        /* btn-icon--warning manquant dans app.css, on l'ajoute ici */
        .btn-icon--warning:hover {
            background: rgba(245, 158, 11, .12);
            color: #d97706;
        }

        @media (max-width: 960px) {
            .form-layout {
                grid-template-columns: 1fr;
            }

            .sidebar-col {
                order: -1;
            }

            .action-card {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .btn-submit-role,
            .btn-cancel-role {
                flex: 1;
            }
        }


        /* start laravel-notify */
        .notify {
            z-index: 9999 !important;
        }

        /* end laravel-notify */
    </style>

    {{-- ── BREADCRUMB ── --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('roles.index') }}">Rôles</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Edit rôle</span>
    </div>

    <form method="POST" action="{{ route('roles.update', $role->id) }}" id="roleForm">
        @csrf
        @method('PUT')
        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Informations générales --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Informations générales</h2>
                    </div>

                    {{-- Nom du rôle --}}
                    <div class="field" style="margin-bottom:16px">
                        <label for="name">Nom du rôle <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Identifiant court et explicite (ex : Responsable Flotte, Comptable…)</p>
                        <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}"
                            placeholder="Ex : Responsable Flotte" required maxlength="60" autocomplete="off">
                        @error('name')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="field">
                        <label for="description">Description</label>
                        <p class="field-hint">Décrivez brièvement les responsabilités de ce rôle.</p>
                        <textarea id="description" name="description" rows="3" placeholder="Ce rôle permet de…">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ── PERMISSIONS ── --}}
                <div class="permissions-card">
                    <div class="permissions-header">
                        <span class="permissions-title">
                            <i class="fa-solid fa-shield-halved" style="color:var(--color-primary);margin-right:6px"></i>
                            Permissions
                        </span>
                        <span class="permissions-count" id="permCountHeader">0 sélectionnée(s)</span>
                    </div>


                    @foreach ($groupedPermissions as $module => $permissions)
                        <div class="perm-group">
                            <div class="perm-group-header" data-group="{{ $module }}">
                                <div class="perm-group-header-left">
                                    <div class="perm-group-icon">
                                        <i class="fa-solid {{ $config[$module]['icon'] ?? 'fa-folder' }}"></i>
                                    </div>
                                    <span class="perm-group-name">{{ $config[$module]['label'] ?? ucfirst($module) }}</span>
                                    <span class="perm-group-badge" id="badge-{{ $module }}">0 /
                                        {{ $permissions->count() }}</span>
                                </div>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <label class="perm-group-toggle" title="Tout cocher" onclick="event.stopPropagation()">
                                        <input type="checkbox" class="group-toggle"
                                            data-group="{{ $module }}">
                                        <span class="toggle-track"><span class="toggle-thumb"></span></span>
                                    </label>
                                    <i class="fa-solid fa-chevron-down perm-chevron"></i>
                                </div>
                            </div>

                            <div class="perm-list" id="list-{{ $module }}">
                                @foreach ($permissions as $permission)
                                    @php
                                        $actionName = explode('.', $permission->name)[1] ?? $permission->name;
                                    @endphp
                                    <label class="perm-item">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            class="perm-check" data-group="{{ $module }}" @checked(in_array($permission->name, $rolePermissions))>
                                        <span
                                            class="perm-item-label">{{ $actions[$actionName]['label'] ?? ucfirst($actionName) }}</span>
                                        <span class="perm-item-desc">{{ $actions[$actionName]['desc'] ?? '' }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>{{-- /permissions-card --}}

            </div>{{-- /form-section --}}

            {{-- ══ COLONNE DROITE : résumé + actions ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu du rôle en temps réel --}}
                <div class="role-preview">
                    <div class="role-preview-header">
                        <div class="role-preview-icon"><i class="fa-solid fa-shield-halved"></i></div>
                        <div>
                            <div class="role-preview-label">Aperçu du rôle</div>
                            <div class="role-preview-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="role-preview-body">
                        <div class="preview-row">
                            <div class="preview-row-label">Nom</div>
                            <div class="preview-row-value" id="previewName">—</div>
                        </div>
                        <div class="preview-row">
                            <div class="preview-row-label">Description</div>
                            <div class="preview-row-value" id="previewDesc"
                                style="font-size:12px;font-weight:400;color:var(--text-muted)">—</div>
                        </div>
                        <div class="preview-row">
                            <div class="preview-row-label">Permissions actives</div>
                            <div class="preview-perms-list" id="previewPerms">
                                <span class="preview-empty">Aucune permission sélectionnée</span>
                            </div>
                        </div>
                    </div>
                    <div class="perm-counter">
                        <span class="perm-counter-label">Total sélectionné</span>
                        <span class="perm-counter-value" id="permCountValue">0</span>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit-role" form="roleForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn-cancel-role">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                {{-- Info --}}
                <div class="info-box">
                    <strong>Conseil :</strong> Accordez uniquement les permissions nécessaires.
                    Vous pourrez modifier ce rôle à tout moment depuis la liste des rôles.
                </div>

            </div>{{-- /sidebar-col --}}

        </div>{{-- /form-layout --}}

    </form>

@endsection

@push('scripts')
    <script>
        /**
         * SCRIPT : Formulaire Création de Rôle
         * ─────────────────────────────────────
         * Gère :
         *   1. Ouverture/fermeture des groupes de permissions (accordion)
         *   2. Toggle "tout cocher" par groupe
         *   3. Aperçu temps réel (nom, description, permissions)
         *   4. Compteur de permissions sélectionnées
         */
        document.addEventListener('DOMContentLoaded', function() {

            // ── 1. ACCORDION ──────────────────────────────────────
            document.querySelectorAll('.perm-group-header').forEach(function(header) {
                header.addEventListener('click', function() {
                    const group = this.dataset.group;
                    const list = document.getElementById('list-' + group);
                    const isOpen = list.classList.contains('open');

                    // Fermer tous
                    document.querySelectorAll('.perm-list').forEach(function(l) {
                        l.classList.remove('open');
                    });
                    document.querySelectorAll('.perm-group-header').forEach(function(h) {
                        h.classList.remove('open');
                    });

                    // Ouvrir celui cliqué si était fermé
                    if (!isOpen) {
                        list.classList.add('open');
                        this.classList.add('open');
                    }
                });
            });

            // ── 2. TOGGLE GROUPE (tout cocher / décocher) ─────────
            document.querySelectorAll('.group-toggle').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    const group = this.dataset.group;
                    const checked = this.checked;
                    document.querySelectorAll('.perm-check[data-group="' + group + '"]')
                        .forEach(function(cb) {
                            cb.checked = checked;
                        });
                    updateAll();
                });
            });

            // ── 3. CHECKBOX INDIVIDUELLE ──────────────────────────
            document.querySelectorAll('.perm-check').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    syncGroupToggle(this.dataset.group);
                    updateAll();
                });
            });

            // Synchronise le toggle groupe selon l'état des checkboxes individuelles
            function syncGroupToggle(group) {
                var all = document.querySelectorAll('.perm-check[data-group="' + group + '"]');
                var checked = document.querySelectorAll('.perm-check[data-group="' + group + '"]:checked');
                var toggle = document.querySelector('.group-toggle[data-group="' + group + '"]');
                if (toggle) toggle.checked = (checked.length === all.length && all.length > 0);
            }

            // ── 4. MISE À JOUR GLOBALE ────────────────────────────
            function updateAll() {
                updateBadges();
                updatePreview();
                updateCounter();
            }

            // Badges par groupe "x / total"
            function updateBadges() {
                document.querySelectorAll('.perm-group').forEach(function(group) {
                    var groupName = group.querySelector('.perm-group-header').dataset.group;
                    var all = group.querySelectorAll('.perm-check').length;
                    var checked = group.querySelectorAll('.perm-check:checked').length;
                    var badge = document.getElementById('badge-' + groupName);
                    if (badge) {
                        badge.textContent = checked + ' / ' + all;
                        badge.style.background = checked > 0 ? 'var(--color-primary-dim)' : '';
                        badge.style.color = checked > 0 ? 'var(--color-primary)' : '';
                        badge.style.borderColor = checked > 0 ? 'rgba(224,32,32,.2)' : '';
                    }
                });
            }

            // Compteur total
            function updateCounter() {
                var count = document.querySelectorAll('.perm-check:checked').length;
                document.getElementById('permCountValue').textContent = count;
                document.getElementById('permCountHeader').textContent = count + ' sélectionnée(s)';
            }

            // Aperçu temps réel
            function updatePreview() {
                // Nom
                var nameVal = document.getElementById('name').value.trim();
                document.getElementById('previewName').textContent = nameVal || '—';

                // Description
                var descVal = document.getElementById('description').value.trim();
                document.getElementById('previewDesc').textContent = descVal || '—';

                // Permissions
                var permsList = document.getElementById('previewPerms');
                var checked = document.querySelectorAll('.perm-check:checked');

                permsList.innerHTML = '';
                if (checked.length === 0) {
                    permsList.innerHTML = '<span class="preview-empty">Aucune permission sélectionnée</span>';
                } else {
                    checked.forEach(function(cb) {
                        var tag = document.createElement('span');
                        tag.className = 'preview-perm-tag';
                        tag.textContent = cb.value.replace('.', ' ');
                        permsList.appendChild(tag);
                    });
                }
            }

            // ── 5. LIVE PREVIEW sur saisie ────────────────────────
            document.getElementById('name').addEventListener('input', updatePreview);
            document.getElementById('description').addEventListener('input', updatePreview);

            // ── 6. VALIDATION AVANT ENVOI ────────────────────────────
            const roleForm = document.getElementById('roleForm');
            if (roleForm) {
                console.log(roleForm);

                roleForm.addEventListener('submit', function(e) {
                    const checkedPermissions = document.querySelectorAll('.perm-check:checked').length;

                    if (checkedPermissions === 0) {
                        e.preventDefault(); // Bloque l'envoi

                        Swal.fire({
                            title: 'Action requise',
                            text: 'Vous ne pouvez pas créer un rôle sans aucune permission.',
                            icon: 'warning',
                            confirmButtonText: 'Compris',
                            confirmButtonColor: '#e02020', // Utilisation de ta couleur primaire
                            background: '#ffffff',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    } else {
                        // Optionnel : Afficher un loader sur le bouton pour plus d'interactivité
                        const btn = this.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Création...';
                            btn.style.opacity = '0.7';
                            btn.style.pointerEvents = 'none';
                        }
                    }
                });
            }

            // ── 7. INITIALISATION ÉDITION ──────────────────────────
            // On synchronise tous les toggles de groupe au chargement
            document.querySelectorAll('.perm-group-header').forEach(function(header) {
                syncGroupToggle(header.dataset.group);
            });

            updateAll();


            // Ouvrir le premier groupe par défaut
            var firstHeader = document.querySelector('.perm-group-header');
            if (firstHeader) firstHeader.click();

        });
    </script>
@endpush
