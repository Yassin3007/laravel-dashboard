@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{modelName}} Details</h3>
                        <div class="card-tools">
                            <a href="{{ route('{{viewPath}}.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('{{viewPath}}.edit', ${{modelNameLowerCase}}->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>ID:</label>
                            <p>{{ ${{modelNameLowerCase}}->id }}</p>
                        </div>

                        {{detailFields}}

                        <div class="form-group">
                            <label>Created At:</label>
                            <p>{{ ${{modelNameLowerCase}}->created_at->format('Y-m-d H:i:s') }}</p>
                        </div>

                        <div class="form-group">
                            <label>Updated At:</label>
                            <p>{{ ${{modelNameLowerCase}}->updated_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('{{viewPath}}.destroy', ${{modelNameLowerCase}}->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display: inline-block;">
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
