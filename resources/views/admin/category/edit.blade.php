<x-admin>
    @section('title', 'Edit Category')

    @php
        // Detect route prefix dynamically based on user role
        if (auth()->user()->hasRole('super-admin')) {
            $prefix = 'super-admin';
        } else {
            $prefix = 'admin';
        }
    @endphp

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Category</h3>
                        <div class="card-tools">
                            <a href="{{ route($prefix . '.category.index') }}" class="btn btn-info btn-sm">Back</a>
                        </div>
                    </div>

                    <form class="needs-validation" novalidate
                          action="{{ route($prefix . '.category.update', $data->id) }}"
                          method="POST">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" value="{{ $data->id }}">

                        <div class="card-body">
                            <!-- Category Name -->
                            <div class="form-group">
                                <label for="name">Category Name</label>
                                <input type="text"
                                       class="form-control"
                                       id="name"
                                       name="name"
                                       placeholder="Enter category name"
                                       required
                                       value="{{ $data->name }}">
                            </div>
                            <x-error>name</x-error>

                            <!-- Parent Category Dropdown -->
                            <div class="form-group">
                                <label for="parent_id">Parent Category</label>
                                <select name="parent_id" id="parent_id" class="form-control">
                                    <option value="">-- None (Main Category) --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $data->parent_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <x-error>parent_id</x-error>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="active" {{ $data->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $data->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <x-error>status</x-error> 
                        </div>
                        <!-- Status Dropdown -->


                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary float-right">Update</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin>
