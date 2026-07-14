@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">
            Quotation List
        </h4>
        <a href="{{ route('admin.quotations.create') }}" class="btn-success-custom">
            <i class="bx bx-plus-circle me-1"></i> Create Quotation
        </a>
    </div>

    <div class="custom-card mb-4 p-3">
        <form method="GET" action="{{ route('admin.quotations.index') }}" class="d-flex flex-wrap gap-3 align-items-center">
            <div class="flex-grow-1" style="max-width: 400px;">
                <input type="text" name="search" class="custom-input" placeholder="Search by quotation no..." value="{{ request('search') }}">
            </div>
            <div style="min-width: 200px;">
                <select name="status" class="custom-select">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-gradient-primary">
                    <i class="bx bx-filter-alt me-1"></i> Filter
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.quotations.index') }}" class="btn btn-outline-secondary ms-2" style="border-radius: 8px;">Clear</a>
                @endif
            </div>
        </form>
    </div>

    <div class="custom-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>QUOTATION NO</th>
                        <th>CUSTOMER</th>
                        <th>DATE</th>
                        <th>GRAND TOTAL</th>
                        <th>STATUS</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $key => $quotation)
                    <tr>
                        <td>{{ $quotations->firstItem() + $key }}</td>
                        <td><span class="text-purple-custom">{{ $quotation->quotation_number }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="avatar-circle">{{ strtoupper(substr($quotation->customer->company_name ?? 'N', 0, 1)) }}</span>
                                <span class="table-dark-text">{{ $quotation->customer->company_name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="date-text">{{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</td>
                        <td class="table-dark-text">${{ number_format($quotation->grand_total, 2) }}</td>
                        <td>
                            @php
                                $statusClass = match(strtolower($quotation->status)) {
                                    'draft' => 'badge-draft',
                                    'sent' => 'badge-sent',
                                    'approved' => 'badge-approved',
                                    'expired' => 'badge-expired',
                                    'rejected' => 'badge-rejected',
                                    default => 'badge-draft'
                                };
                            @endphp
                            <span class="badge-custom {{ $statusClass }}">{{ strtoupper($quotation->status) }}</span>
                        </td>
                        <td class="text-center table-actions">
                            <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="action-btn-outline btn-outline-view" title="View"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="action-btn-outline btn-outline-edit" title="Edit"><i class="bx bx-edit"></i></a>
                            <button type="button" class="action-btn-outline btn-outline-delete delete-quotation" data-id="{{ $quotation->id }}" title="Delete"><i class="bx bx-trash"></i></button>
                            <form id="delete-form-{{ $quotation->id }}" action="{{ route('admin.quotations.destroy', $quotation->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bx bx-receipt text-muted mb-2" style="font-size: 3rem;"></i>
                                <h6 class="text-muted">No quotations found</h6>
                                <a href="{{ route('admin.quotations.create') }}" class="btn btn-sm btn-primary mt-2">Create One</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotations->hasPages())
        <div class="d-flex justify-content-center p-4">
            {{ $quotations->links() }}
        </div>
        @endif
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
