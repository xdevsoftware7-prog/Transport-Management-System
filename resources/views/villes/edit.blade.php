{{--
|--------------------------------------------------------------------------
| PAGE : MODIFIER UNE VILLE — OBTRANS TMS
|--------------------------------------------------------------------------
| CHAMPS :
|   - nom  required|string|max:100|unique:villes,nom,{ville->id}
|
| VARIABLES ATTENDUES :
|   - $ville : App\Models\Ville
|
| ROUTE : PUT /villes/{ville} → VilleController@update
|--}}

@extends('layouts.app')

@section('title', 'Modifier — ' . $ville->nom)
@section('page-title', 'Modifier la Ville')
@section('page-subtitle', 'Mettre à jour le nom de la ville dans le référentiel')

@section('content')

<style>
    .breadcrumb {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; color: var(--text-muted); margin-bottom: 4px;
    }
    .breadcrumb a { color: var(--text-muted); transition: color var(--transition); }
    .breadcrumb a:hover { color: var(--color-primary); }
    .breadcrumb-sep { font-size: 10px; }

    /* Bandeau édition */
    .edit-banner {
        display: flex; align-items: center; gap: 14px;
        background: var(--color-dark); border-radius: var(--border-radius);
        padding: 14px 22px; margin-bottom: 4px;
    }
    .edit-banner-icon {
        width: 38px; height: 38px; background: rgba(224,32,32,.15);
        border-radius: 9px; display: flex; align-items: center;
        justify-content: center; color: var(--color-primary);
        font-size: 17px; flex-shrink: 0;
    }
    .edit-banner-text strong { display: block; font-size: 14px; font-weight: 700; color: #fff; }
    .edit-banner-text span   { font-size: 12px; color: #666; }
    .edit-banner-meta { margin-left: auto; text-align: right; flex-shrink: 0; }
    .edit-banner-meta .meta-label { font-size: 10px; color: #555; text-transform: uppercase; letter-spacing: 0.5px; }
    .edit-banner-meta .meta-value { font-size: 13px; color: #888; font-weight: 600; }

    /* Layout centré */
    .form-centered {
        max-width: 640px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* Champs */
    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.6px; color: var(--text-primary);
    }
    .field .field-hint { font-size: 11px; color: var(--text-muted); margin-top: -2px; }

    .field-input-wrap { position: relative; }
    .field-input-wrap .field-prefix {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
        font-size: 14px; color: var(--text-muted); pointer-events: none;
    }
    .field-input-wrap input { padding-left: 36px; }

    .field input[type="text"] {
        width: 100%; padding: 12px 14px;
        border: 1.5px solid var(--border); border-radius: var(--border-radius-sm);
        font-size: 15px; font-family: 'DM Sans', sans-serif;
        color: var(--text-primary); background: #fafafa; outline: none;
        transition: border-color var(--transition), box-shadow var(--transition), background var(--transition);
    }
    .field input:focus {
        border-color: var(--color-primary); background: #fff;
        box-shadow: 0 0 0 3px rgba(224,32,32,.08);
    }
    .field input::placeholder { color: var(--text-muted); }

    .field-error {
        font-size: 12px; color: var(--color-primary);
        display: flex; align-items: center; gap: 4px;
    }

    /* Compteur */
    .char-counter { font-size: 11px; color: var(--text-muted); text-align: right; margin-top: -2px; transition: color .2s; }
    .char-counter.warn { color: #f59e0b; }
    .char-counter.over { color: var(--color-primary); }

    /* Indicateur changements */
    .change-indicator {
        display: none; align-items: center; gap: 6px; font-size: 11px;
        color: #d97706; background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.2);
        border-radius: var(--border-radius-sm); padding: 8px 12px;
    }
    .change-indicator.visible { display: flex; }

    /* Aperçu comparatif */
    .compare-wrap {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        margin-bottom: 20px;
    }
    .compare-card {
        padding: 12px 14px; border-radius: var(--border-radius-sm);
        display: flex; align-items: center; gap: 10px;
    }
    .compare-card.before {
        background: var(--bg-body); border: 1px solid var(--border);
    }
    .compare-card.after {
        background: var(--color-primary-dim);
        border: 1px solid rgba(224,32,32,.2);
    }
    .compare-label {
        font-size: 9px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.8px; color: var(--text-muted); margin-bottom: 2px;
    }
    .compare-value {
        font-size: 14px; font-weight: 700;
    }
    .compare-card.before .compare-value { color: var(--text-muted); }
    .compare-card.after  .compare-value { color: var(--color-primary); }
    .compare-icon {
        width: 30px; height: 30px; border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; flex-shrink: 0;
    }
    .compare-card.before .compare-icon { background: var(--border); color: var(--text-muted); }
    .compare-card.after  .compare-icon { background: rgba(224,32,32,.12); color: var(--color-primary); }

    /* Flèche entre les deux */
    .compare-arrow {
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; color: var(--text-muted);
        align-self: center;
    }

    /* Métadonnées */
    .meta-grid {
        display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
    }
    .meta-item {
        padding: 12px 14px; background: var(--bg-body);
        border: 1px solid var(--border); border-radius: var(--border-radius-sm);
    }
    .meta-item-label {
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--text-muted); margin-bottom: 4px;
        display: flex; align-items: center; gap: 5px;
    }
    .meta-item-value {
        font-size: 13px; font-weight: 600; color: var(--text-primary);
    }
    .meta-item-sub { font-size: 11px; color: var(--text-muted); margin-top: 1px; }

    /* Actions */
    .form-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

    .btn-submit {
        padding: 13px 28px; background: var(--color-dark); color: #fff;
        border: none; border-radius: var(--border-radius-sm);
        font-size: 14px; font-weight: 700; font-family: 'DM Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; gap: 8px;
        transition: background var(--transition), transform var(--transition);
    }
    .btn-submit:hover { background: var(--color-primary); transform: translateY(-1px); }

    .btn-cancel {
        padding: 12px 18px; background: transparent; color: var(--text-secondary);
        border: 1.5px solid var(--border); border-radius: var(--border-radius-sm);
        font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; gap: 7px;
        text-decoration: none;
        transition: border-color var(--transition), color var(--transition);
    }
    .btn-cancel:hover { border-color: var(--color-primary); color: var(--color-primary); }

    .btn-delete {
        margin-left: auto;
        padding: 12px 16px; background: transparent; color: var(--color-primary);
        border: 1.5px solid rgba(224,32,32,.25); border-radius: var(--border-radius-sm);
        font-size: 13px; font-weight: 600; font-family: 'DM Sans', sans-serif;
        cursor: pointer; display: flex; align-items: center; gap: 7px;
        transition: background var(--transition), border-color var(--transition);
    }
    .btn-delete:hover { background: rgba(224,32,32,.06); border-color: var(--color-primary); }

    #deleteForm { display: none; }

    .info-box {
        background: var(--bg-body); border: 1px solid var(--border);
        border-left: 3px solid var(--color-primary);
        border-radius: var(--border-radius-sm); padding: 12px 14px;
        font-size: 12px; color: var(--text-secondary); line-height: 1.6;
    }
    .info-box strong { color: var(--text-primary); }

    @media (max-width: 540px) {
        .compare-wrap { grid-template-columns: 1fr; }
        .compare-arrow { display: none; }
        .meta-grid { grid-template-columns: 1fr; }
        .btn-delete { margin-left: 0; }
    }
</style>

{{-- Breadcrumb --}}
<div class="breadcrumb">
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('villes.index') }}">Villes</a>
    <span class="breadcrumb-sep"><i class="fa-solid fa-chevron-right"></i></span>
    <span>Modifier · {{ $ville->nom }}</span>
</div>

{{-- Bandeau édition --}}
<div class="edit-banner">
    <div class="edit-banner-icon"><i class="fa-solid fa-pen-to-square"></i></div>
    <div class="edit-banner-text">
        <strong>Mode édition — {{ $ville->nom }}</strong>
        <span>ID #{{ $ville->id }} · Créée {{ $ville->created_at->diffForHumans() }}</span>
    </div>
    <div class="edit-banner-meta">
        <div class="meta-label">Dernière modif.</div>
        <div class="meta-value">{{ $ville->updated_at->format('d/m/Y') }}</div>
    </div>
</div>

<div class="form-centered">

    <form method="POST" action="{{ route('villes.update', $ville) }}" id="villeForm">
    @csrf
    @method('PUT')

        {{-- ── Formulaire ── --}}
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fa-solid fa-location-dot"
                       style="color:var(--color-primary);margin-right:6px;font-size:14px"></i>
                    Modifier le nom
                </h2>
                <div class="change-indicator" id="changeIndicator">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Modifications non sauvegardées
                </div>
            </div>

            {{-- Comparaison avant / après --}}
            <div class="compare-wrap">
                <div class="compare-card before">
                    <div class="compare-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                    <div>
                        <div class="compare-label">Nom actuel</div>
                        <div class="compare-value">{{ $ville->nom }}</div>
                    </div>
                </div>

                <div class="compare-card after">
                    <div class="compare-icon"><i class="fa-solid fa-arrow-right"></i></div>
                    <div>
                        <div class="compare-label">Nouveau nom</div>
                        <div class="compare-value" id="previewAfter">{{ old('nom', $ville->nom) }}</div>
                    </div>
                </div>
            </div>

            {{-- Champ nom --}}
            <div class="field">
                <label for="nom">
                    Nom de la ville <span style="color:var(--color-primary)">*</span>
                </label>
                <p class="field-hint">100 caractères maximum · Doit être unique</p>
                <div class="field-input-wrap">
                    <i class="fa-solid fa-location-dot field-prefix"></i>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        value="{{ old('nom', $ville->nom) }}"
                        placeholder="Nom de la ville"
                        required maxlength="100" autocomplete="off"
                    >
                </div>
                <div class="char-counter" id="charCounter">
                    {{ strlen(old('nom', $ville->nom)) }} / 100
                </div>
                @error('nom')
                    <span class="field-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </span>
                @enderror
            </div>
        </div>

        {{-- ── Métadonnées (lecture seule) ── --}}
        <div class="section-card">
            <div class="section-header">
                <h2 class="section-title" style="font-size:13px">
                    <i class="fa-solid fa-circle-info"
                       style="color:var(--color-primary);margin-right:5px;font-size:12px"></i>
                    Informations système
                </h2>
            </div>
            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-item-label">
                        <i class="fa-solid fa-hashtag"></i> Identifiant
                    </div>
                    <div class="meta-item-value" style="font-family:'JetBrains Mono',monospace;font-size:14px">
                        #{{ $ville->id }}
                    </div>
                </div>
                <div class="meta-item">
                    <div class="meta-item-label">
                        <i class="fa-solid fa-calendar-plus"></i> Créée le
                    </div>
                    <div class="meta-item-value">{{ $ville->created_at->format('d/m/Y') }}</div>
                    <div class="meta-item-sub">{{ $ville->created_at->format('H:i') }} · {{ $ville->created_at->diffForHumans() }}</div>
                </div>
                <div class="meta-item" style="grid-column:1/-1">
                    <div class="meta-item-label">
                        <i class="fa-solid fa-calendar-pen"></i> Dernière modification
                    </div>
                    <div class="meta-item-value">{{ $ville->updated_at->format('d/m/Y à H:i') }}</div>
                    <div class="meta-item-sub">{{ $ville->updated_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>

        {{-- ── Actions ── --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit" form="villeForm">
                <i class="fa-solid fa-floppy-disk"></i>
                Enregistrer les modifications
            </button>
            <a href="{{ route('villes.index') }}" class="btn-cancel">
                <i class="fa-solid fa-xmark"></i>
                Annuler
            </a>
            <button type="button" class="btn-delete" onclick="handleDeleteVille()">
                <i class="fa-solid fa-trash"></i>
                Supprimer
            </button>
        </div>

        <div class="info-box">
            <strong>Attention :</strong> Si cette ville est utilisée dans des commandes ou des voyages,
            la supprimer peut affecter l'historique existant.
        </div>

    </form>

</div>

{{-- Formulaire suppression --}}
<form id="deleteForm" method="POST" action="{{ route('villes.destroy', $ville) }}">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    var input      = document.getElementById('nom');
    var counter    = document.getElementById('charCounter');
    var indicator  = document.getElementById('changeIndicator');
    var prevAfter  = document.getElementById('previewAfter');
    var original   = '{{ addslashes($ville->nom) }}';
    var maxLen     = 100;

    function update() {
        var val = input.value;
        var len = val.length;

        // Compteur
        counter.textContent = len + ' / ' + maxLen;
        counter.className   = 'char-counter';
        if (len >= maxLen)         counter.classList.add('over');
        else if (len >= maxLen * .8) counter.classList.add('warn');

        // Aperçu après
        prevAfter.textContent = val.trim() || '—';

        // Indicateur changements
        if (val.trim() !== original) {
            indicator.classList.add('visible');
        } else {
            indicator.classList.remove('visible');
        }
    }

    input.addEventListener('input', update);
    update(); // init
});

// function confirmDelete() {
//     var nom = '{{ addslashes($ville->nom) }}';
//     if (confirm('Supprimer la ville « ' + nom + ' » ? Cette action est irréversible.')) {
//         document.getElementById('deleteForm').submit();
//     }
// }
function handleDeleteVille(id, name) {
            Swal.fire({
                title: 'Supprimer la ville ?',
                text: `Êtes-vous sûr de vouloir supprimer cette ville "${name}" ? Cette action est irréversible.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e02020', // Ton rouge OBTRANS
                cancelButtonColor: '#1a1a1a', // Ton gris foncé/noir
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                background: '#111', // Fond sombre pour matcher ton thème
                color: '#fff', // Texte blanc
                customClass: {
                    popup: 'swal-custom-radius'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // On soumet le formulaire correspondant
                    document.getElementById('deleteForm').submit();
                }
            })
        }
</script>
@endpush