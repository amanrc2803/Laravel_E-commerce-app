@extends('admin.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('categories.index') }}" class="btn btn-primary">Back to List</a>
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
                    <form id="editCategoryForm" method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $category->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="slug">Slug</label>
                            <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $category->slug) }}" required readonly>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image">Category Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                            <!-- Display the current image if it exists -->
                            @if($category->image)
                                <div class="mt-2">
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" width="100" class="img-thumbnail">
                                    <p>Current Image</p>
                                </div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">Update Category</button>
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
                        window.location.href = "{{ route('categories.index') }}"; // Redirect to the categories list
                    } else {
                        alert('An error occurred while updating the category.');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while updating the category.');
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
