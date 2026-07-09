@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Status Wise Report</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.status_wise') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Select Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="">Select Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotations) && $quotations->count() > 0)
    <div class="card mt-3">
        <div class="card-header"><strong>Results for: {{ ucfirst(request('status')) }}</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $key => $q)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $q->quotation_number }}</td>
                            <td>{{ $q->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $q->created_at ? date('d-m-Y', strtotime($q->created_at)) : 'N/A' }}</td>
                            <td>{{ number_format($q->grand_total, 2) }}</td>
                            <td>@php $badge = $q->status == 'approved' ? 'success' : ($q->status == 'sent' ? 'primary' : ($q->status == 'draft' ? 'secondary' : ($q->status == 'expired' ? 'warning' : 'danger'))); @endphp
                            <span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($quotations->sum('grand_total'), 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request('status'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">No quotations found with this status.</div>
    </div>
    @endif
</div>
@endsection
