@extends('admin.layouts.app')


<!-- Content Wrapper. Contains page content -->


@section('content')

<div class="content-wrapper">
@if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Brand</h1> <!-- Changed from 'Category' to 'Brand' -->
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('brands.index') }}" class="btn btn-primary">Back to List</a>
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
                    <form id="editBrandForm" method="POST" action="{{ route('brands.update', $brand->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{ old('name', $brand->name) }}">
                                    <span class="text-danger" id="name-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" value="{{ old('slug', $brand->slug) }}">
                                    <span class="text-danger" id="slug-error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ old('status', $brand->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $brand->status) == 0 ? 'selected' : '' }}>Block</option>
                                    </select>
                                    <span class="text-danger" id="status-error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="pb-5 pt-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('brands.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
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
        $('#editBrandForm').on('submit', function(e) {
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
                        window.location.href = "{{ route('brands.index') }}"; // Redirect to the brands list
                    } else {
                        alert('An error occurred while updating the brand.');
                    }
                },
                error: function(xhr) {
                    alert('An error occurred while updating the brand.');
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
