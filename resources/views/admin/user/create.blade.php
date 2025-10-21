<x-admin>
@section('title', 'Create User')

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create User</h3>
        <div class="card-tools">
            <a href="{{ route('super-admin.user.index') }}" class="btn btn-sm btn-dark">Back</a>
        </div>
    </div>

    <div class="card-body">
        <form id="createUserForm" action="{{ route('super-admin.user.store') }}" method="post" >
            @csrf
            <div class="row">
                <!-- Name -->
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="name" class="form-label">Name:* </label>
                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                        <span class="text-danger" id="error-name"></span>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Email:*</label>
                        <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                        <span class="text-danger" id="error-email"></span>
                    </div>
                </div>

                <!-- Password -->
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Password:*</label>
                        <input type="password" class="form-control" name="password" required>
                        <span class="text-danger" id="error-password"></span>
                    </div>
                </div>

                <!-- Role -->
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="role" class="form-label">Role:*</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="" selected disabled>Select the role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ $role->name == old('role') ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="error-role"></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-lg-12">
                    <div class="float-right">
                        <button class="btn btn-primary" type="submit">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

</x-admin>
