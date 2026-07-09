@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Date Wise Report</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.date_wise') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">From Date <span class="text-danger">*</span></label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date <span class="text-danger">*</span></label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" required>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotations) && $quotations->count() > 0)
    <div class="card mt-3">
        <div class="card-header"><strong>Results from {{ request('from_date') }} to {{ request('to_date') }}</strong></div>
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
    @elseif(request('from_date') && request('to_date'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">No quotations found in this date range.</div>
    </div>
    @endif
</div>
@endsection
