@extends('layouts.app')

@section('title', 'Merge Contacts')
@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Merge Contacts</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Primary Contact (will be kept)</h6>
                <div class="card">
                    <div class="card-body">
                        <h5>{{ $contact->name }}</h5>
                        <p>Email: {{ $contact->email }}</p>
                        <p>Phone: {{ $contact->phone }}</p>
                        <p>Gender: {{ ucfirst($contact->gender) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Secondary Contact (will be merged and deactivated)</h6>
                <form method="POST" id="mergeform" action="{{ route('contacts.merge.submit', $contact) }}">
                    @csrf
                    <select name="master_contact_id" class="form-select mb-3" required>
                        <option value="">Select a contact to merge with</option>
                        @foreach($contacts as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->email }})</option>
                        @endforeach
                    </select>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('contacts.show', $contact) }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="button" class="btn btn-warning mergecontact">Merge Contacts</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $(".mergecontact").on('click', function() {
            Swal.fire({
                title: "Are you sure want to merge it?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, merge it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $("#mergeform").submit();
                }
            });
        });
    })
</script>
@endpush