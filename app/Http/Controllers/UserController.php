<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')
            ->latest()
            ->paginate(10);

        // 2. Récupération de tous les rôles pour le filtre
        $allRoles = Role::all();

        // 3. Calcul des statistiques (KPI)
        $stats = [
            'total'   => User::count(),
            // Actif si email vérifié (ou selon ta propre logique métier)
            'actifs'  => User::whereNotNull('email_verified_at')->count(),
            'inactifs' => User::whereNull('email_verified_at')->count(),
            // Compte les utilisateurs ayant le rôle 'admin' via la table pivot de Spatie
            'admins'  => User::role('super_admin')->count(),
        ];
        return view('users.index', compact('stats', 'allRoles', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('roles.permissions')->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoleIds = $user->roles->pluck('id')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoleIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validation
        $request->validate([
            'name'     => 'required|string|max:255',
            // Note l'utilisation des guillemets doubles pour injecter l'ID de l'utilisateur
            'email'    => "required|string|lowercase|email|max:255|unique:users,email,{$user->id}",
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,id'
        ]);

        // 2. Mise à jour des infos de base
        $user->name = $request->name;
        $user->email = $request->email;

        // 3. Gestion intelligente du mot de passe
        // On ne change le mot de passe QUE s'il a été saisi dans le formulaire
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // 4. Synchronisation des rôles (Spatie)
        // syncRoles remplace les anciens rôles par les nouveaux
        if ($request->has('roles')) {
            // Conversion des IDs en entiers si nécessaire
            $roleIds = array_map('intval', $request->roles);
            $user->syncRoles($roleIds);
        }
        Alert::success("L'utilisateur {$user->name} a été mis à jour avec succès.");
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            Alert::error('Vous ne pouvez pas supprimer votre propre compte.');
            return redirect()->back();
        }

        try {
            // Détacher les rôles avant la suppression (Spatie le fait souvent auto, mais c'est plus propre)
            $user->roles()->detach();

            $user->delete();
            Alert::success("L'utilisateur {$user->name} a été supprimé avec succès.");
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            Alert::error("Une erreur est survenue lors de la suppression.");
            return redirect()->back();
        }
    }
}
