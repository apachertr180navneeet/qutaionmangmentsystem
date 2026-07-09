@extends('admin.layouts.app')
@section('style')
<style>
.table-actions { white-space: nowrap; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Item List</span>
    </h5>
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('admin.items.index') }}" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control" placeholder="Search by name, SKU..." value="{{ request('search') }}" style="min-width:250px;">
                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.items.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> Add New Item</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Unit</th>
                            <th>Rate</th>
                            <th>Tax (%)</th>
                            <th>Type</th>
                            <th>HSN Code</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $item)
                        <tr>
                            <td>{{ $items->firstItem() + $key }}</td>
                            <td>
                                @if($item->image)
                                    <img src="{{ $item->image }}" alt="{{ $item->name }}" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->sku }}</td>
                            <td>{{ $item->unit }}</td>
                            <td>{{ number_format($item->rate, 2) }}</td>
                            <td>{{ $item->tax_percentage }}</td>
                            <td>{{ ucfirst($item->type) }}</td>
                            <td>{{ $item->hsn_code }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center table-actions">
                                <a href="{{ route('admin.items.show', $item->id) }}" class="btn btn-sm btn-info" title="View"><i class="bx bx-show"></i></a>
                                <a href="{{ route('admin.items.edit', $item->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="bx bx-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-danger delete-item" data-id="{{ $item->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.items.destroy', $item->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center text-muted py-3">No items found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    $('.delete-item').on('click', function(){
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });
});
</script>
@endsection
