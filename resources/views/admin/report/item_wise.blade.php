@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Item Wise Report</span>
    </h5>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.item_wise') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Select Item <span class="text-danger">*</span></label>
                    <select name="item_id" class="form-select select2" required>
                        <option value="">Select Item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotationItems) && $quotationItems->count() > 0)
    <div class="card mt-3">
        <div class="card-header"><strong>Results for: {{ $quotationItems->first()->item->name ?? 'N/A' }}</strong></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Quotation No</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotationItems as $key => $qi)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $qi->quotation->quotation_number ?? 'N/A' }}</td>
                            <td>{{ $qi->quotation->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $qi->quotation->date ? date('d-m-Y', strtotime($qi->quotation->date)) : 'N/A' }}</td>
                            <td>{{ $qi->quantity }}</td>
                            <td>{{ number_format($qi->rate, 2) }}</td>
                            <td>{{ number_format($qi->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>{{ $quotationItems->sum('quantity') }}</strong></td>
                            <td></td>
                            <td><strong>{{ number_format($quotationItems->sum('total'), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @elseif(request('item_id'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">No quotations found for this item.</div>
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
