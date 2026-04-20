{{--
|--------------------------------------------------------------------------
| LOGIN PAGE — OBTRANS TMS
|--------------------------------------------------------------------------
| Garde tous les composants Blade originaux :
|   <x-guest-layout>, <x-auth-session-status>, <x-input-label>,
|   <x-text-input>, <x-input-error>, <x-primary-button>
|
| Seul le style et la structure visuelle sont redesignés.
|--}}

<x-guest-layout>

{{-- ══════════════════════════════════════════
     STYLES INLINE (spécifiques à la page login)
     Pour déplacer dans app.css : couper tout ce bloc <style>
     et le coller à la fin de public/css/app.css
══════════════════════════════════════════ --}}


{{-- Google Fonts (si pas déjà dans le layout guest) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="login-wrapper">

    {{-- ══ PANNEAU GAUCHE ══ --}}
    <div class="login-panel">

        {{-- Logo --}}
        <div class="panel-logo">
            <div class="panel-logo-mark">OB</div>
            <div>
                <div class="panel-logo-text">OBTRANS</div>
                <div class="panel-logo-sub">Transport Management System</div>
            </div>
        </div>

        {{-- Tagline --}}
        <div class="panel-tagline">
            <h1>Gérez votre<br>flotte avec<br><span>précision.</span></h1>
            <p>Plateforme complète de gestion du transport — véhicules, chauffeurs, commandes et facturation en un seul endroit.</p>
        </div>

        {{-- Stats --}}
        <div class="panel-stats">
            <div class="panel-stat">
                <strong>102</strong>
                <span>Véhicules</span>
            </div>
            <div class="panel-stat">
                <strong>106</strong>
                <span>Chauffeurs</span>
            </div>
            <div class="panel-stat">
                <strong>90%</strong>
                <span>Affectation</span>
            </div>
        </div>

    </div>

    {{-- ══ COLONNE FORMULAIRE ══ --}}
    <div class="login-form-col">
        <div class="login-form-inner">

            {{-- En-tête --}}
            <div class="form-heading">
                <div class="form-heading-label">Espace sécurisé</div>
                <h2>Connexion</h2>
                <p>Entrez vos identifiants pour accéder au tableau de bord.</p>
            </div>

            {{-- ── Session Status (composant original conservé) ── --}}
            <x-auth-session-status class="auth-session-status mb-4" :status="session('status')" />

            {{-- ── Formulaire (structure originale conservée) ── --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="form-field">
                    <x-input-label for="email" :value="__('Adresse e-mail')" />
                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="vous@exemple.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- Mot de passe --}}
                <div class="form-field">
                    <x-input-label for="password" :value="__('Mot de passe')" />
                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- Se souvenir de moi --}}
                <label class="remember-row" for="remember_me">
                    <input id="remember_me" type="checkbox" name="remember">
                    <span>{{ __('Se souvenir de moi') }}</span>
                </label>

                {{-- Actions --}}
                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            {{ __('Mot de passe oublié ?') }}
                        </a>
                    @endif

                    <x-primary-button>
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        {{ __('Se connecter') }}
                    </x-primary-button>
                </div>

            </form>

            {{-- Footer --}}
            <div class="form-footer">
                ©  OBTRANS — Tous droits réservés
            </div>

        </div>
    </div>

</div>

</x-guest-layout>