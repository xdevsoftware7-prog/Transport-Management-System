{{--
|--------------------------------------------------------------------------
| PAGE : CRÉER UN UTILISATEUR — OBTRANS TMS
|--------------------------------------------------------------------------
| CHAMPS (validation Laravel) :
|   - name            required|string|max:255
|   - email           required|string|lowercase|email|max:255|unique:users
|   - password        required|confirmed|Password::defaults()
|   - password_confirmation  required
|   - roles[]         required|array / roles.*|exists:roles,id
|
| VARIABLES ATTENDUES :
|   - $roles : Collection de tous les rôles disponibles
|
| ROUTE : POST /users → UtilisateurController@store
|--}}

@extends('layouts.app')

@section('title', 'Nouvel Utilisateur')
@section('page-title', 'Nouvel Utilisateur')
@section('page-subtitle', 'Créer un compte et assigner les rôles d\'accès')

@section('content')

<style>
    .breadcrumb {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: var(--text-muted); margin-bottom: 4px;
    }
    .breadcrumb a { color: var(--text-muted); transition: color var(--transition); }
    .breadcrumb a:hover { color: var(--color-primary); }
    .breadcrumb-sep { font-size: 10px; }

    /* ── LAYOUT ── */
    .form-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
    .form-section { display: flex; flex-direction: column; gap: 20px; }
    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* ── CHAMPS ── */
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-primary);
    }
    .field .field-hint { font-size: 11px; color: var(--text-muted); margin-top: -2px; }

    .field input[type="text"],
    .field input[type="email"],
    .field input[type="password"] {
        width: 100%; padding: 11px 14px;
        border: 1.5px solid var(--border); border-radius: var(--border-radius-sm);
        font-size: 14px; font-family: 'DM Sans', sans-serif;
        color: var(--text-primary); background: #fafafa; outline: none;
        transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
    }
    .field input:focus {
        border-color: var(--color-primary); background: #fff;
        box-shadow: 0 0 0 3px rgba(224,32,32,.08);
    }
    .field input::placeholder { color: var(--text-muted); }

    /* Champ mot de passe avec bouton toggle */
    .pw-wrap { position: relative; }
    .pw-wrap input { padding-right: 42px; }
    .pw-toggle {
        position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
        background: none; border: none; cursor: pointer;
        color: var(--text-muted); font-size: 13px; padding: 2px;
        transition: color var(--transition);
    }
    .pw-toggle:hover { color: var(--color-primary); }

    /* Indicateur force mot de passe */
    .pw-strength { display: flex; flex-direction: column; gap: 5px; }
    .pw-strength-bar {
        display: flex; gap: 3px; height: 3px;
    }
    .pw-bar-seg {
        flex: 1; border-radius: 2px; background: var(--border);
        transition: background .3s;
    }
    .pw-bar-seg.filled-weak   { background: var(--color-primary); }
    .pw-bar-seg.filled-medium { background: #f59e0b; }
    .pw-bar-seg.filled-strong { background: #10b981; }
    .pw-strength-label { font-size: 11px; color: var(--text-muted); }

    /* Erreurs */
    .field-error {
        font-size: 12px; color: var(--color-primary);
        display: flex; align-items: center; gap: 4px;
    }

    /* ── SÉLECTION RÔLES ── */
    .roles-required-note {
        font-size: 11px; color: var(--color-primary);
        display: flex; align-items: center; gap: 4px; margin-bottom: 8px;
    }
    .roles-select-list {
        border: 1.5px solid var(--border); border-radius: var(--border-radius-sm);
        overflow: hidden; background: #fafafa;
    }
    .role-select-item {
        display: flex; align-items: center; gap: 10px;
        padding: 11px 14px; cursor: pointer;
        border-bottom: 1px solid var(--border);
        transition: background var(--transition);
    }
    .role-select-item:last-child { border-bottom: none; }
    .role-select-item:hover { background: #fff; }
    .role-select-item input[type="checkbox"] {
        width: 15px; height: 15px; accent-color: var(--color-primary);
        cursor: pointer; flex-shrink: 0;
    }
    .role-select-label { flex: 1; font-size: 13px; color: var(--text-secondary); cursor: pointer; }
    .role-select-sub   { font-size: 11px; color: var(--text-muted); }

    /* Erreur globale sur les rôles */
    .roles-error {
        font-size: 12px; color: var(--color-primary);
        display: flex; align-items: center; gap: 4px; margin-top: 6px;
    }

    /* ── SIDEBAR ── */
    .sidebar-col { display: flex; flex-direction: column; gap: 16px; }

    .preview-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: var(--border-radius); overflow: hidden; box-shadow: var(--shadow-sm);
    }
    .preview-card-header {
        background: var(--color-dark); padding: 14px 18px;
        display: flex; align-items: center; gap: 10px;
    }
    .preview-avatar-lg {
        width: 42px; height: 42px; border-radius: 50%;
        background: rgba(224,32,32,.2); border: 2px solid rgba(224,32,32,.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 800; color: var(--color-primary);
        font-family: 'JetBrains Mono', monospace; flex-shrink: 0;
        transition: background .2s;
    }
    .preview-card-info {}
    .preview-card-label { font-size: 13px; font-weight: 700; color: #fff; }
    .preview-card-sub { font-size: 11px; color: #666; margin-top: 1px; }

    .preview-card-body { padding: 16px 18px; display: flex; flex-direction: column; gap: 12px; }
    .prev-row { display: flex; flex-direction: column; gap: 3px; }
    .prev-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-muted); font-weight: 700; }
    .prev-value { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .prev-value.primary { color: var(--color-primary); }
    .prev-value.muted { font-weight: 400; color: var(--text-muted); font-size: 12px; }
    .prev-roles-list { display: flex; flex-wrap: wrap; gap: 5px; min-height: 22px; }
    .prev-role-tag {
        font-size: 10px; font-weight: 600; padding: 3px 8px;
        background: var(--color-primary-dim); color: var(--color-primary);
        border-radius: 4px; text-transform: uppercase; letter-spacing: 0.3px;
    }

    .action-card {
        background: var(--bg-card); border: 1px solid var(--border);
        border-radius: var(--border-radius); padding: 18px;
        display: flex; flex-direction: column; gap: 10px; box-shadow: var(--shadow-sm);
    }
    .btn-submit {
        width: 100%; padding: 13px; background: var(--color-dark); color: #fff;
        border: none; border-radius: var(--border-radius-sm);
        font-size: 14px; font-weight: 700; font-family: 'DM Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        transition: background var(--transition), transform var(--transition);
    }
    .btn-submit:hover { background: var(--color-primary); transform: translateY(-1px); }

    .btn-cancel {
        width: 100%; padding: 11px; background: transparent; color: var(--text-secondary);
        border: 1.5px solid var(--border); border-radius: var(--border-radius-sm);
        font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
        text-decoration: none; transition: border-color var(--transition), color var(--transition);
    }
    .btn-cancel:hover { border-color: var(--color-primary); color: var(--color-primary); }

    .info-box {
        background: var(--bg-body); border: 1px solid var(--border);
        border-left: 3px solid var(--color-primary);
        border-radius: var(--border-radius-sm); padding: 12px 14px;
        font-size: 12px; color: var(--text-secondary); line-height: 1.6;
    }
    .info-box strong { color: var(--text-primary); }

    /* Checklist règles mot de passe */
    .pw-rules { display: flex; flex-direction: column; gap: 4px; }
    .pw-rule {
        display: flex; align-items: center; gap: 6px;
        font-size: 11px; color: var(--text-muted);
        transition: color .2s;
    }
    .pw-rule i { width: 12px; font-size: 10px; }
    .pw-rule.valid { color: #10b981; }
    .pw-rule.valid i::before { content: '\f00c'; } /* fa-check */

    /* Séparateur section */
    .section-divider {
        height: 1px; background: var(--border); margin: 8px 0;
    }

    @media (max-width: 960px) {
        .form-layout { grid-template-columns: 1fr; }
        .sidebar-col { order: -1; }
        .field-row { grid-template-columns: 1fr; }
    }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('users.index') }}">users</a>
    <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
    <span>Nouvel utilisateur</span>
</div>

<form method="POST" action="{{ route('createUser') }}" id="userForm">
@csrf

<div class="form-layout">

    {{-- ══ COLONNE PRINCIPALE ══ --}}
    <div class="form-section">

        {{-- ── Informations personnelles ── --}}
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fa-solid fa-user" style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                    Informations personnelles
                </h2>
            </div>

            {{-- Nom --}}
            <div class="field" style="margin-bottom:16px">
                <label for="name">
                    Nom complet <span style="color:var(--color-primary)">*</span>
                </label>
                <p class="field-hint">Prénom et nom de l'utilisateur</p>
                <input
                    type="text" id="name" name="name"
                    value="{{ old('name') }}"
                    placeholder="Ex : Ahmed Benali"
                    required maxlength="255" autocomplete="name"
                >
                @error('name')
                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                @enderror
            </div>

            {{-- Email --}}
            <div class="field">
                <label for="email">
                    Adresse e-mail <span style="color:var(--color-primary)">*</span>
                </label>
                <p class="field-hint">Doit être unique · sera utilisée pour la connexion</p>
                <input
                    type="email" id="email" name="email"
                    value="{{ old('email') }}"
                    placeholder="ahmed@obtrans.ma"
                    required maxlength="255" autocomplete="email"
                >
                @error('email')
                    <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- ── Mot de passe ── --}}
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fa-solid fa-lock" style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                    Mot de passe
                </h2>
            </div>

            <div class="field-row">

                {{-- Password --}}
                <div class="field">
                    <label for="password">
                        Mot de passe <span style="color:var(--color-primary)">*</span>
                    </label>
                    <p class="field-hint">8 caractères minimum</p>
                    <div class="pw-wrap">
                        <input
                            type="password" id="password" name="password"
                            placeholder="••••••••"
                            required autocomplete="new-password"
                        >
                        <button type="button" class="pw-toggle" onclick="togglePw('password', this)">
                            <i class="fa-solid fa-eye" id="pwIcon1"></i>
                        </button>
                    </div>
                    {{-- Barre de force --}}
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
                        <span class="field-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div class="field">
                    <label for="password_confirmation">
                        Confirmer <span style="color:var(--color-primary)">*</span>
                    </label>
                    <p class="field-hint">Répétez le mot de passe</p>
                    <div class="pw-wrap">
                        <input
                            type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="••••••••"
                            required autocomplete="new-password"
                        >
                        <button type="button" class="pw-toggle" onclick="togglePw('password_confirmation', this)">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    {{-- Indicateur correspondance --}}
                    <span id="pwMatch" style="font-size:11px;display:none"></span>
                </div>

            </div>

            {{-- Règles --}}
            <div class="section-divider"></div>
            <div class="pw-rules" id="pwRules">
                <div class="pw-rule" id="rule-len">
                    <i class="fa-regular fa-circle"></i> Au moins 8 caractères
                </div>
                <div class="pw-rule" id="rule-upper">
                    <i class="fa-regular fa-circle"></i> Une lettre majuscule
                </div>
                <div class="pw-rule" id="rule-digit">
                    <i class="fa-regular fa-circle"></i> Un chiffre
                </div>
                <div class="pw-rule" id="rule-special">
                    <i class="fa-regular fa-circle"></i> Un caractère spécial
                </div>
            </div>
        </div>

        {{-- ── Rôles (REQUIRED) ── --}}
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fa-solid fa-shield-halved" style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                    Rôles <span style="color:var(--color-primary)">*</span>
                </h2>
            </div>

            <p class="roles-required-note">
                <i class="fa-solid fa-circle-exclamation"></i>
                Au moins un rôle est obligatoire.
            </p>

            @if(isset($roles) && $roles->count() > 0)
                <div class="roles-select-list">
                    @foreach($roles as $role)
                        <label class="role-select-item">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                class="role-checkbox"
                            >
                            <div style="flex:1">
                                <div class="role-select-label">{{ $role->name }}</div>
                                @if($role->description)
                                    <div class="role-select-sub">{{ $role->description }}</div>
                                @endif
                            </div>
                            <span style="font-size:11px;color:var(--text-muted)">
                                {{ $role->permissions_count ?? $role->permissions->count() ?? 0 }} permission(s)
                            </span>
                        </label>
                    @endforeach
                </div>
            @else
                <p style="font-size:13px;color:var(--text-muted);font-style:italic">
                    Aucun rôle disponible.
                    <a href="{{ route('roles.create') }}" style="color:var(--color-primary)">Créer un rôle</a>
                </p>
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

        {{-- Aperçu utilisateur --}}
        <div class="preview-card">
            <div class="preview-card-header">
                <div class="preview-avatar-lg" id="prevAvatar">?</div>
                <div class="preview-card-info">
                    <div class="preview-card-label" id="prevName">Nouvel utilisateur</div>
                    <div class="preview-card-sub" id="prevEmail">email@exemple.com</div>
                </div>
            </div>
            <div class="preview-card-body">
                <div class="prev-row">
                    <div class="prev-label">Nom complet</div>
                    <div class="prev-value primary" id="prevNameBody">—</div>
                </div>
                <div class="prev-row">
                    <div class="prev-label">Adresse e-mail</div>
                    <div class="prev-value" id="prevEmailBody" style="font-size:12px;word-break:break-all">—</div>
                </div>
                <div class="prev-row">
                    <div class="prev-label">Rôles assignés</div>
                    <div class="prev-roles-list" id="prevRoles">
                        <span style="font-size:12px;color:var(--text-muted);font-style:italic">Aucun sélectionné</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="action-card">
            <button type="submit" class="btn-submit" form="userForm">
                <i class="fa-solid fa-user-plus"></i>
                Créer l'utilisateur
            </button>
            <a href="{{ route('users.index') }}" class="btn-cancel">
                <i class="fa-solid fa-xmark"></i>
                Annuler
            </a>
        </div>

        <div class="info-box">
            <strong>Email de bienvenue :</strong> L'utilisateur peut se connecter immédiatement avec les identifiants définis.
            Partagez-lui son mot de passe de manière sécurisée.
        </div>

    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var nameInput    = document.getElementById('name');
    var emailInput   = document.getElementById('email');
    var pwInput      = document.getElementById('password');
    var pwConfirm    = document.getElementById('password_confirmation');

    // ── APERÇU ────────────────────────────────────────────
    function updatePreview() {
        var name  = nameInput.value.trim();
        var email = emailInput.value.trim();

        // Avatar initiales
        var initials = '?';
        if (name) {
            var parts = name.split(' ').filter(Boolean);
            initials  = parts.length >= 2
                ? (parts[0][0] + parts[1][0]).toUpperCase()
                : name.substring(0, 2).toUpperCase();
        }
        document.getElementById('prevAvatar').textContent  = initials;
        document.getElementById('prevName').textContent    = name || 'Nouvel utilisateur';
        document.getElementById('prevEmail').textContent   = email || 'email@exemple.com';
        document.getElementById('prevNameBody').textContent  = name || '—';
        document.getElementById('prevEmailBody').textContent = email || '—';

        // Rôles
        var checked = document.querySelectorAll('.role-checkbox:checked');
        var prevRoles = document.getElementById('prevRoles');
        prevRoles.innerHTML = '';
        if (checked.length === 0) {
            prevRoles.innerHTML = '<span style="font-size:12px;color:var(--text-muted);font-style:italic">Aucun sélectionné</span>';
        } else {
            checked.forEach(function (cb) {
                var label = cb.closest('.role-select-item').querySelector('.role-select-label').textContent.trim();
                var tag   = document.createElement('span');
                tag.className   = 'prev-role-tag';
                tag.textContent = label;
                prevRoles.appendChild(tag);
            });
        }
    }

    nameInput.addEventListener('input', updatePreview);
    emailInput.addEventListener('input', updatePreview);
    document.querySelectorAll('.role-checkbox').forEach(function (cb) {
        cb.addEventListener('change', updatePreview);
    });

    // ── FORCE MOT DE PASSE ────────────────────────────────
    pwInput.addEventListener('input', function () {
        var val = this.value;

        var rules = {
            len:     val.length >= 8,
            upper:   /[A-Z]/.test(val),
            digit:   /[0-9]/.test(val),
            special: /[^A-Za-z0-9]/.test(val),
        };

        // Checklist
        Object.keys(rules).forEach(function (key) {
            var el = document.getElementById('rule-' + key);
            if (!el) return;
            el.classList.toggle('valid', rules[key]);
        });

        // Barre de force
        var score = Object.values(rules).filter(Boolean).length;
        var strengthEl = document.getElementById('pwStrength');
        var label      = document.getElementById('pwStrengthLabel');
        var segs       = ['seg1','seg2','seg3','seg4'];
        var cls        = score <= 1 ? 'filled-weak' : score <= 2 ? 'filled-medium' : 'filled-strong';
        var labelText  = score <= 1 ? 'Faible' : score <= 2 ? 'Moyen' : score === 3 ? 'Bon' : 'Fort';

        strengthEl.style.display = val.length > 0 ? 'flex' : 'none';
        label.textContent = labelText;
        label.style.color = score <= 1 ? 'var(--color-primary)' : score <= 2 ? '#f59e0b' : '#10b981';

        segs.forEach(function (id, i) {
            var seg = document.getElementById(id);
            seg.className = 'pw-bar-seg';
            if (i < score) seg.classList.add(cls);
        });

        checkMatch();
    });

    // ── CORRESPONDANCE ────────────────────────────────────
    function checkMatch() {
        var matchEl = document.getElementById('pwMatch');
        var pw      = pwInput.value;
        var conf    = pwConfirm.value;
        if (!conf) { matchEl.style.display = 'none'; return; }
        matchEl.style.display = 'inline';
        if (pw === conf) {
            matchEl.textContent = '✓ Les mots de passe correspondent';
            matchEl.style.color = '#10b981';
        } else {
            matchEl.textContent = '✗ Les mots de passe ne correspondent pas';
            matchEl.style.color = 'var(--color-primary)';
        }
    }

    pwConfirm.addEventListener('input', checkMatch);
});

// ── TOGGLE VISIBILITÉ MOT DE PASSE ────────────────────
function togglePw(fieldId, btn) {
    var input = document.getElementById(fieldId);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush