<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nettoyer le cache des permissions (très important avec Spatie)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'villes' => ['index', 'show', 'create', 'edit', 'delete'],
            'clients' => ['index', 'show', 'create', 'edit', 'delete'],
            'articles' => ['index', 'show', 'create', 'edit', 'delete'],
            'chauffeurs' => ['index', 'show', 'create', 'edit', 'delete'],
            'chauffeur_permis' => ['index', 'show', 'create', 'edit', 'delete'],
            'vehicules' => ['index', 'show', 'create', 'edit', 'delete'],
            'semi_remorques' => ['index', 'show', 'create', 'edit', 'delete'],
            'trajets' => ['index', 'show', 'create', 'edit', 'delete'],
            'tarif_clients' => ['index', 'show', 'create', 'edit', 'delete'],
            'prime_deplacements' => ['index', 'show', 'create', 'edit', 'delete'],
            'commandes' => ['index', 'show', 'create', 'edit', 'delete'],
            'ligne_commandes' => ['index', 'show', 'create', 'edit', 'delete'],
            'bon_livraisons' => ['index', 'show', 'create', 'edit', 'delete'],
            'factures' => ['index', 'show', 'create', 'edit', 'delete'],
            'document_vehicules' => ['index', 'show', 'create', 'edit', 'delete'],
            'maintenances' => ['index', 'show', 'create', 'edit', 'delete'],
            'infractions' => ['index', 'show', 'create', 'edit', 'delete'],
            'absences' => ['index', 'show', 'create', 'edit', 'delete'],
            'roles' => ['index', 'show', 'create', 'edit', 'delete'],
            'permissions' => ['index', 'show', 'create', 'edit', 'delete'],
            'users' => ['index', 'show', 'create', 'edit', 'delete','register'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$module.$action", 'guard_name' => 'web']);
            }
        }

        $adminRole = Role::firstOrCreate(['name' => 'super_admin', 'description' => 'Accès total au système']);
        $adminRole->syncPermissions(Permission::all());

        $adminUser = User::where('email', 'ayoublamoudenfullstack@gmail.com')->first();

        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }
    }
}
