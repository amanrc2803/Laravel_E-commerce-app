@extends('admin.layouts.app')

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid my-2">
                <div class="row mb-2">
           
                <div class="col-sm-6">
                        <h1>Create Brand</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('brands.index') }}" class="btn btn-primary">Back</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form id="createBrandForm" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}">
                                        <p class="text-danger" id="name-error"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="slug">Slug</label>
                                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" readonly placeholder="Slug" value="{{ old('slug') }}">
                                        <p class="text-danger" id="slug-error"></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Block</option>
                                        </select>
                                        <p class="text-danger" id="status-error"></p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Brand</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection

@section('customJs')
<!-- Include Toastr JS and CSS -->

<script>
$(document).ready(function() {
    // Handle form submission with AJAX
    $('#createBrandForm').submit(function(e) {
        e.preventDefault();
        resetErrors();
        let formData = $(this).serialize(); // Serialize form data as URL-encoded

        $.ajax({
            type: 'POST',
            url: "{{ route('brands.store') }}",
            data: formData,
            success: function(response) {
                if (response.status) {
                    // Display success message using Toastr
                    toastr.success(response.message);
                    setTimeout(function() {
                        window.location.href = "{{ route('brands.index') }}"; // Redirect to the brands list
                    }, 2000); // Adjust the delay as needed
                } else {
                    // Display error message using Toastr
                    toastr.error('An error occurred while creating the brand.');
                }
            },
            error: function(xhr) {
                // Handle validation errors
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) {
                        $('#name').addClass('is-invalid');
                        $('#name-error').text(errors.name[0]);
                    } else {
                        $('#name').removeClass('is-invalid');
                        $('#name-error').text('');
                    }
                    if (errors.slug) {
                        $('#slug').addClass('is-invalid');
                        $('#slug-error').text(errors.slug[0]);
                    } else {
                        $('#slug').removeClass('is-invalid');
                        $('#slug-error').text('');
                    }
                    if (errors.status) {
                        $('#status').addClass('is-invalid');
                        $('#status-error').text(errors.status[0]);
                    } else {
                        $('#status').removeClass('is-invalid');
                        $('#status-error').text('');
                    }
                } else {
                    // Display generic error message using Toastr
                    toastr.error('An unexpected error occurred.');
                }
            }
        });
    });

    // Reset error messages and input styles
    function resetErrors() {
        $('#name').removeClass('is-invalid');
        $('#slug').removeClass('is-invalid');
        $('#status').removeClass('is-invalid');
        $('#name-error').text('');
        $('#slug-error').text('');
        $('#status-error').text('');
    }

    // Update slug based on name input
    $("#name").on('input', function() {
        let name = $(this).val();
        if (name.trim() !== '') {
            $.ajax({
                type: 'POST',
                url: "{{ route('generateSlug') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function(response) {
                    if (response.status) {
                        $('#slug').val(response.slug);
                    } else {
                        // Display error message using Toastr
                        toastr.error('Unable to generate slug.');
                    }
                },
                error: function() {
                    // Display error message using Toastr
                    toastr.error('Error generating slug.');
                }
            });
        } else {
            $('#slug').val('');
        }
    });
});
</script>
@endsection
