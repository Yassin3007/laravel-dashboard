<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions based on your routes
        $permissions = [
            // Role permissions
            'view_role',
            'create_role',
            'edit_role',
            'delete_role',

            // Permission permissions
            'view_permission',
            'create_permission',
            'edit_permission',
            'delete_permission',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create Admin role
        $adminRole = Role::query()->updateOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Optional: Create other roles with specific permissions
        $userRole = Role::query()->updateOrCreate([
            'name' => 'user',
            'guard_name' => 'web'
        ]);

        // Give user role only view permissions
        $userRole->givePermissionTo([
            'view_role',
            'view_permission'
        ]);

        // Optional: Create a super admin role (if needed)
        $superAdminRole = Role::query()->updateOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web'
        ]);

        // Super admin gets all permissions
        $superAdminRole->givePermissionTo($permissions);

        $this->command->info('Roles and Permissions created successfully!');
        $this->command->info('Created permissions: ' . implode(', ', $permissions));
        $this->command->info('Created roles: admin, user, super-admin');
    }
}
