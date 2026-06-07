<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Formateur;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (guard 'web' for admin/formateur users)
        Permission::firstOrCreate(['name' => 'manage_queue', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'view_sessions', 'guard_name' => 'web']);

        // Create roles
        $roleFormateur = Role::firstOrCreate(['name' => 'formateur', 'guard_name' => 'web']);
        $roleFormateur->givePermissionTo(['manage_queue', 'view_sessions']);

        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleAdmin->givePermissionTo('view_sessions');

        // Get all user_ids that have a formateur entry
        // Since Formateur table is deleted, we consider User ID 1 as admin, the rest as formateurs
        foreach (User::all() as $user) {
            if ($user->id === 1) {
                // This user is an admin
                $user->syncRoles('admin');
            } else {
                // This user is a formateur
                $user->syncRoles('formateur');
            }
        }
    }
}

