<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      
        {{-- SUPER ADMIN --}}
        @role('super-admin')
            <li class="nav-item">
                <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ Route::is('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('super-admin.user.index') }}" class="nav-link {{ Route::is('super-admin.user.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Users <span class="badge badge-info right">{{ $userCount }}</span></p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('super-admin.role.index') }}" class="nav-link {{ Route::is('super-admin.role.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tag"></i>
                    <p>Role <span class="badge badge-success right">{{ $RoleCount }}</span></p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('super-admin.permission.index') }}" class="nav-link {{ Route::is('super-admin.permission.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-hat-cowboy"></i>
                    <p>Permission <span class="badge badge-danger right">{{ $PermissionCount }}</span></p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('super-admin.category.index') }}" class="nav-link {{ Route::is('super-admin.category.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list-alt"></i>
                    <p>Category <span class="badge badge-warning right">{{ $CategoryCount }}</span></p>
                </a>
            </li>

            <!-- <li class="nav-item">
                <a href="{{ route('super-admin.subcategory.index') }}" class="nav-link {{ Route::is('super-admin.subcategory.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list"></i>
                    <p>Sub Category <span class="badge badge-secondary right">{{ $SubCategoryCount }}</span></p>
                </a>
            </li> -->

            <li class="nav-item">
                <a href="{{ route('super-admin.collection.index') }}" class="nav-link {{ Route::is('super-admin.collection.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-pdf"></i>
                    <p>Collection <span class="badge badge-primary right">{{ $CollectionCount }}</span></p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('super-admin.profile.edit') }}" class="nav-link {{ Route::is('super-admin.profile.edit') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-id-card"></i>
                    <p>Profile</p>
                </a>
            </li>
        @endrole

        {{-- ADMIN --}}
        @role('admin')
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.category.index') }}" class="nav-link {{ Route::is('admin.category.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list-alt"></i>
                    <p>Category <span class="badge badge-warning right">{{ $CategoryCount }}</span></p>
                </a>
            </li>

            <!-- <li class="nav-item">
                <a href="{{ route('admin.subcategory.index') }}" class="nav-link {{ Route::is('admin.subcategory.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list"></i>
                    <p>Sub Category <span class="badge badge-secondary right">{{ $SubCategoryCount }}</span></p>
                </a>
            </li> -->

            <li class="nav-item">
                <a href="{{ route('admin.collection.index') }}" class="nav-link {{ Route::is('admin.collection.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-pdf"></i>
                    <p>Collection <span class="badge badge-primary right">{{ $CollectionCount }}</span></p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.profile.edit') }}" class="nav-link {{ Route::is('admin.profile.edit') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-id-card"></i>
                    <p>Profile</p>
                </a>
            </li>
        @endrole
    </ul>
</nav>
