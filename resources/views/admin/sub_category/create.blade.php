@extends('admin.layouts.app')

@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Sub Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('sub-category.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('sub-category.store') }}" name="subCategoryForm" id="subCategoryForm" method="post">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="category_id">Category</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Select A Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="category-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                    <span class="text-danger" id="name-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug">
                                    <span class="text-danger" id="slug-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                    <span class="text-danger" id="status-error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pb-5 pt-3">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ route('sub-category.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

@endsection

@section('customJs')
<script>
    $(document).ready(function() {
        // Handle form submission with AJAX
        $('#subCategoryForm').submit(function(e) {
            e.preventDefault();
            resetErrors();
           // $("button[type=submit]").prop('disabled', true);
            let formData = new FormData(this);

            $.ajax({
                type: 'POST',
                url: "{{ route('sub-category.store') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response.status) {
                        toastr.success(response.message);
                        $('#subCategoryForm')[0].reset();
                        window.location.href = "{{ route('sub-category.index') }}";
                    } else {
                        toastr.error('An error occurred while creating the sub-category.');
                    }
                },
                error: function(xhr) {
                   // $("button[type=submit]").prop('disabled', false);
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.name) {
                            $('#name').addClass('is-invalid');
                            $('#name-error').text(errors.name[0]);
                        }
                        if (errors.slug) {
                            $('#slug').addClass('is-invalid');
                            $('#slug-error').text(errors.slug[0]);
                        }
                        if (errors.category_id) {
                            $('#category_id').addClass('is-invalid');
                            $('#category-error').text(errors.category_id[0]);
                        }
                        if (errors.status) {
                            $('#status').addClass('is-invalid');
                            $('#status-error').text(errors.status[0]);
                        }
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        });

        // Reset error messages and input styles
        function resetErrors() {
            $('#name-error').text('');
            $('#slug-error').text('');
            $('#category-error').text('');
            $('#status-error').text('');
            $('#name').removeClass('is-invalid');
            $('#slug').removeClass('is-invalid');
            $('#category_id').removeClass('is-invalid');
            $('#status').removeClass('is-invalid');
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
    });
</script>
@endsection
