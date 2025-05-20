@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Role Details</h3>
                        <div class="card-tools">
                            <a href="{{ route('roles.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>ID:</label>
                            <p>{{ $role->id }}</p>
                        </div>

                        <div class="mb-3">
        <strong>Name:</strong> {{ $role->name }}
    </div>
                <div class="mb-3">
        <strong>Guard Name:</strong> {{ $role->guard_name }}
    </div>

                        <div class="form-group">
                            <label>Created At:</label>
                            <p>{{ $role->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>

                        <div class="form-group">
                            <label>Updated At:</label>
                            <p>{{ $role->updated_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display: inline-block;">
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
