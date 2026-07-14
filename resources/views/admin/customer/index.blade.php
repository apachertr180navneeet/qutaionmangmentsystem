@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Customer List
        </h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.customers.import_template') }}" class="btn btn-outline-primary" style="border-radius: 8px;">
                <i class="bx bx-download me-1"></i> Template
            </a>
            <button type="button" class="btn-gradient-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-upload me-1"></i> Import
            </button>
            <a href="{{ route('admin.customers.create') }}" class="btn-success-custom">
                <i class="bx bx-plus-circle me-1"></i> Add New Customer
            </a>
        </div>
    </div>
    <div class="custom-card mb-4 p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex gap-3 flex-wrap align-items-center flex-grow-1">
                <div class="flex-grow-1" style="max-width: 400px;">
                    <input type="text" name="search" class="custom-input no-icon" placeholder="Search by company, person, email..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn-gradient-primary"><i class="bx bx-search me-1"></i> Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-primary" style="border-radius: 8px;">Clear</a>
                @endif
            </form>
        </div>
    </div>
    <div class="custom-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>COMPANY NAME</th>
                        <th>CONTACT PERSON</th>
                        <th>EMAIL</th>
                        <th>PHONE</th>
                        <th>GST</th>
                        <th>STATUS</th>
                        <th class="text-center">ACTIONS</th>
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
                                <span class="badge-custom badge-active">ACTIVE</span>
                            @else
                                <span class="badge-custom badge-inactive">INACTIVE</span>
                            @endif
                        </td>
                        <td class="text-center table-actions">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-btn-outline btn-outline-view" title="View"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-btn-outline btn-outline-edit" title="Edit"><i class="bx bx-edit"></i></a>
                            <button type="button" class="action-btn-outline btn-outline-delete delete-customer" data-id="{{ $customer->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                            <form id="delete-form-{{ $customer->id }}" action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" style="display:none;">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center p-4">
            {{ $customers->links() }}
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.customers.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Customers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label custom-form-label">Upload CSV/Excel File</label>
                        <input class="form-control" type="file" id="file" name="file" accept=".csv, .xlsx, .xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" style="border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn-gradient-primary">Import</button>
                </div>
            </div>
        </form>
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
            confirmButtonColor: '#ff6b6b',
            cancelButtonColor: '#cbd5e1',
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
