@extends('layouts.app')

@section('title', 'Edit Custom Field')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit Custom Field</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('custom-fields.update', $customField) }}">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="field_name" class="form-label">Field Name</label>
                    <input type="text" class="form-control" id="field_name" name="field_name" 
                           value="{{ old('field_name', $customField->field_name) }}" required>
                    @error('field_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="field_type" class="form-label">Field Type</label>
                    <select class="form-select" id="field_type" name="field_type" required>
                        <option value="">Select Field Type</option>
                        <option value="text" {{ old('field_type', $customField->field_type) == 'text' ? 'selected' : '' }}>Text</option>
                        <option value="date" {{ old('field_type', $customField->field_type) == 'date' ? 'selected' : '' }}>Date</option>
                        <option value="select" {{ old('field_type', $customField->field_type) == 'select' ? 'selected' : '' }}>Select Dropdown</option>
                        <option value="radio" {{ old('field_type', $customField->field_type) == 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                        <option value="checkbox" {{ old('field_type', $customField->field_type) == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                        <option value="textarea" {{ old('field_type', $customField->field_type) == 'textarea' ? 'selected' : '' }}>Text Area</option>
                    </select>
                    @error('field_type')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3" id="optionsContainer" 
                 style="display: {{ in_array($customField->field_type, ['select', 'radio']) ? 'block' : 'none' }};">
                <div class="col-md-12">
                    <label for="field_options" class="form-label">Options (comma separated)</label>
                    <input type="text" class="form-control" id="field_options" name="field_options" 
                           value="{{ old('field_options', $customField->field_options ? implode(', ', $customField->field_options) : '') }}" 
                           placeholder="Option 1, Option 2, Option 3">
                    <small class="text-muted">Only required for select and radio field types</small>
                    @error('field_options')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('custom-fields.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Field</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldType = document.getElementById('field_type');
    const optionsContainer = document.getElementById('optionsContainer');
    
    function toggleOptions() {
        if (['select', 'radio'].includes(fieldType.value)) {
            optionsContainer.style.display = 'block';
        } else {
            optionsContainer.style.display = 'none';
        }
    }
    
    // Add event listener
    fieldType.addEventListener('change', toggleOptions);
});
</script>
@endpush
@endsection