@extends('admin.layouts.app')
@section('style')
<style>
.table-actions { white-space: nowrap; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Quotation List</span>
    </h5>
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <form method="GET" action="{{ route('admin.quotations.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="text" name="search" class="form-control" placeholder="Search by quotation no..." value="{{ request('search') }}" style="min-width:200px;">
                <select name="status" class="form-select" style="min-width:150px;">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.quotations.create') }}" class="btn btn-success"><i class="bx bx-plus"></i> Create Quotation</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotations as $key => $quotation)
                        <tr>
                            <td>{{ $quotations->firstItem() + $key }}</td>
                            <td>{{ $quotation->quotation_number }}</td>
                            <td>{{ $quotation->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</td>
                            <td>{{ number_format($quotation->grand_total, 2) }}</td>
                            <td>
                                @php
                                    $statusClasses = ['draft' => 'bg-label-secondary', 'sent' => 'bg-label-primary', 'approved' => 'bg-label-success', 'expired' => 'bg-label-warning', 'rejected' => 'bg-label-danger'];
                                @endphp
                                <span class="badge {{ $statusClasses[$quotation->status] ?? 'bg-label-secondary' }}">{{ ucfirst($quotation->status) }}</span>
                            </td>
                            <td class="text-center table-actions">
                                <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="btn btn-sm btn-info" title="View"><i class="bx bx-show"></i></a>
                                <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn btn-sm btn-primary" title="Edit"><i class="bx bx-edit"></i></a>
                                <button type="button" class="btn btn-sm btn-danger delete-quotation" data-id="{{ $quotation->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                                <form id="delete-form-{{ $quotation->id }}" action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No quotations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{ $quotations->links() }}
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$(document).ready(function(){
    $('.delete-quotation').on('click', function(){
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
