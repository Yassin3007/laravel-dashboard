@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Categories Management</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route(\'dashboard\') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Categories
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
                                <h4 class="card-title">Categories List</h4>
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
                                    @can('create_category')
                                    <a href="{{ route(\'categories.create\') }}" class="btn btn-primary mb-1">
                                        <i class="icon-plus2"></i> Add New Category
                                    </a>
                                    @endcan
                                </div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                <th>Content</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($categories as $category)
                                            <tr>
                                                <th scope="row">{{ $loop->iteration }}</th>
                                                <td>{{ $category->title }}</td>
                <td>{{ $category->content }}</td>
                                                <td>
                                                    @can('view_category')
                                                    <a href="{{ route(\'categories.show\', $category->id) }}" class="btn btn-info btn-sm">
                                                        <i class="icon-eye6"></i> View
                                                    </a>
                                                    @endcan

                                                    @can('edit_category')
                                                    <a href="{{ route(\'categories.edit\', $category->id) }}" class="btn btn-warning btn-sm">
                                                        <i class="icon-pencil3"></i> Edit
                                                    </a>
                                                    @endcan

                                                    @can('delete_category')
                                                    <form action="{{ route(\'categories.destroy\', $category->id) }}" method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this Category?\');">
                                                            <i class="icon-trash4"></i> Delete
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ 2 + count(Schema::getColumnListing('categories')) }}" class="text-center">No Categories found.</td>
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
