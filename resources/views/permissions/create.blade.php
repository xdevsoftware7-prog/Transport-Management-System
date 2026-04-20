{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UNE PERMISSION — OBTRANS TMS
|--------------------------------------------------------------------------
| VARIABLES ATTENDUES :
|   - $roles : Collection de tous les rôles (pour pré-assigner)
|
| ROUTE : POST /permissions → PermissionController@store
| CHAMPS : name, slug, group, description, roles[]
| --}}

@extends('layouts.app')

@section('title', 'Nouvelle Permission')
@section('page-title', 'Nouvelle Permission')
@section('page-subtitle', 'Définir une permission et ses affectations aux rôles')

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

        /* ── LAYOUT ── */
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

        /* ── CHAMPS ── */
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

        /* Slug auto-généré */
        .slug-wrap {
            position: relative;
        }

        .slug-prefix {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
            pointer-events: none;
            user-select: none;
        }

        .slug-wrap input {
            padding-left: 14px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }

        .field-error {
            font-size: 12px;
            color: var(--color-primary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ── GRILLE DE 2 CHAMPS CÔTE À CÔTE ── */
        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* ── SÉLECTION RÔLES ── */
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

        /* ── APERÇU SIDEBAR ── */
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

        /* Groupes disponibles (chips cliquables) */
        .group-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .group-chip-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--border-radius-sm);
            background: #fafafa;
            font-size: 12px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-secondary);
            cursor: pointer;
            transition: border-color var(--transition), background var(--transition), color var(--transition);
        }

        .group-chip-btn:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
        }

        .group-chip-btn.selected {
            border-color: var(--color-primary);
            background: var(--color-primary-dim);
            color: var(--color-primary);
        }

        .group-chip-btn i {
            font-size: 11px;
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
        <span>Nouvelle permission</span>
    </div>

    <form method="POST" action="{{ route('permissions.store') }}" id="permForm">
        @csrf

        <div class="form-layout">

            {{-- ══ COLONNE PRINCIPALE ══ --}}
            <div class="form-section">

                {{-- Informations --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">Informations de la permission</h2>
                    </div>

                    {{-- Nom + Groupe côte à côte --}}
                    <div class="field-row" style="margin-bottom:16px">

                        {{-- Nom --}}
                        <div class="field">
                            <label for="name">Nom <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Nom lisible (ex : Voir les véhicules)</p>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Ex : Voir les véhicules" required maxlength="80" autocomplete="off">
                            @error('name')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Groupe --}}
                        <div class="field">
                            <label for="group">Groupe <span style="color:var(--color-primary)">*</span></label>
                            <p class="field-hint">Catégorie fonctionnelle</p>
                            <select id="group" name="group" required>
                                <option value="">— Sélectionner —</option>
                                @foreach ($modules as $module)
                                    <option value="{{ $module }}" {{ old('group') ==  $module  ? 'selected' : '' }}>— {{ $module }} —</option>
                                @endforeach
                            </select>
                            @error('group')
                                <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- Slug (auto-généré) --}}
                    <div class="field" style="margin-bottom:16px">
                        <label for="slug">Slug <span style="color:var(--color-primary)">*</span></label>
                        <p class="field-hint">Identifiant technique unique, généré automatiquement depuis le nom.</p>
                        <div class="slug-wrap">
                            <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                                placeholder="vehicules.voir" required pattern="[a-z0-9._\-]+" autocomplete="off">
                        </div>
                        @error('slug')
                            <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                        @enderror
                    </div>

                </div>

                {{-- Assignation aux rôles --}}
                <div class="section-card">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i class="fa-solid fa-shield-halved"
                                style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                            Assigner aux rôles
                        </h2>
                        <span style="font-size:12px;color:var(--text-muted)">Optionnel</span>
                    </div>

                    @if (isset($roles) && $roles->count() > 0)
                        <div class="roles-select-list">
                            @foreach ($roles as $role)
                                <label class="role-select-item">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                    <span class="role-select-label">{{ $role->name }}</span>
                                    <span class="role-select-count">
                                        {{ $role->users_count ?? 0 }} utilisateur(s)
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size:13px;color:var(--text-muted);font-style:italic">
                            Aucun rôle disponible. <a href="{{ route('roles.create') }}"
                                style="color:var(--color-primary)">Créer un rôle</a>
                        </p>
                    @endif
                </div>

            </div>{{-- /form-section --}}

            {{-- ══ SIDEBAR DROITE ══ --}}
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
                            <div class="prev-value primary" id="prevName">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Slug</div>
                            <span class="prev-slug-badge" id="prevSlug">—</span>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Groupe</div>
                            <div class="prev-value" id="prevGroup">—</div>
                        </div>
                        <div class="prev-row">
                            <div class="prev-label">Rôles assignés</div>
                            <div class="prev-value" id="prevRoles"
                                style="font-size:12px;font-weight:500;color:var(--text-muted)">Aucun</div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="action-card">
                    <button type="submit" class="btn-submit" form="permForm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Créer la permission
                    </button>
                    <a href="{{ route('permissions.index') }}" class="btn-cancel">
                        <i class="fa-solid fa-xmark"></i>
                        Annuler
                    </a>
                </div>

                <div class="info-box">
                    <strong>Format du slug :</strong> utilisez uniquement des lettres minuscules, chiffres, points et
                    tirets.
                    Exemple : <code style="font-family:'JetBrains Mono',monospace;font-size:11px">vehicules.voir</code>
                </div>

            </div>

        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var nameInput = document.getElementById('name');
            var slugInput = document.getElementById('slug');
            var groupSelect = document.getElementById('group');
            var descInput = document.getElementById('description');

            // ── AUTO-GÉNÉRATION DU SLUG ───────────────────────────
            var slugEdited = false;

            slugInput.addEventListener('input', function() {
                slugEdited = true; // L'utilisateur a modifié manuellement → ne plus écraser
                updatePreview();
            });

            nameInput.addEventListener('input', function() {
                if (!slugEdited) {
                    var groupVal = groupSelect.value || 'permission';
                    var nameSlug = this.value
                        .toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // enlever accents
                        .replace(/\s+/g, '-')
                        .replace(/[^a-z0-9\-]/g, '');
                    slugInput.value = groupVal + '.' + nameSlug;
                }
                updatePreview();
            });

            groupSelect.addEventListener('change', function() {
                if (!slugEdited) {
                    var nameSlug = nameInput.value
                        .toLowerCase()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/[^a-z0-9\-]/g, '');
                    slugInput.value = this.value + (nameSlug ? '.' + nameSlug : '');
                }
                updatePreview();
            });

            descInput.addEventListener('input', updatePreview);

            // Rôles cochés
            document.querySelectorAll('input[name="roles[]"]').forEach(function(cb) {
                cb.addEventListener('change', updatePreview);
            });

            // ── MISE À JOUR APERÇU ────────────────────────────────
            function updatePreview() {
                document.getElementById('prevName').textContent = nameInput.value.trim() || '—';
                document.getElementById('prevSlug').textContent = slugInput.value.trim() || '—';
                document.getElementById('prevDesc').textContent = descInput.value.trim() || '—';

                var groupText = groupSelect.options[groupSelect.selectedIndex].text;
                document.getElementById('prevGroup').textContent = groupSelect.value ? groupText : '—';

                var checked = document.querySelectorAll('input[name="roles[]"]:checked');
                if (checked.length === 0) {
                    document.getElementById('prevRoles').textContent = 'Aucun';
                } else {
                    var names = Array.from(checked).map(function(cb) {
                        return cb.closest('.role-select-item').querySelector('.role-select-label')
                            .textContent;
                    });
                    document.getElementById('prevRoles').textContent = names.join(', ');
                }
            }

            updatePreview();
        });
    </script>
@endpush
