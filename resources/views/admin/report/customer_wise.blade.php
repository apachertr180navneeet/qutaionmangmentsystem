@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Customer Wise Report</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.customer_wise') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select select2" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }}</option>
                        @endforeach
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
        <div class="card-header"><strong>Results for: {{ $quotations->first()->customer->company_name ?? 'N/A' }}</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Date</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $key => $q)
                        @php $badge = $q->status == 'approved' ? 'success' : ($q->status == 'sent' ? 'primary' : ($q->status == 'draft' ? 'secondary' : ($q->status == 'expired' ? 'warning' : 'danger'))); @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $q->quotation_number }}</td>
                            <td>{{ $q->created_at ? date('d-m-Y', strtotime($q->created_at)) : 'N/A' }}</td>
                            <td>{{ number_format($q->grand_total, 2) }}</td>
                            <td><span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>{{ number_format($quotations->sum('grand_total'), 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request('customer_id'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">No quotations found for this customer.</div>
    </div>
    @endif
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%' });
});
</script>
@endsection
