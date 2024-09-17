@extends('admin.layouts.app')

@section('content')

<!-- Display Success and Error Messages -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <!-- Form for Creating a Category -->
                    <form method="POST" name="categoryForm" id="categoryForm" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="row">
                            <!-- Name Field -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           placeholder="Enter Category Name" 
                                           value="{{ old('name') }}">
                                    <p class="text-danger" id="name-error"></p> <!-- Error display -->
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Slug Field (Read-Only) -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" readonly 
                                           class="form-control" 
                                           value="{{ old('slug') }}">
                                    <p class="text-danger" id="slug-error"></p> <!-- Error display -->
                                </div>
                            </div>

                            <!-- Image Dropzone -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                <input type="hidden" name="image_id" id="image_id">

                                    <label for="image">Image</label>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needs-click">
                                        <br>Drop files here or click to upload.<br><br>
                                    </div>
                                </div>
                                </div>
                                
                            </div>

                            <!-- Status Field -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" 
                                            class="form-control @error('status') is-invalid @enderror">
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit and Cancel Buttons -->
                        <div class="pb-5 pt-3">
                            <button type="submit" class="btn btn-primary">Create</button>
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('customJs')

<script>
    $(document).ready(function() {
        // Handle form submission with AJAX
        $('#categoryForm').submit(function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ route('categories.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Handle success and reset form
                    $('#name-error').text('');
                    $('#slug-error').text('');
                    if (response.status) {
                        toastr.success(response.message);
                        $('#categoryForm')[0].reset();
                        window.location.href = "{{ route('categories.index') }}";
                    } else {
                        toastr.error('An error occurred while creating the category.');
                    }
                },
                error: function(xhr) {
                    // Handle validation errors
                    $('#name-error').text('');
                    $('#slug-error').text('');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid');
                            $('#name-error').text(errors.name[0]);
                        } else {
                            $('#name').removeClass('is-invalid');
                        }
                        if (errors.slug) {
                            $('#slug').addClass('is-invalid');
                            $('#slug-error').text(errors.slug[0]);
                        } else {
                            $('#slug').removeClass('is-invalid');
                        }
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        });

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
                            toastr.error('Unable to generate slug.');
                        }
                    },
                    error: function() {
                        toastr.error('Error generating slug.');
                    }
                });
            } else {
                $('#slug').val('');
            }
        });

        // Initialize Dropzone
          });

             // Initialize Dropzone
        Dropzone.autoDiscover = false; // Disable auto-discovery
        const dropzone = new Dropzone('#image', {
            url: "{{ route('temp-images.create') }}",
            maxFiles: 1,
            paramName: 'image',
            addRemoveLinks: true,
            acceptedFiles: 'image/jpeg,image/png,image/gif',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(file, response) {
                $("#image_id").val(response.image_id);
            }
        });
    
</script>
@endsection
