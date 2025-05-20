@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Edit Permission</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permission Management</a>
                            </li>
                            <li class="breadcrumb-item active">Edit Permission
                            </li>
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
                                    <h4 class="card-title" id="basic-layout-tooltip">Edit Permission #{{ $permission->id }}</h4>
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
                                            <p>Update the information for this Permission.</p>
                                        </div>

                                        <form class="form" method="POST" action="{{ route('permissions.update', $permission->id) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-body">
                                                <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
                   name="name" value="{{ isset($permission) ? $permission->name : old('name') }}"
                   placeholder="name" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="guard_name">Guard Name</label>
            <input type="text" id="guard_name" class="form-control @error('guard_name') is-invalid @enderror"
                   name="guard_name" value="{{ isset($permission) ? $permission->guard_name : old('guard_name') }}"
                   placeholder="guard name" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Guard Name">
            @error('guard_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                                            </div>

                                            <div class="form-actions">
                                                <a href="{{ route('permissions.index') }}" class="btn btn-warning mr-1">
                                                    <i class="icon-cross2"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="icon-check2"></i> Update
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
