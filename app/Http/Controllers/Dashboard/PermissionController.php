<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        $permissions = Permission::latest()->paginate(10);

        return view('dashboard.permissions.index', compact('permissions'));
    }


    public function create()
    {
        return view('dashboard.permissions.create');
    }


    public function store(PermissionRequest $request)
    {
        $permission = Permission::create($request->validated());

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission created successfully.');
    }


    public function show(Permission $permission)
    {
        return view('dashboard.permissions.show', compact('permission'));
    }


    public function edit(Permission $permission)
    {
        return view('dashboard.permissions.edit', compact('permission'));
    }


    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->update($request->validated());

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }


    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('dashboard.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
