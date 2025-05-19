@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">{{modelNamePlural}} Management</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">{{modelNamePlural}}
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
                                <h4 class="card-title">{{modelNamePlural}} List</h4>
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
                                    <a href="{{ route('{{viewPath}}.create') }}" class="btn btn-primary mb-1">
                                        <i class="icon-plus2"></i> Add New {{modelName}}
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            {{tableHeaders}}
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse(${{modelNamePluralLowerCase}} as ${{modelNameSingularLowerCase}})
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                {{tableRows}}
                                                <td>
                                                    <a href="{{ route('{{viewPath}}.show', ${{modelNameSingularLowerCase}}->id) }}" class="btn btn-info btn-sm">
                                                        <i class="icon-eye6"></i> View
                                                    </a>
                                                    <a href="{{ route('{{viewPath}}.edit', ${{modelNameSingularLowerCase}}->id) }}" class="btn btn-warning btn-sm">
                                                        <i class="icon-pencil3"></i> Edit
                                                    </a>
                                                    <form action="{{ route('{{viewPath}}.destroy', ${{modelNameSingularLowerCase}}->id) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this {{modelName}}?');">
                                                            <i class="icon-trash4"></i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ 2 + count(Schema::getColumnListing('{{viewPath}}')) }}" class="text-center">No {{modelNamePlural}} found.</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
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
