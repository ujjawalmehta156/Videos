<x-admin>
    @section('title','Collections')

    @php
        $prefix = auth()->user()->hasRole('super-admin') ? 'super-admin' : 'admin';
    @endphp

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Collection Table</h3>
            <div class="card-tools">
                <a href="{{ route($prefix.'.collection.create') }}" class="btn btn-sm btn-info">New</a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="collectionTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Sub Category</th>
                        <th>Status</th>
                        <th>Video Status</th>
                        <th>Visibility</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $collection)
                        <tr>
                            <td>
                                <img src="{{ $collection->image ? asset('collection-image/' . $collection->image) : asset('default.png') }}" 
                                     alt="{{ $collection->title }}" class="img-th" loading="lazy">
                            </td>
                            <td>{{ $collection->title }}</td>
                            <td>{{ $collection->category->name ?? '-' }}</td>
                            <td>{{ $collection->subcategory->name ?? '-' }}</td>
                            <td>
                                @if($collection->video_status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-warning">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @switch($collection->status)
                                    @case('processing')
                                        <span class="badge bg-warning">Processing</span>
                                        @break
                                    @case('ready')
                                        <span class="badge bg-success">Ready</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger">Failed</span>
                                        @break
                                    @case('deleted')
                                        <span class="badge bg-secondary">Deleted</span>
                                        @break
                                    @default
                                        <span class="badge bg-dark">Unknown</span>
                                @endswitch
                            </td>
                            <td>
                                @switch($collection->visibility) {{-- or $collection->video_status --}}
                                    @case('public')
                                        <span class="badge bg-success">Public</span>
                                        @break
                                    @case('private')
                                        <span class="badge bg-danger">Private</span>
                                        @break
                                    @case('unlisted')
                                        <span class="badge bg-warning">Unlisted</span>
                                        @break
                                    @default
                                        <span class="badge bg-dark">Unknown</span>
                                @endswitch
                            </td>
                            <td>
                                <a href="{{ route($prefix.'.collection.edit', encrypt($collection->id)) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route($prefix.'.collection.destroy', encrypt($collection->id)) }}" 
                                      method="POST" style="display:inline-block;" 
                                      onsubmit="return confirm('Are you sure want to delete?')">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @section('css')
        <style>
            img.img-th {
                height: 40px;
                width: auto;
                border-radius: 5px;
            }
        </style>
    @endsection

    @section('js')
        <script>
            $(function() {
                $('#collectionTable').DataTable({
                    paging: true,
                    searching: true,
                    ordering: true,
                    responsive: true,
                });
            });
        </script>
    @endsection
</x-admin>
