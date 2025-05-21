@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Edit Role</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Role Management</a></li>
                            <li class="breadcrumb-item active">Edit Role</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-tooltip">Edit Role: {{ $role->name }}</h4>
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
                                    <div class="card-block">
                                        <div class="card-text">
                                            <p>Update role information and modify permissions to control access to different parts of the system.</p>
                                        </div>

                                        <form class="form" method="POST" action="{{ route('roles.update', $role->id) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-body">
                                                <!-- Role Basic Information -->
                                                <h4 class="form-section"><i class="icon-user6"></i> Role Information</h4>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="name">Role Name <span class="danger">*</span></label>
                                                            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                                                                   name="name" value="{{ old('name', $role->name) }}"
                                                                   placeholder="Enter role name" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Enter a unique name for this role">
                                                            @error('name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="guard_name">Guard Name</label>
                                                            <select id="guard_name" class="form-control @error('guard_name') is-invalid @enderror" name="guard_name">
                                                                <option value="web" {{ old('guard_name', $role->guard_name) === 'web' ? 'selected' : '' }}>Web</option>
                                                                <option value="api" {{ old('guard_name', $role->guard_name) === 'api' ? 'selected' : '' }}>API</option>
                                                            </select>
                                                            @error('guard_name')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Permissions Section -->
                                                <h4 class="form-section"><i class="icon-lock4"></i> Permissions</h4>
                                                <div class="form-group">
                                                    <label>Select Permissions</label>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="checkbox">
                                                                        <input type="checkbox" id="select_all" class="chk-col-primary">
                                                                        <label for="select_all"><strong>Select All Permissions</strong></label>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    @if($permissions->count() > 0)
                                                                        @foreach($permissions as $module => $modulePermissions)
                                                                            <div class="permission-group mb-2">
                                                                                <h5 class="text-capitalize text-primary">
                                                                                    <i class="icon-folder-open"></i> {{ str_replace('_', ' ', $module) }} Module
                                                                                </h5>
                                                                                <div class="row">
                                                                                    @foreach($modulePermissions as $permission)
                                                                                        <div class="col-md-3 col-sm-6">
                                                                                            <div class="checkbox">
                                                                                                <input type="checkbox"
                                                                                                       id="permission_{{ $permission->id }}"
                                                                                                       name="permissions[]"
                                                                                                       value="{{ $permission->id }}"
                                                                                                       class="chk-col-success permission-checkbox"
                                                                                                    {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                                                                <label for="permission_{{ $permission->id }}">
                                                                                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endforeach
                                                                                </div>
                                                                                <hr>
                                                                            </div>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="text-center text-muted py-3">
                                                                            <i class="icon-info22 font-large-1"></i>
                                                                            <p class="mt-1">No permissions available. Please create permissions first.</p>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @error('permissions')
                                                    <div class="text-danger mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="form-actions">
                                                <a href="{{ route('roles.index') }}" class="btn btn-warning mr-1">
                                                    <i class="icon-cross2"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="icon-check2"></i> Update Role
                                                </button>
                                            </div>
                                        </form>
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
            // Select All functionality
            $('#select_all').on('change', function() {
                console.log('test')
                $('.permission-checkbox').prop('checked', $(this).is(':checked'));
            });

            // Update select all checkbox based on individual permissions
            $('.permission-checkbox').on('change', function() {
                const totalPermissions = $('.permission-checkbox').length;
                const checkedPermissions = $('.permission-checkbox:checked').length;

                $('#select_all').prop('checked', totalPermissions === checkedPermissions);
                $('#select_all').prop('indeterminate', checkedPermissions > 0 && checkedPermissions < totalPermissions);
            });

            // Initialize select all state
            const totalPermissions = $('.permission-checkbox').length;
            const checkedPermissions = $('.permission-checkbox:checked').length;

            if (totalPermissions > 0) {
                $('#select_all').prop('checked', totalPermissions === checkedPermissions);
                $('#select_all').prop('indeterminate', checkedPermissions > 0 && checkedPermissions < totalPermissions);
            }
        });
    </script>
@endsection
