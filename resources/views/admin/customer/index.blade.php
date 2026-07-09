@extends('admin.layouts.app')
@section('style')
<style>
.table-actions { white-space: nowrap; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Customer List</span>
    </h5>
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control" placeholder="Search by company, person, email..." value="{{ request('search') }}" style="min-width:250px;">
                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.customers.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> Add New Customer</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>GST</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $key => $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $key }}</td>
                            <td>{{ $customer->company_name }}</td>
                            <td>{{ $customer->contact_person }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->gst_number }}</td>
                            <td>
                                @if($customer->status)
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center table-actions">
                                <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-info" title="View"><i class="bx bx-show"></i></a>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="bx bx-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-danger delete-customer" data-id="{{ $customer->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                                <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    $('.delete-customer').on('click', function(){
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
