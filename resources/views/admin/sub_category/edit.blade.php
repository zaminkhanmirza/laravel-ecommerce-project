@extends('admin.layouts.app')

@section('title')
Laravel Shop :: Administrative Panel
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Sub-Category</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('sub-categories.list') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="javascript:void(0)" method="post" id="subCategoryFormId" name="categoryForm">
            @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" value="{{ $subCategory->name }}" class="form-control" placeholder="Name">
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug">Slug</label>
                            <input type="text" name="slug" readonly id="slug" value="{{ $subCategory->slug }}" class="form-control" placeholder="Slug">
                            <p></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Category</label>
                            <select name="category" id="category" class="form-control">
                                @if ($categories->isNotEmpty())
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $category->id ==  $subCategory->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <p></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1" {{ $subCategory->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $subCategory->status == 0 ? 'selected' : '' }}>Block</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>							
        </div>
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('sub-categories.list') }}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
<script>
    $("#subCategoryFormId").submit(function(event) { 
        event.preventDefault();
        var element = $(this);
        $('button[type=submit]').prop('disabled', true);
        $.ajax({
            url: '{{ route("subCategories.update", $subCategory->id) }}',
            type: 'put',
            data: element.serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('button[type=submit]').prop('disabled', false);
                if (response['status'] == true) {
                    window.location.href="{{ route('sub-categories.list') }}";
                    $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    $('#status').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    $('#category').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                } else {
                    if (response['notFound']) {
                        window.location.href="{{ route('sub-categories.list') }}";
                    }
                    var errors = response['errors'];
                    if (errors['name']) {
                        $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
                    }

                    if (errors['slug']) {
                        $('#slug').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug']);
                    }

                    if (errors['status']) {
                        $('#status').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['status']);
                    }

                    if (errors['category']) {
                        $('#category').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['category']);
                    }
                }
            }, error: function(jqXHR, exception) {
                console.log('something went wrong');
            }
        });
    });

    $('#name').change( function () {
        var element = $(this);
        $('button[type=submit]').prop('disabled', true);
        $.ajax({
            url: '{{ route("getSlug") }}',
            type: 'GET',
            data: {title: element.val()},
            dataType: 'json',
            success: function(response) {
                $('button[type=submit]').prop('disabled', false);
                if (response['status'] == true) {
                    $('#slug').val(response['slug']);
                }
            }
        });
    });

    Dropzone.autoDiscover = false;    
    const dropzone = $("#image").dropzone({ 
        init: function() {
            this.on('addedfile', function(file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
        },
        url:  "{{ route('temp-images.create') }}",
        maxFiles: 1,
        paramName: 'image',
        addRemoveLinks: true,
        acceptedFiles: "image/jpeg,image/png,image/gif",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }, success: function(file, response){
            $("#image_id").val(response.image_id);
            //console.log(response)
        }
    });
</script>
    
@endsection