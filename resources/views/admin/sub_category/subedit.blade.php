@extends('admin.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Subcategory</h1> <!-- Changed from 'Category' to 'Subcategory' -->
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('sub-category.index') }}" class="btn btn-primary">Back to List</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form id="editCategoryForm" method="POST" action="{{ route('subcategories.update', $subcategory->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                       
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="category_id">Category</label>
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">Select A Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $subcategory->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="category-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ old('name', $subcategory->name) }}">
                                    <span class="text-danger" id="name-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" value="{{ old('slug', $subcategory->slug) }}">
                                    <span class="text-danger" id="slug-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ old('status', $subcategory->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $subcategory->status) == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                    <span class="text-danger" id="status-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="pb-5 pt-3">
                            <button type="submit" class="btn btn-primary">Update Subcategory</button> <!-- Changed from 'Category' to 'Subcategory' -->
                            <a href="{{ route('sub-category.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection

@section('customJs')
<script>
    $(document).ready(function() {
        // Handle the form submission
        $('#editCategoryForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            let formData = new FormData(this); // Use FormData to handle file uploads

            $.ajax({
                url: $(this).attr('action'), // Get the form action URL
                type: 'POST', // Use POST since we're using FormData
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        alert(response.message);
                        window.location.href = "{{ route('sub-category.index') }}"; // Redirect to the subcategories list
                    } else {
                        alert('An error occurred while updating the subcategory.');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while updating the subcategory.');
                }
            });
        });

        // Handle the slug auto-generation
        $('#name').on('change', function() {
            let name = $(this).val();

            $.ajax({
                url: "{{ route('generateSlug') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    name: name
                },
                success: function(response) {
                    if (response.status) {
                        $('#slug').val(response.slug);
                    } else {
                        alert('An error occurred while generating the slug.');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while generating the slug.');
                }
            });
        });
    });
</script>
@endsection
