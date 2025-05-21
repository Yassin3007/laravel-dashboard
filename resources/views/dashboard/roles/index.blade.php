@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Roles Management</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Roles</li>
                        </ol>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible mb-2" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible mb-2" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Error!</strong> {{ session('error') }}
                    </div>
                @endif

                <!-- Table head options start -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Roles List</h4>
                                <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="icon-minus4"></i></a></li>
                                        <li><a data-action="reload"><i class="icon-reload"></i></a></li>
                                        <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                                        <li><a data-action="close"><i class="icon-cross2"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body collapse in">
                                <div class="card-block card-dashboard">
                                    @can('create_role')
                                        <a href="{{ route('roles.create') }}" class="btn btn-primary mb-1">
                                            <i class="icon-plus2"></i> Add New Role
                                        </a>
                                    @endcan
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Guard Name</th>
                                            <th>Permissions Count</th>
                                            <th>Users Count</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($roles as $role)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration + ($roles->currentPage() - 1) * $roles->perPage() }}</th>
                                                <td>
                                                    <strong>{{ $role->name }}</strong>
                                                    @if($role->name === 'Super Admin')
                                                        <span class="tag tag-primary tag-sm ml-1">System</span>
                                                    @endif
                                                </td>
                                                <td>{{ $role->guard_name }}</td>
                                                <td>
                                                    <span class="tag tag-info">{{ $role->permissions->count() }} permissions</span>
                                                </td>
                                                <td>
                                                    <span class="tag tag-primary">{{ $role->users->count() }} users</span>
                                                </td>
                                                <td>{{ $role->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        @can('view_role')
                                                            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info btn-sm" title="View Details">
                                                                <i class="icon-eye6"></i>
                                                            </a>
                                                        @endcan

                                                        @can('edit_role')
                                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm" title="Edit Role">
                                                                <i class="icon-pencil3"></i>
                                                            </a>
                                                        @endcan

                                                        @can('delete_role')
                                                            @if($role->name !== 'Super Admin' && $role->users->count() === 0)
                                                                <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline-block;" class="delete-form">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="btn btn-danger btn-sm delete-btn" title="Delete Role">
                                                                        <i class="icon-trash4"></i>
                                                                    </button>
                                                                </form>
                                                            @elseif($role->users->count() > 0)
                                                                <button class="btn btn-secondary btn-sm" disabled title="Cannot delete role with assigned users">
                                                                    <i class="icon-lock4"></i>
                                                                </button>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="icon-info22 font-large-1"></i>
                                                    <p class="mt-1">No roles found.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                @if($roles->hasPages())
                                    <div class="row">
                                        <div class="col-md-12">
                                            <nav aria-label="Page navigation">
                                                {{ $roles->links() }}
                                            </nav>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table head options end -->
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();

                const form = $(this).closest('.delete-form');
                const roleName = $(this).closest('tr').find('td:first strong').text();

                if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
                    form.submit();
                }
            });
        });
    </script>
@endsection
