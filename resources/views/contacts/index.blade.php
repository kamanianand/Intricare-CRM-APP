@extends('layouts.app')

@section('title', 'Contacts')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Contacts</h5>
        <a href="{{ route('contacts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Add Contact
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <form id="searchForm">
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" id="name" name="name" class="form-control" placeholder="Search by name">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="email" name="email" class="form-control" placeholder="Search by email">
                        </div>
                        <div class="col-md-2">
                            <select name="gender" id="gender" class="form-select">
                                <option value="">All Genders</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <!-- Custom Fields Filters (Collapsible) -->
                        <div id="customFieldsFilters">
                            <div class="row mt-3">
                                @foreach($customFields as $field)
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">{{ $field->field_name }}</label>
                                    
                                    @if($field->field_type === 'select' || $field->field_type === 'radio')
                                        <select name="custom_fields[{{ $field->id }}]" class="form-select">
                                            <option value="">All</option>
                                            @foreach($field->field_options as $option)
                                                <option value="{{ $option }}" 
                                                    {{ request("custom_fields.{$field->id}") == $option ? 'selected' : '' }}>
                                                    {{ $option }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($field->field_type === 'checkbox')
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                name="custom_fields[{{ $field->id }}]" 
                                                value="1"
                                                id="custom_field_{{ $field->id }}"
                                                {{ request("custom_fields.{$field->id}") ? 'checked' : '' }}>
                                            <label class="form-check-label" for="custom_field_{{ $field->id }}">
                                                Has {{ $field->field_name }}
                                            </label>
                                        </div>
                                    @elseif($field->field_type === 'date')
                                        <input type="date" class="form-control" 
                                            name="custom_fields[{{ $field->id }}]"
                                            value="{{ request("custom_fields.{$field->id}") }}">
                                    @else
                                        <input type="text" class="form-control" 
                                            name="custom_fields[{{ $field->id }}]"
                                            value="{{ request("custom_fields.{$field->id}") }}"
                                            placeholder="Filter by {{ $field->field_name }}">
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary search w-100">Search</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('contacts.index') }}" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="contactsTable">
            @include('contacts.partials.table', ['contacts' => $contacts])
        </div>
    </div>
</div>

<!-- Modal for AJAX operations -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modalBody">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // AJAX search
        $('.search').on('click', function(e) {
            e.preventDefault();
            var name = $("#name").val();
            var gender = $("#gender").val();
            var email = $("#email").val();
            var custom_fields = $("#custom_fields").val();
            $.ajax({
                url: "{{ route('api.contacts.search') }}",
                type: "GET",
                data: {name:name,email:email,gender:gender,custom_fields:custom_fields},
                success: function(response) {
                    $('#contactsTable').html(response);
                },
                error: function(xhr) {
                    alert('Error searching contacts');
                }
            });
        });

        // Reset button functionality
        $('.reset-filters').on('click', function(e) {
            e.preventDefault();
            $('#searchForm')[0].reset();
            $('#searchForm').submit();
        });    

        $(document).on('click', '.ajaxdelete', function(e) {
            e.preventDefault();
            let contactId = $(this).data('id');
            let deleteUrl = $(this).attr('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function() {
                            $(`#contact-${contactId}`).css('opacity', '0.5');
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                $(`#contact-${contactId}`).fadeOut(300, function() {
                                    $(this).remove();
                                });
                                if ($('#contactsTable tbody tr').length === 0) {
                                    $('#contactsTable tbody').html(
                                        '<tr><td colspan="6" class="text-center">No contacts found</td></tr>'
                                    );
                                }
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Error deleting contact');
                            $(`#contact-${contactId}`).css('opacity', '1');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush