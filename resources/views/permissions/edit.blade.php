{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UNE PERMISSION — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $permission        : App\Models\Permission
|   - $roles             : Collection de tous les rôles
|   - $assignedRoleIds   : array des IDs de rôles ayant cette permission
|
| ROUTE : PUT /permissions/{permission} → PermissionController@update
| --}}

@extends('layouts.app')

@section('title', 'Modifier — ' . $permission->name)
@section('page-title', 'Modifier la Permission')
@section('page-subtitle', 'Mettre à jour les informations et les affectations')

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

        .edit-banner-icon {
            width: 36px;
            height: 36px;
            background: rgba(224, 32, 32, .15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-primary);
            font-size: 16px;
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

        .edit-banner-slug {
            margin-left: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #555;
            background: rgba(255, 255, 255, .05);
            border: 1px solid #222;
            border-radius: 6px;
            padding: 4px 10px;
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
        .field select,
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
            resize: vertical;
            transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
        }

        .field select {
            cursor: pointer;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: var(--color-primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(224, 32, 32, .08);
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: var(--text-muted);
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Slug */
        .slug-wrap {
            position: relative;
        }

        .slug-wrap input {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
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

        /* Rôles */
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
            padding: 10px 14px;
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
            font-size: 13px;
            color: var(--text-secondary);
            cursor: pointer;
            flex: 1;
        }

        .role-select-count {
            font-size: 11px;
            color: var(--text-muted);
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

        .preview-card-icon {
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
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .prev-value.primary {
            color: var(--color-primary);
        }

        .prev-slug-badge {
            display: inline-block;
            font-size: 12px;
            font-family: 'JetBrains Mono', monospace;
            background: var(--bg-body);
            border: 1px solid var(--border);
            border-radius: 5px;
            padding: 3px 8px;
            color: var(--text-secondary);
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

        /* Assignations courantes (badges) */
        .current-roles {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .cur-role-chip {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            background: var(--color-primary-dim);
            color: var(--color-primary);
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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
        }
    </style>

    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Dashboard</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <a href="{{ route('permissions.index') }}">Permissions</a>
        <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
        <span>Modifier · {{ $permission->name }}</span>
    </div>

    {{-- Bandeau édition --}}
    <div class="edit-banner">
        <div class="edit-banner-icon"><i class="fa-solid fa-pen-to-square"></i></div>
        <div class="edit-banner-text">
            <strong>Mode édition — {{ $permission->name }}</strong>
            <span>Les modifications seront répercutées sur tous les rôles utilisant cette permission.</span>
        </div>
        <span class="edit-banner-slug">{{ explode('.', $permission->name)[0] ?? '' }}</span>
    </div>

    <form method="POST" action="{{ route('permissions.update', $permission) }}" id="permForm">
        @csrf
        @method('PUT')

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Informations</h2>
                        <div class="change-indicator" id="changeIndicator">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            Modifications non sauvegardées
                        </div>
                    </div>

                    {{-- Nom + Groupe --}}
                    <div class="field-row" style="margin-bottom:16px">
                        <div class="field">
                            <label for="name">Nom <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Nom lisible de la permission</p>
                            <input type="text" id="name" name="name" value="{{ old('name', $permission->name) }}"
                                required maxlength="80" autocomplete="off">
                            @error('name')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label for="group">Groupe <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Catégorie fonctionnelle</p>
                            <select id="group" name="group" required>
                                <option value="">— Sélectionner —</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module }}"
                                        {{ old('group', explode('.', $permission->name)[0] ?? '') == $module ? 'selected' : '' }}>
                                        {{ $module }}
                                    </option>
                                @endforeach
                            </select>
                            @error('group')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="field" style="margin-bottom:16px">
                        <label for="slug">Slug <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Identifiant technique. Modifier avec précaution si déjà utilisé en base.</p>
                        <div class="slug-wrap">
                            <input type="text" id="slug" name="slug"
                                value="{{ old('slug', $permission->name) }}" required pattern="[a-z0-9._\-]+"
                                autocomplete="off">
                        </div>
                        @error('slug')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Assignation rôles --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-shield-halved"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Rôles assignés
                        </h2>
                        <span style="font-size:12px;color:var(--text-muted)">
                            {{ count($assignedRoleIds ?? []) }} rôle(s) actuel(s)
                        </span>
                    </div>

                    @if (isset($roles) && $roles->count() > 0)
                        <div class="roles-select-list">
                            @foreach ($roles as $role)
                                <label class="role-select-item">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', $assignedRoleIds ?? [])) ? 'checked' : '' }}>
                                    <span class="role-select-label">{{ $role->name }}</span>
                                    <span class="role-select-count">{{ $role->users_count ?? 0 }} utilisateur(s)</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size:13px;color:var(--text-muted);font-style:italic">
                            Aucun rôle disponible.
                        </p>
                    @endif
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR ══ --}}
            <div class="sidebar-col">

                {{-- Aperçu --}}
                <div class="preview-card">
                    <div class="preview-card-header">
                        <div class="preview-card-icon"><i class="fa-solid fa-eye"></i></div>
                        <div>
                            <div class="preview-card-label">Aperçu</div>
                            <div class="preview-card-sub">Mis à jour en temps réel</div>
                        </div>
                    </div>
                    <div class="preview-card-body">
                        <div class="prev-row">
                            <div class="prev-label">Nom</div>
                            <div class="prev-value primary" id="prevName">{{ $permission->name }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Slug</div>
                            <span class="prev-slug-badge" id="prevSlug">{{ $permission->slug }}</span>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Groupe</div>
                            <div class="prev-value" id="prevGroup">{{ $permission->group ?? '—' }}</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Description</div>
                            <div class="prev-value" id="prevDesc"
                                style="font-size:12px;font-weight:400;color:var(--text-muted)">
                                {{ $permission->description ?: '—' }}
                            </div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Rôles assignés</div>
                            <div class="prev-value" id="prevRoles"
                                style="font-size:12px;font-weight:500;color:var(--text-muted)">
                                Chargement…
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="permForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Enregistrer les modifications
                    </button>
                    <a href="{{ route('permissions.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                    <div class="divider"></div>
                    <button type="button" class="btn-delete" onclick="confirmDelete()">
                        <i class="fa-solid fa-trash"></i>
                        Supprimer cette permission
                    </button>
                </div>

                {{-- Rôles actuels --}}
                @if (isset($roles) && $roles->whereIn('id', $assignedRoleIds ?? [])->count() > 0)
                    <div class="info-box">
                        <strong>Actuellement assignée à :</strong><br>
                        <div class="current-roles" style="margin-top:7px">
                            @foreach ($roles->whereIn('id', $assignedRoleIds ?? []) as $r)
                                <span class="cur-role-chip">{{ $r->name }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="info-box">
                    <strong>Attention :</strong> Modifier le slug d'une permission déjà utilisée
                    peut casser les vérifications de droits dans l'application.
                </div>

            </div>

        </div>
    </form>

    <form id="deleteForm" method="POST" action="{{ route('permissions.destroy', $permission) }}">
        @csrf
        @method('DELETE')
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var nameInput = document.getElementById('name');
            var slugInput = document.getElementById('slug');
            var groupSelect = document.getElementById('group');
            var descInput = document.getElementById('description');
            var slugEdited = false;

            // Marquage modification manuelle du slug
            slugInput.addEventListener('input', function() {
                slugEdited = true;
                updatePreview();
                markChanged();
            });

            nameInput.addEventListener('input', function() {
                if (!slugEdited) {
                    var groupVal = groupSelect.value || 'permission';
                    var nameSlug = this.value.toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
                    slugInput.value = groupVal + '.' + nameSlug;
                }
                updatePreview();
                markChanged();
            });

            groupSelect.addEventListener('change', function() {
                if (!slugEdited) {
                    var nameSlug = nameInput.value.toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
                    slugInput.value = this.value + (nameSlug ? '.' + nameSlug : '');
                }
                updatePreview();
                markChanged();
            });

            descInput.addEventListener('input', function() {
                updatePreview();
                markChanged();
            });

            document.querySelectorAll('input[name="roles[]"]').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    updatePreview();
                    markChanged();
                });
            });

            function markChanged() {
                document.getElementById('changeIndicator').classList.add('visible');
            }

            function updatePreview() {
                document.getElementById('prevName').textContent = nameInput.value.trim() || '—';
                document.getElementById('prevSlug').textContent = slugInput.value.trim() || '—';
                document.getElementById('prevDesc').textContent = descInput.value.trim() || '—';

                var groupText = groupSelect.options[groupSelect.selectedIndex]?.text;
                document.getElementById('prevGroup').textContent = groupSelect.value ? groupText : '—';

                var checked = document.querySelectorAll('input[name="roles[]"]:checked');
                document.getElementById('prevRoles').textContent = checked.length === 0 ?
                    'Aucun' :
                    Array.from(checked).map(function(cb) {
                        return cb.closest('.role-select-item').querySelector('.role-select-label').textContent;
                    }).join(', ');
            }

            updatePreview();
        });

        function confirmDelete() {
            if (confirm('Supprimer cette permission ? Elle sera retirée de tous les rôles qui l\'utilisent.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endpush
