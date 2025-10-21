<x-admin>
    @section('title', 'Category')

    @php
        if (auth()->user()->hasRole('super-admin')) {
            $prefix = 'super-admin';
        } else {
            $prefix = 'admin';
        }
    @endphp

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Category Table</h3>
            <div class="card-tools">
                <a href="{{ route($prefix . '.category.create') }}" class="btn btn-sm btn-info">New</a>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-striped" id="categoryTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $cat)
                        {{-- Main Parent --}}
                        <tr class=" parent-row" data-parent-id="{{ $cat->id }}">
                            <td>
                                @if($cat->subcategories && $cat->subcategories->count() > 0)
                                    <button class="btn btn-sm btn-link p-0 toggle-children" data-parent-id="{{ $cat->id }}">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                @endif
                                <strong>{{ $cat->name }}</strong>
                            </td>
                            <td>-</td>
                            <td><span class="badge bg-primary">Main</span></td>
                            <td>
                                <span class="badge {{ $cat->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($cat->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route($prefix . '.category.edit', encrypt($cat->id)) }}"
                                    class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                            </td>
                            <td>
                                <form action="{{ route($prefix . '.category.destroy', encrypt($cat->id)) }}"
                                    method="POST" onsubmit="return confirm('Are you sure?')">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        {{-- Children --}}
                        @if($cat->subcategories && $cat->subcategories->count() > 0)
                            @foreach ($cat->subcategories as $child)
                                <tr class="child-row" data-parent-id="{{ $cat->id }}" data-child-id="{{ $child->id }}" style="display: none;">
                                    <td style="padding-left: 30px;">
                                        @if($child->subcategories && $child->subcategories->count() > 0)
                                            <button class="btn btn-sm btn-link p-0 toggle-subchildren" data-child-id="{{ $child->id }}">
                                                <i class="fas fa-chevron-down"></i>
                                            </button>
                                        @endif
                                        <i class="fas fa-level-up-alt fa-rotate-90"></i> {{ $child->name }}
                                    </td>
                                    <td>{{ $cat->name }}</td>
                                    <td><span class="badge bg-info">Sub</span></td>
                                     <td>
                                        <span class="badge {{ $cat->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($cat->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route($prefix . '.category.edit', encrypt($child->id)) }}"
                                            class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                    </td>
                                    <td>
                                        <form action="{{ route($prefix . '.category.destroy', encrypt($child->id)) }}"
                                            method="POST" onsubmit="return confirm('Are you sure?')">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Sub-children --}}
                                @if($child->subcategories && $child->subcategories->count() > 0)
                                    @foreach ($child->subcategories as $subChild)
                                        <tr class="subchild-row" data-child-id="{{ $child->id }}" style="display: none;">
                                            <td style="padding-left: 60px;">
                                                <i class="fas fa-level-up-alt fa-rotate-90"></i>
                                                <i class="fas fa-level-up-alt fa-rotate-90"></i>
                                                {{ $subChild->name }}
                                            </td>
                                            <td>{{ $child->name }}</td>
                                            <td><span class="badge bg-secondary">Sub-sub</span></td>
                                            <td>
                                                <a href="{{ route($prefix . '.category.edit', encrypt($subChild->id)) }}"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                            <td>
                                                <form action="{{ route($prefix . '.category.destroy', encrypt($subChild->id)) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure?')">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @section('js')
        <script>
            $(function() {
                // Initialize DataTable
                let table = $('#categoryTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": false,
                    "responsive": true,
                    "pageLength": 50,
                    "drawCallback": function() {
                        // Reapply visibility after redraw
                        $('.child-row, .subchild-row').hide();
                    }
                });

                // Toggle children
                $(document).on('click', '.toggle-children', function(e) {
                    e.preventDefault();
                    let parentId = $(this).data('parent-id');
                    let icon = $(this).find('i');
                    let children = $('tr[data-parent-id="' + parentId + '"].child-row');
                    
                    if (children.is(':visible')) {
                        // Hide children and their subchildren
                        children.hide();
                        children.each(function() {
                            let childId = $(this).data('child-id');
                            $('tr[data-child-id="' + childId + '"].subchild-row').hide();
                            $(this).find('.toggle-subchildren i').removeClass('fa-chevron-up').addClass('fa-chevron-down');
                        });
                        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    } else {
                        children.show();
                        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    }
                });

                // Toggle subchildren
                $(document).on('click', '.toggle-subchildren', function(e) {
                    e.preventDefault();
                    let childId = $(this).data('child-id');
                    let icon = $(this).find('i');
                    let subchildren = $('tr[data-child-id="' + childId + '"].subchild-row');
                    
                    if (subchildren.is(':visible')) {
                        subchildren.hide();
                        icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                    } else {
                        subchildren.show();
                        icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                    }
                });
            });
        </script>
    @endsection
</x-admin>