@extends('admin.layouts.app')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h5 class="mb-0">
            <span class="text-primary fw-light">Reports /</span> Date Wise Report
        </h5>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bx bx-arrow-back me-1"></i> All Reports
        </a>
    </div>

    <div class="card filter-card no-print mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.date_wise') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">From Date <span class="text-danger">*</span></label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">To Date <span class="text-danger">*</span></label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" required>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-search me-1"></i> Generate Report</button>
                    @if(request('from_date') && request('to_date'))
                        <a href="{{ route('admin.reports.date_wise') }}" class="btn btn-outline-secondary">Reset</a>
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
                    <strong>Results:</strong> {{ date('d-m-Y', strtotime(request('from_date'))) }} to {{ date('d-m-Y', strtotime(request('to_date'))) }}
                </h5>
                <small class="text-muted">Found {{ $quotations->total() }} quotation(s)</small>
            </div>
            <div class="report-actions no-print">
                <button type="button" class="btn-action btn-report-print" onclick="window.print()">
                    <i class="bx bx-printer"></i>
                    <span>Print</span>
                </button>
                <a href="{{ route('admin.reports.export.pdf', ['report_type' => 'date_wise', 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="btn-action btn-report-pdf" target="_blank">
                    <i class="bx bxs-file-pdf"></i>
                    <span>PDF</span>
                </a>
                <a href="{{ route('admin.reports.export.excel', ['report_type' => 'date_wise', 'from_date' => request('from_date'), 'to_date' => request('to_date')]) }}" class="btn-action btn-report-excel">
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
                        <h5 class="mb-1 text-primary">Date Wise Quotation Report</h5>
                        <p class="mb-0 text-muted"><strong>Period:</strong> {{ date('d-m-Y', strtotime(request('from_date'))) }} to {{ date('d-m-Y', strtotime(request('to_date'))) }}</p>
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
                            <th style="width: 20%;">Quotation No</th>
                            <th style="width: 30%;">Customer</th>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 15%;" class="text-end">Grand Total</th>
                            <th style="width: 15%;" class="text-center">Status</th>
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
                            <td>{{ $q->customer->company_name ?? 'N/A' }}</td>
                            <td>{{ $q->date ? date('d-m-Y', strtotime($q->date)) : ($q->created_at ? date('d-m-Y', strtotime($q->created_at)) : 'N/A') }}</td>
                            <td class="text-end fw-semibold">₹{{ formatNumber($q->grand_total) }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $badge }}">{{ ucfirst($q->status) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total (All Pages):</strong></td>
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
    @elseif(request('from_date') && request('to_date'))
    <div class="card mt-3">
        <div class="card-body text-center text-muted py-4">
            <i class="bx bx-info-circle fs-1 text-muted d-block mb-2"></i>
            No quotations found in this date range.
        </div>
    </div>
    @endif
</div>
@endsection
