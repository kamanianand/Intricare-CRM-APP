<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM App - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <style>
        .gap{
            gap:5px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="javascript::void(0);">CRM App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contacts.index') }}">Contacts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('custom-fields.index') }}">Custom Fields</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Add these before closing body -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
    </script>
    @stack('scripts')
    <script>
        $(document).ready(function() {
            // AJAX form submission for contacts
            $('.ajax-form').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const formData = new FormData(form[0]);
                const url = form.attr('action');
                const method = form.attr('method');
                
                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Refresh the table or show success message
                            $('#contactsTable').load(location.href + ' #contactsTable');
                            alert('Operation successful');
                        }
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            let errorHtml = '<div class="alert alert-danger"><ul>';
                            
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            $('#formErrors').html(errorHtml).show();
                        } else {
                            alert('An error occurred');
                        }
                    }
                });
            });
            
            // AJAX delete
            $('.ajax-delete').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this?')) {
                    const url = $(this).attr('href');
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#contactsTable').load(location.href + ' #contactsTable');
                                alert('Deleted successfully');
                            }
                        },
                        error: function() {
                            alert('Error deleting record');
                        }
                    });
                }
            });
            
            // AJAX search/filter
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        $('#contactsTable').html(response);
                    },
                    error: function() {
                        alert('Error searching contacts');
                    }
                });
            });
        });
    </script>
</body>
</html>