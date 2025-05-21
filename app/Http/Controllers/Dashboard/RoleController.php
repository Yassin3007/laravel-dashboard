<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{


    public function index()
    {
        $roles = Role::with('permissions')->latest()->paginate(10);

        return view('dashboard.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('_', $permission->name)[1] ?? 'general';
        });

        return view('dashboard.roles.create', compact('permissions'));
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function show(Role $role)
    {
        $role->load('permissions');
        $groupedPermissions = $role->permissions->groupBy(function($permission) {
            return explode('_', $permission->name)[1] ?? 'general';
        });

        return view('dashboard.roles.show', compact('role', 'groupedPermissions'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('_', $permission->name)[1] ?? 'general';
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('dashboard.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(RoleRequest $request, Role $role)
    {
//        return $request ;
        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name ?? 'web'
        ]);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->syncPermissions($permissions);
        }
//        else {
//            $role->syncPermissions([]);
//        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that has users assigned to it.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
