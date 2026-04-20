<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount("users")->paginate(3);

        $allRoles = Role::withCount("users")->get();

        $stats = [
            'total' => $allRoles->count(),
            'utilises' => $allRoles->where('users_count', '>', 0)->count(),
            'libres' => $allRoles->where('users_count', '==', 0)->count(),
            'total_users_assignes' => $allRoles->sum('users_count')
        ];

        return view('roles.index', compact('roles', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Récupérer toutes les permissions groupées dynamiquement
        $groupedPermissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });

        // Pour la page create, on passe un tableau vide pour les permissions cochées
        $rolePermissions = [];
        return view('roles.create', compact('groupedPermissions', 'rolePermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'description' => 'nullable',
            'permissions' => 'required|array|min:1'
        ]);

        $role = Role::firstOrCreate([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web'
        ]);

        $role->syncPermissions($request->permissions);
        Alert::success('Succès', 'Le rôle a été créé avec succès!');
        return to_route('roles.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $groupedPermissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });

        // On récupère uniquement les noms des permissions pour la comparaison dans la vue
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.show', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $groupedPermissions = Permission::all()->groupBy(function ($perm) {
            return explode('.', $perm->name)[0];
        });

        // On récupère uniquement les noms des permissions pour la comparaison dans la vue
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'description' => 'nullable',
            'permissions' => 'required|array|min:1'
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => 'web'
        ]);

        $role->syncPermissions($request->permissions);
        Alert::success('Succès', 'Le rôle a été modifier avec succès!');
        return to_route('roles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // 1. Protection contre la suppression de rôles critiques (optionnel)
        $protectedRoles = ['admin', 'super-admin'];
        if (in_array($role->name, $protectedRoles)) {
            Alert::error('Ce rôle système ne peut pas être supprimé');
            return redirect()->back();
        }

        // 2. Vérifier si des utilisateurs possèdent ce rôle
        if ($role->users()->count() > 0) {
            Alert::error('Impossible de supprimer ce rôle car il est assigné à des utilisateurs.');
            return redirect()->back();
        }

        // 3. Suppression
        $role->delete();
        Alert::success('Le rôle a été supprimé avec succès.');
        return to_route('roles.index');
    }
}
