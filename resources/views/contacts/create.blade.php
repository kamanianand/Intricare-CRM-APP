@extends('layouts.app')

@section('title', 'Contacts')
@section('content')
<h2 class="mb-5 mt-5">Add contact</h2>
<he />
<div id="formErrors" class="alert alert-danger" style="display: none;"></div>
<form id="createContactForm" method="POST" data-action="{{ route('contacts.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $contact->name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $contact->email ?? '') }}" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $contact->phone ?? '') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Gender</label>
            <div class="d-flex gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="male" value="male"
                        {{ old('gender', $contact->gender ?? '') === 'male' ? 'checked' : '' }}>
                    <label class="form-check-label" for="male">Male</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="female" value="female"
                        {{ old('gender', $contact->gender ?? '') === 'female' ? 'checked' : '' }}>
                    <label class="form-check-label" for="female">Female</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="other" value="other"
                        {{ old('gender', $contact->gender ?? '') === 'other' ? 'checked' : '' }}>
                    <label class="form-check-label" for="other">Other</label>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="profile_image" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="profile_image" name="profile_image">
            @if(!empty($contact) && $contact->profile_image)
            <div class="mt-2">
                <img src="{{ Storage::url($contact->profile_image) }}" alt="Profile Image" width="100">
            </div>
            @endif
        </div>
        <div class="col-md-6">
            <label for="additional_file" class="form-label">Additional File</label>
            <input type="file" class="form-control" id="additional_file" name="additional_file">
            @if(!empty($contact) && $contact->additional_file)
            <div class="mt-2">
                <a href="{{ Storage::url($contact->additional_file) }}" target="_blank">View File</a>
            </div>
            @endif
        </div>
    </div>

    <!-- Custom Fields -->
    @foreach($customFields as $field)
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="custom_field_{{ $field->id }}" class="form-label">{{ $field->field_name }}</label>

            @if($field->field_type === 'text')
            <input type="text" class="form-control" id="custom_field_{{ $field->id }}"
                name="custom_field_{{ $field->id }}"
                value="{{ old('custom_field_' . $field->id, $contact->customValues->firstWhere('field_id', $field->id)?->field_value ?? '') }}">

            @elseif($field->field_type === 'date')
            <input type="date" class="form-control" id="custom_field_{{ $field->id }}"
                name="custom_field_{{ $field->id }}"
                value="{{ old('custom_field_' . $field->id, $contact->customValues->firstWhere('field_id', $field->id)?->field_value ?? '') }}">

            @elseif(in_array($field->field_type, ['select', 'radio']))
            @foreach($field->field_options as $option)
            <div class="form-check {{ $field->field_type === 'radio' ? 'form-check-inline' : '' }}">
                <input class="form-check-input" type="{{ $field->field_type }}"
                    id="custom_field_{{ $field->id }}_{{ $loop->index }}"
                    name="custom_field_{{ $field->id }}"
                    value="{{ $option }}"
                    {{ old('custom_field_' . $field->id, $contact->customValues->firstWhere('field_id', $field->id)?->field_value ?? '') === $option ? 'checked' : '' }}>
                <label class="form-check-label" for="custom_field_{{ $field->id }}_{{ $loop->index }}">
                    {{ $option }}
                </label>
            </div>
            @endforeach

            @elseif($field->field_type === 'checkbox')
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                    id="custom_field_{{ $field->id }}"
                    name="custom_field_{{ $field->id }}"
                    value="1"
                    {{ old('custom_field_' . $field->id, $contact->customValues->firstWhere('field_id', $field->id)?->field_value ?? '') ? 'checked' : '' }}>
                <label class="form-check-label" for="custom_field_{{ $field->id }}">
                    {{ $field->field_name }}
                </label>
            </div>

            @elseif($field->field_type === 'textarea')
            <textarea class="form-control" id="custom_field_{{ $field->id }}"
                name="custom_field_{{ $field->id }}">{{ old('custom_field_' . $field->id, $contact->customValues->firstWhere('field_id', $field->id)?->field_value ?? '') }}</textarea>
            @endif
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-end">
        <button type="submit" id="submitBtn" class="btn btn-primary">
            {{ !empty($contact) ? 'Update' : 'Create' }}
        </button>
    </div>
</form>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('#createContactForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: $(this).attr('data-action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success(response.message);

                        // Reset form
                        $('#createContactForm')[0].reset();

                        // Redirect or update table
                        // window.location.href = "{{ route('contacts.index') }}";
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<div class="alert alert-danger"><ul>';

                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value + '</li>';
                        $(`#${key}`).addClass('is-invalid');
                    });

                    errorHtml += '</ul></div>';
                    $('#formErrors').html(errorHtml).show();
                },
                complete: function() {
                    $('#submitBtn').prop('disabled', false).html('Save');
                }
            });
        });

    });
</script>
@endpush