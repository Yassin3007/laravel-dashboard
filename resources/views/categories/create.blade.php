@extends('dashboard.layouts.master')

@section('content')
    <div class="app-content content container-fluid">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-xs-12 mb-1">
                    <h2 class="content-header-title">Create Category</h2>
                </div>
                <div class="content-header-right breadcrumbs-right breadcrumbs-top col-md-6 col-xs-12">
                    <div class="breadcrumb-wrapper col-xs-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Category Management</a>
                            </li>
                            <li class="breadcrumb-item active">Create New Category
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
                                    <h4 class="card-title" id="basic-layout-tooltip">Create New Category</h4>
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
                                            <p>Please fill in all required fields to create a new Category.</p>
                                        </div>

                                        <form class="form" method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">
                                                <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" class="form-control @error('title') is-invalid @enderror"
                   name="title" value="{{ isset($category) ? $category->title : old('title') }}"
                   placeholder="title" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Title">
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                <div class="form-group">
            <label for="content">Content</label>
            <textarea id="content" rows="5" class="form-control @error('content') is-invalid @enderror"
                      name="content" data-toggle="tooltip" data-trigger="hover" data-placement="top" data-title="Content">{{ isset($category) ? $category->content : old('content') }}</textarea>
            @error('content')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
                                            </div>

                                            <div class="form-actions">
                                                <a href="{{ route('categories.index') }}" class="btn btn-warning mr-1">
                                                    <i class="icon-cross2"></i> Cancel
                                                </a>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="icon-check2"></i> Save
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
