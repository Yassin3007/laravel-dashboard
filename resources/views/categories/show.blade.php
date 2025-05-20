@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Category Details</h3>
                        <div class="card-tools">
                            <a href="{{ route('categories.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>ID:</label>
                            <p>{{ $category->id }}</p>
                        </div>

                        <div class="mb-3">
        <strong>Title:</strong> {{ $category->title }}
    </div>
                <div class="mb-3">
        <strong>Content:</strong> {{ $category->content }}
    </div>

                        <div class="form-group">
                            <label>Created At:</label>
                            <p>{{ $category->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>

                        <div class="form-group">
                            <label>Updated At:</label>
                            <p>{{ $category->updated_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
