@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Permissions Management</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Permissions
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Table head options start -->
                <div class="row">
                    <div class="col-xs-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Permissions List</h4>
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
                                    @can('create_permission')
                                    <a href="{{ route('permissions.create') }}" class="btn btn-primary mb-1">
                                        <i class="icon-plus2"></i> Add New Permission
                                    </a>
                                    @endcan
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                <th>Guard Name</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($permissions as $permission)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $permission->name }}</td>
                <td>{{ $permission->guard_name }}</td>
                                                <td>
                                                    @can('view_permission')
                                                    <a href="{{ route('permissions.show', $permission->id) }}" class="btn btn-info btn-sm">
                                                        <i class="icon-eye6"></i> View
                                                    </a>
                                                    @endcan

                                                    @can('edit_permission')
                                                    <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-warning btn-sm">
                                                        <i class="icon-pencil3"></i> Edit
                                                    </a>
                                                    @endcan

                                                    @can('delete_permission')
                                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this Permission?');">
                                                            <i class="icon-trash4"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ 2 + count(Schema::getColumnListing('permissions')) }}" class="text-center">No Permissions found.</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                    {{$permissions->links()}}

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- Table head options end -->
            </div>
        </div>
    </div>
@endsection
