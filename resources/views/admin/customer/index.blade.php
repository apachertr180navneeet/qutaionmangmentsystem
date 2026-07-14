@extends('admin.layouts.app')
@section('style')
<style>
.customer-page-title {
    color: #9f7aea;
    font-weight: 500;
    font-size: 1.15rem;
    margin-bottom: 1rem;
    padding-left: 0.25rem;
}
.custom-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    border: none;
    overflow: hidden;
}
.custom-card-header {
    padding: 1.5rem;
    border-bottom: none;
    background: transparent;
}
.search-input {
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
    min-width: 320px;
    outline: none;
    transition: all 0.2s;
    color: #475569;
}
.search-input::placeholder {
    color: #cbd5e1;
}
.search-input:focus {
    border-color: #9f7aea;
    box-shadow: 0 0 0 3px rgba(159, 122, 234, 0.1);
}
.btn-gradient-search {
    background: linear-gradient(135deg, #9f7aea, #4bd2f2);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.25rem;
    font-weight: 500;
    font-size: 0.9rem;
    transition: opacity 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}
.btn-gradient-search:hover {
    opacity: 0.9;
    color: white;
}
.btn-add-customer {
    background-color: #7bd45c;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.25rem;
    font-weight: 500;
    font-size: 0.9rem;
    transition: background-color 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}
.btn-add-customer:hover {
    background-color: #6ac44e;
    color: white;
}
.custom-table {
    margin-bottom: 0;
    width: 100%;
    border-collapse: collapse;
}
.custom-table thead th {
    background-color: #fcfcfd;
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    border-top: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
}
.custom-table thead th:last-child {
    border-right: none;
}
.custom-table tbody td {
    padding: 1.1rem 1.5rem;
    vertical-align: middle;
    color: #64748b;
    font-size: 0.875rem;
    border-bottom: 1px solid #f1f5f9;
    border-right: 1px solid #f1f5f9;
}
.custom-table tbody td:last-child {
    border-right: none;
}
.custom-table tbody tr:hover td {
    background-color: #f8fafc;
}
.status-badge-active {
    background-color: #e6f8ea;
    color: #52c463;
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.status-badge-inactive {
    background-color: #fee2e2;
    color: #ef4444;
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    color: white;
    margin: 0 2px;
    transition: transform 0.2s, box-shadow 0.2s;
    text-decoration: none;
}
.action-btn:hover {
    transform: translateY(-2px);
    color: white;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.btn-view {
    background-color: #4bd2f2;
}
.btn-edit {
    background: linear-gradient(135deg, #9f7aea, #4bd2f2);
}
.btn-delete {
    background-color: #ff6b6b;
}
.table-actions {
    white-space: nowrap;
}
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="customer-page-title">
        Customer List
    </div>
    <div class="custom-card">
        <div class="custom-card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex gap-3 flex-wrap align-items-center">
                <input type="text" name="search" class="search-input" placeholder="Search by company, person, email..." value="{{ request('search') }}">
                <button type="submit" class="btn-gradient-search"><i class="bx bx-search"></i> Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.customers.create') }}" class="btn-add-customer"><i class="bx bx-plus"></i> Add New Customer</a>
        </div>
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
                                <span class="status-badge-active">ACTIVE</span>
                            @else
                                <span class="status-badge-inactive">INACTIVE</span>
                            @endif
                        </td>
                        <td class="text-center table-actions">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="action-btn btn-view" title="View"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="action-btn btn-edit" title="Edit"><i class="bx bx-edit"></i></a>
                            <button type="button" class="action-btn btn-delete delete-customer" data-id="{{ $customer->id }}" title="Delete"><i class="bx bx-trash"></i></button>
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
