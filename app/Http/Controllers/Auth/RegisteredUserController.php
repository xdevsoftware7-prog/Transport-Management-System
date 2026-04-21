<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use RealRashid\SweetAlert\Facades\Alert;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation stricte
        // dd($request);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,id'] // Sécurité : vérifie que chaque ID de rôle existe
        ]);

        // 2. Création de l'utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Assignation des rôles (Spatie)
        // On convertit les IDs en entiers pour éviter l'erreur de Guard "1"
        if ($request->has('roles')) {
            $roleIds = array_map('intval', $request->roles);
            $user->assignRole($roleIds);
        }

        // 4. Déclenchement de l'événement standard (facultatif si tu gères tes propres mails)
        event(new Registered($user));

        // 5. ENVOI DE LA NOTIFICATION OBTRANS
        // On passe le mot de passe en clair (non haché) pour que l'utilisateur puisse le lire
        $user->notify(new \App\Notifications\NewUserNotification($user->name, $request->password));

        // IMPORTANT : On retire Auth::login($user) car c'est l'ADMIN qui crée le compte.
        // L'admin doit rester connecté sur sa session.
        Alert::success("L'utilisateur {$user->name} a été créé et ses accès ont été envoyés par Gmail.");
        return redirect(route('users.index'));
    }
}
