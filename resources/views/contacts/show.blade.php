@extends('layouts.app')

@section('title', 'View Contact')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Contact Details</h5>
        <div class="btn-group">
            <a href="{{ route('contacts.edit', $contact) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('contacts.merge', $contact) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-merge"></i> Merge
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if($contact->profile_image)
                    <img src="{{ Storage::url($contact->profile_image) }}" alt="Profile Image" class="img-fluid mb-3">
                @endif
            </div>
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Name</th>
                        <td>{{ $contact->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $contact->email }}</td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td>{{ ucfirst($contact->gender) }}</td>
                    </tr>
                    @foreach($contact->customValues as $value)
                    <tr>
                        <th>{{ $value->field->field_name }}</th>
                        <td>{{ $value->field_value }}</td>
                    </tr>
                    @endforeach
                    @if($contact->additional_file)
                    <tr>
                        <th>Additional File</th>
                        <td>
                            <a href="{{ Storage::url($contact->additional_file) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        
        @if(session('merged_data'))
            <div class="mt-4">
                <h5>Merge Details</h5>
                <div class="alert alert-info">
                    <p>The following data was merged into this contact:</p>
                    <ul>
                        @foreach(session('merged_data') as $field => $details)
                            @if(!is_array($details))
                                <li><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong> {{ $details['from'] }} → {{ $details['to'] }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        
        @if($contact->mergedAsMaster->count())
            <div class="mt-4">
                <h5>Merged Contacts</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Merged Contact</th>
                            <th>Merge Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($contact->mergedAsMaster as $merged)
                        <tr>
                            <td>{{ $merged->mergedContact->name }} ({{ $merged->mergedContact->email }}) - ({{ $merged->mergedContact->phone }})</td>
                            <td>{{ $merged->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection