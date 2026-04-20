<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. On récupère le module depuis l'URL (ex: ?module=chauffeurs)
        $selectedModule = $request->get('module');

        // 2. Construction de la requête avec filtrage dynamique
        $query = Permission::withCount('roles');

        if ($selectedModule) {
            // SQL: WHERE name LIKE 'chauffeurs.%'
            $query->where('name', 'like', $selectedModule . '.%');
        }

        $permissionsPaginées = $query->paginate(20)->withQueryString();

        // 3. Statistiques globales (pour les compteurs du haut)
        $allPermissions = Permission::withCount('roles')->get();

        // 4. Liste complète des modules (pour ton menu de filtrage)
        // On extrait dynamiquement les préfixes uniques de TOUTE la table
        $modules = $allPermissions->map(function ($p) {
            return explode('.', $p->name)[0];
        })->unique()->values();

        $stats = [
            'total' => $allPermissions->count(),
            'utilisees' => $allPermissions->where('roles_count', '>', 0)->count(),
            'orphelines' => $allPermissions->where('roles_count', '==', 0)->count(),
            'moyenne_par_role' => $allPermissions->count() > 0
                ? round($allPermissions->avg('roles_count'), 1)
                : 0
        ];

        // 5. Groupement pour l'affichage (uniquement les résultats de la page)
        $groupedPermissions = $permissionsPaginées->getCollection()->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('permissions.index', [
            'groupedPermissions' => $groupedPermissions,
            'modules' => $modules,
            'selectedModule' => $selectedModule, // Pour garder l'état dans la vue
            'permissions' => $permissionsPaginées,
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allPermissions = Permission::withCount('roles')->get();
        $modules = $allPermissions->map(function ($p) {
            return explode('.', $p->name)[0];
        })->unique()->values();
        return view('permissions.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:permissions,name|max:255',
        ]);

        Permission::create(['name' => strtolower($request->slug), 'guard_name' => 'web']);
        Alert::success('Permission créée avec succès !');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        $allPermissions = Permission::withCount('roles')->get();
        $roles = Role::withCount("users")->get();
        $modules = $allPermissions->map(function ($p) {
            return explode('.', $p->name)[0];
        })->unique()->values();
        return view('permissions.edit', compact('permission', 'modules', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        // dd($request);
        $request->validate([
            'slug' => 'required|max:255|unique:permissions,name,' . $permission->id,
            'roles' => 'nullable|array', // On vérifie que c'est bien un tableau
            'roles.*' => 'exists:roles,id', // On vérifie que chaque ID existe en BDD
        ]);


        $permission->update([
            'name' => strtolower($request->slug),
        ]);


        // Spatie accepte directement un tableau d'IDs ou de noms
        if ($request->has('roles')) {
            // convertir les ids strings en entiers
            $roleIds = array_map('intval', $request->roles);
            $permission->syncRoles($roleIds);
        } else {
            // Si aucun rôle n'est coché, on les retire tous
            $permission->syncRoles([]);
        }

        Alert::success('Permission mise à jour avec succès !');
        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();
        Alert::success('Permission est bien supprimer!');
        return to_route('permissions.index');
    }
}
