@extends('layouts.app')

@section('title', 'Custom Fields')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Custom Fields</h5>
        <a href="{{ route('custom-fields.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Add Custom Field
        </a>
    </div>
    <div class="card-body">
        @if($fields->isEmpty())
            <div class="alert alert-info">No custom fields found.</div>
        @else
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Field Name</th>
                        <th>Field Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fields as $field)
                    <tr>
                        <td>{{ $field->field_name }}</td>
                        <td>{{ ucfirst($field->field_type) }}</td>
                        
                        <td>
                            <div class="btn-group gap">
                                <a href="{{ route('custom-fields.edit', $field) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('custom-fields.destroy', $field) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($fields->hasPages())
            <div class="d-flex justify-content-center">
                {{ $fields->links() }}
            </div>
            @endif
        @endif
    </div>
</div>
@endsection