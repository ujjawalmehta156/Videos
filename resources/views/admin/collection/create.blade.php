<x-admin>
    @section('title','Create collection')
     @php
        if (auth()->user()->hasRole('super-admin')) {
            $prefix = 'super-admin';
        } else {
            $prefix = 'admin';
        }
    @endphp
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Create Collection</h3>
                        <div class="card-tools">
                            <a href="{{ route( $prefix.'.collection.index') }}" class="btn btn-info btn-sm">Back</a>
                        </div>
                    </div>
                    <form class="needs-validation" novalidate action="{{ route( $prefix.'.collection.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                {{-- Name --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
                                        <x-error>name</x-error>
                                    </div>
                                </div>

                                {{-- Category --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select name="category" id="category" class="form-control" required>
                                            <option value="" selected disabled>Select category</option>
                                            @foreach ($category as $cat)
                                                <option {{ old('category') == $cat->id ? 'selected' : '' }} value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-error>category</x-error>
                                    </div>
                                </div>

                                {{-- Sub Category --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="subcategory">Sub Category</label>
                                        <select name="subcategory" id="subcategory" class="form-control" >
                                            <option value="" selected disabled>Select subcategory</option>
                                            {{-- This will be dynamically loaded by JS --}}
                                        </select>
                                        <x-error>subcategory</x-error>
                                    </div>
                                </div>

                                {{-- Image --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">Video</label>
                                            <input type="file" name="video" id="video" class="form-control" accept="video/*" required>
                                        <x-error>image</x-error>
                                    </div>
                                </div>

                                {{-- Meta Title --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="meta_title" class="form-label">Meta Title</label>
                                        <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" class="form-control">
                                        <x-error>meta_title</x-error>
                                    </div>
                                </div>

                                {{-- Meta Keywords --}}
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                        <input type="text" name="meta_keywords" id="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control">
                                        <x-error>meta_keywords</x-error>
                                    </div>
                                </div>

                                {{-- Meta Description --}}
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="meta_description" class="form-label">Meta Description</label>
                                        <textarea name="meta_description" id="meta_description" rows="3" class="form-control">{{ old('meta_description') }}</textarea>
                                        <x-error>meta_description</x-error>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary float-right">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
{{-- Ajax for Sub Category --}}
@section('js')
<script>
$(document).ready(function() {
    $('#category').on('change', function() {
        var categoryId = $(this).val();
        var $subcategorySelect = $('#subcategory');

        $subcategorySelect.html('<option value="" disabled selected>Loading...</option>');

        // Role wise URL
        var url = '';
        @if(auth()->user()->hasRole('admin'))
            url = '{{ url("admin/get-subcategories") }}/' + categoryId;
        @elseif(auth()->user()->hasRole('super-admin'))
            url = '{{ url("super-admin/get-subcategories") }}/' + categoryId;
        @endif

        // AJAX request
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var options = '<option value="" disabled selected>Select subcategory</option>';
                $.each(data, function(index, subcat) {
                    options += '<option value="' + subcat.id + '">' + subcat.name + '</option>';
                });
                $subcategorySelect.html(options);
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $subcategorySelect.html('<option value="" disabled selected>No subcategories found</option>');
            }
        });
    });
});
</script>

@endsection

</x-admin>

