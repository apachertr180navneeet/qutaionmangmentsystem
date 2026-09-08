@extends('admin.layouts.app')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0">
            <span class="text-primary fw-light">Reports /</span> Customer Wise Report
        </h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> All Reports
        </a>
    </div>

    <div class="card filter-card no-print mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.customer_wise') }}" class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Select Customer <span class="text-danger">*</span></label>
                    <select name="customer_id" class="form-select select2" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search me-1"></i> Generate Report</button>
                    @if(request('customer_id'))
                        <a href="{{ route('admin.reports.customer_wise') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(isset($quotations) && $quotations->count() > 0)
    <div class="card report-result-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
            <div>
                <h5 class="card-title mb-0">
                    <strong>Results for:</strong> {{ $customer->company_name ?? ($quotations->first()->customer->company_name ?? 'N/A') }}
                </h5>
                <small class="text-muted">Found {{ $quotations->total() }} quotation(s)</small>
            </div>
            <div class="report-actions no-print">
                <button type="button" class="btn-action btn-report-print" onclick="window.print()">
                    <i class="bx bx-printer"></i>
                    <span>Print</span>
                </button>
                <a href="{{ route('admin.reports.export.pdf', ['report_type' => 'customer_wise', 'customer_id' => request('customer_id')]) }}" class="btn-action btn-report-pdf" target="_blank">
                    <i class="bx bxs-file-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('admin.reports.export.excel', ['report_type' => 'customer_wise', 'customer_id' => request('customer_id')]) }}" class="btn-action btn-report-excel">
                    <i class="bx bx-spreadsheet"></i>
                    <span>Excel</span>
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Print-only header -->
            <div class="print-only p-3 mb-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 fw-bold">{{ config('app.name', 'Quotation Management System') }}</h4>
                        <h5 class="mb-1 text-primary">Customer Wise Quotation Report</h5>
                        <p class="mb-0 text-muted"><strong>Customer:</strong> {{ $customer->company_name ?? ($quotations->first()->customer->company_name ?? 'N/A') }}</p>
                    </div>
                    <div class="text-end text-muted small">
                        <div><strong>Printed On:</strong> {{ date('d-m-Y H:i') }}</div>
                        <div><strong>Total Records:</strong> {{ $quotations->total() }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">Quotation No</th>
                            <th style="width: 20%;">Date</th>
                            <th style="width: 25%;" class="text-end">Grand Total</th>
                            <th style="width: 25%;" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotations as $key => $q)
                        @php $badge = $q->status == 'approved' ? 'success' : ($q->status == 'sent' ? 'primary' : ($q->status == 'draft' ? 'secondary' : ($q->status == 'expired' ? 'warning' : 'danger'))); @endphp
                        <tr>
                            <td>{{ $quotations->firstItem() + $key }}</td>
                            <td class="fw-semibold">
                                <a href="{{ route('admin.quotations.show', $q->id) }}" class="no-print text-primary">{{ $q->quotation_number }}</a>
                                <span class="print-only">{{ $q->quotation_number }}</span>
                            </td>
                            <td>{{ $q->date ? date('d-m-Y', strtotime($q->date)) : ($q->created_at ? date('d-m-Y', strtotime($q->created_at)) : 'N/A') }}</td>
                            <td class="text-end fw-semibold">₹{{ formatNumber($q->grand_total) }}</td>
                            <td class="text-center"><span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total (All Pages):</strong></td>
                            <td class="text-end"><strong>₹{{ formatNumber($totalGrandTotal) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3 mb-3 no-print">
                {{ $quotations->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    @elseif(request('customer_id'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">
            <i class="bx bx-info-circle fs-1 text-muted d-block mb-2"></i>
            No quotations found for this customer.
        </div>
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
