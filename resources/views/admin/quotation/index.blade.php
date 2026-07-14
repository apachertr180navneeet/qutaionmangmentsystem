@extends('admin.layouts.app')
@section('style')
<style>
.table-actions { white-space: nowrap; }
</style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0" style="background: -webkit-linear-gradient(45deg, #8E2DE2, #4A00E0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            Quotation List
        </h4>
        <a href="{{ route('admin.quotations.create') }}" class="btn btn-success shadow-sm" style="background: linear-gradient(135deg, #28a745, #20c997); border: none;">
            <i class="bx bx-plus-circle me-1"></i> Create Quotation
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.quotations.index') }}" class="row g-3 align-items-center">
                <div class="col-12 col-md-4 col-lg-5">
                    <div class="input-group input-group-merge shadow-sm">
                        <span class="input-group-text border-0" id="basic-addon-search31"><i class="bx bx-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0" placeholder="Search by quotation no..." value="{{ request('search') }}" aria-label="Search" aria-describedby="basic-addon-search31">
                    </div>
                </div>
                <div class="col-12 col-md-4 col-lg-3">
                    <select name="status" class="form-select shadow-sm border-0">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 col-lg-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary shadow-sm" style="background: linear-gradient(135deg, #8E2DE2, #4A00E0); border: none;">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.quotations.index') }}" class="btn btn-light shadow-sm text-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">#</th>
                        <th class="fw-bold">Quotation No</th>
                        <th class="fw-bold">Customer</th>
                        <th class="fw-bold">Date</th>
                        <th class="fw-bold">Grand Total</th>
                        <th class="fw-bold">Status</th>
                        <th class="fw-bold text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($quotations as $key => $quotation)
                    <tr>
                        <td><span class="text-muted">{{ $quotations->firstItem() + $key }}</span></td>
                        <td><span class="fw-medium text-primary">{{ $quotation->quotation_number }}</span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ substr($quotation->customer->company_name ?? 'N', 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-truncate" style="max-width: 150px;">{{ $quotation->customer->company_name ?? 'N/A' }}</h6>
                                </div>
                            </div>
                        </td>
                        <td>{{ $quotation->created_at ? date('d-m-Y', strtotime($quotation->created_at)) : 'N/A' }}</td>
                        <td><strong>${{ number_format($quotation->grand_total, 2) }}</strong></td>
                        <td>
                            @php
                                $statusClasses = [
                                    'draft' => 'bg-label-secondary',
                                    'sent' => 'bg-label-info',
                                    'approved' => 'bg-label-success',
                                    'expired' => 'bg-label-warning',
                                    'rejected' => 'bg-label-danger'
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$quotation->status] ?? 'bg-label-secondary' }} rounded-pill">{{ ucfirst($quotation->status) }}</span>
                        </td>
                        <td class="text-center table-actions">
                            <a href="{{ route('admin.quotations.show', $quotation->id) }}" class="btn btn-sm btn-icon btn-outline-info rounded-circle me-1" title="View"><i class="bx bx-show"></i></a>
                            <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="btn btn-sm btn-icon btn-outline-primary rounded-circle me-1" title="Edit"><i class="bx bx-edit"></i></a>
                            <button type="button" class="btn btn-sm btn-icon btn-outline-danger rounded-circle delete-quotation" data-id="{{ $quotation->id }}" title="Delete"><i class="bx bx-trash"></i></button>
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
        <div class="card-footer d-flex justify-content-center border-top">
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
