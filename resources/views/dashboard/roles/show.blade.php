@extends('dashboard.layouts.master')

@section('content')
    <!-- Content section -->
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Role Details</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role Management</a></li>
                            <li class="breadcrumb-item active">{{ $role->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Role Information Card -->
                <section id="role-details">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="icon-user6"></i> Role Information
                                    </h4>
                                    <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary"><i class="icon-pencil"></i> Edit</a></li>
                                            <li><a href="{{ route('roles.index') }}" class="btn btn-sm btn-secondary"><i class="icon-arrow-left4"></i> Back</a></li>
                                            <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                            <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <tbody>
                                                <tr>
                                                    <th width="200"><i class="icon-hash"></i> ID</th>
                                                    <td><span class="badge badge-info">{{ $role->id }}</span></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="icon-user"></i> Role Name</th>
                                                    <td><strong class="text-primary">{{ $role->name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th><i class="icon-shield"></i> Guard Name</th>
                                                    <td>
                                                        <span class="badge badge-{{ $role->guard_name === 'web' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($role->guard_name) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="icon-users"></i> Users Count</th>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $role->users->count() }} Users</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="icon-lock4"></i> Permissions Count</th>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $role->permissions->count() }} Permissions</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="icon-calendar"></i> Created At</th>
                                                    <td>
                                                        <span class="text-muted">{{ $role->created_at->format('M d, Y - H:i:s') }}</span>
                                                        <small class="text-muted d-block">{{ $role->created_at->diffForHumans() }}</small>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th><i class="icon-edit"></i> Updated At</th>
                                                    <td>
                                                        <span class="text-muted">{{ $role->updated_at->format('M d, Y - H:i:s') }}</span>
                                                        <small class="text-muted d-block">{{ $role->updated_at->diffForHumans() }}</small>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats Card -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="icon-bar-chart"></i> Quick Stats
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <div class="media">
                                                <div class="media-left media-middle">
                                                    <i class="icon-users font-large-1 text-primary"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading text-bold-600">{{ $role->users->count() }}</h4>
                                                    <span class="text-muted">Assigned Users</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <div class="media">
                                                <div class="media-left media-middle">
                                                    <i class="icon-lock4 font-large-1 text-success"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading text-bold-600">{{ $role->permissions->count() }}</h4>
                                                    <span class="text-muted">Total Permissions</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="media">
                                                <div class="media-left media-middle">
                                                    <i class="icon-folder-open font-large-1 text-warning"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h4 class="media-heading text-bold-600">{{ $groupedPermissions->count() }}</h4>
                                                    <span class="text-muted">Permission Modules</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Permissions Section -->
                <section id="role-permissions" class="mt-2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        <i class="icon-lock4"></i> Assigned Permissions
                                        <span class="badge badge-primary ml-1">{{ $role->permissions->count() }}</span>
                                    </h4>
                                    <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-primary"><i class="icon-edit"></i> Manage Permissions</a></li>
                                            <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                            <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card-body collapse in">
                                    <div class="card-block">
                                        @if($role->permissions->count() > 0)
                                            @foreach($groupedPermissions as $module => $permissions)
                                                <div class="permission-module mb-3">
                                                    <h5 class="text-capitalize text-primary mb-2">
                                                        <i class="icon-folder-open"></i> {{ str_replace('_', ' ', $module) }} Module
                                                        <span class="badge badge-outline-primary">{{ $permissions->count() }} permissions</span>
                                                    </h5>
                                                    <div class="row">
                                                        @foreach($permissions as $permission)
                                                            <div class="col-md-3 col-sm-6 col-xs-12 mb-1">
                                                                <div class="permission-item">
                                                                    <span class="badge badge-success">
                                                                        <i class="icon-check2"></i>
                                                                        {{ ucfirst(str_replace('_', ' ', str_replace($module.'_', '', $permission->name))) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    @if(!$loop->last)
                                                        <hr class="my-2">
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <i class="icon-info22 font-large-2"></i>
                                                <h5 class="mt-2">No Permissions Assigned</h5>
                                                <p>This role currently has no permissions assigned.</p>
                                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">
                                                    <i class="icon-plus"></i> Assign Permissions
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Assigned Users Section -->
                @if($role->users->count() > 0)
                    <section id="assigned-users" class="mt-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <i class="icon-users"></i> Assigned Users
                                            <span class="badge badge-secondary ml-1">{{ $role->users->count() }}</span>
                                        </h4>
                                        <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                                        <div class="heading-elements">
                                            <ul class="list-inline mb-0">
                                                <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                                <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body collapse in">
                                        <div class="card-block">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered">
                                                    <thead class="thead-light">
                                                    <tr>
                                                        <th><i class="icon-hash"></i> ID</th>
                                                        <th><i class="icon-user"></i> Name</th>
                                                        <th><i class="icon-mail"></i> Email</th>
                                                        <th><i class="icon-calendar"></i> Assigned Date</th>
                                                        <th><i class="icon-settings"></i> Actions</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($role->users as $user)
                                                        <tr>
                                                            <td><span class="badge badge-info">{{ $user->id }}</span></td>
                                                            <td>
                                                                <div class="media">
                                                                    <div class="media-left">
                                                                        <div class="avatar avatar-sm">
                                                                            <img src="{{ $user->image_url }}" alt="Avatar" class="media-object rounded-circle">
                                                                        </div>
                                                                    </div>
                                                                    <div class="media-body media-middle">
                                                                        <span class="media-heading">{{ $user->name }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                <span class="text-muted">{{ $user->created_at->format('M d, Y') }}</span>
                                                            </td>
                                                            <td>
                                                                @if(Route::has('users.show'))
                                                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info">
                                                                        <i class="icon-eye"></i> View
                                                                    </a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                <!-- Action Buttons -->
                <section id="role-actions" class="mt-2">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">
                                                <i class="icon-arrow-left4"></i> Back to Roles
                                            </a>
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">
                                                <i class="icon-pencil"></i> Edit Role
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            @if($role->name !== 'Super Admin')
                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="delete-form d-inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger delete-btn">
                                                        <i class="icon-trash"></i> Delete Role
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-muted">
                                                    <i class="icon-info"></i> Super Admin role cannot be deleted
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            // Delete confirmation
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();

                const form = $(this).closest('form');
                const roleName = '{{ $role->name }}';

                // Enhanced confirmation dialog
                if (confirm(`Are you sure you want to delete the role "${roleName}"?\n\nThis action cannot be undone and will remove all permissions associated with this role.`)) {
                    form.submit();
                }
            });

            // Tooltip initialization (if using Bootstrap tooltips)
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection
