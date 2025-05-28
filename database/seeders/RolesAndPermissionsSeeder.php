<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Get all registered routes
        $routes = Route::getRoutes();
        $permissions = [];

        foreach ($routes as $route) {
            // Optional: Filter by route prefix or name pattern
            // if (!str_starts_with($route->getName(), 'admin.')) continue;

            // Get middleware for this route
            $middleware = $route->middleware();

            // Look for 'can:' middleware
            foreach ($middleware as $middlewareItem) {
                if (is_string($middlewareItem) && str_starts_with($middlewareItem, 'can:')) {
                    // Extract permission name after 'can:'
                    $permissionName = substr($middlewareItem, 4);
                    $permissions[] = $permissionName;
                } elseif (is_array($middlewareItem) && isset($middlewareItem[0]) && $middlewareItem[0] === 'can') {
                    // Handle array format: ['can', 'permission_name']
                    if (isset($middlewareItem[1])) {
                        $permissions[] = $middlewareItem[1];
                    }
                }
            }
        }

        // Remove duplicates
        $permissions = array_unique($permissions);

        // Create permissions in database
        $createdPermissions = [];
        foreach ($permissions as $permission) {
            $createdPermissions[] = Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Assign all permissions to Super Admin role
        $superAdminRole->syncPermissions($createdPermissions);

//        // Optional: Create a super admin user
//        $superAdmin = User::firstOrCreate(
//            ['email' => 'admin@example.com'],
//            [
//                'name' => 'Super Admin',
//                'password' => Hash::make('password'), // Change this!
//                'email_verified_at' => now(),
//            ]
//        );

        // Assign super_admin role to the user

        $this->command->info('Created ' . count($permissions) . ' permissions from routes.');
        $this->command->info('Permissions: ' . implode(', ', $permissions));
        $this->command->info('Created Super Admin role with all permissions assigned.');
        $this->command->info('Created Super Admin user: admin@example.com');
    }
}
